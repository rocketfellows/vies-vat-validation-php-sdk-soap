<?php

namespace rocketfellows\ViesVatValidationSoap\tests\unit\services;

use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use rocketfellows\SoapClientFactory\SoapClientFactory;
use rocketfellows\ViesVatValidationInterface\exceptions\service\GlobalMaxConcurrentReqServiceException;
use rocketfellows\ViesVatValidationInterface\exceptions\service\GlobalMaxConcurrentReqTimeServiceException;
use rocketfellows\ViesVatValidationInterface\exceptions\service\InvalidInputServiceException;
use rocketfellows\ViesVatValidationInterface\exceptions\service\InvalidRequesterInfoServiceException;
use rocketfellows\ViesVatValidationInterface\exceptions\service\IPBlockedServiceException;
use rocketfellows\ViesVatValidationInterface\exceptions\service\MSMaxConcurrentReqServiceException;
use rocketfellows\ViesVatValidationInterface\exceptions\service\MSMaxConcurrentReqTimeServiceException;
use rocketfellows\ViesVatValidationInterface\exceptions\service\MSUnavailableServiceException;
use rocketfellows\ViesVatValidationInterface\exceptions\service\ServiceUnavailableException;
use rocketfellows\ViesVatValidationInterface\exceptions\service\TimeoutServiceException;
use rocketfellows\ViesVatValidationInterface\exceptions\service\UnknownServiceErrorException;
use rocketfellows\ViesVatValidationInterface\exceptions\service\VatBlockedServiceException;
use rocketfellows\ViesVatValidationInterface\exceptions\ServiceRequestException;
use rocketfellows\ViesVatValidationInterface\FaultCodeExceptionFactory;
use rocketfellows\ViesVatValidationInterface\VatNumber;
use rocketfellows\ViesVatValidationInterface\VatNumberValidationResult;
use rocketfellows\ViesVatValidationInterface\VatNumberValidationServiceInterface;
use SoapFault;
use stdClass;

/**
 * @group vies-vat-validation-soap
 */
abstract class VatNumberValidationServiceTest extends TestCase
{
    private const EXPECTED_INTERFACE_IMPLEMENTATIONS = [
        VatNumberValidationServiceInterface::class,
    ];

    protected const EXPECTED_WSDL_SOURCE = '';

    private const COUNTRY_CODE_TEST_VALUE = 'DE';
    private const VAT_NUMBER_TEST_VALUE = '123123';

    protected $vatNumberValidationSoapService;
    protected $faultCodeExceptionFactory;
    protected $soapClientFactory;

    abstract protected function getVatNumberValidationSoapService(): VatNumberValidationServiceInterface;

    protected function setUp(): void
    {
        parent::setUp();

        $this->faultCodeExceptionFactory = new FaultCodeExceptionFactory();
        $this->soapClientFactory = $this->createMock(SoapClientFactory::class);

        $this->vatNumberValidationSoapService = $this->getVatNumberValidationSoapService();
    }

    public function testVatNumberValidationSoapServiceImplementedInterfaces(): void
    {
        foreach (self::EXPECTED_INTERFACE_IMPLEMENTATIONS as $expectedInterfaceImplementation) {
            $this->assertInstanceOf($expectedInterfaceImplementation, $this->vatNumberValidationSoapService);
        }
    }

    public function testHandleCheckVatException(): void
    {
        $client = $this->getSoapClientMock('checkVat');
        $client
            ->method('checkVat')
            ->with(['countryCode' => self::COUNTRY_CODE_TEST_VALUE, 'vatNumber' => self::VAT_NUMBER_TEST_VALUE])
            ->willThrowException(new Exception());

        $this->soapClientFactory
            ->method('create')
            ->with($this::EXPECTED_WSDL_SOURCE)
            ->willReturn($client);

        $this->expectException(ServiceRequestException::class);

        $this->vatNumberValidationSoapService->validateVat(
            $this->getValidatingVatNumberTestValue()
        );
    }

    public function testHandleCreateClientException(): void
    {
        $this->soapClientFactory
            ->method('create')
            ->with($this::EXPECTED_WSDL_SOURCE)
            ->willThrowException(new Exception());

        $this->expectException(ServiceRequestException::class);

        $this->vatNumberValidationSoapService->validateVat(
            $this->getValidatingVatNumberTestValue()
        );
    }

