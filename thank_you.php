<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);

include "functions.php"; // Get Configuration
$type = sanitize_string($_REQUEST['type']);
$partner = sanitize_string($_REQUEST['partner']);
$product_id = sanitize_string($_REQUEST['product_id']);
// print_r($product_id);exit;
// Starting session
//session_start();

$razorpay_payment_id = sanitize_string($_REQUEST['razorpay_payment_id']);
$razorpay_order_id = sanitize_string($_REQUEST['razorpay_order_id']);
$razorpay_signature = sanitize_string($_REQUEST['razorpay_signature']);



if (isset($_COOKIE["FTAFF_ID"], $_COOKIE["FTAFF_MOBILE"])) {
    $session_id = $_COOKIE["FTAFF_SESSIONID"];
// $partner = $_COOKIE["AFF_PARTNER"];
    $user_mobile = $_COOKIE["FTAFF_MOBILE"];


# get_customer_profile
    $dataUserProfile = get_customer_profile($_COOKIE["FTAFF_MOBILE"], $session_id);
    if ($dataUserProfile['RESULT'] == "SUCCESS") {
        $userProfileDetails = $dataUserProfile['RESPONSE'];
        $user_name = $userProfileDetails['name'];
        $user_email = $userProfileDetails['email'];
    } // get_customer_profile


    if ($resultValidatePurchaseOrder = @file_get_contents("$sapiUrl?VERSION=$sapiVersion&ENCODING=$sapiEncoding&METHOD=VALIDATE_PURCHASE_ORDER&razorpay_payment_id=$razorpay_payment_id&razorpay_order_id=$razorpay_order_id&razorpay_signature=$razorpay_signature")) {

        $dataValidatePurchaseOrder = json_decode($resultValidatePurchaseOrder, true);

        if ($dataValidatePurchaseOrder['RESULT'] == "SUCCESS") {
            $validatePurchaseOrderDetails = $dataValidatePurchaseOrder['RESPONSE'];

            foreach ($validatePurchaseOrderDetails as $pubProdkdata => $pubProdvdata) {
                $product_id = $pubProdvdata['details']['product_id'];
                $product_title = $pubProdvdata['details']['product_title'];
                $product_code = $pubProdvdata['details']['product_code'];
                $product_tag = $pubProdvdata['details']['product_tag'];

                $payid = $pubProdvdata['payid'];

                $ty_title = $pubProdvdata['details']['thank_you']['title'];
                $ty_description = $pubProdvdata['details']['thank_you']['description'];
                $ty_customer_care = $pubProdvdata['details']['thank_you']['customer_care'];
                $ty_button_name = $pubProdvdata['details']['thank_you']['button_name'];
                $ty_button_link = $pubProdvdata['details']['thank_you']['button_link'];
                $ty_additional_button_name = $pubProdvdata['details']['thank_you']['additional_button_name'];
                $ty_additional_button_link = $pubProdvdata['details']['thank_you']['additional_button_link'];
                $ty_tracking_url = $pubProdvdata['details']['thank_you']['tracking_url'];
                $product_category = $pubProdvdata['details']['product_category'];
                $affise_tracking_url = $pubProdvdata['details']['affise_tracking_url'];
                $currency = $pubProdvdata['pricing']['pricing']['in']['currency'];
                $product_sales_price = $pubProdvdata['pricing']['pricing']['in']['product_sales_price'];

                if (!empty($pubProdvdata['details']['affise_secure_postback_code'])) {
                    $affise_secure_postback_code = $pubProdvdata['details']['affise_secure_postback_code'];
                } else {
                    $affise_secure_postback_code = "";
                }

                $shareable_link = 'https://www.shop369.org/product.php?partner=' . $partner . '&type=buyer&product_id=' . $product_id . '&referer=' . $payid;
                $facebook_share = 'https://www.facebook.com/sharer/sharer.php?u=' . urlencode($shareable_link);
                $twitter_share = 'https://twitter.com/share?url=' . urlencode($shareable_link) . '&text=' . urlencode($product_title . ' - Buy this Now! ');
                $whatsapp_share = 'whatsapp://send?text=' . urlencode($product_title . ' - Buy this Now! ') . ' ' . urlencode($shareable_link);
                $copy_link = $shareable_link;
            }


# USER_LOG
            if ($result_log = @file_get_contents("https://api.flickstree.com/video_commerce/user/process_request.php?do_action=USER_LOG&user_id=$user_mobile&product_id=$product_id&partner=$partner&type=buyer&status=ORDER_COMPLETED")) {
                $log_array = json_decode($result_log, true);

                if ($log_array['RESULT'] == "SUCCESS") {
                    //echo $log_array['RESPONSE'];
                } else {
                    //echo "not done."; 
                }
            } // USER_LOG       
//*****Send Confirmation to Seller*****//

            if ($ty_tracking_url != "" && count($product_tag) > 0) {
//The data you want to send via POST
                $postData = [
                    'name' => $user_name,
                    'email' => $user_email,
                    'phone_number' => $user_mobile,
                    'code' => $product_code,
                    'tag' => $product_tag[0],
                    'product_name' => $product_title,
                    'product_price' => $product_sales_price,
                    'flickstree_token' => 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9'
                ];

//url-ify the data for the POST
                $postfields_string = http_build_query($postData);

//open connection
                $ch = curl_init();

//set the url, number of POST vars, POST data
                curl_setopt($ch, CURLOPT_URL, $ty_tracking_url);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields_string);

//So that curl_exec returns the contents of the cURL; rather than echoing it
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

//execute post
                $output = curl_exec($ch);
                $result = json_decode($output, true);
                if ($result['status'] == "error") {
                    $trackingUrlMsg = "Please contact us with above order id.";
                } else {
                    $trackingUrlMsg = "";
                }
//echo $result;
            } // if($ty_tracking_url!="" && count($product_tag)>0)
//*****Send Confirmation to Seller*****//
//s2s- affise postback
            if (isset($_COOKIE['affise_postdata'])) {
                $affise_postdata = $_COOKIE["affise_postdata"];

                $affise_postback_url = "http://offers.flickstree.affise.com/postback";

                # if secure postback set, then check
                if ($affise_secure_postback_code != "") {
                    $affise_postdata = $affise_postdata . "&status=1&sum=" . $product_sales_price . "&secure=" . $affise_secure_postback_code;
                } else {
                    $affise_postdata = $affise_postdata . "&status=1&sum=" . $product_sales_price;
                }

                // init the resource
                $curl_affise = curl_init();
                curl_setopt_array($curl_affise, array(
                    CURLOPT_URL => $affise_postback_url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_POST => true,
                    CURLOPT_POSTFIELDS => $affise_postdata
                ));
                curl_setopt($curl_affise, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($curl_affise, CURLOPT_SSL_VERIFYPEER, 0);
                $curl_affise_response = curl_exec($curl_affise);
                $affise_result = json_decode($curl_affise_response, true);
                curl_close($curl_affise);

                setcookie("affise_postdata", "", 1, "/");
            } //s2s - affise postback
        } // if($dataPurchaseOrder['RESULT']=="SUCCESS")    
    } // VALIDATE_PURCHASE_ORDER
    // unset unlogged user cookie
    setcookie("AFF_PRODUCT_ID", "", 1, "/");
    setcookie("AFF_PRODUCT_QTY", "", 1, "/");
    setcookie("AFF_PRODUCT_PRICE", "", 1, "/");

    // Removing session data
    /* if(isset($_SESSION["AFF_PRODUCT_ID"])){
      unset($_SESSION["AFF_PRODUCT_ID"]);
      }
      if(isset($_SESSION["AFF_PRODUCT_QTY"])){
      unset($_SESSION["AFF_PRODUCT_QTY"]);
      }
      if(isset($_SESSION["AFF_PRODUCT_PRICE"])){
      unset($_SESSION["AFF_PRODUCT_PRICE"]);
      } */
    $curl = curl_init($sapiUrl); //Get product list

    $curl_post_data = array(
        "VERSION" => $sapiVersion,
        "ENCODING" => $sapiEncoding,
        "access_code" => $sapiAccessCode,
        "METHOD" => "GET_PUBLISHER_PRODUCTS",
        "partner_id" => $partner
    );

    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $curl_post_data);
    $curl_response = curl_exec($curl);
    curl_close($curl);
    $result_products = json_decode($curl_response, true);

    $response_products = $result_products['RESPONSE'];
