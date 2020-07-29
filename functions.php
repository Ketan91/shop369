<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);

$last_updated = "Last Updated: 2019-11-27";

#set max execution time
ini_set('max_execution_time', 1200); //1200 seconds = 20 minutes

// Mandatory Parameter
$sapiVersion = 1.50;
$sapiUrl = 'https://api.flickstree.com/sapi_service_150v.php';
$sapiEncoding = 'JSON';
$sapiAccessCode = 'BXuAH61Ah';
$USERID = 12345;


// SMS Gateway - MSG91
$smsGateway = "MSG91";
$smsAuthKey = "186995A9XstGJgNC5a27d9c1";
$smsSenderId = "FLICKS";
$smsApiUrl = "https://control.msg91.com/api/";

date_default_timezone_set("Asia/Calcutta");

// Swift Mailer Library
$support_email = "anoop@flickstree.com";
$do_not_reply_email = "anoop@flickstree.com";
// require_once 'mail_functions.php';

/// SAPI Functions ///

function add_otp($mobile,$otp)
{
	global $sapiUrl,$sapiVersion,$sapiEncoding;
	
	$response = Array();
	
	$url = $sapiUrl."?VERSION=".$sapiVersion."&ENCODING=".$sapiEncoding."&METHOD=ADD_OTP&mobile=".$mobile."&otp=".$otp;
	
	$data = json_decode(file_get_contents($url),true);
	
	/* if ($data['RESULT'] == 'SUCCESS')
	{
			$response = $data['RESPONSE'];
			return $response;
			
	} 
	else{
		   return $response;
		} */
   return $data;
} // add_otp

 
function add_customer($name, $email, $mobile, $yob, $gender, $referer, $password)
{
	global $sapiUrl,$sapiVersion,$sapiEncoding;
	
	$response = Array();
	
	$url = $sapiUrl."?VERSION=".$sapiVersion."&ENCODING=".$sapiEncoding."&METHOD=ADD_CUSTOMER&name=".urlencode($name)."&email=".$email."&mobile=".$mobile."&yob=".$yob."&gender=".$gender."&referer=".$referer."&password=".$password;
	
	
    $data = json_decode(file_get_contents($url),true);
	
	
	/* if ($data['RESULT'] == 'SUCCESS')
	{
			$response = $data['RESPONSE'];
			return $response;
			
	} 
	else{
		return $response;
		} */
return $data;
} // add_customer
	
function customer_login($customerid, $password, $addon)
{
	global $sapiUrl,$sapiVersion,$sapiEncoding;
	
	$response = Array();
	
	if($addon!=''){
	$url = $sapiUrl."?VERSION=".$sapiVersion."&ENCODING=".$sapiEncoding."&METHOD=CUSTOMER_LOGIN&USERID=".$customerid."&USERPASSWORD=".$password."&addon=".$addon;
	}else{
	$url = $sapiUrl."?VERSION=".$sapiVersion."&ENCODING=".$sapiEncoding."&METHOD=CUSTOMER_LOGIN&USERID=".$customerid."&USERPASSWORD=".$password;	
	}
	
	$data = json_decode(file_get_contents($url),true);
	
	/* if ($data['RESULT'] == 'SUCCESS')
	{
		
		$response = $data['RESPONSE'];
		return $response;
			
	} 
	else{
		return $response;
		} */
return $data;
} // customer_login

function add_affiliate($name, $email, $mobile, $password, $custom_session_id, $additional_info)
{
	global $sapiUrl,$sapiVersion,$sapiEncoding;
	
	$response = Array();
	
	if($custom_session_id!=''){
	    $url = $sapiUrl."?VERSION=".$sapiVersion."&ENCODING=".$sapiEncoding."&METHOD=ADD_AFFILIATE&name=".urlencode($name)."&email=".$email."&mobile=".$mobile."&password=".$password."&custom_session_id=".$custom_session_id."&additional_info=".$additional_info;
	}else{
		$url = $sapiUrl."?VERSION=".$sapiVersion."&ENCODING=".$sapiEncoding."&METHOD=ADD_AFFILIATE&name=".urlencode($name)."&email=".$email."&mobile=".$mobile."&password=".$password."&additional_info=".$additional_info;
	}
	
	
    $data = json_decode(file_get_contents($url),true);
	
	
	/* if ($data['RESULT'] == 'SUCCESS')
	{
			$response = $data['RESPONSE'];
			return $response;
			
	} 
	else{
		return $response;
		} */
return $data;
} // add_affiliate



