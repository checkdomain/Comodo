README
======

What is Checkdomain/Comodo
--------------------------
Checkdomain/Comodo is a library to connect with the comodo SSL Api. It yet offers functions to apply, revoke certificates and to resend the DCV mail.

The API also offers a function to enter the DCV authorization code, which has been sent to customer by mail.

Requirements
------------
Checkdomain/comodo requires
php >= 5.3 and 
guzzle >= 3.*,<4

Installation
------------
The easiest way to install this library is through [composer](http://getcomposer.org/). Just add the following lines to your **composer.json** file:

```json
{
   "require": {
        "checkdomain/comodo": "dev-master"
    }
}+
```
Another way would be to download this library and configure the autoloading yourself. This library relies on a [PSR-0](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md) compatible autoloader for automatic class loading.

Usage
-----
To send a request to comodo, just instantiate the Util class and call any of the available functions.

The API always throws and Exception , if a parameter is missing or wrong formatted, or anything else did not work.

If a request has been successfully, you will get the return value true, or in case of using the AutoApply or
GetDCVEmailAddressList function, you receive a result-object with additional information.

Contributing
------------
Checkdomain/comodo is open source. There are a lot of functions left in the API to be integrated:

https://secure.comodo.net/api/pdf/webhostreseller/sslcertificates/

Pull requests will be reviewed and merged fast.