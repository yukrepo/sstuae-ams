<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function sendSMS($mob = null, $message = null)
    {
        $user="ssawebser"; //your username
		$password="SSA@oman17"; //your password
		$mobilenumbers=$mob; //enter Mobile numbers comma seperated
		$lang=0; //Delivery Reports
		$url="https://www.ismartsms.net/iBulkSMS/HttpWS/SMSDynamicAPI.aspx";
		$message = urlencode($message);
		$ch = curl_init();
		if (!$ch){die("Couldn't initialize a cURL handle");}
		$ret = curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt ($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt ($ch, CURLOPT_POSTFIELDS, 
		"UserId=$user&Password=$password&MobileNo=$mobilenumbers&Message=$message&Lang=$lang");
		$ret = curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		//If you are behind proxy then please uncomment below line and provide your proxy ip with port.
		// $ret = curl_setopt($ch, CURLOPT_PROXY, "PROXY IP ADDRESS:PORT");
		$curlresponse = curl_exec($ch); // execute
		$cerror = '';
		$info = '';
		if(curl_errno($ch)) return 'curl error : '. curl_error($ch);

		if (empty($ret)) {
			// some kind of an error happened
			$cerror = curl_error($ch);
			curl_close($ch); // close cURL handler
		}
		else {
			$info = curl_getinfo($ch);
			curl_close($ch); // close cURL handler
		}
		$return  = array('err' => $cerror, 'info' => $info, 'response' => $curlresponse);

		return $return;
    }

	public function sendWhatsappNote($mob = null, $message = null)
    {
        $apiKey="97548f0a92d342828586c7e1d26e1336c9d90420";
		$instance="unc8g7pYadsevTM";
		$message = urlencode($message);
		$url = 'https://senderomatic.xyz/api/send-text.php?number='.$mob.'&msg='.$message.'&apikey='.$apiKey.'&instance='.$instance;

		
		$ch = curl_init();
		if (!$ch){die("Couldn't initialize a cURL handle");}
		$ret = curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$ret = curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		//If you are behind proxy then please uncomment below line and provide your proxy ip with port.
		// $ret = curl_setopt($ch, CURLOPT_PROXY, "PROXY IP ADDRESS:PORT");
		$curlresponse = curl_exec($ch); // execute
		$cerror = '';
		$info = '';
		if(curl_errno($ch)) return 'curl error : '. curl_error($ch);

		if (empty($ret)) {
			// some kind of an error happened
			$cerror = curl_error($ch);
			curl_close($ch); // close cURL handler
		}
		else {
			$info = curl_getinfo($ch);
			curl_close($ch); // close cURL handler
		}
		$return  = array('err' => $cerror, 'info' => $info, 'response' => $curlresponse);

		return $return;
    }
}
