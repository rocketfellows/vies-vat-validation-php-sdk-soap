# VIES VAT number validation PHP sdk SOAP.

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
![PHPStan Badge](https://img.shields.io/badge/PHPStan-level%205-brightgreen.svg?style=flat)
![Code Coverage Badge](./badge.svg)

An implementation of interface https://github.com/rocketfellows/vies-vat-validation-php-sdk-interface for the VIES service for checking the validity of the VAT number via the SOAP protocol.
The implementation is designed to send a request and process a response from the VAT validation service via the SOAP protocol.

For more information about VIES VAT number validation services via the SOAP protocol see https://ec.europa.eu/taxation_customs/vies/#/technical-information.

## Installation.

```shell
composer require rocketfellows/vies-vat-validation-php-sdk-soap
```

## Dependencies.

Current implementation dependencies:
- https://github.com/rocketfellows/soap-client-factory v1.0.0;
- https://github.com/rocketfellows/vies-vat-validation-php-sdk-interface v1.0.0.

## VIES VAT number validation SOAP service description.

For more information about VIES VAT number validation SOAP service see: https://ec.europa.eu/taxation_customs/vies/#/technical-information.

For the SOAP service, three WSDLs are available:
- https://ec.europa.eu/taxation_customs/vies/services/checkVatService.wsdl to verify the validity of a VAT number;
- https://ec.europa.eu/taxation_customs/vies/checkVatTestService.wsdl to verify the integration with the original service by using the below values (common for all Member States / CountryCodes) for each required result: `vatNumber` value=100, response= VALID;
  `vatNumber` value=200, response = INVALID.

## VIES VAT number validation PHP sdk SOAP component description.

`AbstractVatNumberValidationSoapService` - is an abstract class that implements the interface https://github.com/rocketfellows/vies-vat-validation-php-sdk-interface and is intended for sending a request for VAT validation using the SOAP protocol, processing response/faults and returning an object of type validation result.

`VatNumberValidationSoapService` - is an inheritor of the `AbstractVatNumberValidationSoapService` class, configured to send a request to the sales service according to wsdl https://ec.europa.eu/taxation_customs/vies/services/checkVatService.wsdl.

`VatNumberValidationSoapTestService` - is an inheritor of the `AbstractVatNumberValidationSoapService` class, configured to send a request to the test service according to wsdl https://ec.europa.eu/taxation_customs/vies/checkVatTestService.wsdl.

`VatNumberValidationSoapExpansibleService` - is an inheritor of the `AbstractVatNumberValidationSoapService` class, configured to send a request to the service according to wsdl, passed through the class constructor (customizable service).

## Usage examples.

### VatNumberValidationSoapService usage.

<hr>

VAT number validation result (VAT is valid):

```php
use rocketfellows\SoapClientFactory\SoapClientFactory;
use rocketfellows\ViesVatValidationInterface\VatNumber;
use rocketfellows\ViesVatValidationSoap\services\VatNumberValidationSoapService;

require_once __DIR__ . '/vendor/autoload.php';

// Service initialization
$service = new VatNumberValidationSoapService((new SoapClientFactory()));

$validationResult = $service->validateVat(VatNumber::create('DE', '206223519'));

var_dump(sprintf('VAT country code: %s', $validationResult->getCountryCode()));
var_dump(sprintf('VAT number: %s', $validationResult->getVatNumber()));
var_dump(sprintf('Request date: %s', $validationResult->getRequestDateString()));
var_dump(sprintf('Is VAT valid: %s', $validationResult->isValid() ? 'true' : 'false'));
var_dump(sprintf('VAT holder name: %s', $validationResult->getName()));
var_dump(sprintf('VAT holder address: %s', $validationResult->getAddress()));
```
```shell
VAT country code: DE
VAT number: 206223519
Request date: 2023-12-11+01:00
Is VAT valid: true
VAT holder name: ---
VAT holder address: ---
```

VAT number validation result (VAT is not valid):

```php
use rocketfellows\SoapClientFactory\SoapClientFactory;
use rocketfellows\ViesVatValidationInterface\VatNumber;
use rocketfellows\ViesVatValidationSoap\services\VatNumberValidationSoapService;

require_once __DIR__ . '/vendor/autoload.php';

// Service initialization
$service = new VatNumberValidationSoapService((new SoapClientFactory()));

$validationResult = $service->validateVat(VatNumber::create('DE', '206223511'));

var_dump(sprintf('VAT country code: %s', $validationResult->getCountryCode()));
var_dump(sprintf('VAT number: %s', $validationResult->getVatNumber()));
var_dump(sprintf('Request date: %s', $validationResult->getRequestDateString()));
var_dump(sprintf('Is VAT valid: %s', $validationResult->isValid() ? 'true' : 'false'));
var_dump(sprintf('VAT holder name: %s', $validationResult->getName()));
var_dump(sprintf('VAT holder address: %s', $validationResult->getAddress()));
```
```shell
VAT country code: DE
VAT number: 206223511
Request date: 2023-12-11+01:00
Is VAT valid: false
VAT holder name: ---
VAT holder address: ---
```

### VatNumberValidationSoapTestService usage.

<hr>

According to https://ec.europa.eu/taxation_customs/vies/checkVatTestService.wsdl here is the list of VAT Number to use to receive each kind of answer:
- 100 = Valid request with Valid VAT Number
- 200 = Valid request with an Invalid VAT Number
- 201 = Error : INVALID_INPUT
- 202 = Error : INVALID_REQUESTER_INFO
- 300 = Error : SERVICE_UNAVAILABLE
- 301 = Error : MS_UNAVAILABLE
- 302 = Error : TIMEOUT
- 400 = Error : VAT_BLOCKED
- 401 = Error : IP_BLOCKED
- 500 = Error : GLOBAL_MAX_CONCURRENT_REQ
- 501 = Error : GLOBAL_MAX_CONCURRENT_REQ_TIME
- 600 = Error : MS_MAX_CONCURRENT_REQ
- 601 = Error : MS_MAX_CONCURRENT_REQ_TIME

For all the other cases, The web service will responds with a "SERVICE_UNAVAILABLE" error.

Some usage examples below.

VAT number validation result (VAT is valid):

```php
use rocketfellows\SoapClientFactory\SoapClientFactory;
use rocketfellows\ViesVatValidationInterface\VatNumber;
use rocketfellows\ViesVatValidationSoap\services\VatNumberValidationSoapTestService;

require_once __DIR__ . '/vendor/autoload.php';

// Service initialization
$service = new VatNumberValidationSoapTestService((new SoapClientFactory()));

$validationResult = $service->validateVat(VatNumber::create('DE', '100'));

var_dump(sprintf('VAT country code: %s', $validationResult->getCountryCode()));
var_dump(sprintf('VAT number: %s', $validationResult->getVatNumber()));
var_dump(sprintf('Request date: %s', $validationResult->getRequestDateString()));
var_dump(sprintf('Is VAT valid: %s', $validationResult->isValid() ? 'true' : 'false'));
var_dump(sprintf('VAT holder name: %s', $validationResult->getName()));
var_dump(sprintf('VAT holder address: %s', $validationResult->getAddress()));
```
```php
VAT country code: DE
VAT number: 100
Request date: 2023-12-13+01:00
Is VAT valid: true
VAT holder name: John Doe
VAT holder address: 123 Main St, Anytown, UK
```

VAT number validation result (VAT is not valid):

```php
use rocketfellows\SoapClientFactory\SoapClientFactory;
use rocketfellows\ViesVatValidationInterface\VatNumber;
use rocketfellows\ViesVatValidationSoap\services\VatNumberValidationSoapTestService;

require_once __DIR__ . '/vendor/autoload.php';

// Service initialization
$service = new VatNumberValidationSoapTestService((new SoapClientFactory()));

$validationResult = $service->validateVat(VatNumber::create('DE', '200'));

var_dump(sprintf('VAT country code: %s', $validationResult->getCountryCode()));
var_dump(sprintf('VAT number: %s', $validationResult->getVatNumber()));
var_dump(sprintf('Request date: %s', $validationResult->getRequestDateString()));
var_dump(sprintf('Is VAT valid: %s', $validationResult->isValid() ? 'true' : 'false'));
var_dump(sprintf('VAT holder name: %s', $validationResult->getName()));
var_dump(sprintf('VAT holder address: %s', $validationResult->getAddress()));
```
```php
VAT country code: DE
VAT number: 200
Request date: 2023-12-13+01:00
Is VAT valid: false
VAT holder name: ---
VAT holder address: ---
```

VAT number validation resulted with INVALID_INPUT fault:

```php
use rocketfellows\SoapClientFactory\SoapClientFactory;
use rocketfellows\ViesVatValidationInterface\VatNumber;
use rocketfellows\ViesVatValidationSoap\services\VatNumberValidationSoapTestService;

require_once __DIR__ . '/vendor/autoload.php';

// Service initialization
$service = new VatNumberValidationSoapTestService((new SoapClientFactory()));

try {
    $validationResult = $service->validateVat(VatNumber::create('DE', '201'));
} catch (Exception $exception) {
    var_dump(get_class($exception));
    var_dump($exception->getMessage());
}
```
```php
rocketfellows\ViesVatValidationInterface\exceptions\service\InvalidInputServiceException
INVALID_INPUT
```

VAT number validation resulted with IP_BLOCKED fault:

```php
use rocketfellows\SoapClientFactory\SoapClientFactory;
use rocketfellows\ViesVatValidationInterface\VatNumber;
use rocketfellows\ViesVatValidationSoap\services\VatNumberValidationSoapTestService;

require_once __DIR__ . '/vendor/autoload.php';

// Service initialization
$service = new VatNumberValidationSoapTestService((new SoapClientFactory()));

try {
    $validationResult = $service->validateVat(VatNumber::create('DE', '401'));
} catch (Exception $exception) {
    var_dump(get_class($exception));
    var_dump($exception->getMessage());
}
```
```php
rocketfellows\ViesVatValidationInterface\exceptions\service\IPBlockedServiceException
IP_BLOCKED
```

### VatNumberValidationSoapExpansibleService usage.

<hr>

`VatNumberValidationSoapExpansibleService` - is an inheritor of the `AbstractVatNumberValidationSoapService` class, configured to send a request to the service according to wsdl, passed through the class constructor (customizable service).

For example init service with wsdl - https://ec.europa.eu/taxation_customs/vies/services/checkVatService.wsdl.

VAT number validation result (VAT is valid):

```php
use rocketfellows\SoapClientFactory\SoapClientFactory;
use rocketfellows\ViesVatValidationInterface\VatNumber;
use rocketfellows\ViesVatValidationSoap\services\VatNumberValidationSoapExpansibleService;

require_once __DIR__ . '/vendor/autoload.php';

// Service initialization
$service = new VatNumberValidationSoapExpansibleService(
    'https://ec.europa.eu/taxation_customs/vies/services/checkVatService.wsdl',
    (new SoapClientFactory())
);

$validationResult = $service->validateVat(VatNumber::create('DE', '206223519'));

var_dump(sprintf('VAT country code: %s', $validationResult->getCountryCode()));
var_dump(sprintf('VAT number: %s', $validationResult->getVatNumber()));
var_dump(sprintf('Request date: %s', $validationResult->getRequestDateString()));
var_dump(sprintf('Is VAT valid: %s', $validationResult->isValid() ? 'true' : 'false'));
var_dump(sprintf('VAT holder name: %s', $validationResult->getName()));
var_dump(sprintf('VAT holder address: %s', $validationResult->getAddress()));
```
```php
VAT country code: DE
VAT number: 206223519
Request date: 2023-12-11+01:00
Is VAT valid: true
VAT holder name: ---
VAT holder address: ---
```

VAT number validation result (VAT is not valid):

```php
use rocketfellows\SoapClientFactory\SoapClientFactory;
use rocketfellows\ViesVatValidationInterface\VatNumber;
use rocketfellows\ViesVatValidationSoap\services\VatNumberValidationSoapExpansibleService;

require_once __DIR__ . '/vendor/autoload.php';

// Service initialization
$service = new VatNumberValidationSoapExpansibleService(
    'https://ec.europa.eu/taxation_customs/vies/services/checkVatService.wsdl',
    (new SoapClientFactory())
);

$validationResult = $service->validateVat(VatNumber::create('DE', '206223511'));

var_dump(sprintf('VAT country code: %s', $validationResult->getCountryCode()));
var_dump(sprintf('VAT number: %s', $validationResult->getVatNumber()));
var_dump(sprintf('Request date: %s', $validationResult->getRequestDateString()));
var_dump(sprintf('Is VAT valid: %s', $validationResult->isValid() ? 'true' : 'false'));
var_dump(sprintf('VAT holder name: %s', $validationResult->getName()));
var_dump(sprintf('VAT holder address: %s', $validationResult->getAddress()));
```
```php
VAT country code: DE
VAT number: 206223511
Request date: 2023-12-11+01:00
Is VAT valid: false
VAT holder name: ---
VAT holder address: ---
```

## Contributing.

Welcome to pull requests. If there is a major changes, first please open an issue for discussion.

Please make sure to update tests as appropriate.
