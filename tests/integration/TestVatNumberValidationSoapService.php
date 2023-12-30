<?php

namespace rocketfellows\ViesVatValidationSoap\tests\integration;

use rocketfellows\ViesVatValidationSoap\AbstractVatNumberValidationSoapService;

class TestVatNumberValidationSoapService extends AbstractVatNumberValidationSoapService
{
    protected function getWsdlSource(): string
    {
        return 'https://ec.europa.eu/taxation_customs/vies/checkVatTestService.wsdl';
    }
}
