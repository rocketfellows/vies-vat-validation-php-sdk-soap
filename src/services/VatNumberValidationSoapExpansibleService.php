<?php

namespace rocketfellows\ViesVatValidationSoap\services;

use rocketfellows\SoapClientFactory\SoapClientFactory;
use rocketfellows\ViesVatValidationInterface\FaultCodeExceptionFactory;
use rocketfellows\ViesVatValidationInterface\VatNumberValidationResultFactory;
use rocketfellows\ViesVatValidationSoap\AbstractVatNumberValidationSoapService;

class VatNumberValidationSoapExpansibleService extends AbstractVatNumberValidationSoapService
{
    private $wsdl;

    public function __construct(
        string $wsdl,
        FaultCodeExceptionFactory $faultCodeExceptionFactory,
        SoapClientFactory $soapClientFactory,
        VatNumberValidationResultFactory $vatNumberValidationResultFactory
    ) {
        parent::__construct($faultCodeExceptionFactory, $soapClientFactory, $vatNumberValidationResultFactory);

        $this->wsdl = $wsdl;
    }

    protected function getWsdlSource(): string
    {
        return $this->wsdl;
    }
}
