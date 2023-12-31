<?php

namespace rocketfellows\ViesVatValidationSoap\tests\unit\services;

use rocketfellows\ViesVatValidationInterface\VatNumberValidationServiceInterface;
use rocketfellows\ViesVatValidationSoap\services\VatNumberValidationSoapExpansibleService;

/**
 * @group vies-vat-validation-soap
 */
class VatNumberValidationSoapExpansibleServiceTest extends VatNumberValidationServiceTest
{
    protected const EXPECTED_WSDL_SOURCE = 'foo';

    protected function getVatNumberValidationSoapService(): VatNumberValidationServiceInterface
    {
        return new VatNumberValidationSoapExpansibleService(
            self::EXPECTED_WSDL_SOURCE,
            $this->faultCodeExceptionFactory,
            $this->soapClientFactory,
            $this->vatNumberValidationResultFactory
        );
    }
}
