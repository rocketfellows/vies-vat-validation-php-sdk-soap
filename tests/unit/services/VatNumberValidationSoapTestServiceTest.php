<?php

namespace rocketfellows\ViesVatValidationSoap\tests\unit\services;

/**
 * @group vies-vat-validation-soap
 */
class VatNumberValidationSoapTestServiceTest extends VatNumberValidationSoapServiceTest
{
    protected const EXPECTED_WSDL_SOURCE = 'https://ec.europa.eu/taxation_customs/vies/checkVatTestService.wsdl';
}
