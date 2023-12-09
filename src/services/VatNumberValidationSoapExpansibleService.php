<?php

namespace rocketfellows\ViesVatValidationSoap\services;

use rocketfellows\SoapClientFactory\SoapClientFactory;
use rocketfellows\ViesVatValidationSoap\AbstractVatNumberValidationSoapService;

class VatNumberValidationSoapExpansibleService extends AbstractVatNumberValidationSoapService
{
    private $wsdl;

    public function __construct(
        string $wsdl,
        SoapClientFactory $soapClientFactory
    ) {
        parent::__construct($soapClientFactory);

        $this->wsdl = $wsdl;
    }

    protected function getWsdlSource(): string
    {
        return $this->wsdl;
    }
}