function trusted_customer_login($name, $email, $customerid, $yob, $gender, $partner)
{
	global $sapiUrl,$sapiVersion,$sapiEncoding;
	
	$response = Array();
	
	$url = $sapiUrl."?VERSION=".$sapiVersion."&ENCODING=".$sapiEncoding."&METHOD=TRUSTED_CUSTOMER_LOGIN&USERID=".$customerid."&partner=".$partner."&name=".urlencode($name)."&email=".$email."&yob=".$yob."&gender=".$gender;	
	
	$data = json_decode(file_get_contents($url),true);
	
    return $data;

} // trusted_customer_login


function update_customer($name, $email, $mobile, $yob, $gender, $address, $user_id, $session_id)
{
	global $sapiUrl,$sapiVersion,$sapiEncoding;
	
	$response = Array();
	
	$nameStr = $emailStr = $mobileStr = $yobStr = $genderStr = $addressStr = "";
	
	if($name!=''){
		$nameStr = "&name=".urlencode($name);
	}
	if($email!=''){
		$emailStr = "&email=".$email;
	}
	if($mobile!=''){
		$mobileStr = "&mobile=".$mobile;
	}
	if($yob!=''){
		$yobStr = "&yob=".$yob;
	}
	if($gender!=''){
		$genderStr = "&gender=".$gender;
	}
	if($address!=''){
		$addressStr = "&payee_account_address=".urlencode($address);
	}
	
	$url = $sapiUrl."?VERSION=".$sapiVersion."&ENCODING=".$sapiEncoding."&SESSIONID=".$session_id."&USERID=".$user_id."&METHOD=CUSTOMER_PROFILE_UPDATE".$nameStr.$emailStr.$mobileStr.$yobStr.$genderStr.$addressStr;
	
    $data = json_decode(file_get_contents($url),true);
		
return $data;
} // update_customer

function get_customer_profile($customerid, $sessionid)
{
	global $sapiUrl,$sapiVersion,$sapiEncoding;
	
	$response = Array();
	
	$url = $sapiUrl."?VERSION=".$sapiVersion."&ENCODING=".$sapiEncoding."&METHOD=GET_CUSTOMER_PROFILE&USERID=".$customerid."&SESSIONID=".$sessionid;	
	
	$data = json_decode(file_get_contents($url),true);
	
    return $data;

} // get_customer_profile

function get_user_payid($customerid, $partner_id)
{
	global $sapiUrl,$sapiVersion,$sapiEncoding;
	
	$response = Array();
	
	$url = $sapiUrl."?VERSION=".$sapiVersion."&ENCODING=".$sapiEncoding."&METHOD=GET_USER_PAYID&USERID=".$customerid."&partner_id=".$partner_id;	
	
	$data = json_decode(file_get_contents($url),true);
	
    return $data;

} // get_user_payid

function get_product_detail($product_id, $partner_id)
{
	global $sapiUrl,$sapiVersion,$sapiEncoding;
	
	$response = Array();
	
	$url = $sapiUrl."?VERSION=".$sapiVersion."&ENCODING=".$sapiEncoding."&METHOD=GET_PRODUCT_DETAIL&partner_id=".$partner_id."&product_id=".$product_id;	
	
	$data = json_decode(file_get_contents($url),true);
	
    return $data;

} // get_user_payid


function create_user_log($user_id,$product_id,$partner,$type,$status,$submit_datetime)
{
	
// chdir('/mnt/flickstree/flickstree/htdocs/video_commerce/actions/logs');
	
$filename = getcwd() . "/".$user_id."_log_data.txt";	

$user_log_array[$product_id] = Array("product_id"=>$product_id,"partner"=>$partner,"type"=>$type,"status"=>$status,"submit_datetime"=>$submit_datetime);

if(file_exists($filename)){

	$temp_user_log_array = json_decode(file_get_contents($filename),true);
	if(count($temp_user_log_array)>0){
    if(isset($temp_user_log_array[$product_id])){
		    
			$temp_user_log_array = array_merge($temp_user_log_array,$user_log_array);
			$jsonData = json_encode($temp_user_log_array);
			file_put_contents($filename, $jsonData);
			return true;
		
	} //  if(isset($temp_user_log_array[$product_id]))
		else{
			$temp_user_log_array = array_merge($temp_user_log_array,$user_log_array);
			$jsonData = json_encode($temp_user_log_array);
			file_put_contents($filename, $jsonData);
			return true;
		}
		}else{
			file_put_contents($filename, json_encode($user_log_array));
			return true;
		}

}else{ 
file_put_contents($filename, json_encode($user_log_array));
 return true;	
}
	
} // create_user_log


