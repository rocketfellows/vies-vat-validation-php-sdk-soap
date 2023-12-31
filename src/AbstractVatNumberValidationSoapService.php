<?php

namespace rocketfellows\ViesVatValidationSoap;

use Exception;
use rocketfellows\SoapClientFactory\SoapClientFactory;
use rocketfellows\ViesVatValidationInterface\exceptions\ServiceRequestException;
use rocketfellows\ViesVatValidationInterface\FaultCodeExceptionFactory;
use rocketfellows\ViesVatValidationInterface\VatNumber;
use rocketfellows\ViesVatValidationInterface\VatNumberValidationResult;
use rocketfellows\ViesVatValidationInterface\VatNumberValidationResultFactory;
use rocketfellows\ViesVatValidationInterface\VatNumberValidationServiceInterface;
use SoapFault;
use stdClass;

abstract class AbstractVatNumberValidationSoapService implements VatNumberValidationServiceInterface
{
    private $faultCodeExceptionFactory;
    private $soapClientFactory;
    private $vatNumberValidationResultFactory;

    public function __construct(
        FaultCodeExceptionFactory $faultCodeExceptionFactory,
        SoapClientFactory $soapClientFactory,
        VatNumberValidationResultFactory $vatNumberValidationResultFactory
    ) {
        $this->faultCodeExceptionFactory = $faultCodeExceptionFactory;
        $this->soapClientFactory = $soapClientFactory;
        $this->vatNumberValidationResultFactory = $vatNumberValidationResultFactory;
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
        return $this->faultCodeExceptionFactory->create($fault->getMessage(), $fault->getMessage());
    }
}
