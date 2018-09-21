<?php namespace App\helpers;

define( 'SSL_P_URL', 'https://www.paypal.com/cgi-bin/webscr' );
define( 'SSL_SANDS_URL', 'https://www.sandbox.paypal.com/cgi-bin/webscr' );
class PayPal_IPN
{
	
	function infotuts_ipn($im_debut_ipn,$request) 
	{
		mail("gurpal.singh@softobiz.com","i m in ipn","data");
		$hostname = gethostbyaddr($_SERVER['REMOTE_ADDR']);
        if (!preg_match('/paypal\.com$/', $hostname)) 
        {
            $ipn_status = 'Validation post isn\'t from PayPal';
            if ($im_debut_ipn == true) 
            {
			 	//mail("gurpal.singh@softobiz.com","Post data isn\'t from PayPal","data");
            }
            return false;
        }
		// parse the paypal URL
        $paypal_url = ($request['test_ipn'] == 1) ? SSL_SANDS_URL : SSL_P_URL;
        $url_parsed = parse_url($paypal_url);
		
	    $post_string = '';
        foreach ($request as $field => $value) {
            $post_string .= $field . '=' . urlencode(stripslashes($value)) . '&';
        }
        $post_string.="cmd=_notify-validate"; // append ipn command
        // get the correct paypal url to post request to
        $paypal_mode_status = $im_debut_ipn; //get_option('im_sabdbox_mode');
        if ($paypal_mode_status == true)
            $fp = fsockopen('ssl://www.sandbox.paypal.com', "443", $err_num, $err_str, 60);
        else
            $fp = fsockopen('ssl://www.paypal.com', "443", $err_num, $err_str, 60);

        $ipn_response = '';

        if (!$fp) 
        {
				// could not open the connection.  If loggin is on, the error message
				// will be in the log.
            $ipn_status = "fsockopen error no. $err_num: $err_str";
            if ($im_debut_ipn == true) {
               $msg='fsockopen fail';
                mail("gurpal.singh@softobiz.com",$msg,"data");
            }
            return false;
        } 
        else 
        {
			// Post the data back to paypal
            fputs($fp, "POST $url_parsed[path] HTTP/1.1\r\n");
            fputs($fp, "Host: $url_parsed[host]\r\n");
            fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
            fputs($fp, "Content-length: " . strlen($post_string) . "\r\n");
            fputs($fp, "Connection: close\r\n\r\n");
            fputs($fp, $post_string . "\r\n\r\n");

			// loop through the response from the server and append to variable
            while (!feof($fp)) {
                $ipn_response .= fgets($fp, 1024);
            }
            fclose($fp); // close connection
        }

		// Invalid IPN transaction.  Check the $ipn_status and log for details.
        if (!preg_match("/VERIFIED/s", $ipn_response))
        {
            $ipn_status = 'IPN Validation Failed';

            if ($im_debut_ipn == true) {
                $msg='Validation fail';
               // mail("gurpal.singh@softobiz.com",$msg,"data");
            }
            return false;
        } 
        else
        {
            $ipn_status = "IPN VERIFIED";
            if ($im_debut_ipn == true) {
              //  echo 'SUCCESS';
                $msg='SUCCESS';
                //mail("gurpal.singh@softobiz.com","success","data");
                
				}

            return true;
        }
    }
	function ipn_response($request)
	{
		//mail("gurpal.singh@softobiz.com","My subject success",print_r($request,true));
		$im_debut_ipn=true;
		
	    if ($this->infotuts_ipn($im_debut_ipn,$request)) 
	    {
	    	//mail("gurpal.singh@softobiz.com","My subject true",print_r($request,true));
			if ($im_debut_ipn == true)
			{
                 $sub = 'PayPal IPN Debug Email Main';
                 //$msg = print_r($request, true);
                 //$aname = 'infotuts';
                // mail("gurpal.singh@softobiz.com",$sub,$msg);
                   
             }
             return true;

        }
	}
	
}
	
