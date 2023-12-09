<?php

namespace rocketfellows\ViesVatValidationSoap\services;

use rocketfellows\ViesVatValidationSoap\AbstractVatNumberValidationSoapService;

class VatNumberValidationSoapService extends AbstractVatNumberValidationSoapService
{
    protected function getWsdlSource(): string
    {
        return 'https://ec.europa.eu/taxation_customs/vies/services/checkVatService.wsdl';
    }
}