    /**
     * @dataProvider getCheckVatProvidedData
     */
    public function testSuccessCheckVat(
        VatNumber $vatNumber,
        array $checkVatCallArgs,
        stdClass $checkVatResponse,
        VatNumberValidationResult $expectedVatNumberValidationResult
    ): void {
        $client = $this->getSoapClientMock('checkVat');
        $client->method('checkVat')->with($checkVatCallArgs)->willReturn($checkVatResponse);

        $this->soapClientFactory
            ->method('create')
            ->with($this::EXPECTED_WSDL_SOURCE)
            ->willReturn($client);

        $this->assertEquals(
            $expectedVatNumberValidationResult,
            $this->vatNumberValidationSoapService->validateVat($vatNumber)
        );
    }

    public function getCheckVatProvidedData(): array
    {
        return [
            'response country code set, vat number set, request date set, is valid, name set, address set' => [
                'vatNumber' => new VatNumber(
                    'DE',
                    '12312312'
                ),
                'checkVatCallArgs' => [
                    'countryCode' => 'DE',
                    'vatNumber' => '12312312',
                ],
                'checkVatResponse' => (object) [
                    'countryCode' => 'DE',
                    'vatNumber' => '12312312',
                    'requestDate' => '2023-11-11 23:23:23',
                    'valid' => true,
                    'name' => 'foo',
                    'address' => 'bar',
                ],
                'expectedVatNumberValidationResult' => new VatNumberValidationResult(
                    new VatNumber('DE', '12312312'),
                    '2023-11-11 23:23:23',
                    true,
                    'foo',
                    'bar'
                ),
            ],
            'response country code set, vat number set, request date set, not valid, name set, address set' => [
                'vatNumber' => new VatNumber(
                    'DE',
                    '12312312'
                ),
                'checkVatCallArgs' => [
                    'countryCode' => 'DE',
                    'vatNumber' => '12312312',
                ],
                'checkVatResponse' => (object) [
                    'countryCode' => 'DE',
                    'vatNumber' => '12312312',
                    'requestDate' => '2023-11-11 23:23:23',
                    'valid' => false,
                    'name' => 'foo',
                    'address' => 'bar',
                ],
                'expectedVatNumberValidationResult' => new VatNumberValidationResult(
                    new VatNumber('DE', '12312312'),
                    '2023-11-11 23:23:23',
                    false,
                    'foo',
                    'bar'
                ),
            ],
            'response country code not set, vat number not set, request date not set, validation not set, name not set, address not set' => [
                'vatNumber' => new VatNumber(
                    'DE',
                    '12312312'
                ),
                'checkVatCallArgs' => [
                    'countryCode' => 'DE',
                    'vatNumber' => '12312312',
                ],
                'checkVatResponse' => (object) [],
                'expectedVatNumberValidationResult' => new VatNumberValidationResult(
                    new VatNumber('', ''),
                    '',
                    false,
                    null,
                    null
                ),
            ],
            'response country code empty, vat number empty, request date empty, not valid, name empty, address empty' => [
                'vatNumber' => new VatNumber(
                    'DE',
                    '12312312'
                ),
                'checkVatCallArgs' => [
                    'countryCode' => 'DE',
                    'vatNumber' => '12312312',
                ],
                'checkVatResponse' => (object) [
                    'countryCode' => '',
                    'vatNumber' => '',
                    'requestDate' => '',
                    'valid' => false,
                    'name' => '',
                    'address' => '',
                ],
                'expectedVatNumberValidationResult' => new VatNumberValidationResult(
                    new VatNumber('', ''),
                    '',
                    false,
                    '',
                    ''
                ),
            ],
        ];
    }

    /**
     * @dataProvider getHandlingCheckVatFaultProvidedData
     */
    public function testHandleCheckVatFault(
        VatNumber $vatNumber,
        array $checkVatCallArgs,
        SoapFault $thrownCheckVatFault,
        string $expectedExceptionClass
    ): void {
        $client = $this->getSoapClientMock('checkVat');
        $client->method('checkVat')->with($checkVatCallArgs)->willThrowException($thrownCheckVatFault);

        $this->soapClientFactory
            ->method('create')
            ->with($this::EXPECTED_WSDL_SOURCE)
            ->willReturn($client);

        $this->expectException($expectedExceptionClass);

        $this->vatNumberValidationSoapService->validateVat($vatNumber);
    }

