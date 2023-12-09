<?php

namespace rocketfellows\ViesVatValidationSoap\tests\unit\services;

use rocketfellows\ViesVatValidationInterface\VatNumberValidationServiceInterface;
use rocketfellows\ViesVatValidationSoap\services\VatNumberValidationSoapTestService;

/**
 * @group vies-vat-validation-soap
 */
class VatNumberValidationSoapTestServiceTest extends VatNumberValidationSoapServiceTest
{
    protected const EXPECTED_WSDL_SOURCE = 'https://ec.europa.eu/taxation_customs/vies/checkVatTestService.wsdl';

    protected function getVatNumberValidationSoapService(): VatNumberValidationServiceInterface
    {
        return new VatNumberValidationSoapTestService($this->soapClientFactory);
    }
}
