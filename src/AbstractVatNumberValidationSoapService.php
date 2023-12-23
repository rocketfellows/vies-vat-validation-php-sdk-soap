<?php

namespace rocketfellows\ViesVatValidationSoap;

use Exception;
use rocketfellows\SoapClientFactory\SoapClientFactory;
use rocketfellows\ViesVatValidationInterface\exceptions\service\GlobalMaxConcurrentReqServiceException;
use rocketfellows\ViesVatValidationInterface\exceptions\service\GlobalMaxConcurrentReqTimeServiceException;
use rocketfellows\ViesVatValidationInterface\exceptions\service\InvalidInputServiceException;
use rocketfellows\ViesVatValidationInterface\exceptions\service\InvalidRequesterInfoServiceException;
use rocketfellows\ViesVatValidationInterface\exceptions\service\IPBlockedServiceException;
use rocketfellows\ViesVatValidationInterface\exceptions\service\MSMaxConcurrentReqServiceException;
use rocketfellows\ViesVatValidationInterface\exceptions\service\MSMaxConcurrentReqTimeServiceException;
use rocketfellows\ViesVatValidationInterface\exceptions\service\MSUnavailableServiceException;
use rocketfellows\ViesVatValidationInterface\exceptions\service\ServiceRequestException;
use rocketfellows\ViesVatValidationInterface\exceptions\service\ServiceUnavailableException;
use rocketfellows\ViesVatValidationInterface\exceptions\service\TimeoutServiceException;
use rocketfellows\ViesVatValidationInterface\exceptions\service\UnknownServiceErrorException;
use rocketfellows\ViesVatValidationInterface\exceptions\service\VatBlockedServiceException;
use rocketfellows\ViesVatValidationInterface\FaultCodeExceptionFactory;
use rocketfellows\ViesVatValidationInterface\VatNumber;
use rocketfellows\ViesVatValidationInterface\VatNumberValidationResult;
use rocketfellows\ViesVatValidationInterface\VatNumberValidationServiceInterface;
use SoapFault;
use stdClass;

abstract class AbstractVatNumberValidationSoapService implements VatNumberValidationServiceInterface
{
    private const SOAP_FAULT_CODE_INVALID_INPUT = 'INVALID_INPUT';
    private const SOAP_FAULT_CODE_SERVICE_UNAVAILABLE = 'SERVICE_UNAVAILABLE';
    private const SOAP_FAULT_CODE_MS_UNAVAILABLE = 'MS_UNAVAILABLE';
    private const SOAP_FAULT_CODE_TIMEOUT = 'TIMEOUT';
    private const SOAP_FAULT_CODE_INVALID_REQUESTER_INFO = 'INVALID_REQUESTER_INFO';
    private const SOAP_FAULT_CODE_VAT_BLOCKED = 'VAT_BLOCKED';
    private const SOAP_FAULT_CODE_IP_BLOCKED = 'IP_BLOCKED';
    private const SOAP_FAULT_CODE_GLOBAL_MAX_CONCURRENT_REQ = 'GLOBAL_MAX_CONCURRENT_REQ';
    private const SOAP_FAULT_CODE_GLOBAL_MAX_CONCURRENT_REQ_TIME = 'GLOBAL_MAX_CONCURRENT_REQ_TIME';
    private const SOAP_FAULT_CODE_MS_MAX_CONCURRENT_REQ = 'MS_MAX_CONCURRENT_REQ';
    private const SOAP_FAULT_CODE_MS_MAX_CONCURRENT_REQ_TIME = 'MS_MAX_CONCURRENT_REQ_TIME';

    private $faultCodeExceptionFactory;
    private $soapClientFactory;

    public function __construct(
        FaultCodeExceptionFactory $faultCodeExceptionFactory,
        SoapClientFactory $soapClientFactory
    ) {
        $this->faultCodeExceptionFactory = $faultCodeExceptionFactory;
        $this->soapClientFactory = $soapClientFactory;
    }

    abstract protected function getWsdlSource(): string;

    public function validateVat(VatNumber $vatNumber): VatNumberValidationResult
    {
        try {
            $client = $this->soapClientFactory->create($this->getWsdlSource());

            return $this->handleResponse(
                $client->checkVat([
                    'countryCode' => $vatNumber->getCountryCode(),
                    'vatNumber' => $vatNumber->getVatNumber(),
                ])
            );
        } catch (SoapFault $exception) {
            throw $this->handleSoapFault($exception);
        } catch (Exception $exception) {
            throw new ServiceRequestException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    private function handleResponse(stdClass $response): VatNumberValidationResult
    {
        return VatNumberValidationResult::create(
            VatNumber::create($response->countryCode ?? '', $response->vatNumber ?? ''),
            $response->requestDate ?? '',
            $response->valid ?? false,
            $response->name ?? null,
            $response->address ?? null
        );
    }

    private function handleSoapFault(SoapFault $fault): Exception
    {
        switch ($fault->getMessage()) {
            case self::SOAP_FAULT_CODE_INVALID_INPUT:
                return new InvalidInputServiceException($fault->getMessage(), $fault->getCode(), $fault);
            case self::SOAP_FAULT_CODE_SERVICE_UNAVAILABLE:
                return new ServiceUnavailableException($fault->getMessage(), $fault->getCode(), $fault);
            case self::SOAP_FAULT_CODE_MS_UNAVAILABLE:
                return new MSUnavailableServiceException($fault->getMessage(), $fault->getCode(), $fault);
            case self::SOAP_FAULT_CODE_TIMEOUT:
                return new TimeoutServiceException($fault->getMessage(), $fault->getCode(), $fault);
            case self::SOAP_FAULT_CODE_INVALID_REQUESTER_INFO:
                return new InvalidRequesterInfoServiceException($fault->getMessage(), $fault->getCode(), $fault);
            case self::SOAP_FAULT_CODE_VAT_BLOCKED:
                return new VatBlockedServiceException($fault->getMessage(), $fault->getCode(), $fault);
            case self::SOAP_FAULT_CODE_IP_BLOCKED:
                return new IPBlockedServiceException($fault->getMessage(), $fault->getCode(), $fault);
            case self::SOAP_FAULT_CODE_GLOBAL_MAX_CONCURRENT_REQ:
                return new GlobalMaxConcurrentReqServiceException($fault->getMessage(), $fault->getCode(), $fault);
            case self::SOAP_FAULT_CODE_GLOBAL_MAX_CONCURRENT_REQ_TIME:
                return new GlobalMaxConcurrentReqTimeServiceException($fault->getMessage(), $fault->getCode(), $fault);
            case self::SOAP_FAULT_CODE_MS_MAX_CONCURRENT_REQ:
                return new MSMaxConcurrentReqServiceException($fault->getMessage(), $fault->getCode(), $fault);
            case self::SOAP_FAULT_CODE_MS_MAX_CONCURRENT_REQ_TIME:
                return new MSMaxConcurrentReqTimeServiceException($fault->getMessage(), $fault->getCode(), $fault);
            default:
                return new UnknownServiceErrorException($fault->getMessage(), $fault->getCode(), $fault);
        }
    }
}
