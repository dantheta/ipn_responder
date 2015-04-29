<?php

// Original file at: 
// https://raw.githubusercontent.com/paypal/ipn-code-samples/master/paypal_ipn.php

include_once("config.php");
include_once("template.php");
include_once("vendor/autoload.php");


function msglog($msg) {
    error_log(date('[Y-m-d H:i:s e] ').$msg . PHP_EOL,3,LOG_FILE);
}


// Read POST data
// reading posted data directly from $_POST causes serialization
// issues with array data in POST. Reading raw POST data from input stream instead.
$raw_post_data = file_get_contents('php://input');
$raw_post_array = explode('&', $raw_post_data);
$myPost = array();
foreach ($raw_post_array as $keyval) {
	$keyval = explode ('=', $keyval);
	if (count($keyval) == 2)
		$myPost[$keyval[0]] = urldecode($keyval[1]);
}
// read the post from PayPal system and add 'cmd'
$req = 'cmd=_notify-validate';
if(function_exists('get_magic_quotes_gpc')) {
	$get_magic_quotes_exists = true;
}
foreach ($myPost as $key => $value) {
	if(@$get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) {
		$value = urlencode(stripslashes($value));
	} else {
		$value = urlencode($value);
	}
	$req .= "&$key=$value";
}

// Post IPN data back to PayPal to validate the IPN data is genuine
// Without this step anyone can fake IPN data

if(USE_SANDBOX == true) {
	$paypal_url = "https://www.sandbox.paypal.com/cgi-bin/webscr";
} else {
	$paypal_url = "https://www.paypal.com/cgi-bin/webscr";
}

$ch = curl_init($paypal_url);
if ($ch == FALSE) {
	return FALSE;
}

curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);

if(DEBUG == true) {
	curl_setopt($ch, CURLOPT_HEADER, 1);
	curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
}

// CONFIG: Optional proxy configuration
//curl_setopt($ch, CURLOPT_PROXY, $proxy);
//curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 1);

// Set TCP timeout to 30 seconds
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));

// CONFIG: Please download 'cacert.pem' from "http://curl.haxx.se/docs/caextract.html" and set the directory path
// of the certificate as shown below. Ensure the file is readable by the webserver.
// This is mandatory for some environments.

//$cert = __DIR__ . "./cacert.pem";
//curl_setopt($ch, CURLOPT_CAINFO, $cert);

$res = curl_exec($ch);
if (curl_errno($ch) != 0) // cURL error
	{
	if(DEBUG == true) {	
		msglog( "Can't connect to PayPal to validate IPN message: " . curl_error($ch) );
	}
	curl_close($ch);
	exit;

} else {
		// Log the entire HTTP response if debug is switched on.
		if(DEBUG == true) {
			msglog( "HTTP request of validation request:". curl_getinfo($ch, CURLINFO_HEADER_OUT) ." for IPN payload: $req" );
			msglog( "HTTP response of validation request: $res" );
		}
		curl_close($ch);
}

// Inspect IPN validation result and act accordingly

// Split response headers and payload, a better way for strcmp
$tokens = explode("\r\n\r\n", trim($res));
$res = trim(end($tokens));

if (strcmp ($res, "VERIFIED") == 0) {
	
	if(DEBUG == true) {
		msglog( "Verified IPN: $req ");
	}

    if ($_POST['payment_status'] == 'Completed') {


        $mail = new PHPMailer();
        if ($CONFIG['smtp_server']) {
            $mail->IsSMTP();
            $mail->Host = $CONFIG['smtp_server'];
        }

        $mail->From = $CONFIG['from_address'];
        $mail->AddAddress($_POST['payer_email']);

        $mail->Subject = $CONFIG['subject'];
        $mail->WordWrap = 70;

        $template_args = array(
            'FIRST_NAME' => $_POST['first_name'],
            'LAST_NAME' => $_POST['last_name'],
            'EMAIL' => $_POST['payer_email']
        );

        $mail->AltBody = run_template($CONFIG['template_text'], $template_args, false);
        $mail->Body = run_template($CONFIG['template_html'], $template_args, true);

        if(!$mail->Send()) {
            msglog("Error sending mail: {$mail->ErrorInfo}");
        } else {
            msglog("Email sent to {$_POST['payer_email']}");
            msglog("Content: {$mail->AltBody}");
        }

    } else {
        msglog("Invalid payment_type or payment_status: {$_POST['payment_type']}, {$_POST['payment_status']}");
    }


} else if (strcmp ($res, "INVALID") == 0) {
	// log for manual investigation
	if(DEBUG == true) {
		msglog( "Invalid IPN: $req" );
	}
}

?>
