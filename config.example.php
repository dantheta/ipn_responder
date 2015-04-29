<?php

// Set this to 0 once you go live or don't require logging.
define("DEBUG", 1);

// Set to 0 once you're ready to go live
define("USE_SANDBOX", 1);

define("LOG_FILE", "./ipn.log");

$CONFIG = array(
    'smtp_server' => 'localhost',
    'from_address' => 'mail@example.com',
    'subject' => 'Thank you!',
    'template_html' => 'template.html',
    'template_text' => 'template.txt'
    );