    public function getHandlingCheckVatFaultProvidedData(): array
    {
        return [
            'INVALID_INPUT fault' => [
                'vatNumber' => new VatNumber(
                    'DE',
                    '12312312'
                ),
                'checkVatCallArgs' => [
                    'countryCode' => 'DE',
                    'vatNumber' => '12312312',
                ],
                'thrownCheckVatFault' => new SoapFault(
                    'INVALID_INPUT',
                    'INVALID_INPUT'
                ),
                'expectedExceptionClass' => InvalidInputServiceException::class,
            ],
            'invalid_input fault' => [
                'vatNumber' => new VatNumber(
                    'DE',
                    '12312312'
                ),
                'checkVatCallArgs' => [
                    'countryCode' => 'DE',
                    'vatNumber' => '12312312',
                ],
                'thrownCheckVatFault' => new SoapFault(
                    'invalid_input',
                    'invalid_input'
                ),
                'expectedExceptionClass' => InvalidInputServiceException::class,
            ],
            'SERVICE_UNAVAILABLE fault' => [
                'vatNumber' => new VatNumber(
                    'DE',
                    '12312312'
                ),
                'checkVatCallArgs' => [
                    'countryCode' => 'DE',
                    'vatNumber' => '12312312',
                ],
                'thrownCheckVatFault' => new SoapFault(
                    'SERVICE_UNAVAILABLE',
                    'SERVICE_UNAVAILABLE'
                ),
                'expectedExceptionClass' => ServiceUnavailableException::class,
            ],
            'service_unavailable fault' => [
                'vatNumber' => new VatNumber(
                    'DE',
                    '12312312'
                ),
                'checkVatCallArgs' => [
                    'countryCode' => 'DE',
                    'vatNumber' => '12312312',
                ],
                'thrownCheckVatFault' => new SoapFault(
                    'service_unavailable',
                    'service_unavailable'
                ),
                'expectedExceptionClass' => ServiceUnavailableException::class,
            ],
            'MS_UNAVAILABLE fault' => [
                'vatNumber' => new VatNumber(
                    'DE',
                    '12312312'
                ),
                'checkVatCallArgs' => [
                    'countryCode' => 'DE',
                    'vatNumber' => '12312312',
                ],
                'thrownCheckVatFault' => new SoapFault(
                    'MS_UNAVAILABLE',
                    'MS_UNAVAILABLE'
                ),
                'expectedExceptionClass' => MSUnavailableServiceException::class,
            ],
            'ms_unavailable fault' => [
                'vatNumber' => new VatNumber(
                    'DE',
                    '12312312'
                ),
                'checkVatCallArgs' => [
                    'countryCode' => 'DE',
                    'vatNumber' => '12312312',
                ],
                'thrownCheckVatFault' => new SoapFault(
                    'ms_unavailable',
                    'ms_unavailable'
                ),
                'expectedExceptionClass' => MSUnavailableServiceException::class,
            ],
            'TIMEOUT fault' => [
                'vatNumber' => new VatNumber(
                    'DE',
                    '12312312'
                ),
                'checkVatCallArgs' => [
                    'countryCode' => 'DE',
                    'vatNumber' => '12312312',
                ],
                'thrownCheckVatFault' => new SoapFault(
                    'TIMEOUT',
                    'TIMEOUT'
                ),
                'expectedExceptionClass' => TimeoutServiceException::class,
            ],
            'INVALID_REQUESTER_INFO fault' => [
                'vatNumber' => new VatNumber(
                    'DE',
                    '12312312'
                ),
                'checkVatCallArgs' => [
                    'countryCode' => 'DE',
                    'vatNumber' => '12312312',
                ],
                'thrownCheckVatFault' => new SoapFault(
                    'INVALID_REQUESTER_INFO',
                    'INVALID_REQUESTER_INFO'
                ),
                'expectedExceptionClass' => InvalidRequesterInfoServiceException::class,
            ],
            'VAT_BLOCKED fault' => [
                'vatNumber' => new VatNumber(
                    'DE',
                    '12312312'
                ),
                'checkVatCallArgs' => [
                    'countryCode' => 'DE',
                    'vatNumber' => '12312312',
                ],
                'thrownCheckVatFault' => new SoapFault(
                    'VAT_BLOCKED',
                    'VAT_BLOCKED'
                ),
                'expectedExceptionClass' => VatBlockedServiceException::class,
            ],
            'IP_BLOCKED fault' => [
                'vatNumber' => new VatNumber(
                    'DE',
                    '12312312'
                ),
                'checkVatCallArgs' => [
                    'countryCode' => 'DE',
                    'vatNumber' => '12312312',
                ],
                'thrownCheckVatFault' => new SoapFault(
                    'IP_BLOCKED',
                    'IP_BLOCKED'
                ),
                'expectedExceptionClass' => IPBlockedServiceException::class,
            ],
            'GLOBAL_MAX_CONCURRENT_REQ fault' => [
                'vatNumber' => new VatNumber(
                    'DE',
                    '12312312'
                ),
                'checkVatCallArgs' => [
                    'countryCode' => 'DE',
                    'vatNumber' => '12312312',
                ],
                'thrownCheckVatFault' => new SoapFault(
                    'GLOBAL_MAX_CONCURRENT_REQ',
                    'GLOBAL_MAX_CONCURRENT_REQ'
                ),
                'expectedExceptionClass' => GlobalMaxConcurrentReqServiceException::class,
            ],
            'GLOBAL_MAX_CONCURRENT_REQ_TIME fault' => [
                'vatNumber' => new VatNumber(
                    'DE',
                    '12312312'
                ),
                'checkVatCallArgs' => [
                    'countryCode' => 'DE',
                    'vatNumber' => '12312312',
                ],
                'thrownCheckVatFault' => new SoapFault(
                    'GLOBAL_MAX_CONCURRENT_REQ_TIME',
                    'GLOBAL_MAX_CONCURRENT_REQ_TIME'
                ),
                'expectedExceptionClass' => GlobalMaxConcurrentReqTimeServiceException::class,
            ],
            'MS_MAX_CONCURRENT_REQ fault' => [
                'vatNumber' => new VatNumber(
                    'DE',
                    '12312312'
                ),
                'checkVatCallArgs' => [
                    'countryCode' => 'DE',
                    'vatNumber' => '12312312',
                ],
                'thrownCheckVatFault' => new SoapFault(
                    'MS_MAX_CONCURRENT_REQ',
                    'MS_MAX_CONCURRENT_REQ'
                ),
                'expectedExceptionClass' => MSMaxConcurrentReqServiceException::class,
            ],
            'MS_MAX_CONCURRENT_REQ_TIME fault' => [
                'vatNumber' => new VatNumber(
                    'DE',
                    '12312312'
                ),
                'checkVatCallArgs' => [
                    'countryCode' => 'DE',
                    'vatNumber' => '12312312',
                ],
                'thrownCheckVatFault' => new SoapFault(
                    'MS_MAX_CONCURRENT_REQ_TIME',
                    'MS_MAX_CONCURRENT_REQ_TIME'
                ),
                'expectedExceptionClass' => MSMaxConcurrentReqTimeServiceException::class,
            ],
            'unknown fault' => [
                'vatNumber' => new VatNumber(
                    'DE',
                    '12312312'
                ),
                'checkVatCallArgs' => [
                    'countryCode' => 'DE',
                    'vatNumber' => '12312312',
                ],
                'thrownCheckVatFault' => new SoapFault(
                    'foo',
                    'foo'
                ),
                'expectedExceptionClass' => UnknownServiceErrorException::class,
            ],
        ];
    }

    /**
     * @param string ...$methodsNames
     * @return MockObject
     */
    private function getSoapClientMock(string ...$methodsNames): MockObject
    {
        return $this
            ->getMockBuilder('SoapClient')
            ->disableOriginalConstructor()
            ->addMethods($methodsNames)
            ->getMock();
    }

    private function getValidatingVatNumberTestValue(): VatNumber
    {
        return new VatNumber(self::COUNTRY_CODE_TEST_VALUE, self::VAT_NUMBER_TEST_VALUE);
    }
}
