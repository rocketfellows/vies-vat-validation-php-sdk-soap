<?php

namespace rocketfellows\ViesVatValidationSoap\tests\unit\services;

use rocketfellows\ViesVatValidationInterface\VatNumberValidationServiceInterface;
use rocketfellows\ViesVatValidationSoap\services\VatNumberValidationSoapService;

/**
 * @group vies-vat-validation-soap
 */
class VatNumberValidationSoapServiceTest extends VatNumberValidationServiceTest
{
    protected const EXPECTED_WSDL_SOURCE = 'https://ec.europa.eu/taxation_customs/vies/services/checkVatService.wsdl';

    protected function getVatNumberValidationSoapService(): VatNumberValidationServiceInterface
    {
        return new VatNumberValidationSoapService($this->soapClientFactory);
    }
}