function add_publisher_onboarding($legal_entry_name,$legal_entry_type,$cin_number,$affiliate_name,$first_name,$last_name,$email,$mobile,$office_landline,$building_name,$address1,$address2,$city,$state,$pin,$country,$size_of_business,$website_title,$website_url,$website_status,$unique_monthly_visitors,$primary_sector,$secondary_sector,$account_name,$bank_name,$bank_city,$branch_location,$micr_number,$ifsc_code,$beneficiary_type,$beneficiary_nationality,$account_card_type,$account_card_number,$submit_datetime)
{

$publisher_onboarding_array[$mobile] = Array("legal_entry_name"=>$legal_entry_name,"legal_entry_type"=>$legal_entry_type,"cin_number"=>$cin_number,"affiliate_name"=>$affiliate_name,"first_name"=>$first_name,"last_name"=>$last_name,"email"=>$email,"mobile"=>$mobile,"office_landline"=>$office_landline,"building_name"=>$building_name,"address1"=>$address1,"address2"=>$address2,"city"=>$city,"state"=>$state,"pin"=>$pin,"country"=>$country,"size_of_business"=>$size_of_business,"website_title"=>$website_title,"website_url"=>$website_url,"website_status"=>$website_status,"unique_monthly_visitors"=>$unique_monthly_visitors,"primary_sector"=>$primary_sector,"secondary_sector"=>$secondary_sector,"account_name"=>$account_name,"bank_name"=>$bank_name,"bank_city"=>$bank_city,"branch_location"=>$branch_location,"micr_number"=>$micr_number,"ifsc_code"=>$ifsc_code,"beneficiary_type"=>$beneficiary_type,"beneficiary_nationality"=>$beneficiary_nationality,"account_card_type"=>$account_card_type,"account_card_number"=>$account_card_number,"submit_datetime"=>$submit_datetime);

if(file_exists("publisher_onboarding_data.txt")){

	$temp_publisher_onboarding_array = json_decode(file_get_contents('publisher_onboarding_data.txt'),true);
	
    if(!isset($temp_publisher_onboarding_array[$mobile])){
		if(count($temp_publisher_onboarding_array)>0){
			$temp_publisher_onboarding_array = array_merge($temp_publisher_onboarding_array,$publisher_onboarding_array);
			$jsonData = json_encode($temp_publisher_onboarding_array);
			file_put_contents('publisher_onboarding_data.txt', $jsonData);
			return true;
		}else{
			file_put_contents('publisher_onboarding_data.txt', json_encode($publisher_onboarding_array));
			return true;
		}
	}else{
		return false;
	}

}else{
	file_put_contents('publisher_onboarding_data.txt', json_encode($publisher_onboarding_array));
    return true;	
}
	
} // add_publisher_onboarding


/// General Functions ///

function sanitize_string($a_string)
{
  $a_string = htmlspecialchars($a_string, ENT_QUOTES);
  $a_string = strip_tags($a_string);
  return trim($a_string);  
} // sanitize_string


// not used this function -- ereg is deprecated 
function check_email_address($email) 
{
 // First, we check that there's one @ symbol, and that the lengths are right
  if (!ereg("^[^@]{1,64}@[^@]{1,255}$", $email)) 
  {
    // Email invalid because wrong number of characters in one section, or wrong number of @ symbols.
    return false;
  }
  // Split it into sections to make life easier
  $email_array = explode("@", $email);
  $local_array = explode(".", $email_array[0]);
  for ($i = 0; $i < sizeof($local_array); $i++) 
  {
    if (!ereg("^(([A-Za-z0-9!#$%&'*+/=?^_`{|}~-][A-Za-z0-9!#$%&'*+/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$", $local_array[$i])) 
    {
      return false;
    }
  }
  if (!ereg("^\[?[0-9\.]+\]?$", $email_array[1])) 
  { // Check if domain is IP. If not, it should be valid domain name
    $domain_array = explode(".", $email_array[1]);
    if (sizeof($domain_array) < 2) 
    {
      return false; // Not enough parts to domain
    }
    for ($i = 0; $i < sizeof($domain_array); $i++) 
    {
      if (!ereg("^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$", $domain_array[$i])) 
      {
        return false;
      }
    }
  }
  return true;
} // check_email_address