// print_r($response);exit;
// print_r($product_type);exit;
}




$urlPubList = $sapiUrl . "?VERSION=" . $sapiVersion . "&ENCODING=" . $sapiEncoding . "&METHOD=ADMIN_GET_PUBLISHER&access_code=" . $sapiAccessCode . "&partner_id=" . $partner;

$dataPubList = json_decode(file_get_contents($urlPubList), true);

if ($dataPubList['RESULT'] == 'SUCCESS') {
    $responsePubList = $dataPubList['RESPONSE'];
    $commission_percentage = $responsePubList['commission_percentage'];
    $influencer_commission_percentage = $responsePubList['influencer_commission_percentage'];
    if (isset($responsePubList['flickstree_commission_percentage'])) {
        $flickstree_commission_percentage = $responsePubList['flickstree_commission_percentage'];
    } else {
        $flickstree_commission_percentage = 20;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">
        <title>Thank You</title>
        <link rel="shortcut icon" type="image/x-icon" href="images/logo/favicon.ico">
        <!-- Vendor CSS -->
        <link href="js/vendor/bootstrap/bootstrap.min.css" rel="stylesheet">
        <link href="css/product_style_light.min.css" rel="stylesheet">
        <link href="fonts/icomoon/icomoon.css" rel="stylesheet">
        <link href="css/custom.css" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css?family=Montserrat:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i,800,800i" rel="stylesheet">
        <!-- Global site tag (gtag.js) - Google Analytics -->

        <script async src="https://www.googletagmanager.com/gtag/js?id=UA-55810795-26"></script>

        <script>

            window.dataLayer = window.dataLayer || [];

            function gtag() {
                dataLayer.push(arguments);
            }

            gtag('js', new Date());



            gtag('config', 'UA-55810795-26');



        </script>

        <style>
            img#tile_img {
                object-fit: fill;
                flex-shrink: 0;
                height: 230px;
            }
            .prd-img-area {
                display: flex;
                justify-content: center;
                align-items: center;
                overflow: hidden!important;
            }
            .prd-horizontal-simple .prd-img-area {
                flex: 0 0 38%;
            }
            /*rating*/
            .rating {
                background:url("images/star-grey-icon.png") no-repeat;
                width: 31px;
                height: 31px;
                display: inline-block;
                cursor: pointer;
            }
            .rated {
                background:url("images/star-yellow-icon.png") no-repeat !important;
                width: 31px;
                height: 31px;
                display: inline-block;
                cursor: pointer;
            }
            .hdr.minicart-icon-style-4 .minicart-total, .hdr.minicart-icon-style-4 .minicart-title {
                display: block !important;
            }
            .hdr.minicart-icon-style-4 .minicart-link > .icon {
                float: left !important;
            }
            .minicart-link > .icon {
                font-size: 30px !important;
            }
            .minicart-title {
                padding-left: 37px !important;
            }
            @media screen and (max-width: 991px){
                .footer-block {
                    margin: 0 -15px;
                    margin-bottom: 47px !important;
                } 
            }
            @media screen and (max-width: 991px){
                .hdr-mobile-style2 .hdr-mobile.is-sticky {
                    position: fixed;
                    top: 0px;
                }
            }
            .sticky-holder {
                top: 0px;
            }
            .btn.btn--add-to-cart:only-child {
                width: 100%;
            }
            .btn:not(:disabled):not(.disabled) {
                cursor: pointer;
            }
            .btn.btn--add-to-cart {
                font-size: 14px;
                height: 54px;
                line-height: 22px;
                padding: 16px 20px;
            }
            .btn, .btn:active, .btn:active:focus, .btn:visited, .btn:focus {
                box-shadow: none;
                color: #fff;
                outline: none;
                text-decoration: none;
            }
            .btn {
                text-transform: uppercase;
                border-radius: 0;
                border: 0;
                font-weight: 500;
                font-family: "Montserrat", sans-serif;
                transition: all 0.2s ease;
            }
        </style>
    </head>

    <body class="is-dropdn-click">

        <header class="hdr global_width hdr-style-3 hdr_sticky minicart-icon-style-4 hdr-mobile-style2" style="border: 1px solid #f7f7f7;">

            <!-- Mobile Menu -->
            <div class="mobilemenu js-push-mbmenu" id="clmenu">
                <div class="mobilemenu-content">
                    <div class="mobilemenu-close mobilemenu-toggle" id="closemenu">CLOSE</div>
                    <div class="mobilemenu-scroll">
                        <div class="mobilemenu-search"></div>
                        <div class="nav-wrapper show-menu">
                            <div class="nav-toggle"><span class="nav-back"><i class="icon-arrow-left"></i></span> <span class="nav-title"></span></div>
                            <ul class="nav nav-level-1">
                                <li><a >Category</a><span class="arrow"></span>
                                    <ul class="nav-level-2">
                                        <?php
                                        $array = [];
                                        foreach ($response_products as $index) {
                                            $product_cat = $index['details']['product_category'];
                                            $array[] = $product_cat;
                                        }
                                        $all_categories = array_values(array_unique($array));
                                        for ($j = 0; $j < count($all_categories); $j++) {
                                            $categories = $all_categories[$j];
                                            if ($categories != 'null' && $categories != '') {
                                            ?>

                                            <li>
                                            <?php echo '<a href="products.php?partner=' . $partner . '&type=' . $type . '&cat=' . $categories . '">' . $categories . '</a>'; ?>'
                                            </li>
                                            <input type="hidden" id="hdn_category">
                                            <?php
                                            }
                                        }
                                        ?>


                                    </ul>
                                </li>
                                        <?php
                                        if ($session_id) {
                                            echo '<li><a href="profile.php?partner=' . $partner . '&type=' . $type . '">Profile</li>';
                                        }
                                        ?>
                            </ul>
                        </div>
                        <div class="mobilemenu-bottom">
                            <div class="mobilemenu-currency"></div>
                            <div class="mobilemenu-language"></div>
                            <div class="mobilemenu-settings"></div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Mobile Menu -->
            <div class="hdr-mobile show-mobile">
                <div class="hdr-content">
                    <div class="container">
                        <!-- Menu Toggle -->
                        <div class="menu-toggle"><a href="#" class="mobilemenu-toggle"><i class="icon icon-menu"></i></a></div>
                        <!-- /Menu Toggle -->
                                <?php echo'<div class="logo-holder"><a href="products.php?partner=' . $partner . '&type=' . $type . '" class="logo"><img src="images/logo/shop369_logo.png" srcset="images/logo/shop369_logo.png" alt=""></a></div>'; ?>
                        <div class="col-auto hdr-content-right">
                            <div class="search-holder">
                                <form action="#" class="search" style="padding: 0px 0px 0px 0px !important;border: 1px solid #00000014;margin-top: 10px;" id="mobile_search_form">
                                    <a href="javascript:void(0);" onclick="mobile_partnerwise_search();"><p class="search-button" style="width: 30px;line-height: 35px;background-color: #2b4f68;">
                                            <i class="icon-search2" style="font-size: 21px;color: #fff;"></i></p></a>
                                    <input type="text" class="search-input" placeholder="search" style="width: 100px !important;font-size: 15px;height: 35px;margin-left: 8px" id="mobile_search">
                                </form>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="hdr-desktop hide-mobile">

                <div class="hdr-content hide-mobile">
                    <div class="container">
                        <div class="row">
                            <div class="col logo-holder">
                        <?php echo'<div class="menu-toggle hide-mobile"></div><a href="products.php?partner=' . $partner . '&type=' . $type . '" class="logo"><img src="images/logo/shop369_logo.png" srcset="images/logo/shop369_logo.png" alt=""></a>
                            </div>'; ?>
                                <div class="col search-holder nav-holder" style="max-width: 61%;height: 28px;">
                                    <!-- Header Search -->
                                    <form action="#" class="search" style="border: 1px solid #00000026;padding: 0px 0 0px 0px;height: 51px;" id="desk_search_form">
    <!--                                        <p class="search-button"><i class="icon-search2"></i>
                                            </p> 
                                            <input type="text" class="search-input" placeholder="search keyword" id="search_title"  style="font-size: 17px;margin-left: 11px;"  onkeyup="partnerwise_serach();" onkeypress="return enter_key_search(event)">
                                        -->
                                        <a href="javascript:void(0);" onclick="desktop_partnerwise_search();"><p class="search-button" style="background-color: #2b4f68;"><i class="icon-search2" style="color: #fff;"></i>
                                            </p> </a>
                                        <input type="text" class="search-input" placeholder="search keyword" id="search_title"  style="font-size: 17px;margin-left: 11px;"  onkeypress="return enter_key_search(event)">
                                    </form>
                                    <!-- /Header Search -->
                                </div>
                                <div class="col-auto minicart-holder">
                                    <?php
                                    if ($session_id) {
                                        ?>
                                        <div class="dropdn dropdn_account only-icon">
                                    <?php echo'<a href="javascript:void(0);" class="minicart-link" onclick="profile()" style="text-decoration:none;"><i class="icon icon-person" style="text-align: center;"></i>  <span class="minicart-title" style="text-align: center;"><b>' . strtoupper($user_name) . '</b></span> <span class="minicart-title" style="text-align: center;">' . $user_mobile . '</span></a>'; ?>

                                        </div>
                                    <?php
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="sticky-holder compensate-for-scrollbar">
                    <div class="container">
                        <div class="row"></a>
                        <?php echo'<div class="col-auto logo-holder-s"><a href="products.php?partner=' . $partner . '&type=' . $type . '" class="logo"><img src="images/logo/shop369_logo.png" srcset="images/logo/shop369_logo.png" alt=""></a></div>'; ?>
                            <!--navigation-->
                            <div class="prev-menu-scroll icon-angle-left prev-menu-js"></div>
                            <div class="nav-holder-s"></div>
                            <div class="next-menu-scroll icon-angle-right next-menu-js"></div>
                            <!--//navigation-->
                            <div class="col-auto minicart-holder-s"></div>
                        </div>
                    </div>
                </div>
        </header>
        <div class="page-content">
            <div class="holder mt-0">
                <div class="container">
                    <br><br>
                </div>
            </div>
            <div class="holder mt-0" id="thank_you">
                <div class="container">
                    <div class="row">
                        <div class="col-md-2 aside aside--left">

                        </div>
                        <div class="col-md-8 aside">
                            <h2 style="text-align: center;"><?php echo $ty_title; ?></h2>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="card">
                                        <div class="card-body" style="text-align: center;">
                                            <div style="text-align: center;margin: 20px 0px;">
                                                <div>
                                                    <img src="images/checked.png">
                                                </div>
                                            </div>
                                            <p><?php echo $product_title; ?><br><b>Order Number is:</b>  <?php echo $razorpay_order_id; ?><br> <?php echo $ty_description; ?><br> <?php echo $ty_customer_care; ?></p><br>
                                            <div style="text-align: center; margin-bottom: 25px;">
                                                <div><b>Rate the product</b></div> 
                                                <div>            
                                                    <span class="rating" id="1" onclick="setRating(1);"></span> 
                                                    <span class="rating" id="2" onclick="setRating(2);"></span> 
                                                    <span class="rating" id="3" onclick="setRating(3);"></span> 
                                                    <span class="rating" id="4" onclick="setRating(4);"></span> 
                                                    <span class="rating" id="5" onclick="setRating(5);"></span>
                                                </div>
                                            </div>


                                        </div>
                                    </div>
                                </div>
                                <div class="hdr-content hide-mobile" style="width:100%">
                                    <div class="text-center">
                                        <div class="footer-menu">
                                            <div class="btn-wrap">
                                                <?php 
                                                echo'<a href="products.php?partner=' . $partner . '&type=' . $type . '"  class="btn btn--add-to-cart"   style="padding: 11px 14px 11px 14px !important;background-color: #2b4f68;"><i class="icon icon-handbag"></i><span>Continue Shopping</span></a>
                                                 </div>';
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="col-md-2 aside aside--right">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="holder py-3 py-md-6 fullboxed aside--bg-none holder-bg-09" style="margin-top: 0px !important;">
                <div class="container">
                    <div class="text-center" id="all_product">
                        <h2 class="h1-style">Related Products</h2>
                    </div>
                    <div class="text-center" id="search_result" style="display:none">
                        <h2 class="h1-style">Search Result</h2>
                    </div>
                    <div class="prd-grid prd-grid--nopad data-to-show-3 data-to-show-md-2 data-to-show-sm-2 data-to-show-xs-1 js-product-isotope mt-4"  id="product_list">

                    </div>
                    <div class="prd-grid prd-grid--nopad data-to-show-3 data-to-show-md-2 data-to-show-sm-2 data-to-show-xs-1 js-product-isotope mt-4"  id="search_list" style="display:none">

                    </div>
                    <div id="no_data" style="display:none;text-align: center;height: 350px;">
                        <h5>No Data Found</h5>
                    </div>
                </div>
            </div>
            <div class="hdr-mobile show-mobile" style="position: fixed;height: 60px;left:0;bottom: 8px;float:left;width:100%;z-index: 100;">
                <div class="footer-block py-1 py-md-0 mt-0 text-center">
                    <div class="footer-menu">
                        <div class="btn-wrap">
                        <?php 
                        echo'<a class="btn btn--add-to-cart"  href="products.php?partner=' . $partner . '&type=' . $type . '" style="padding: 11px 14px 11px 14px !important;background-color: #2b4f68;"><i class="icon icon-handbag"></i><span>Continue Shopping</span>
                            </a>'; 
                        ?>
                        </div>
                    </div>
                </div>
            </div>


            <!---------------- Search Share & Earn modal-------------------->
            <div class="modal fade" id="shareEarn-modal">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">

                            <h5 class="modal-title" id="exampleModalLabel">Share & Earn!</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p id="copy_link_text" style="display: none;"><b>Link Copied</b></p>
                            <div class="container">
                                <p style="margin: 15px 0px 0px 0px; font-weight: 600; font-size: 16px;" id="search_modal_product_title"></p>
                                <p id="search_modal_product_sales_price"></p>
                                <p id="search_modal_influencer_commission_value"></p>
                                <div class="social-buttons" style="justify-content: flex-start;" id="search_shareable_link"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> 

            <!-------------------------Search Share and Earn modal end--------------------------------------------->



            <!----------------Share & Earn modal-------------------->
            <div class="modal fade" id="sell-modal">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">

                            <h5 class="modal-title" id="exampleModalLabel">Share & Earn!</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p id="copy_link_text" style="display: none;"><b>Link Copied</b></p>
                            <div class="container">
                                <p style="margin: 15px 0px 0px 0px; font-weight: 600; font-size: 16px;" id="modal_product_title"></p>
                                <p id="modal_product_sales_price"></p>
                                <p id="modal_influencer_commission_value"></p>
                                <div class="social-buttons" style="justify-content: flex-start;" id="shareable_link"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> 

            <!-------------------------modal end--------------------------------------------->
            <?php include('footer.php'); ?>
            <script src="js/vendor/jquery/jquery.min.js"></script>
            <script src="js/vendor/scrollLock/jquery-scrollLock.min.js"></script>
            <script src="js/vendor/bootstrap-tabcollapse/bootstrap-tabcollapse.min.js"></script>
            <script src="js/vendor/cookie/jquery.cookie.min.js"></script>

            <script src="js/custom_app.min.js"></script>


            <script type="text/javascript">
                    var search_array = <?php echo json_encode($result_products['RESPONSE']) ?>;
                    function setRating(rating) {
                        console.log(rating);
                        for (var i = 1; i <= 5; i++) {
                            if (i <= rating) {
                                document.getElementById(i).className = 'rated';
                            } else {
                                document.getElementById(i).className = 'rating';
                            }
                        } // for

                    } // setRating


                    $(document).ready(function () {
                        var apiVersion = '<?php echo $sapiVersion; ?>';
                        var ENCODING = '<?php echo $sapiEncoding; ?>';
                        var access_code = '<?php echo $sapiAccessCode; ?>';
                        var partner_id = '<?php echo $partner; ?>';
                        var type = '<?php echo $type; ?>';
                        var session_id = '<?php echo $session_id; ?>';
                        var product_cat = '<?php echo $product_category; ?>';
                        var user_id = '<?php echo $user_mobile; ?>';
                        var influencer_commission_percentage = '<?php echo $influencer_commission_percentage; ?>';
                        console.log(type);
                        $.ajax({
                            url: '<?php echo $sapiUrl; ?>',
                            method: "POST",
//                                                async: false,
                            data: {VERSION: apiVersion, ENCODING: ENCODING, access_code: access_code, METHOD: "GET_PUBLISHER_PRODUCTS", partner_id: partner_id, show_category: product_cat},
                            success: function (data)
                            {
                                data = JSON.parse(data);
                                if (data['RESULT'] == 'SUCCESS') {
                                    var response = data['RESPONSE'];
                                    console.log(response);

                                    for (var index in response) {
                                        var product_category = response[index]['details']['product_category'];
//                                                            
                                        var product_category = response[index]['details']['product_category'];
                                        var added_by = response[index]['details']['added_by'];
                                        var product_id = response[index]['details']['product_id'];
                                        var product_title = response[index]['details']['product_title'];
                                        if (product_title.length > 35) {
                                            product_title = product_title.substr(0, 35) + " ...";
                                        } else {
                                            product_title = response[index]['details']['product_title'];
                                        }
                                        if (response[index]['details']['product_short_description']) {
                                            var product_short_description = response[index]['details']['product_short_description'];
                                            if (product_short_description.length > 60) {
                                                product_short_description = product_short_description.substr(0, 60) + " ...";
                                            } else {
                                                product_short_description = response[index]['details']['product_short_description'];
                                            }
                                        } else {

                                            var affise_description = response[index]['details']['affise_description'];
                                            if (affise_description) {
                                                product_short_description = affise_description.substr(0, 100) + " ...";
                                                console.log(product_short_description);
                                            } else {
                                                product_short_description = "";
                                            }


                                        }

                                        var product_image = 'https://api.flickstree.com/resize_img.php?url=' + response[index]['details']['product_images'][0] + '&width=200';

                                        if (response[index]['pricing']) {
                                            var product_mrp = response[index]['pricing']['pricing']['in']['product_mrp'];
                                            var url = 'product.php?partner=' + partner_id + '&type=' + type + '&product_id=' + product_id + '';
                                        } else {
                                            var product_mrp = "";
                                            var url = 'product.php?partner=' + partner_id + '&type=' + type + '&product_id=' + product_id + '';
                                        }

                                        if (response[index]['pricing']) {
                                            var product_sales_price = response[index]['pricing']['pricing']['in']['product_sales_price'];
                                        } else {
                                            var product_sales_price = "";
                                        }

                                        if (response[index]['pricing']) {
                                            var save_price = product_mrp - product_sales_price;

                                            var pricing = '<div class="prd-price">\n\
                                            <div class="price-new">&#8377; ' + product_sales_price + '</div>\n\
                                                <div class="price-old">&#8377; ' + product_mrp + '</div>\n\
                                                <div class="price-comment" style="color: #7e818c;">You save &#8377; ' + save_price + '</div>\n\
                                            </div>';

                                        } else {
                                            var save_price = "";
                                            var pricing = '<div class="prd-price">\n\
                                            <div class="price-new">Avail the Offer</div>\n\
                                            </div>';
                                        }

                                        if (response[index]['pricing']) {
                                            var offer = Math.round(((product_mrp - product_sales_price) / product_mrp) * 100);
                                            var offer = '<div class="label-new">' + offer + '% Off</div>'
                                            var rate = '4.5 <i class="icon-star fill"></i>';
                                        } else {
                                            offer = "";
                                            var offer = '';
                                            var rate = '';
                                        }

                                        if (response[index]['pricing']) {
                                            var influencer_commission_value = (product_sales_price * influencer_commission_percentage) / 100;

                                        } else {
                                            var influencer_commission_value = "";
                                        }

                                        var product_type = response[index]['details']['product_type'];
                                        var product_status = response[index]['details']['product_status'];

                                        if(product_status== 'Active'){
                                        var categorywise_product = '<div class="prd prd-horizontal-simple prd-popular prd-new">';
                                        categorywise_product += '<div class="prd-inside">\n\
                                        <div class="prd-img-area" style="height: 230px;box-shadow: -3px -3px 5px #888888ab;">';
                                        if (session_id) {
                                            categorywise_product += '<a href="' + url + '" class="prd-img"><img src="images/default_img.jpg" data-src="' + product_image + '" alt="" id="tile_img">\n\
                                        ' + offer + '<p class="label-wishlist icon-heart js-label-wishlist"></p></a>';
                                        } else {
                                            if (type == 'buyer') {
                                                categorywise_product += '<a href="' + url + '" class="prd-img"><img src="images/default_img.jpg" data-src="' + product_image + '" alt="" id="tile_img">\n\
                                        ' + offer + '<p class="label-wishlist icon-heart js-label-wishlist"></p></a>';
                                            } else {
                                                categorywise_product += "<a onclick=openSignupModal('" + product_id + "') class='prd-img'><img src='images/default_img.jpg' data-src='" + product_image + "' alt='' id='tile_img'>\n\
                                        " + offer + "<p class='label-wishlist icon-heart js-label-wishlist'></p></a>";
                                            }

                                        }


                                        categorywise_product += '</div>\n\
                                        <div class="prd-info" style="display:block !important;">';

                                        if (session_id) {
                                            categorywise_product += '<a href="' + url + '"  style="text-transform: none;text-decoration: none;"><h2 class="prd-title" style="color: #282c3f;">' + product_title + '</h2>\n\
                                        <div class="prd-tag" style="text-transform: none;color: #7e818c;line-height: 1.2em;">' + product_short_description + '</div>\n\
                                        <div class="prd-rating">' + rate + '</div>\n\
                                        ' + pricing + '</a>';
                                        } else {
                                            if (type == 'buyer') {
                                                categorywise_product += '<a href="' + url + '"  style="text-transform: none;text-decoration: none;"><h2 class="prd-title" style="color: #282c3f;">' + product_title + '</h2>\n\
                                        <div class="prd-tag" style="text-transform: none;color: #7e818c;line-height: 1.2em;">' + product_short_description + '</div>\n\
                                        <div class="prd-rating">' + rate + '</div>\n\
                                        ' + pricing + '</a>';
                                            } else {
                                                categorywise_product += '<a onclick=openSignupModal("' + product_id + '")  style="text-transform: none;text-decoration: none;"><h2 class="prd-title" style="color: #282c3f;"">' + product_title + '</h2>\n\
                                        <div class="prd-tag" style="text-transform: none;color: #7e818c;line-height: 1.2em;"">' + product_short_description + '</div>\n\
                                        <div class="prd-rating">' + rate + '</div>\n\
                                        ' + pricing + '</a>';
                                            }

                                        }


                                        if (type == 'buyer') {
                                            if (product_type == 'External') {
                                                var affise_tracking_url = response[index]['details']['affise_tracking_url'];
                                                categorywise_product += '<div class="prd-action" style="width: 90%;">\n\
                                        <form action="#"><input type="hidden">\n\
                                            <a href="product.php?partner=' + partner_id + '&type=' + type + '&product_id=' + product_id + '" class="btn" style="color: #fff;background-color: #2b4f68;background-color: #2b4f68;"><span>Buy Now!</span></a>\n\
                                        </form>\n\
                                        </div>';
                                            } else {
                                                categorywise_product += '<div class="prd-action" style="width: 90%;">\n\
                                        <form action="#"><input type="hidden">\n\
                                            <a href="product.php?partner=' + partner_id + '&type=' + type + '&product_id=' + product_id + '" class="btn" style="color: #fff;background-color: #2b4f68;background-color: #2b4f68;"><span>Buy Now!</span></a>\n\
                                        </form>\n\
                                        </div>';
                                            }
                                        } else {
                                            if (product_type == 'External') {
                                                var affise_tracking_url = response[index]['details']['affise_tracking_url'];
                                                if (session_id) {
                                                    categorywise_product += '<div class="prd-action" style="width: 90%;">\n\
                                        <form><input type="hidden"><a href="product.php?partner=' + partner_id + '&type=' + type + '&product_id=' + product_id + '" class="btn" style="color: #fff;background-color: #2b4f68;"><span>Share & Earn</span></a>\n\
                                        </form>\n\
                                        </div>';
                                                } else {
                                                    categorywise_product += "<div class='prd-action' style='width: 90%;'><form><input type='hidden'>\n\
                                        <a onclick=openSignupModal('" + product_id + "') class='btn' style='color: #fff;background-color: #2b4f68;background-color: #2b4f68;'><span>Share & Earn</span></a>\n\
                                        </form>\n\
                                        </div>";
                                                }

                                            } else {
                                                categorywise_product += '<div class="prd-action" style="width: 90%;">\n\
                                        <form><input type="hidden">\n\
                                        ';
                                                if (session_id) {
                                                    categorywise_product += '<a href="product.php?partner=' + partner_id + '&type=' + type + '&product_id=' + product_id + '" class="btn" style="color: #fff;    background-color: #2b4f68;"><span>Share & Earn</span></a>';
                                                } else {
                                                    categorywise_product += '<a class="btn" onclick=openSignupModal("' + product_id + '") style="color: #fff;background-color: #2b4f68;"><span>Share & Earn</span></a>';
                                                }

                                                categorywise_product += '</form>\n\
                                        </div>';
                                            }
                                        }

                                        categorywise_product += '</div>\n\
                                        </div>\n\
                                        </div>';
                                        $('#product_list').append(categorywise_product);
                                        var imgDefer = document.getElementsByTagName('img');
                                        for (var i = 0; i < imgDefer.length; i++) {
                                            if (imgDefer[i].getAttribute('data-src')) {
                                                imgDefer[i].setAttribute('src', imgDefer[i].getAttribute('data-src'));
                                            }
                                        }
                                        }
                                    }

                                }
                            }

                        });

                    });
            </script>
            <script>
                function desktop_partnerwise_search() {

                    document.getElementById("desk_search_form").style.borderColor = "";
                    var partner_id = '<?php echo $partner; ?>';
                    var type = '<?php echo $type; ?>';
                    var session_id = '<?php echo $session_id; ?>';
                    var user_id = '<?php echo $user_mobile; ?>';
                    if ($('#search_title').val() == "") {
                        $('#product_list').show();
                        $("#search_list").empty();
                        $('#thank_you').show();
                        $('#all_product').show();
                        $('#search_result').hide();

                        $('#no_data').hide();
                        return false;
                    } else {
                        var search_title = $('#search_title').val();
                        search_title = search_title.toLowerCase();

                        var search_product = [];

                        for (var index in search_array) {

                            var product_title = search_array[index]['details']['product_title'];
                            product_title = product_title.toLowerCase();
                            var product_array = [];
                            var search_result = product_title.includes(search_title);

                            if (search_result) {

                                var counter = 1;
                                product_array['product_id'] = search_array[index]['details']['product_id'];
                                console.log(counter);
                                if (product_array['product_id']) {

                                    product_array['product_title'] = search_array[index]['details']['product_title'];
                                    if (product_array['product_title'].length > 35) {
                                        product_array['product_title'] = product_array['product_title'].substr(0, 35) + " ...";
                                    } else {
                                        product_array['product_title'] = search_array[index]['details']['product_title'];
                                    }

                                    if (search_array[index]['details']['product_short_description']) {
                                        product_array['product_short_description'] = search_array[index]['details']['product_short_description'];
                                        if (product_array['product_short_description'].length > 60) {
                                            product_array['product_short_description'] = product_array['product_short_description'].substr(0, 60) + " ...";
                                        } else {
                                            product_array['product_short_description'] = search_array[index]['details']['product_short_description'];
                                        }
                                    } else {

                                        product_array['affise_description'] = search_array[index]['details']['affise_description'];
                                        if (product_array['affise_description']) {
                                            product_array['product_short_description'] = product_array['affise_description'].substr(0, 100) + " ...";
                                        } else {
                                            product_array['product_short_description'] = "";
                                        }

                                    }

                                    product_array['product_image'] = search_array[index]['details']['product_images'][0];


                                    product_array['product_type'] = search_array[index]['details']['product_type'];
                                    var atag = 'product.php?partner=' + partner_id + '&type=' + type + '&product_id=' + product_array['product_id'] + '';

                                }


                            }

                            if (counter >= 1) {

                                if (product_array['product_id']) {
                                    product_array['product_status'] = search_array[index]['details']['product_status'];
                
                                    if(product_array['product_status'] == 'Active'){
                                    search_product += '<div class="prd prd-horizontal-simple prd-popular prd-new">\n\
                                                                <div class="prd-inside">\n\
                                                                <div class="prd-img-area" style="height: 230px;box-shadow: -3px -3px 5px #888888ab;">';
                                    if (session_id) {
                                        search_product += '<a href="' + atag + '" class="prd-img"><img src=' + product_array['product_image'] + ' alt="" id="tile_img"></a>';
                                    } else {
                                        if (type == 'buyer') {
                                            search_product += '<a href="' + atag + '" class="prd-img"><img src=' + product_array['product_image'] + ' alt="" id="tile_img"></a>';
                                        } else {
                                            search_product += "<a onclick=openSignupModal('" + product_array['product_id'] + "') class='prd-img'><img src='" + product_array['product_image'] + "' alt='' id='tile_img'></a>";
                                        }

                                    }

                                    search_product += '<div class="label-new"></div><a href="#" class="label-wishlist icon-heart js-label-wishlist"></a>\n\
                                                                </div>\n\
                                                                <div class="prd-info" style="display:block !important;">';

                                    if (session_id) {
                                        search_product += '<a href="' + atag + '"  style="text-transform: none;text-decoration: none;"><h2 class="prd-title" style="color: #282c3f;">' + product_array['product_title'] + '</h2>\n\
                                                                <div class="prd-tag" style="text-transform: none;color: #7e818c;line-height: 1.2em;">' + product_array['product_short_description'] + '</div>\n\
                                                                <div class="prd-rating"></div>\n\
                                                                <div class="prd-price">\n\
                                                                <div class="price-new"></div>\n\
                                                                <div class="price-old"></div>\n\
                                                                <div class="price-comment" style="color: #7e818c;"></div></a>';
                                    } else {

                                        if (type == 'buyer') {
                                            search_product += '<a href="' + atag + '"  style="text-transform: none;text-decoration: none;"><h2 class="prd-title" style="color: #282c3f;">' + product_array['product_title'] + '</h2>\n\
                                                                    <div class="prd-tag" style="text-transform: none;color: #7e818c;line-height: 1.2em;">' + product_array['product_short_description'] + '</div>\n\
                                                                    <div class="prd-rating"></div>\n\
                                                                    <div class="prd-price">\n\
                                                                    <div class="price-new"></div>\n\
                                                                    <div class="price-old"></div>\n\
                                                                    <div class="price-comment" style="color: #7e818c;"></div></a>';
                                        } else {
                                            search_product += '<a onclick=openSignupModal("' + product_array['product_id'] + '")  style="text-transform: none;text-decoration: none;"><h2 class="prd-title" style="color: #282c3f;">' + product_array['product_title'] + '</h2>\n\
                                                                    <div class="prd-tag" style="text-transform: none;color: #7e818c;line-height: 1.2em;">' + product_array['product_short_description'] + '</div>\n\
                                                                    <div class="prd-rating"></div>\n\
                                                                    <div class="prd-price">\n\
                                                                    <div class="price-new"></div>\n\
                                                                    <div class="price-old"></div>\n\
                                                                    <div class="price-comment" style="color: #7e818c;"></div></a>';
                                        }

                                    }
                                    search_product += '</div>';


                                    search_product += '<div class="prd-action" style="width: 90%;">\n\
                                                                    <form><input type="hidden">';
                                    if (session_id) {
                                        if (type == 'buyer') {
                                            search_product += '<a href="product.php?partner=' + partner_id + '&type=' + type + '&product_id=' + product_array['product_id'] + '" class="btn" style="color: #fff;background-color: #2b4f68;"><span>Buy Now!</span></a>';
                                        } else {
                                            search_product += '<a href="product.php?partner=' + partner_id + '&type=' + type + '&product_id=' + product_array['product_id'] + '" class="btn" style="color: #fff;background-color: #2b4f68;"><span>Share & Earn</span></a>';
                                        }

                                    } else {
                                        if (type == 'buyer') {
                                            search_product += '<a href="product.php?partner=' + partner_id + '&type=' + type + '&product_id=' + product_array['product_id'] + '" class="btn" style="color: #fff;background-color: #2b4f68;"><span>Buy Now!</span></a>';
                                        } else {
                                            search_product += '<a class="btn" onclick=openSignupModal("' + product_array['product_id'] + '") style="color: #fff;background-color: #2b4f68;"><span>Share & Earn</span></a>';
                                        }

                                    }

                                    search_product += '</form>\n\
                                                                </div>';

                                    search_product += '</div>\n\
                                                                </div>\n\
                                                                </div>';

                                    document.getElementById("search_list").innerHTML = search_product;
                                    $('#no_data').hide();
                                    if (search_title == "") {
                                        $('#hdn_container').show();
                                    } else {
                                        $('#hdn_container').hide();
                                    }
                                    $('#thank_you').hide();
                                    $('#hdn_bar').hide();
                                    $('#product_list').hide();
                                    $('#category_list').hide();
                                    $('#all_product').hide();
                                    $('#search_result').show();
                                    $('#search_list').show();
                                    }
                                }
                            } else {
                                $('#thank_you').hide();
                                $("#product_list").hide();
                                $("#search_list").empty();
                                $("#category_list").empty();
                                $('#hdn_container').hide();
                                $('#hdn_bar').hide();
                                $('#all_product').hide();

                                $('#no_data').show();
                            }

                        }
                    }



                }


                function mobile_partnerwise_search() {
                    var partner_id = '<?php echo $partner; ?>';
                    var type = '<?php echo $type; ?>';
                    var session_id = '<?php echo $session_id; ?>';
                    var user_id = '<?php echo $user_mobile; ?>';
                    if ($('#mobile_search').val() == "") {
                        $('#product_list').show();
                        $("#search_list").empty();
                        $('#thank_you').show();
                        $('#all_product').show();
                        $('#search_result').hide();
                        $('#no_data').hide();

                        return false;
                    } else {
                        var search_title = $('#mobile_search').val();
                        search_title = search_title.toLowerCase();

                        var search_product = [];

                        for (var index in search_array) {

                            var product_title = search_array[index]['details']['product_title'];
                            product_title = product_title.toLowerCase();
                            var product_array = [];
                            var search_result = product_title.includes(search_title);

                            if (search_result) {

                                var counter = 1;
                                product_array['product_id'] = search_array[index]['details']['product_id'];
                                console.log(counter);
                                if (product_array['product_id']) {

                                    product_array['product_title'] = search_array[index]['details']['product_title'];
                                    if (product_array['product_title'].length > 35) {
                                        product_array['product_title'] = product_array['product_title'].substr(0, 35) + " ...";
                                    } else {
                                        product_array['product_title'] = search_array[index]['details']['product_title'];
                                    }

                                    if (search_array[index]['details']['product_short_description']) {
                                        product_array['product_short_description'] = search_array[index]['details']['product_short_description'];
                                        if (product_array['product_short_description'].length > 60) {
                                            product_array['product_short_description'] = product_array['product_short_description'].substr(0, 60) + " ...";
                                        } else {
                                            product_array['product_short_description'] = search_array[index]['details']['product_short_description'];
                                        }
                                    } else {

                                        product_array['affise_description'] = search_array[index]['details']['affise_description'];
                                        if (product_array['affise_description']) {
                                            product_array['product_short_description'] = product_array['affise_description'].substr(0, 100) + " ...";
                                        } else {
                                            product_array['product_short_description'] = "";
                                        }

                                    }

                                    product_array['product_image'] = search_array[index]['details']['product_images'][0];


                                    product_array['product_type'] = search_array[index]['details']['product_type'];
                                    var atag = 'product.php?partner=' + partner_id + '&type=' + type + '&product_id=' + product_array['product_id'] + '';

                                }


                            }

                            if (counter >= 1) {

                                if (product_array['product_id']) {
                                    product_array['product_status'] = search_array[index]['details']['product_status'];
                
                                    if(product_array['product_status'] == 'Active'){
                                    search_product += '<div class="prd prd-horizontal-simple prd-popular prd-new">\n\
                                                                <div class="prd-inside">\n\
                                                                <div class="prd-img-area" style="height: 230px;box-shadow: -3px -3px 5px #888888ab;">';
                                    if (session_id) {
                                        search_product += '<a href="' + atag + '" class="prd-img"><img src=' + product_array['product_image'] + ' alt="" id="tile_img"></a>';
                                    } else {
                                        if (type == 'buyer') {
                                            search_product += '<a href="' + atag + '" class="prd-img"><img src=' + product_array['product_image'] + ' alt="" id="tile_img"></a>';
                                        } else {
                                            search_product += "<a onclick=openSignupModal('" + product_array['product_id'] + "') class='prd-img'><img src='" + product_array['product_image'] + "' alt='' id='tile_img'></a>";
                                        }

                                    }

                                    search_product += '<div class="label-new"></div><a href="#" class="label-wishlist icon-heart js-label-wishlist"></a>\n\
                                                                </div>\n\
                                                                <div class="prd-info" style="display:block !important;">';

                                    if (session_id) {
                                        search_product += '<a href="' + atag + '"  style="text-transform: none;text-decoration: none;"><h2 class="prd-title" style="color: #282c3f;">' + product_array['product_title'] + '</h2>\n\
                                                                <div class="prd-tag" style="text-transform: none;color: #7e818c;line-height: 1.2em;">' + product_array['product_short_description'] + '</div>\n\
                                                                <div class="prd-rating"></div>\n\
                                                                <div class="prd-price">\n\
                                                                <div class="price-new"></div>\n\
                                                                <div class="price-old"></div>\n\
                                                                <div class="price-comment" style="color: #7e818c;"></div></a>';
                                    } else {

                                        if (type == 'buyer') {
                                            search_product += '<a href="' + atag + '"  style="text-transform: none;text-decoration: none;"><h2 class="prd-title" style="color: #282c3f;">' + product_array['product_title'] + '</h2>\n\
                                                                    <div class="prd-tag" style="text-transform: none;color: #7e818c;line-height: 1.2em;">' + product_array['product_short_description'] + '</div>\n\
                                                                    <div class="prd-rating"></div>\n\
                                                                    <div class="prd-price">\n\
                                                                    <div class="price-new"></div>\n\
                                                                    <div class="price-old"></div>\n\
                                                                    <div class="price-comment" style="color: #7e818c;"></div></a>';
                                        } else {
                                            search_product += '<a onclick=openSignupModal("' + product_array['product_id'] + '")  style="text-transform: none;text-decoration: none;"><h2 class="prd-title" style="color: #282c3f;">' + product_array['product_title'] + '</h2>\n\
                                                                    <div class="prd-tag" style="text-transform: none;color: #7e818c;line-height: 1.2em;">' + product_array['product_short_description'] + '</div>\n\
                                                                    <div class="prd-rating"></div>\n\
                                                                    <div class="prd-price">\n\
                                                                    <div class="price-new"></div>\n\
                                                                    <div class="price-old"></div>\n\
                                                                    <div class="price-comment" style="color: #7e818c;"></div></a>';
                                        }

                                    }
                                    search_product += '</div>';


                                    search_product += '<div class="prd-action" style="width: 90%;">\n\
                                                                    <form><input type="hidden">';
                                    if (session_id) {
                                        if (type == 'buyer') {
                                            search_product += '<a href="product.php?partner=' + partner_id + '&type=' + type + '&product_id=' + product_array['product_id'] + '" class="btn" style="color: #fff;background-color: #2b4f68;"><span>Buy Now!</span></a>';
                                        } else {
                                            search_product += '<a href="product.php?partner=' + partner_id + '&type=' + type + '&product_id=' + product_array['product_id'] + '" class="btn" style="color: #fff;background-color: #2b4f68;"><span>Share & Earn</span></a>';
                                        }

                                    } else {
                                        if (type == 'buyer') {
                                            search_product += '<a href="product.php?partner=' + partner_id + '&type=' + type + '&product_id=' + product_array['product_id'] + '" class="btn" style="color: #fff;background-color: #2b4f68;"><span>Buy Now!</span></a>';
                                        } else {
                                            search_product += '<a class="btn" onclick=openSignupModal("' + product_array['product_id'] + '") style="color: #fff;background-color: #2b4f68;"><span>Share & Earn</span></a>';
                                        }

                                    }

                                    search_product += '</form>\n\
                                                                </div>';

                                    search_product += '</div>\n\
                                                                </div>\n\
                                                                </div>';

                                    document.getElementById("search_list").innerHTML = search_product;
                                    $('#no_data').hide();
                                    if (search_title == "") {
                                        $('#hdn_container').show();
                                    } else {
                                        $('#hdn_container').hide();
                                    }
                                    $('#thank_you').hide();
                                    $('#hdn_bar').hide();
                                    $('#product_list').hide();
                                    $('#category_list').hide();
                                    $('#all_product').hide();
                                    $('#search_result').show();
                                    $('#search_list').show();
                                    }
                                }
                            } else {
                                $('#thank_you').hide();
                                $('#product_list').hide();
                                $("#search_list").empty();
                                $("#category_list").empty();
                                $('#hdn_container').hide();
                                $('#hdn_bar').hide();

                                $('#all_product').hide();

                                $('#no_data').show();
                            }

                        }
                    }



                }
                function enter_key_search(e) {

                    var search_title = $('#search_title').val();
                    var search_title = search_title.trim();
                    if (e.keyCode == 13) {
                        if (search_title == "") {
                            $('#product_list').show();
                            $("#search_list").empty();
                            $('#thank_you').show();
                            $('#all_product').show();
                            $('#search_result').hide();
                            $('#no_data').hide();
                            return false;
                        } else {

                            desktop_partnerwise_search();
                            return false;
                        }

                    }
                }

                function share_earn_modal(partner_id, product_id) {

                    $('#shareEarn-modal').modal('show');
                    document.getElementById("search_shareable_link").innerHTML = '';
                    document.getElementById("search_modal_product_title").innerHTML = '';
                    document.getElementById("search_modal_product_sales_price").innerHTML = '';
                    document.getElementById("search_modal_influencer_commission_value").innerHTML = '';

                    var apiVersion = '<?php echo $sapiVersion; ?>';
                    var ENCODING = '<?php echo $sapiEncoding; ?>';
                    var USERID = '<?php echo $user_mobile; ?>';
                    var influencer_commission_percentage = '<?php echo $influencer_commission_percentage; ?>';
                    $.ajax({
                        url: '<?php echo $sapiUrl; ?>',
                        method: "POST",
                        data: {VERSION: apiVersion, ENCODING: ENCODING, USERID: USERID, METHOD: "GET_PRODUCT_DETAIL", partner_id: partner_id, product_id: product_id},
                        success: function (data)
                        {
                            data = JSON.parse(data);
                            if (data['RESULT'] == 'SUCCESS') {
                                var response = data['RESPONSE'];

                                for (var index in response) {

                                    var product_mrp = response[index]['pricing']['pricing']['in']['product_mrp'];
                                    var sales_price = response[index]['pricing']['pricing']['in']['product_sales_price'];

                                    var influencer_commission = (sales_price * influencer_commission_percentage) / 100;

                                    var sapiUrl = '<?php echo $sapiUrl; ?>';
                                    var sapiVersion = '<?php echo $sapiVersion; ?>';
                                    var sapiEncoding = '<?php echo $sapiEncoding; ?>';
                                    var USERID = '<?php echo $user_mobile; ?>';
                                    var product_title = '';
                                    var response = [];
                                    var request = new XMLHttpRequest();
                                    request.open('POST', sapiUrl + '?VERSION=' + sapiVersion + '&ENCODING=' + sapiEncoding + '&METHOD=GET_USER_PAYID&USERID=' + USERID + '&partner_id=' + partner_id, true);
                                    request.onload = function () {

                                        // Begin accessing JSON data here
                                        var data = JSON.parse(this.response);

                                        if (request.status >= 200 && request.status < 400) {

                                            console.log(data);

                                            response = data['RESPONSE'];

                                            //console.log(response['payid']);
                                            var shareable_link = 'https://shop369.org/product.php?partner=' + partner_id + '&type=buyer&product_id=' + product_id + '&referer=' + response['payid'];
                                            if (shareable_link) {

                                                $.ajax({
                                                    url: "https://www.flickstree.com/shorty.php?long_link=" + encodeURIComponent(shareable_link),
                                                    method: "GET",
                                                    contentType: "application/json",
                                                    crossDomain: true,
                                                    data: {},

                                                    success: function (data)
                                                    {
                                                        data = JSON.parse(data);
                                                        console.log(data.short_url);
                                                        var short_url = data.short_url;


                                                        var facebook_share = 'https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(short_url);
                                                        var twitter_share = 'https://twitter.com/share?url=' + encodeURIComponent(short_url) + '&text=' + encodeURIComponent(product_title + ' - Buy this Now! ');
                                                        var whatsapp_share = 'whatsapp://send?text=' + encodeURIComponent(product_title + ' - Buy this Now! ') + ' ' + encodeURIComponent(short_url);

                                                        var copy_link = short_url;

                                                        if (typeof response['payid'] != 'undefined') {

                                                            document.getElementById("search_shareable_link").innerHTML = '<a href="' + twitter_share + '" target="_blank" class="col button button-fill twitter external" style="margin-right: 10px; padding: 0 10px;"><i class="icon icon-twitter"  style="font-size: 22px !important;"></i></a>\
                                                            <a href="' + facebook_share + '" target="_blank" class="col button button-fill facebook external" style="margin-right: 10px; padding: 0 10px;"><i class="icon icon-facebook" style="font-size: 22px !important;"></i></a>\
                                                            <a href="' + whatsapp_share + '" target="_blank" class="col button button-fill whatsapp external" style="margin-right: 10px; padding: 0 10px;"><img src="images/whatsapp.png" style="width:22px;height:22px;margin-bottom: 11px;"></a>\
                                                            <a onclick="copyTextToClipboard(\'' + product_id + '\')" class="col button button-fill link" style="padding: 0 10px;"><img src="images/link.png" style="width:22px;height:22px;margin-bottom: 11px;"></a>\
                                                            <input type="hidden" value="' + copy_link + '" id="' + product_id + '">';

                                                            document.getElementById("search_modal_product_sales_price").innerHTML = '<strong>Sale Price:</strong> INR ' + sales_price;
                                                            document.getElementById("search_modal_influencer_commission_value").innerHTML = '<strong>Your Commission:</strong> INR ' + influencer_commission;


                                                        } else {
                                                            document.getElementById("search_shareable_link").innerHTML = 'No Shareable Link Found';
                                                        }
                                                    }
                                                });

                                            }


                                        } else {
                                            console.log('data not found');
                                        }
                                    }

                                    request.send();



                                }
                            }
                        }
                    });
                }


                function sellModal(partner_id, product_id, product_sales_price, influencer_commission_value) {


                    // sell_modal = document.getElementById("sell-modal");    
                    // sell_modal.classList.toggle("show-modal");
                    $('#sell-modal').modal('show');
                    getProductDetails(partner_id, product_id, product_sales_price, influencer_commission_value);
                }

                function getProductDetails(partner_id, product_id, product_sales_price, influencer_commission_value) {

                    var traking_url = '<?php echo $affise_tracking_url; ?>';

                    document.getElementById("shareable_link").innerHTML = '';
                    document.getElementById("modal_product_title").innerHTML = '';
                    document.getElementById("modal_product_sales_price").innerHTML = '';
                    document.getElementById("modal_influencer_commission_value").innerHTML = '';

                    var sapiUrl = '<?php echo $sapiUrl; ?>';
                    var sapiVersion = '<?php echo $sapiVersion; ?>';
                    var sapiEncoding = '<?php echo $sapiEncoding; ?>';
                    var USERID = '<?php echo $user_mobile; ?>';
                    var product_title = '';
                    var response = [];
                    var request = new XMLHttpRequest();
                    request.open('POST', sapiUrl + '?VERSION=' + sapiVersion + '&ENCODING=' + sapiEncoding + '&METHOD=GET_USER_PAYID&USERID=' + USERID + '&partner_id=' + partner_id, true);
                    request.onload = function () {

                        // Begin accessing JSON data here
                        var data = JSON.parse(this.response);

                        if (request.status >= 200 && request.status < 400) {

                            console.log(data);

                            response = data['RESPONSE'];

                            //console.log(response['payid']);
                            if (traking_url) {
                                var shareable_link = traking_url + '&referer=' + response['payid'];
                                console.log(shareable_link);
                            } else {
                                var shareable_link = 'https://www.shop369.org/product.php?partner=' + partner_id + '&type=seller&product_id=' + product_id + '&referer=' + response['payid'];
                            }
                            if (shareable_link) {

                                $.ajax({
                                    url: "https://www.flickstree.com/shorty.php?long_link=" + encodeURIComponent(shareable_link),
                                    method: "GET",
                                    contentType: "application/json",
                                    crossDomain: true,
                                    data: {},

                                    success: function (data)
                                    {
                                        data = JSON.parse(data);
                                        console.log(data.short_url);
                                        var short_url = data.short_url;


                                        var facebook_share = 'https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(short_url);
                                        var twitter_share = 'https://twitter.com/share?url=' + encodeURIComponent(short_url) + '&text=' + encodeURIComponent(product_title + ' - Buy this Now! ');
                                        var whatsapp_share = 'whatsapp://send?text=' + encodeURIComponent(product_title + ' - Buy this Now! ') + ' ' + encodeURIComponent(short_url);

                                        var copy_link = short_url;

                                        if (typeof response['payid'] != 'undefined') {

                                            document.getElementById("shareable_link").innerHTML = '<a href="' + twitter_share + '" target="_blank" class="col button button-fill twitter external" style="margin-right: 10px; padding: 0 10px;"><i class="icon icon-twitter"  style="font-size: 22px !important;"></i></a>\
                                            <a href="' + facebook_share + '" target="_blank" class="col button button-fill facebook external" style="margin-right: 10px; padding: 0 10px;"><i class="icon icon-facebook" style="font-size: 22px !important;"></i></a>\
                                            <a href="' + whatsapp_share + '" target="_blank" class="col button button-fill whatsapp external" style="margin-right: 10px; padding: 0 10px;"><img src="images/whatsapp.png" style="width:22px;height:22px;margin-bottom: 11px;"></a>\
                                            <a onclick="copyTextToClipboard(\'' + product_id + '\')" class="col button button-fill link" style="padding: 0 10px;"><img src="images/link.png" style="width:22px;height:22px;margin-bottom: 11px;"></a>\
                                            <input type="hidden" value="' + copy_link + '" id="' + product_id + '">';

                                            document.getElementById("modal_product_title").innerHTML = product_title;
                                            document.getElementById("modal_product_sales_price").innerHTML = '<strong>Sale Price:</strong> INR ' + product_sales_price;
                                            document.getElementById("modal_influencer_commission_value").innerHTML = '<strong>Your Commission:</strong> INR ' + influencer_commission_value;


                                        } else {
                                            document.getElementById("shareable_link").innerHTML = 'No Shareable Link Found';
                                        }
                                    }
                                });

                            }


                        } else {
                            console.log('data not found');
                        }
                    }

                    request.send();

                }
                function profile() {
                    var partner_id = '<?php echo $partner; ?>';
                    var type = '<?php echo $type; ?>';
                    window.location.href = 'profile.php?partner=' + partner_id + '&type=' + type;
                }
            </script>
    </body>

</html>
