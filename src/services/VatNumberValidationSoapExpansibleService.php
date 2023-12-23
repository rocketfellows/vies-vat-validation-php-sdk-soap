<?php

namespace rocketfellows\ViesVatValidationSoap\services;

use rocketfellows\SoapClientFactory\SoapClientFactory;
use rocketfellows\ViesVatValidationInterface\FaultCodeExceptionFactory;
use rocketfellows\ViesVatValidationSoap\AbstractVatNumberValidationSoapService;

class VatNumberValidationSoapExpansibleService extends AbstractVatNumberValidationSoapService
{
    private $wsdl;

    public function __construct(
        string $wsdl,
        FaultCodeExceptionFactory $faultCodeExceptionFactory,
        SoapClientFactory $soapClientFactory
    ) {
        parent::__construct($faultCodeExceptionFactory, $soapClientFactory);

        $this->wsdl = $wsdl;
    }

    protected function getWsdlSource(): string
    {
        return $this->wsdl;
    }
}
