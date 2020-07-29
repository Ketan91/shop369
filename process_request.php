<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
include "functions.php"; // Get Configuration

$data =  array();

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    
    if (!empty($_REQUEST["do_action"])) {
        
        $do_action = sanitize_string($_REQUEST["do_action"]);
        
        if ($do_action == 'SEND_OTP') {
            
			// Starting session
            session_start();
			
            $user_name      = sanitize_string($_REQUEST["user_name"]);
			$user_email     = sanitize_string($_REQUEST["user_email"]);
            $user_mobile     = sanitize_string($_REQUEST["user_mobile"]);
            $user_yob       = sanitize_string($_REQUEST["user_yob"]);
            $user_gender    = sanitize_string($_REQUEST["user_gender"]);
         
            
            //Your message to send, Add URL encoding here.
            $rndno    = rand(1000, 9999);
            $message  = urlencode("otp number." . $rndno);
            //Define route 
            $route    = "route=4";
            //Prepare you post parameters
            $postData = array(
                'authkey' => $smsAuthKey,
                'mobile' => $user_mobile,
                'sender' => $smsSenderId
            );
            //API URL
            $url      = $smsApiUrl."sendotp.php";
            // init the resource
            $ch       = curl_init();
            curl_setopt_array($ch, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $postData
                //,CURLOPT_FOLLOWLOCATION => true
            ));
            //Ignore SSL certificate verification
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            //get response
            $output         = curl_exec($ch);
            $decoded_output = json_decode($output, true);
            
            
            //Print error if any
            if (curl_errno($ch)) {
				    
					$data['RESULT'] = "FAILED";
					$data['REQUEST'] = "SEND_OTP";
				    $data['RESPONSE'] = "Sorry! Something went wrong.";
                    //$data['status'] = curl_error($ch);
                    echo json_encode($data);
            }
            //curl_close($ch);
            if ($decoded_output['type'] == "success") {
                
                setcookie("AFF_NAME", $user_name, time() + (86400 * 1), "/");
				setcookie("AFF_EMAIL", $user_email, time() + (86400 * 1), "/");
                setcookie("AFF_MOBILE", $user_mobile, time() + (86400 * 1), "/");
                setcookie("AFF_YOB", $user_yob, time() + (86400 * 1), "/");
                setcookie("AFF_GENDER", $user_gender, time() + (86400 * 1), "/");
                
				
				// Storing session data
				/* $_SESSION["AFF_NAME"] = $user_name;
				$_SESSION["AFF_EMAIL"] = $user_email;
				$_SESSION["AFF_MOBILE"] = $user_mobile;
				$_SESSION["AFF_YOB"] = $user_yob;
				$_SESSION["AFF_GENDER"] = $user_gender; */
				
				$data['RESULT'] = "SUCCESS";
				$data['REQUEST'] = "SEND_OTP";
				$data['RESPONSE'] = "OTP sent successfully.";
                echo json_encode($data);
                
            }
			else{
				    $data['RESULT'] = "FAILED";
					$data['REQUEST'] = "SEND_OTP";
					$data['RESPONSE'] = "Incorrect mobile number. Please type the correct mobile number and try again.";
					echo json_encode($data);
			}
            
            
        } // if($do_action=='SEND_OTP')    
        
        elseif ($do_action == 'VERIFY_OTP') {
            
            $otp_number = sanitize_string($_REQUEST["otp_number"]);
			$partner = sanitize_string($_REQUEST["partner"]);
			$type = sanitize_string($_REQUEST["type"]);
			
			// Starting session
            //session_start();
                
            if (isset($_COOKIE["AFF_MOBILE"])) {
                
                $mobile = $_COOKIE["AFF_MOBILE"];
                
                //Prepare you post parameters
                $postData = array(
                    'authkey' => $smsAuthKey,
                    'mobile' => $mobile,
                    'otp' => $otp_number
                );
                //API URL
                $url = $smsApiUrl."verifyRequestOTP.php";
                // init the resource
                $ch  = curl_init();
                curl_setopt_array($ch, array(
                    CURLOPT_URL => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_POST => true,
                    CURLOPT_POSTFIELDS => $postData
                    //,CURLOPT_FOLLOWLOCATION => true
                ));
                //Ignore SSL certificate verification
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                //get response
                $output = curl_exec($ch);
                
                $decoded_output = json_decode($output, true);
                
                
                //Print error if any
                if (curl_errno($ch)) {
					$data['RESULT'] = "FAILED";
					$data['REQUEST'] = "VERIFY_OTP";
				    $data['RESPONSE'] = "Sorry! Something went wrong.";
					//$data['status'] = curl_error($ch);
                    echo json_encode($data);
                }
                curl_close($ch);
                
                if ($decoded_output['type'] == "success") {
                    
					$otpResp = add_otp($mobile,$otp_number);
					
                    if($otpResp['RESULT'] == 'SUCCESS'){
						
						
						$mobile = $_COOKIE["AFF_MOBILE"];
						
						if(isset($_COOKIE['AFF_REFERER'])){
						$referer = $_COOKIE['AFF_REFERER'];
						}else{
						$referer = "";	
						}
						
						$password = $otp_number;
						
						if(isset($_COOKIE['AFF_NAME'])){
							
						$name = $_COOKIE['AFF_NAME'];
						$email = $_COOKIE['AFF_EMAIL'];
						$yob = $_COOKIE['AFF_YOB'];
						$gender = $_COOKIE['AFF_GENDER'];
						
						if($type=='seller'){
						$addUserResp = trusted_customer_login($name, $email, $mobile, $yob, $gender, $partner);
						}else{
						$addUserResp = add_customer($name, $email, $mobile, $yob, $gender, $referer, $password);	
						}
						
						if($addUserResp['RESULT'] == 'SUCCESS'){
							
							$session_id = $addUserResp['RESPONSE']['session_id'];
							$id = $addUserResp['RESPONSE']['id'];
							$name = $addUserResp['RESPONSE']['name'];
							
							// unset unlogged user cookie
							setcookie("AFF_NAME", "", 1, "/");
							setcookie("AFF_EMAIL", "", 1, "/");
							setcookie("AFF_MOBILE", "", 1, "/");
							setcookie("AFF_YOB", "", 1, "/");
							setcookie("AFF_GENDER", "", 1, "/");
							//setcookie("AFF_REFERER", "", 1, "/");
							//setcookie("AFF_STATUS", "", 1, "/");
							
							// Removing session data
							/* if(isset($_SESSION["AFF_NAME"])){
							unset($_SESSION["AFF_NAME"]);
							}
							if(isset($_SESSION["AFF_EMAIL"])){
							unset($_SESSION["AFF_EMAIL"]);
							}
							if(isset($_SESSION["AFF_MOBILE"])){
							unset($_SESSION["AFF_MOBILE"]);
							}
							if(isset($_SESSION["AFF_YOB"])){
							unset($_SESSION["AFF_YOB"]);
							}
							if(isset($_SESSION["AFF_GENDER"])){
							unset($_SESSION["AFF_GENDER"]);
							} */
							
							
							// set new cookie - when successfull login
							setcookie("FTAFF_SESSIONID", $session_id, time() + (86400 * 30), "/");
							setcookie("FTAFF_ID", $id, time() + (86400 * 30), "/");
							setcookie("FTAFF_NAME", $name, time() + (86400 * 30), "/");
							setcookie("FTAFF_MOBILE", $mobile, time() + (86400 * 30), "/");
							
							// Storing session data
							/* $_SESSION["FTAFF_SESSIONID"] = $session_id;
							$_SESSION["FTAFF_ID"] = $id;
							$_SESSION["FTAFF_NAME"] = $name;
							$_SESSION["FTAFF_MOBILE"] = $mobile; */
							
							$data['RESULT'] = "SUCCESS";
							$data['REQUEST'] = "VERIFY_OTP";
				            $data['RESPONSE'] = "Your mobile number is added successfully.";
							//$data['status'] = "success";
                            echo json_encode($data);
							
						} // if(!empty($addUserResp))
						else{
					
					// unset unlogged user cookie
							setcookie("AFF_NAME", "", 1, "/");
							setcookie("AFF_EMAIL", "", 1, "/");
							setcookie("AFF_MOBILE", "", 1, "/");
							setcookie("AFF_YOB", "", 1, "/");
							setcookie("AFF_GENDER", "", 1, "/");
                     					
					$data['RESULT'] = "FAILED";
					$data['REQUEST'] = "VERIFY_OTP";
				    $data['RESPONSE'] = "Either your mobile number already exists or you entered wrong details.";
					echo json_encode($data);
						}	
						
						}else{
							$loginUserResp = customer_login($mobile,$password,'influencer');
							
							if($loginUserResp['RESULT'] == 'SUCCESS'){
							
							$session_id = $loginUserResp['RESPONSE']['session_id'];
							$id = $loginUserResp['RESPONSE']['id'];
							$name = $loginUserResp['RESPONSE']['name'];
							
							// unset unlogged user cookie
							setcookie("AFF_MOBILE", "", 1, "/");
							// Removing session data
							/* if(isset($_SESSION["AFF_MOBILE"])){
							unset($_SESSION["AFF_MOBILE"]);
							} */
							
							
							// set new cookie - when successfull login
							setcookie("FTAFF_SESSIONID", $session_id, time() + (86400 * 30), "/");
							setcookie("FTAFF_ID", $id, time() + (86400 * 30), "/");
							setcookie("FTAFF_NAME", $name, time() + (86400 * 30), "/");
							setcookie("FTAFF_MOBILE", $mobile, time() + (86400 * 30), "/");
							// Storing session data
							/* $_SESSION["FTAFF_SESSIONID"] = $session_id;
							$_SESSION["FTAFF_ID"] = $id;
							$_SESSION["FTAFF_NAME"] = $name;
							$_SESSION["FTAFF_MOBILE"] = $mobile; */
							
							$data['RESULT'] = "SUCCESS";
							$data['REQUEST'] = "VERIFY_OTP";
				            $data['RESPONSE'] = "You logged in successfully.";
							//$data['status'] = "success";
                            echo json_encode($data);
							
						} // if(!empty($loginUserResp))
						else{
					$data['RESULT'] = "FAILED";
					$data['REQUEST'] = "VERIFY_OTP";
				    $data['RESPONSE'] = "This mobile number doesn't exist in our system. Please log in with correct mobile number.";
					echo json_encode($data);
						}
							
						}
						
					} // if(!empty($otpResp))
						else{
					$data['RESULT'] = "FAILED";
					$data['REQUEST'] = "VERIFY_OTP";
				    $data['RESPONSE'] = "Sorry! OTP is not added in our system.";
					echo json_encode($data);
						}
                    
                    
                } // if($decoded_output['type']=="success")
                else {
                    $data['RESULT'] = "FAILED";
					$data['REQUEST'] = "VERIFY_OTP";
					//$data['RESPONSE'] = json_encode($decoded_output);
					//$data['RESPONSE2'] = $url;
					$data['RESPONSE'] = "Incorrect OTP. Please type the correct OTP and submit again.";
                    echo json_encode($data);
                }
                
            } // if(!isset($_COOKIE["AFF_MOBILE"]))
            else {
                $data['status'] = 406;
                echo json_encode($data);
            }
            
        } // if($do_action=='VERIFY_OTP')
            
		elseif ($do_action == 'RETRY_OTP') {
			
			// Starting session
            //session_start();
            
            if (isset($_COOKIE["AFF_MOBILE"])) {
                
                $mobile = $_COOKIE["AFF_MOBILE"];
                
                //Prepare you post parameters
                $postData = array(
                    'authkey' => $smsAuthKey,
                    'mobile' => $mobile,
                    'retrytype' => 'text'
                );
                //API URL
                $url      = $smsApiUrl."retryotp.php";
                // init the resource
                $ch       = curl_init();
                curl_setopt_array($ch, array(
                    CURLOPT_URL => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_POST => true,
                    CURLOPT_POSTFIELDS => $postData
                    //,CURLOPT_FOLLOWLOCATION => true
                ));
                //Ignore SSL certificate verification
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                //get response
                $output = curl_exec($ch);
                
                $decoded_output = json_decode($output, true);
                
                
                //Print error if any
                if (curl_errno($ch)) {
                    $data['RESULT'] = "FAILED";
					$data['REQUEST'] = "RETRY_OTP";
					$data['RESPONSE'] = "Sorry! Something went wrong.";
					//$data['status'] = curl_error($ch);	
                    echo json_encode($data);
                }
                curl_close($ch);
                
                if ($decoded_output['type'] == "success") {
					
					$data['RESULT'] = "SUCCESS";
					$data['REQUEST'] = "RETRY_OTP";
					$data['RESPONSE'] = "OTP sent successfully.";
                    //$data['status'] = "success";
                    echo json_encode($data);
                    
                } // if($decoded_output['type']=="success")
				else{
					$data['RESULT'] = "FAILED";
					$data['REQUEST'] = "RETRY_OTP";
					$data['RESPONSE'] = "Sorry! Something went wrong.";
					echo json_encode($data);
				}	
                					
                
            } // if(!isset($_COOKIE["AFF_MOBILE"]))
            else {
                $data['status'] = 406;
                echo json_encode($data);
            }
            
        } // if($do_action=='RETRY_OTP')    
        
	    elseif ($do_action == 'CUSTOMER_LOGIN') {
            
            $user_mobile     = sanitize_string($_REQUEST["user_mobile"]);
			
			// unset unlogged user cookie
			/* setcookie("AFF_NAME", "", 1, "/");
			setcookie("AFF_EMAIL", "", 1, "/");
			setcookie("AFF_MOBILE", "", 1, "/");
			setcookie("AFF_YOB", "", 1, "/");
			setcookie("AFF_GENDER", "", 1, "/"); */
			//setcookie("AFF_REFERER", "", 1, "/");
			/* setcookie("AFF_STATUS", "", 1, "/"); */
            
            //Your message to send, Add URL encoding here.
            $rndno    = rand(1000, 9999);
            $message  = urlencode("otp number." . $rndno);
            //Define route 
            $route    = "route=4";
            //Prepare you post parameters
            $postData = array(
                'authkey' => $smsAuthKey,
                'mobile' => $user_mobile,
                'sender' => $smsSenderId
            );
            //API URL
            $url      = $smsApiUrl."sendotp.php";
            // init the resource
            $ch       = curl_init();
            curl_setopt_array($ch, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $postData
                //,CURLOPT_FOLLOWLOCATION => true
            ));
            //Ignore SSL certificate verification
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            //get response
            $output         = curl_exec($ch);
            $decoded_output = json_decode($output, true);
            
            
            //Print error if any
            if (curl_errno($ch)) {
				    
                    $data['RESULT'] = "FAILED";
					$data['REQUEST'] = "CUSTOMER_LOGIN";
					$data['RESPONSE'] = "Sorry! Something went wrong.";					
                    //$data['status'] = curl_error($ch);
                    echo json_encode($data);
            }
            //curl_close($ch);
            if ($decoded_output['type'] == "success") {
				
				// Starting session
                //session_start();
                // Storing session data
                //$_COOKIE["AFF_MOBILE"] = $user_mobile;
                setcookie("AFF_MOBILE", $user_mobile, time() + (86400 * 1), "/");
                
				$data['RESULT'] = "SUCCESS";
				$data['REQUEST'] = "CUSTOMER_LOGIN";
				$data['RESPONSE'] = "OTP sent successfully.";	
                echo json_encode($data);
                
            } else{
				    $data['RESULT'] = "FAILED";
					$data['REQUEST'] = "CUSTOMER_LOGIN";
					$data['RESPONSE'] = "Incorrect mobile number. Please type the correct mobile number and try again.";
					echo json_encode($data);
			}
            
            
        } // if($do_action=='CUSTOMER_LOGIN')    
        
        elseif ($do_action == 'ADD_INFLUENCER') {
            
            $user_instagram     = sanitize_string($_REQUEST["user_instagram"]);
			$user_twitter     = sanitize_string($_REQUEST["user_twitter"]);
			$user_facebook     = sanitize_string($_REQUEST["user_facebook"]);
			$user_tiktok     = sanitize_string($_REQUEST["user_tiktok"]);
			$user_whatsapp     = sanitize_string($_REQUEST["user_whatsapp"]);
			
			$additional_info = json_encode(Array("user_instagram"=>$user_instagram,"user_twitter"=>$user_twitter,"user_facebook"=>$user_facebook,"user_tiktok"=>$user_tiktok,"user_whatsapp"=>$user_whatsapp));
			
			if ($user_instagram != '' OR $user_twitter != '' OR $user_facebook != '' OR $user_tiktok != '' OR $user_whatsapp != '') {
                  
				$user_session_id = $_COOKIE['FTCONT_SESSIONID'];	
				$user_id = $_COOKIE['FTCONT_ID'];
				$user_name = htmlentities($_COOKIE['FTCONT_NAME']);	
                
                $otp_new = mt_rand(1000,10000);	

                 $otpResp = add_otp($user_id,$otp_new);
					
                    if($otpResp['RESULT'] == 'SUCCESS'){
						
						$addInfluencerResp = add_influencer($user_name, '', $user_id, $otp_new, $user_session_id, $additional_info); // $name, $email, $mobile, $password, $custom_session_id, $additional_info
						
						if($addInfluencerResp['RESULT'] == 'SUCCESS'){
							
						// set new cookie - when successfull login
							setcookie("FTCONT_SESSIONID", $user_session_id, time() + (86400 * 365), "/");
							setcookie("FTCONT_ID", $user_id, time() + (86400 * 365), "/");
							setcookie("FTCONT_NAME", $user_name, time() + (86400 * 365), "/");
							setcookie("FTCONT_ADDON", 'influencer', time() + (86400 * 365), "/");
							
					$data['RESULT'] = "SUCCESS";
					$data['REQUEST'] = "ADD_INFLUENCER";
					$data['RESPONSE'] = "Great! We will review your request to become influencer.";
                    echo json_encode($data);
                     
							
					} // if(!empty($addInfluencerResp))
						else {
                    $data['RESULT'] = "FAILED";
					$data['REQUEST'] = "ADD_INFLUENCER";
					$data['RESPONSE'] = "Sorry! Something went wrong.";
                    echo json_encode($data);
                }

					} // if(!empty($otpResp))
                    else {
                    $data['RESULT'] = "FAILED";
					$data['REQUEST'] = "ADD_INFLUENCER";
					$data['RESPONSE'] = "Sorry! Something went wrong.";
                    echo json_encode($data);
                }						
                
				}
            
            else {
                    $data['RESULT'] = "FAILED";
					$data['REQUEST'] = "ADD_INFLUENCER";
					$data['RESPONSE'] = "Your didn't provide your social media links. Please provide to become Influencer.";
                    echo json_encode($data);
                }
            
            
        } // if($do_action=='ADD_INFLUENCER')    
        
        elseif ($do_action == 'SUBMIT_FEEDBACK') {
		    
			require_once 'mail_functions.php';
			
            $feedback_name     = sanitize_string($_REQUEST["feedback_name"]);
			$feedback_email     = sanitize_string($_REQUEST["feedback_email"]);
			$feedback_subject     = sanitize_string($_REQUEST["feedback_subject"]);
			$feedback_message     = sanitize_string($_REQUEST["feedback_message"]);

			$from = 'myra@flickstree.video';
			$to = 'support@flickstree.com'; 
			$subject = 'New feedback by '.$feedback_name.' on Contests';
			$body = "<p>Hi,</p><p></p><p>We have received following feedback on Contests:</p><p><strong>Sent By:</strong> $feedback_name</p><p><strong>Email Id:</strong> $feedback_email</p><p><strong>Subject:</strong> $feedback_subject</p><p><strong>Message:</strong> $feedback_message</p><p></p><p>Thanking you.</p>";

			$mail_resp  = send_email($from,$to,$subject,html_entity_decode($body),$type='text/html');
					
					if($mail_resp){
					$data['RESULT'] = "SUCCESS";
					$data['REQUEST'] = "SUBMIT_FEEDBACK";
					$data['RESPONSE'] = "Feedback sent successfully.";
                    echo json_encode($data);
					
					}else{
					$data['RESULT'] = "FAILED";
					$data['REQUEST'] = "SUBMIT_FEEDBACK";
					$data['RESPONSE'] = "Feedback couldn't sent";
                    echo json_encode($data);
					
					}			
			
	    } // elseif ($do_action == 'SUBMIT_FEEDBACK')		
	    
		elseif ($do_action == 'PROD_SEND_OTP') {
            
			// Starting session
            //session_start();
			
            $user_name      = sanitize_string($_REQUEST["user_name"]);
			$user_email     = sanitize_string($_REQUEST["user_email"]);
            $user_mobile     = sanitize_string($_REQUEST["user_mobile"]);
            $user_yob       = sanitize_string($_REQUEST["user_yob"]);
            $user_gender    = sanitize_string($_REQUEST["user_gender"]);
         
            
            //Your message to send, Add URL encoding here.
            $rndno    = rand(1000, 9999);
            $message  = urlencode("otp number." . $rndno);
            //Define route 
            $route    = "route=4";
            //Prepare you post parameters
            $postData = array(
                'authkey' => $smsAuthKey,
                'mobile' => $user_mobile,
                'sender' => $smsSenderId
            );
            //API URL
            $url      = $smsApiUrl."sendotp.php";
            // init the resource
            $ch       = curl_init();
            curl_setopt_array($ch, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $postData
                //,CURLOPT_FOLLOWLOCATION => true
            ));
            //Ignore SSL certificate verification
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            //get response
            $output         = curl_exec($ch);
            $decoded_output = json_decode($output, true);
            
            
            //Print error if any
            if (curl_errno($ch)) {
				    
					$data['RESULT'] = "FAILED";
					$data['REQUEST'] = "PROD_SEND_OTP";
				    $data['RESPONSE'] = "Sorry! Something went wrong.";
                    //$data['status'] = curl_error($ch);
                    echo json_encode($data);
            }
            //curl_close($ch);
            if ($decoded_output['type'] == "success") {
                
                // set new cookie - when successfull login
				setcookie("AFF_NAME", $user_name, time() + (86400 * 30), "/");
				setcookie("AFF_EMAIL", $user_email, time() + (86400 * 30), "/");
				setcookie("AFF_MOBILE", $user_mobile, time() + (86400 * 30), "/");
				setcookie("AFF_YOB", $user_yob, time() + (86400 * 30), "/");
				setcookie("AFF_GENDER", $user_gender, time() + (86400 * 30), "/");
				
				// Storing session data
				/* $_SESSION["AFF_NAME"] = $user_name;
				$_SESSION["AFF_EMAIL"] = $user_email;
				$_SESSION["AFF_MOBILE"] = $user_mobile;
				$_SESSION["AFF_YOB"] = $user_yob;
				$_SESSION["AFF_GENDER"] = $user_gender; */
				
				$data['RESULT'] = "SUCCESS";
				$data['REQUEST'] = "PROD_SEND_OTP";
				$data['RESPONSE'] = "OTP sent successfully.";
                echo json_encode($data);
                
            }
			else{
				    $data['RESULT'] = "FAILED";
					$data['REQUEST'] = "PROD_SEND_OTP";
					$data['RESPONSE'] = "Incorrect mobile number. Please type the correct mobile number and try again.";
					echo json_encode($data);
			}
            
            
        } // if($do_action=='PROD_SEND_OTP')    
        
        elseif ($do_action == 'PROD_VERIFY_OTP') {
            
            $otp_number = sanitize_string($_REQUEST["otp_number"]);
			
			// Starting session
            //session_start();
                
            if (isset($_COOKIE["AFF_MOBILE"])) {
                
                $mobile = $_COOKIE["AFF_MOBILE"];
                
                //Prepare you post parameters
                $postData = array(
                    'authkey' => $smsAuthKey,
                    'mobile' => $mobile,
                    'otp' => $otp_number
                );
                //API URL
                $url      = $smsApiUrl."verifyRequestOTP.php";
                // init the resource
                $ch       = curl_init();
                curl_setopt_array($ch, array(
                    CURLOPT_URL => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_POST => true,
                    CURLOPT_POSTFIELDS => $postData
                    //,CURLOPT_FOLLOWLOCATION => true
                ));
                //Ignore SSL certificate verification
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                //get response
                $output = curl_exec($ch);
                
                $decoded_output = json_decode($output, true);
                
                
                //Print error if any
                if (curl_errno($ch)) {
					$data['RESULT'] = "FAILED";
					$data['REQUEST'] = "PROD_VERIFY_OTP";
				    $data['RESPONSE'] = "Sorry! Something went wrong.";
					//$data['status'] = curl_error($ch);
                    echo json_encode($data);
                }
                curl_close($ch);
                
                if ($decoded_output['type'] == "success") {
                    
					$otpResp = add_otp($mobile,$otp_number);
					
                    if($otpResp['RESULT'] == 'SUCCESS'){
						
						
						$mobile = $_COOKIE["AFF_MOBILE"];
						
						if(isset($_COOKIE['AFF_REFERER'])){
						$referer = $_COOKIE['AFF_REFERER'];
						}else{
						$referer = "";	
						}
						
						$password = $otp_number;
						
						
							
						$name = $_COOKIE['AFF_NAME'];
						$email = $_COOKIE['AFF_EMAIL'];
						$yob = $_COOKIE['AFF_YOB'];
						$gender = $_COOKIE['AFF_GENDER'];
						
						$addUserResp = add_customer($name, $email, $mobile, $yob, $gender, $referer, $password);
						
						if($addUserResp['RESULT'] == 'SUCCESS'){
							
							$session_id = $addUserResp['RESPONSE']['session_id'];
							$id = $addUserResp['RESPONSE']['id'];
							$name = $addUserResp['RESPONSE']['name'];
							
							// unset unlogged user cookie
							setcookie("AFF_NAME", "", 1, "/");
							setcookie("AFF_EMAIL", "", 1, "/");
							setcookie("AFF_MOBILE", "", 1, "/");
							setcookie("AFF_YOB", "", 1, "/");
							setcookie("AFF_GENDER", "", 1, "/");
							setcookie("AFF_REFERER", "", 1, "/");
							
							
							// Removing session data
							/* if(isset($_SESSION["AFF_NAME"])){
							unset($_SESSION["AFF_NAME"]);
							}
							if(isset($_SESSION["AFF_EMAIL"])){
							unset($_SESSION["AFF_EMAIL"]);
							}
							if(isset($_SESSION["AFF_MOBILE"])){
							unset($_SESSION["AFF_MOBILE"]);
							}
							if(isset($_SESSION["AFF_YOB"])){
							unset($_SESSION["AFF_YOB"]);
							}
							if(isset($_SESSION["AFF_GENDER"])){
							unset($_SESSION["AFF_GENDER"]);
							}
							if(isset($_SESSION["AFF_REFERER"])){
							unset($_SESSION["AFF_REFERER"]);
							} */
							
							// set new cookie - when successfull login
							setcookie("FTAFF_SESSIONID", $session_id, time() + (86400 * 30), "/");
							setcookie("FTAFF_ID", $id, time() + (86400 * 30), "/");
							setcookie("FTAFF_NAME", $name, time() + (86400 * 30), "/");
							setcookie("FTAFF_MOBILE", $mobile, time() + (86400 * 30), "/");
							
							// Storing session data
							/* $_SESSION["FTAFF_SESSIONID"] = $session_id;
							$_SESSION["FTAFF_ID"] = $id;
							$_SESSION["FTAFF_NAME"] = $name;
							$_SESSION["FTAFF_MOBILE"] = $mobile; */
							
							$data['RESULT'] = "SUCCESS";
							$data['REQUEST'] = "PROD_VERIFY_OTP";
				            $data['RESPONSE'] = "Your mobile number is added successfully.";
							//$data['status'] = "success";
                            echo json_encode($data);
							
						} // if(!empty($addUserResp))
						else{
							$loginUserResp = customer_login($mobile,$password,'influencer');
							
							if($loginUserResp['RESULT'] == 'SUCCESS'){
							
							$session_id = $loginUserResp['RESPONSE']['session_id'];
							$id = $loginUserResp['RESPONSE']['id'];
							$name = $loginUserResp['RESPONSE']['name'];
							
							// unset unlogged user cookie
							setcookie("AFF_NAME", "", 1, "/");
							setcookie("AFF_EMAIL", "", 1, "/");
							setcookie("AFF_MOBILE", "", 1, "/");
							setcookie("AFF_YOB", "", 1, "/");
							setcookie("AFF_GENDER", "", 1, "/");
							setcookie("AFF_REFERER", "", 1, "/");
							
							// Removing session data
							/* if(isset($_SESSION["AFF_NAME"])){
							unset($_SESSION["AFF_NAME"]);
							}
							if(isset($_SESSION["AFF_EMAIL"])){
							unset($_SESSION["AFF_EMAIL"]);
							}
							if(isset($_SESSION["AFF_MOBILE"])){
							unset($_SESSION["AFF_MOBILE"]);
							}
							if(isset($_SESSION["AFF_YOB"])){
							unset($_SESSION["AFF_YOB"]);
							}
							if(isset($_SESSION["AFF_GENDER"])){
							unset($_SESSION["AFF_GENDER"]);
							}
							if(isset($_SESSION["AFF_REFERER"])){
							unset($_SESSION["AFF_REFERER"]);
							} */
							
							// set new cookie - when successfull login
							setcookie("FTAFF_SESSIONID", $session_id, time() + (86400 * 30), "/");
							setcookie("FTAFF_ID", $id, time() + (86400 * 30), "/");
							setcookie("FTAFF_NAME", $name, time() + (86400 * 30), "/");
							setcookie("FTAFF_MOBILE", $mobile, time() + (86400 * 30), "/");
							
							// Storing session data
							/* $_SESSION["FTAFF_SESSIONID"] = $session_id;
							$_SESSION["FTAFF_ID"] = $id;
							$_SESSION["FTAFF_NAME"] = $name;
							$_SESSION["FTAFF_MOBILE"] = $mobile; */
							
							$data['RESULT'] = "SUCCESS";
							$data['REQUEST'] = "PROD_VERIFY_OTP";
				            $data['RESPONSE'] = "You logged in successfully.";
							//$data['status'] = "success";
                            echo json_encode($data);
							
						} // if(!empty($loginUserResp))
						else{
					$data['RESULT'] = "FAILED";
					$data['REQUEST'] = "PROD_VERIFY_OTP";
				    $data['RESPONSE'] = "This mobile number doesn't exist in our system. Please log in with correct mobile number.";
					echo json_encode($data);
						}
							
						}
						
					} // if(!empty($otpResp))
						else{
					$data['RESULT'] = "FAILED";
					$data['REQUEST'] = "PROD_VERIFY_OTP";
				    $data['RESPONSE'] = "Sorry! OTP is not added in our system.";
					echo json_encode($data);
						}
                    
                    
                } // if($decoded_output['type']=="success")
                else {
                    $data['RESULT'] = "FAILED";
					$data['REQUEST'] = "PROD_VERIFY_OTP";
					$data['RESPONSE'] = "Incorrect OTP. Please type the correct OTP and submit again.";
                    echo json_encode($data);
                }
                
            } // if(!isset($_COOKIE["AFF_MOBILE"]))
            else {
                $data['status'] = 406;
                echo json_encode($data);
            }
            
        } // if($do_action=='PROD_VERIFY_OTP')
            
		elseif ($do_action == 'PROCEED_TO_PAY') {
            
			// Starting session
            //session_start();
			
			
            $user_name      = sanitize_string($_REQUEST["user_name"]);
			$user_mobile     = sanitize_string($_REQUEST["user_mobile"]);
			$user_email     = sanitize_string($_REQUEST["user_email"]);
            $user_address       = sanitize_string($_REQUEST["user_address"]);
			$product_qty    = sanitize_string($_REQUEST["product_qty"]);
			$partner    = sanitize_string($_REQUEST["partner"]);
            $product_id    = sanitize_string($_REQUEST["product_id"]);
			$product_sales_price    = sanitize_string($_REQUEST["product_sales_price"]);
			$promo_code    = sanitize_string($_REQUEST["promo_code"]);
			
			$user_id = $_COOKIE["FTAFF_ID"];
			$session_id = $_COOKIE["FTAFF_SESSIONID"];
         
            $updateUserResp = update_customer($user_name, $user_email, $user_mobile, '', '', $user_address, $user_id, $session_id);
					
					if($updateUserResp['RESULT'] == 'SUCCESS'){
				
			    // set new cookie - when successfull login
				setcookie("AFF_PARTNER", $partner, time() + (86400 * 30), "/");
				setcookie("AFF_PRODUCT_ID", $product_id, time() + (86400 * 30), "/");
				setcookie("AFF_PRODUCT_QTY", $product_qty, time() + (86400 * 30), "/");
				setcookie("AFF_PRODUCT_PRICE", $product_sales_price, time() + (86400 * 30), "/");
				setcookie("AFF_PROMO_CODE", $promo_code, time() + (86400 * 30), "/");
				
				// Storing session data
				/* $_SESSION["AFF_PARTNER"] = $partner;
				$_SESSION["AFF_PRODUCT_ID"] = $product_id;
				$_SESSION["AFF_PRODUCT_QTY"] = $product_qty;
				$_SESSION["AFF_PRODUCT_PRICE"] = $product_sales_price;
				$_SESSION["AFF_PROMO_CODE"] = $promo_code; */
				
				
				$data['RESULT'] = "SUCCESS";
				$data['REQUEST'] = "PROCEED_TO_PAY";
				$data['RESPONSE'] = "Customer details are updated.";
				echo json_encode($data);
				
			}
			else{
				    $data['RESULT'] = "FAILED";
					$data['REQUEST'] = "PROCEED_TO_PAY";
					$data['RESPONSE'] = "Sorry! Something went wrong.";
					echo json_encode($data);
			}
            
            
        } // if($do_action=='PROCEED_TO_PAY') 

        elseif ($do_action == 'USER_LOG') {
            
			$user_id      = sanitize_string($_REQUEST["user_id"]);
			$product_id     = sanitize_string($_REQUEST["product_id"]);
			$partner     = sanitize_string($_REQUEST["partner"]);
            $type       = sanitize_string($_REQUEST["type"]);
			$status    = sanitize_string($_REQUEST["status"]);
			$submit_datetime = date('Y-m-d H:i:s');
			
			$userLogResp = create_user_log($user_id,$product_id,$partner,$type,$status,$submit_datetime);
					
					if($userLogResp){
				
				$data['RESULT'] = "SUCCESS";
				$data['REQUEST'] = "USER_LOG";
				$data['RESPONSE'] = "Log updated";
				echo json_encode($data);
				
			}
			else{
				    $data['RESULT'] = "FAILED";
					$data['REQUEST'] = "USER_LOG";
					$data['RESPONSE'] = "Sorry! Something went wrong.";
					echo json_encode($data);
			}
            
            
        } // if($do_action=='USER_LOG')

        elseif ($do_action == 'PUBLISHER_ONBOARDING') {
            if(!empty($_REQUEST["legal_entry_name"]) && !empty($_REQUEST["legal_entry_type"]) && !empty($_REQUEST["cin_number"]) && !empty($_REQUEST["affiliate_name"]) && !empty($_REQUEST["first_name"]) && !empty($_REQUEST["last_name"]) && !empty($_REQUEST["email"]) && !empty($_REQUEST["mobile"]) && !empty($_REQUEST["city"]) && !empty($_REQUEST["state"]) && !empty($_REQUEST["pin"]) && !empty($_REQUEST["size_of_business"]) && !empty($_REQUEST["website_title"]) && !empty($_REQUEST["website_url"]) && !empty($_REQUEST["website_status"]) && !empty($_REQUEST["unique_monthly_visitors"]) && !empty($_REQUEST["primary_sector"]) && !empty($_REQUEST["secondary_sector"]) && !empty($_REQUEST["account_name"]) && !empty($_REQUEST["bank_name"]) && !empty($_REQUEST["bank_city"]) && !empty($_REQUEST["branch_location"]) && !empty($_REQUEST["micr_number"]) && !empty($_REQUEST["ifsc_code"]) && !empty($_REQUEST["beneficiary_type"]) && !empty($_REQUEST["beneficiary_nationality"]) && !empty($_REQUEST["account_card_type"]) && !empty($_REQUEST["account_card_number"])) {
			$legal_entry_name      = sanitize_string($_REQUEST["legal_entry_name"]);
            $legal_entry_type      = sanitize_string($_REQUEST["legal_entry_type"]);
            $cin_number      = sanitize_string($_REQUEST["cin_number"]);
           
            $affiliate_name      = sanitize_string($_REQUEST["affiliate_name"]);
            $first_name      = sanitize_string($_REQUEST["first_name"]);
            $last_name      = sanitize_string($_REQUEST["last_name"]);
            $email = sanitize_string($_REQUEST["email"]);
            $mobile      = sanitize_string($_REQUEST["mobile"]);
            $office_landline      = sanitize_string($_REQUEST["office_landline"]);
                       
            $building_name      = sanitize_string($_REQUEST["building_name"]);
            $address1 = sanitize_string($_REQUEST["address1"]);
            $address2      = sanitize_string($_REQUEST["address2"]);
            $city = sanitize_string($_REQUEST["city"]);
            $state = sanitize_string($_REQUEST["state"]);
            $pin      = sanitize_string($_REQUEST["pin"]);
            $country = sanitize_string($_REQUEST["country"]);
                        
            $size_of_business      = sanitize_string($_REQUEST["size_of_business"]);
            $website_title = sanitize_string($_REQUEST["website_title"]);
            $website_url      = sanitize_string($_REQUEST["website_url"]);
            $website_status = sanitize_string($_REQUEST["website_status"]);
            $unique_monthly_visitors = sanitize_string($_REQUEST["unique_monthly_visitors"]);
            
            $primary_sector      = sanitize_string($_REQUEST["primary_sector"]);
            $secondary_sector = sanitize_string($_REQUEST["secondary_sector"]);
            
            $account_name      = sanitize_string($_REQUEST["account_name"]);
            $bank_name = sanitize_string($_REQUEST["bank_name"]);
            $bank_city      = sanitize_string($_REQUEST["bank_city"]);
            $branch_location = sanitize_string($_REQUEST["branch_location"]);
            $micr_number = sanitize_string($_REQUEST["micr_number"]);
            $ifsc_code = sanitize_string($_REQUEST["ifsc_code"]);
            $beneficiary_type = sanitize_string($_REQUEST["beneficiary_type"]);
            $beneficiary_nationality      = sanitize_string($_REQUEST["beneficiary_nationality"]);
            $account_card_type = sanitize_string($_REQUEST["account_card_type"]);
            $account_card_number      = sanitize_string($_REQUEST["account_card_number"]);
            
            $submit_datetime = date('Y-m-d H:i:s');
            
			$response = add_publisher_onboarding($legal_entry_name,$legal_entry_type,$cin_number,$affiliate_name,$first_name,$last_name,$email,$mobile,$office_landline,$building_name,$address1,$address2,$city,$state,$pin,$country,$size_of_business,$website_title,$website_url,$website_status,$unique_monthly_visitors,$primary_sector,$secondary_sector,$account_name,$bank_name,$bank_city,$branch_location,$micr_number,$ifsc_code,$beneficiary_type,$beneficiary_nationality,$account_card_type,$account_card_number,$submit_datetime);
           
            if ($response) {
                
               $data['RESULT'] = "SUCCESS";
               $data['REQUEST'] = "PUBLISHER_ONBOARDING";
			   $data['RESPONSE'] = "Details are added";
			   echo json_encode($data);
                
            }else{
                $data['RESULT'] = "FAILED";
                $data['REQUEST'] = "PUBLISHER_ONBOARDING";
				$data['RESPONSE'] = "Details are not added";
				echo json_encode($data);
            }
			
			}else{
			
				$data['RESULT'] = "FAILED";
                $data['REQUEST'] = "PUBLISHER_ONBOARDING";
				$data['RESPONSE'] = "Please fill the mandatory fields";
				echo json_encode($data);
			
			}
            
            
        } // if($do_action=='PUBLISHER_ONBOARDING')			
        
        
		
		else {
                    $data['RESULT'] = "FAILED";
					$data['REQUEST'] = "INVALID REQUEST";
					$data['RESPONSE'] = "Invalid request.";
                    echo json_encode($data);
        }
        
        
    } // if(!empty($_REQUEST["do_action"]))
    else {
                    $data['RESULT'] = "FAILED";
					$data['REQUEST'] = "INVALID REQUEST";
					$data['RESPONSE'] = "Invalid request.";
                    echo json_encode($data);
    }
    
} else {
                    $data['RESULT'] = "FAILED";
					$data['REQUEST'] = "INVALID REQUEST";
					$data['RESPONSE'] = "Invalid method.";
                    echo json_encode($data);
}

?>