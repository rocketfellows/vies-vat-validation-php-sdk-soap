<?php

namespace rocketfellows\ViesVatValidationSoap;

use rocketfellows\SoapClientFactory\SoapClientFactory;
use rocketfellows\ViesVatValidationInterface\exceptions\service\GlobalMaxConcurrentReqServiceException;
use rocketfellows\ViesVatValidationInterface\exceptions\service\GlobalMaxConcurrentReqTimeServiceException;
use rocketfellows\ViesVatValidationInterface\exceptions\service\InvalidInputServiceException;
use rocketfellows\ViesVatValidationInterface\exceptions\service\InvalidRequesterInfoServiceException;
use rocketfellows\ViesVatValidationInterface\exceptions\service\IPBlockedServiceException;
use rocketfellows\ViesVatValidationInterface\exceptions\service\MSMaxConcurrentReqServiceException;
use rocketfellows\ViesVatValidationInterface\exceptions\service\MSMaxConcurrentReqTimeServiceException;
use rocketfellows\ViesVatValidationInterface\exceptions\service\MSUnavailableServiceException;
use rocketfellows\ViesVatValidationInterface\exceptions\service\ServiceRequestException;
use rocketfellows\ViesVatValidationInterface\exceptions\service\ServiceUnavailableException;
use rocketfellows\ViesVatValidationInterface\exceptions\service\TimeoutServiceException;
use rocketfellows\ViesVatValidationInterface\exceptions\service\UnknownServiceErrorException;
use rocketfellows\ViesVatValidationInterface\exceptions\service\VatBlockedServiceException;
use rocketfellows\ViesVatValidationInterface\VatNumber;
use rocketfellows\ViesVatValidationInterface\VatNumberValidationResult;
use rocketfellows\ViesVatValidationInterface\VatNumberValidationServiceInterface;

class AbstractVatNumberValidationSoapService implements VatNumberValidationServiceInterface
{
    private const SOAP_FAULT_CODE_INVALID_INPUT = 'INVALID_INPUT';
    private const SOAP_FAULT_CODE_SERVICE_UNAVAILABLE = 'SERVICE_UNAVAILABLE';
    private const SOAP_FAULT_CODE_MS_UNAVAILABLE = 'MS_UNAVAILABLE';
    private const SOAP_FAULT_CODE_TIMEOUT = 'TIMEOUT';
    private const SOAP_FAULT_CODE_INVALID_REQUESTER_INFO = 'INVALID_REQUESTER_INFO';
    private const SOAP_FAULT_CODE_VAT_BLOCKED = 'VAT_BLOCKED';
    private const SOAP_FAULT_CODE_IP_BLOCKED = 'IP_BLOCKED';

    private $soapClientFactory;

    public function __construct(
        SoapClientFactory $soapClientFactory
    ) {
        $this->soapClientFactory = $soapClientFactory;
    }

    public function validateVat(VatNumber $vatNumber): VatNumberValidationResult
    {
        // TODO: Implement validateVat() method.
    }
}
