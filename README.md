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

TODO: add description.

## Contributing.

Welcome to pull requests. If there is a major changes, first please open an issue for discussion.

Please make sure to update tests as appropriate.
