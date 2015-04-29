# paypal_responder
Email responder for Paypal instant payment notification

## Requirements:
* PHP Curl bindings
* PHPMailer

To add phpmailer:

Download composer(https://getcomposer.org/)

```
php -f composer.phar install --no-dev
```

## Configuration

Copy config.example.php to config.php, and edit to suit environment.

Add a template.txt and template.html file (both files are needed for multipart emails).

## Templates

The responder script supports two templates, one plain text and one HTML.  The template engine will replace text variables enclosed in curly braces. The three parameters currently supported are:

* FIRST_NAME
* LAST_NAME
* EMAIL
 
### Example template text:

```
Dear {FIRST_NAME} {LAST_NAME},

Thank you for your donation!

Regards,


This message was sent to {EMAIL}.
```

## Security

The responder script writes messages to a log file, which in the example config is ipn.log in the same directory as the script.  This should be relocated to somewhere outside of the public area of the webserver.

The responder script does not require or use any API credentials, but the logged data could be considered sensitive.

## Using the paypal responder script

Once the responder script has been installed into a public area of a webserver, templates have been created and the config has been updated, and extra parameter will be needed on the PayPal checkout form or button.

The parameter is:

```
notify_url=http://example.com/path/to/paypal_responder/paypal_ipn.php
```

Change the example.com and /path/to to match the webserver's details.

This script has been tested against the paypal sandbox with a non-hosted donate button.