function valid_email($email) {
return (!preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $email)) ? FALSE : TRUE;
}


// mobile must be 12 digit long.
function validate_mobile($mobile)
{
    return preg_match('/^[0-9]{12}+$/', $mobile);
}

function check_year($number)
{
    return preg_match('/^[0-9]{4}+$/', $number);
}

function strip_html_tags( $text )
{
    $text = preg_replace(
        array(
          // Remove invisible content
            '@<head[^>]*?>.*?</head>@siu',
            '@<style[^>]*?>.*?</style>@siu',
            '@<script[^>]*?.*?</script>@siu',
            '@<object[^>]*?.*?</object>@siu',
            '@<embed[^>]*?.*?</embed>@siu',
            '@<applet[^>]*?.*?</applet>@siu',
            '@<noframes[^>]*?.*?</noframes>@siu',
            '@<noscript[^>]*?.*?</noscript>@siu',
            '@<noembed[^>]*?.*?</noembed>@siu',
          // Add line breaks before and after blocks
            '@</?((address)|(blockquote)|(center)|(del))@iu',
            '@</?((div)|(h[1-9])|(ins)|(isindex)|(p)|(pre))@iu',
            '@</?((dir)|(dl)|(dt)|(dd)|(li)|(menu)|(ol)|(ul))@iu',
            '@</?((table)|(th)|(td)|(caption))@iu',
            '@</?((form)|(button)|(fieldset)|(legend)|(input))@iu',
            '@</?((label)|(select)|(optgroup)|(option)|(textarea))@iu',
            '@</?((frameset)|(frame)|(iframe))@iu',
        ),
        array(
            ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
            "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0",
            "\n\$0", "\n\$0",
        ),
        $text );
    return strip_tags( $text );
} // strip_html_tags



function escapeRegExp($text) {
  return preg_replace('/[-[\]{}()*+?,%&\\^$]/', '', $text);
} // escapeRegExp

function trim_and_lowercase($value) 
{ 
    return strtolower(trim($value)); 
} // trim_and_lowercase

// check if a date is in a given range
function check_in_range($start_date, $end_date, $date_from_user)
{
  // Convert to timestamp
  $start_ts = strtotime($start_date);
  $end_ts = strtotime($end_date);
  $user_ts = strtotime(date('Y-m-d', strtotime($date_from_user)));

  // Check that user date is between start & end
  return (($user_ts >= $start_ts) && ($user_ts <= $end_ts));
}


// Function to get all the dates in given range 
function getDatesFromRange($start, $end, $format = 'Y-m-d') { 
      
    // Declare an empty array 
    $array = array(); 
      
    // Variable that store the date interval 
    // of period 1 day 
    $interval = new DateInterval('P1D'); 
  
    $realEnd = new DateTime($end); 
    $realEnd->add($interval); 
  
    $period = new DatePeriod(new DateTime($start), $interval, $realEnd); 
  
    // Use loop to store date into array 
    foreach($period as $date) {                  
        $array[] = $date->format($format);  
    } 
  
    // Return the array elements 
    return $array; 
} // getDatesFromRange 
 


if (!function_exists('hash_equals')) {
    /**
     * Compares two strings using the same time whether they're equal or not.
     * A difference in length will leak
     *
     * @param string $known_string
     * @param string $user_string
     * @return boolean Returns true when the two strings are equal, false otherwise.
     */
    function hash_equals($known_string, $user_string)
    {
        $result = 0;

        if (!is_string($known_string)) {
            trigger_error("hash_equals(): Expected known_string to be a string", E_USER_WARNING);
            return false;
        }

        if (!is_string($user_string)) {
            trigger_error("hash_equals(): Expected user_string to be a string", E_USER_WARNING);
            return false;
        }

        if (strlen($known_string) != strlen($user_string)) {
            return false;
        }

        for ($i = 0; $i < strlen($known_string); $i++) {
            $result |= (ord($known_string[$i]) ^ ord($user_string[$i]));
        }

        return 0 === $result;
    }
}
 
?>