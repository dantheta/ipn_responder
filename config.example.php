<?php

// Set this to 0 once you go live or don't require logging.
define("DEBUG", 1);

// Set to 0 once you're ready to go live
define("USE_SANDBOX", 1);

// for security, set logfile location outside of document_root
define("LOG_FILE", "./ipn.log");

$CONFIG = array(
    'smtp_server' => 'localhost',
    'from_address' => 'mail@example.com',
    'from_name' => 'Example Notifier',
    'subject' => 'Thank you!',
    'template_html' => 'template.html',
    'template_text' => 'template.txt',
    'recurring_template_html' => 'recurring_template.html',
    'recurring_template_text' => 'recurring_template.txt'
    );
