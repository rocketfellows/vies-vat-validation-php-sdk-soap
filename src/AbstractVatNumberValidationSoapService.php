<?php

namespace rocketfellows\ViesVatValidationSoap;

use rocketfellows\SoapClientFactory\SoapClientFactory;
use rocketfellows\ViesVatValidationInterface\FaultCodeExceptionFactory;
use rocketfellows\ViesVatValidationInterface\VatNumber;
use rocketfellows\ViesVatValidationInterface\VatNumberValidationResult;
use rocketfellows\ViesVatValidationInterface\VatNumberValidationResultFactory;
use rocketfellows\ViesVatValidationInterface\VatNumberValidationServiceInterface;
use SoapFault;

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
            return $this->vatNumberValidationResultFactory->createFromObject(
                $this->soapClientFactory->create($this->getWsdlSource())
                    ->checkVat([
                        'countryCode' => $vatNumber->getCountryCode(),
                        'vatNumber' => $vatNumber->getVatNumber(),
                    ])
            );
        } catch (SoapFault $exception) {
            throw $this->faultCodeExceptionFactory->create($exception->getMessage(), $exception->getMessage());
        }
    }
}
