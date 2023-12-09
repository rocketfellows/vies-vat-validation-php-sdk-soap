<?php

namespace rocketfellows\ViesVatValidationSoap\tests\unit\services;

use PHPUnit\Framework\TestCase;
use rocketfellows\SoapClientFactory\SoapClientFactory;
use rocketfellows\ViesVatValidationSoap\services\VatNumberValidationSoapService;

/**
 * @group vies-vat-validation-soap
 */
class VatNumberValidationSoapServiceTest extends TestCase
{
    private $vatNumberValidationSoapService;
    private $soapClientFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->soapClientFactory = $this->createMock(SoapClientFactory::class);

        $this->vatNumberValidationSoapService = new VatNumberValidationSoapService(
            $this->soapClientFactory
        );
    }
}
