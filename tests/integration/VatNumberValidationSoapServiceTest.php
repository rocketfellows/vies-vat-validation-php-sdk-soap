<?php

namespace rocketfellows\ViesVatValidationSoap\tests\integration;

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
use rocketfellows\ViesVatValidationInterface\exceptions\service\VatBlockedServiceException;
use rocketfellows\ViesVatValidationInterface\FaultCodeExceptionFactory;
use rocketfellows\ViesVatValidationInterface\VatNumber;

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

    /**
     * @dataProvider getValidateVatProvidedData
     */
    public function testValidateVat(VatNumber $vatNumber, array $expectedValidationResultData): void
    {
        $actualValidationResult = $this->testVatNumberValidationSoapService->validateVat($vatNumber);

        $this->assertEquals($expectedValidationResultData['countryCode'], $actualValidationResult->getCountryCode());
        $this->assertEquals($expectedValidationResultData['vatNumber'], $actualValidationResult->getVatNumber());
        $this->assertEquals($expectedValidationResultData['isValid'], $actualValidationResult->isValid());
        $this->assertEquals($expectedValidationResultData['name'], $actualValidationResult->getName());
        $this->assertEquals($expectedValidationResultData['address'], $actualValidationResult->getAddress());
    }

    public function getValidateVatProvidedData(): array
    {
        return [
            'valid vat' => [
                'vatNumber' => new VatNumber('DE', '100'),
                'expectedValidationResultData' => [
                    'countryCode' => 'DE',
                    'vatNumber' => '100',
                    'isValid' => true,
                    'name' => 'John Doe',
                    'address' => '123 Main St, Anytown, UK',
                ],
            ],
            'invalid vat' => [
                'vatNumber' => new VatNumber('DE', '200'),
                'expectedValidationResultData' => [
                    'countryCode' => 'DE',
                    'vatNumber' => '200',
                    'isValid' => false,
                    'name' => '---',
                    'address' => '---',
                ],
            ],
        ];
    }

    /**
     * @dataProvider getValidateVatHandlingExceptionsProvidedData
     */
    public function testValidateVatHandlingExceptions(VatNumber $vatNumber, array $expectedExceptionData): void
    {
        $this->expectException($expectedExceptionData['exceptionClass']);
        $this->expectExceptionMessage($expectedExceptionData['exceptionMessage']);
        $this->expectExceptionCode($expectedExceptionData['exceptionCode']);

        $this->testVatNumberValidationSoapService->validateVat($vatNumber);
    }

    public function getValidateVatHandlingExceptionsProvidedData(): array
    {
        return [
            'INVALID_INPUT error' => [
                'vatNumber' => new VatNumber('DE', '201'),
                'expectedExceptionData' => [
                    'exceptionClass' => InvalidInputServiceException::class,
                    'exceptionMessage' => 'INVALID_INPUT',
                    'exceptionCode' => 0,
                ],
            ],
            'INVALID_REQUESTER_INFO error' => [
                'vatNumber' => new VatNumber('DE', '202'),
                'expectedExceptionData' => [
                    'exceptionClass' => InvalidRequesterInfoServiceException::class,
                    'exceptionMessage' => 'INVALID_REQUESTER_INFO',
                    'exceptionCode' => 0,
                ],
            ],
            'SERVICE_UNAVAILABLE error' => [
                'vatNumber' => new VatNumber('DE', '300'),
                'expectedExceptionData' => [
                    'exceptionClass' => ServiceUnavailableException::class,
                    'exceptionMessage' => 'SERVICE_UNAVAILABLE',
                    'exceptionCode' => 0,
                ],
            ],
            'MS_UNAVAILABLE error' => [
                'vatNumber' => new VatNumber('DE', '301'),
                'expectedExceptionData' => [
                    'exceptionClass' => MSUnavailableServiceException::class,
                    'exceptionMessage' => 'MS_UNAVAILABLE',
                    'exceptionCode' => 0,
                ],
            ],
            'TIMEOUT error' => [
                'vatNumber' => new VatNumber('DE', '302'),
                'expectedExceptionData' => [
                    'exceptionClass' => TimeoutServiceException::class,
                    'exceptionMessage' => 'TIMEOUT',
                    'exceptionCode' => 0,
                ],
            ],
            'VAT_BLOCKED error' => [
                'vatNumber' => new VatNumber('DE', '400'),
                'expectedExceptionData' => [
                    'exceptionClass' => VatBlockedServiceException::class,
                    'exceptionMessage' => 'VAT_BLOCKED',
                    'exceptionCode' => 0,
                ],
            ],
            'IP_BLOCKED error' => [
                'vatNumber' => new VatNumber('DE', '401'),
                'expectedExceptionData' => [
                    'exceptionClass' => IPBlockedServiceException::class,
                    'exceptionMessage' => 'IP_BLOCKED',
                    'exceptionCode' => 0,
                ],
            ],
            'GLOBAL_MAX_CONCURRENT_REQ error' => [
                'vatNumber' => new VatNumber('DE', '500'),
                'expectedExceptionData' => [
                    'exceptionClass' => GlobalMaxConcurrentReqServiceException::class,
                    'exceptionMessage' => 'GLOBAL_MAX_CONCURRENT_REQ',
                    'exceptionCode' => 0,
                ],
            ],
            'GLOBAL_MAX_CONCURRENT_REQ_TIME error' => [
                'vatNumber' => new VatNumber('DE', '501'),
                'expectedExceptionData' => [
                    'exceptionClass' => GlobalMaxConcurrentReqTimeServiceException::class,
                    'exceptionMessage' => 'GLOBAL_MAX_CONCURRENT_REQ_TIME',
                    'exceptionCode' => 0,
                ],
            ],
            'MS_MAX_CONCURRENT_REQ error' => [
                'vatNumber' => new VatNumber('DE', '600'),
                'expectedExceptionData' => [
                    'exceptionClass' => MSMaxConcurrentReqServiceException::class,
                    'exceptionMessage' => 'MS_MAX_CONCURRENT_REQ',
                    'exceptionCode' => 0,
                ],
            ],
            'MS_MAX_CONCURRENT_REQ_TIME error' => [
                'vatNumber' => new VatNumber('DE', '601'),
                'expectedExceptionData' => [
                    'exceptionClass' => MSMaxConcurrentReqTimeServiceException::class,
                    'exceptionMessage' => 'MS_MAX_CONCURRENT_REQ_TIME',
                    'exceptionCode' => 0,
                ],
            ],
            'country code not set' => [
                'vatNumber' => new VatNumber('', '100'),
                'expectedExceptionData' => [
                    'exceptionClass' => InvalidInputServiceException::class,
                    'exceptionMessage' => 'Invalid_input',
                    'exceptionCode' => 0,
                ],
            ],
            'vat not set' => [
                'vatNumber' => new VatNumber('DE', ''),
                'expectedExceptionData' => [
                    'exceptionClass' => InvalidInputServiceException::class,
                    'exceptionMessage' => 'Invalid_input',
                    'exceptionCode' => 0,
                ],
            ],
            'vat not set, vat not set' => [
                'vatNumber' => new VatNumber('', ''),
                'expectedExceptionData' => [
                    'exceptionClass' => InvalidInputServiceException::class,
                    'exceptionMessage' => 'Invalid_input',
                    'exceptionCode' => 0,
                ],
            ],
        ];
    }
}
