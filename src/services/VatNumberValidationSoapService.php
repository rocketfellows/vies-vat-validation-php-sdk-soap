<?php

namespace rocketfellows\ViesVatValidationSoap\services;

use rocketfellows\ViesVatValidationSoap\AbstractVatNumberValidationSoapService;

class VatNumberValidationSoapService extends AbstractVatNumberValidationSoapService
{
    private const WSDL_SOURCE = 'https://ec.europa.eu/taxation_customs/vies/services/checkVatService.wsdl';

    protected function getWsdlSource(): string
    {
        return self::WSDL_SOURCE;
    }
}
