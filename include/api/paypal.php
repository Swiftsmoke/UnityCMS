<?php

// class that works with PayPal Instant Payment Notification. It takes what was
// sent from PayPal and sends an indentical response back to PayPal, then waits
// for verification from PayPal

class Paypal {

    var $paypal_post_vars;
    var $paypal_response;

    var $timeout;

    // error logging info
    var $error_email;
    var $from_email;

    function Paypal($paypal_post_vars, $error_email, $from_email, $timeout = 120) {

        $this->paypal_post_vars = $paypal_post_vars;
        $this->timeout = $timeout;
        $this->error_email=$error_email;
        $this->from_email=$from_email;
        $this->paypal_response = "";
    }

    function request_for_confirmation() {
        $this->send_response();
    }

    function send_response_curl() {

        $values=array();

        foreach($this->paypal_post_vars as $key => $value) {

            // if magic quotes gpc is on, PHP added slashes to the values so we need
            // to strip them before we send the data back to Paypal.
            if(@get_magic_quotes_gpc())    $value = stripslashes($value);

            // make an array of URL encoded values
            $values[] = $key."=".urlencode($value);
        }

        $request = @implode("&", $values);
            $request .= "&cmd=_notify-validate";

         //$ch = curl_init("http://www.eliteweaver.co.uk/cgi-bin/webscr");
           $ch = curl_init("https://www.paypal.com/cgi-bin/webscr");

           // debug settings
           curl_setopt($ch, CURLOPT_VERBOSE, 1);
           curl_setopt($ch, CURLOPT_HEADER, 1);      // to get response header in output

           curl_setopt($ch, CURLOPT_POST, 1);
           curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
           curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
           curl_setopt($ch, CURLOPT_TIMEOUT, 90);

        $this->paypal_response = curl_exec($ch);
        //echo $this->paypal_response."\n";

        curl_close($ch);
    }

    // the same as above but without curl
    function send_response_http() {

        if(!($fp = @fsockopen("www.paypal.com", 80, &$errno, &$errstr, 120))) {
            echo "error<br>";
            $this->error_out("PHP fsockopen() error: " . $errstr);

        } else {

            $values=array();
            foreach($this->paypal_post_vars as $key => $value) {
                // if magic quotes gpc is on, PHP added slashes to the values so we need
                // to strip them before we send the data back to Paypal.
                if(@get_magic_quotes_gpc())    $value = stripslashes($value);
                // make an array of URL encoded values
                $values[] = $key."=".urlencode($value);
            }

            // join the values together into one url encoded string
            $request = @implode("&", $values);

            // add paypal cmd variable
            $request .= "&cmd=_notify-validate";

            fputs($fp, "POST /cgi-bin/webscr HTTP/1.0\r\n" );
            fputs($fp, "Host: https://www.paypal.com\r\n" );
            if (isset($GLOBALS['HTTP_USER_AGENT']))
            fputs($fp, "User-Agent: ".$GLOBALS['HTTP_USER_AGENT'] ."\r\n" );
            fputs($fp, "Accept: */*\r\n" );
            fputs($fp, "Accept: image/gif\r\n" );
            fputs($fp, "Accept: image/x-xbitmap\r\n" );
            fputs($fp, "Accept: image/jpeg\r\n" );
            fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n" );
            fputs($fp, "Content-length: " . strlen($request) . "\r\n\n" );

            // send url encoded string of data
            fputs($fp, "$request\n\r" );
            fputs($fp, "\r\n" );

            $this->send_time = time();
            $this->paypal_response = "";

            // get response from paypal
            while(!feof($fp)) {
                $this->paypal_response .= fgets($fp, 1024);
                // waited too long?
                if($this->send_time < time()-$this->timeout)
                {
                    $this->error_out("Timed out waiting for a response from PayPal. ($this->timeout seconds)");
                    break;
				}
            }
            fclose( $fp );
        }
    }

    function send_response() {
        if (extension_loaded("curl")) {
            $this->send_response_curl();
        } else {
            $this->send_response_http();
        }
    }

    // returns true if paypal says the order is good, false if not
    function is_verified() {
        return ereg("VERIFIED", $this->paypal_response);
    }

    // returns the paypal payment status
    function get_payment_status() {
        return $this->paypal_post_vars["payment_status"];
    }

    // writes error to logfile, exits script
    function error_out($message) {

        $date = date("D M j G:i:s T Y", time());

        // add on the data we sent:
        $message .= "\n\nThe following input was received from (and sent back to) PayPal:\n\n";

        @reset($this->paypal_post_vars);
        while(@list($key,$value) = @each($this->paypal_post_vars))
            $message .= $key . ':' . " \t$value\n";

        if( $this->error_email ) mail($this->error_email, "[$date] paypay_ipn", $message, 'From : "'.$this->from_email.'"');
        return;
    }

}
?>