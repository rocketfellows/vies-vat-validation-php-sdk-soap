<?php

namespace rocketfellows\ViesVatValidationSoap;

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
use rocketfellows\ViesVatValidationInterface\VatNumber;
use rocketfellows\ViesVatValidationInterface\VatNumberValidationResult;
use rocketfellows\ViesVatValidationInterface\VatNumberValidationServiceInterface;
use SoapFault;

class AbstractVatNumberValidationSoapService implements VatNumberValidationServiceInterface
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

    private $soapClientFactory;

    public function __construct(
        SoapClientFactory $soapClientFactory
    ) {
        $this->soapClientFactory = $soapClientFactory;
    }

    abstract protected function getWsdlSource(): string;

    public function validateVat(VatNumber $vatNumber): VatNumberValidationResult
    {
        // TODO: Implement validateVat() method.
    }

    private function handleSoapFault(SoapFault $fault): void
    {
        // TODO: implement
        switch ($fault->getMessage()) {
            case self::SOAP_FAULT_CODE_INVALID_INPUT:
                throw new InvalidInputServiceException($fault->getMessage(), $fault->getCode(), $fault);
            case self::SOAP_FAULT_CODE_SERVICE_UNAVAILABLE:
                throw new ServiceUnavailableException($fault->getMessage(), $fault->getCode(), $fault);
            case self::SOAP_FAULT_CODE_MS_UNAVAILABLE:
                throw new MSUnavailableServiceException($fault->getMessage(), $fault->getCode(), $fault);
            case self::SOAP_FAULT_CODE_TIMEOUT:
                throw new TimeoutServiceException($fault->getMessage(), $fault->getCode(), $fault);
            case self::SOAP_FAULT_CODE_INVALID_REQUESTER_INFO:
                throw new InvalidRequesterInfoServiceException($fault->getMessage(), $fault->getCode(), $fault);
            case self::SOAP_FAULT_CODE_VAT_BLOCKED:
                throw new VatBlockedServiceException($fault->getMessage(), $fault->getCode(), $fault);
            case self::SOAP_FAULT_CODE_IP_BLOCKED:
                throw new IPBlockedServiceException($fault->getMessage(), $fault->getCode(), $fault);
            case self::SOAP_FAULT_CODE_GLOBAL_MAX_CONCURRENT_REQ:
                throw new GlobalMaxConcurrentReqServiceException($fault->getMessage(), $fault->getCode(), $fault);
            case self::SOAP_FAULT_CODE_GLOBAL_MAX_CONCURRENT_REQ_TIME:
                throw new GlobalMaxConcurrentReqTimeServiceException($fault->getMessage(), $fault->getCode(), $fault);
            case self::SOAP_FAULT_CODE_MS_MAX_CONCURRENT_REQ:
                throw new MSMaxConcurrentReqServiceException($fault->getMessage(), $fault->getCode(), $fault);
            case self::SOAP_FAULT_CODE_MS_MAX_CONCURRENT_REQ_TIME:
                throw new MSMaxConcurrentReqTimeServiceException($fault->getMessage(), $fault->getCode(), $fault);
        }
    }
}
