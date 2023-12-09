<?php

namespace rocketfellows\ViesVatValidationSoap\tests\unit\services;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use rocketfellows\SoapClientFactory\SoapClientFactory;
use rocketfellows\ViesVatValidationInterface\exceptions\service\InvalidInputServiceException;
use rocketfellows\ViesVatValidationInterface\VatNumber;
use rocketfellows\ViesVatValidationSoap\services\VatNumberValidationSoapService;
use SoapClient;
use SoapFault;

/**
 * @group vies-vat-validation-soap
 */
class VatNumberValidationSoapServiceTest extends TestCase
{
    private const EXPECTED_WSDL_SOURCE = 'https://ec.europa.eu/taxation_customs/vies/services/checkVatService.wsdl';

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

    /**
     * @dataProvider getHandlingCheckVatFaultProvidedData
     */
    public function testHandleCheckVatFault(
        VatNumber $vatNumber,
        SoapFault $thrownCheckVatFault,
        string $expectedExceptionClass
    ): void {
        $client = $this->getSoapClientMock('checkVat');
        $client->method('checkVat')->willThrowException($thrownCheckVatFault);

        $this->soapClientFactory
            ->method('create')
            ->with(self::EXPECTED_WSDL_SOURCE)
            ->willReturn($client);

        $this->expectException($expectedExceptionClass);

        $this->vatNumberValidationSoapService->validateVat($vatNumber);
    }

    public function getHandlingCheckVatFaultProvidedData(): array
    {
        return [
            'INVALID_INPUT fault' => [
                'vatNumber' => new VatNumber('DE', '12312312'),
                'thrownCheckVatFault' => new SoapFault('INVALID_INPUT', 'INVALID_INPUT'),
                'expectedExceptionClass' => InvalidInputServiceException::class,
            ],
        ];
    }

    /**
     * @param string[] $methodsNames
     * @return SoapClient|MockObject
     */
    private function getSoapClientMock(string ...$methodsNames): SoapClient
    {
        return $this
            ->getMockBuilder('SoapClient')
            ->disableOriginalConstructor()
            ->addMethods($methodsNames)
            ->getMock();
    }
}
