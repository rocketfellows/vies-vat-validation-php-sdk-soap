<?php

namespace rocketfellows\ViesVatValidationSoap\tests\integration;

use PHPUnit\Framework\TestCase;
use rocketfellows\SoapClientFactory\SoapClientFactory;
use rocketfellows\ViesVatValidationInterface\FaultCodeExceptionFactory;

/**
 * @group vies-vat-validation-soap
 */
class VatNumberValidationSoapServiceTest extends TestCase
{
    private $testVatNumberValidationSoapService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->testVatNumberValidationSoapService = new TestVatNumberValidationSoapService(
            (new FaultCodeExceptionFactory()),
            (new SoapClientFactory())
        );
    }
}
