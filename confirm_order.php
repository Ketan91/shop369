<?php
include "functions.php"; // Get Configuration
// Get $_COOKIE['AFF_HTTP_REFERER']

$http_referer = isset($_COOKIE['AFF_HTTP_REFERER']) ? $_COOKIE['AFF_HTTP_REFERER'] : ''; 

$type = sanitize_string($_REQUEST['type']);
$partner = sanitize_string($_REQUEST['partner']);
$product_id = sanitize_string($_REQUEST['product_id']);

if(!empty($_REQUEST["partner"]) && !empty($_REQUEST["product_id"]) && isset($_COOKIE["FTAFF_ID"],$_COOKIE["FTAFF_MOBILE"],$_COOKIE["AFF_PRODUCT_ID"],$_COOKIE["AFF_PRODUCT_QTY"])) {
$session_id = $_COOKIE["FTAFF_SESSIONID"];
$user_id = $_COOKIE["FTAFF_ID"];
$user_name = $_COOKIE["FTAFF_NAME"];
$user_mobile = $_COOKIE["FTAFF_MOBILE"];
$payid = $_COOKIE["FTAFF_PAYID"];

$url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$urlParams = "partner=$partner&type=$type&product_id=$product_id";


$curl = curl_init($sapiUrl);

$curl_post_data = array(
    "VERSION" => $sapiVersion,
    "ENCODING" => $sapiEncoding,
    "USERID" => $USERID,
    "METHOD" => "GET_PRODUCT_DETAIL",
    "partner_id" => $partner,
    "product_id" => $product_id
);

curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $curl_post_data);
$curl_response = curl_exec($curl);
curl_close($curl);
$result = json_decode($curl_response, true);

foreach ($result as $response) {
    $productid = $response[$product_id]['details']['product_id'];
    $product_title = $response[$product_id]['details']['product_title'];
    $product_description = $response[$product_id]['details']['product_description'];
    $product_short_description = $response[$product_id]['details']['product_short_description'];
    $product_code = $response[$product_id]['details']['product_code'];
    $product_images = $response[$product_id]['details']['product_images'][0];
    $product_description_bullets = $response[$product_id]['details']['product_description_bullets'];
    $product_testimonial = $response[$product_id]['details']['product_testimonial'];
    $product_tag = $response[$product_id]['details']['product_tag'];
    $product_tnc = $response[$product_id]['details']['product_tnc'];
    $product_sold_by = $response[$product_id]['details']['product_sold_by'];
    $product_mrp = $response[$product_id]['pricing']['pricing']['in']['product_mrp'];
    $product_sales_price = $response[$product_id]['pricing']['pricing']['in']['product_sales_price'];
    $save_price = $product_mrp - $product_sales_price;
    $currency = $response[$product_id]['pricing']['pricing']['in']['currency'];


    $track_order_url = $response[$product_id]['details']['track_order_url'];
    $pageUrl = "https://api.flickstree.com/video_commerce/user/confirm_order.php?".$urlParams;
    $final_product_price = $_COOKIE["AFF_PRODUCT_PRICE"];
    $final_product_qty = $_COOKIE["AFF_PRODUCT_QTY"];
    $promo_code = $_COOKIE["AFF_PROMO_CODE"];
}

$offer = round((($product_mrp - $product_sales_price) / $product_mrp) * 100);


$curl = curl_init($sapiUrl);//order details

$curl_post_data = array(
    "VERSION" => $sapiVersion,
    "ENCODING" => $sapiEncoding,
    "USERID" => $user_mobile,
    "SESSIONID" => $session_id,
    "METHOD" => "GET_CUSTOMER_PROFILE"
);

curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $curl_post_data);
$curl_response = curl_exec($curl);
curl_close($curl);
$order_details_result = json_decode($curl_response, true);

$id = $order_details_result['RESPONSE']['id'];
$name = $order_details_result['RESPONSE']['name'];
$email = $order_details_result['RESPONSE']['email'];
$mobile = $order_details_result['RESPONSE']['mobile'];
$payee_account_address = $order_details_result['RESPONSE']['payee_account_address'];



if($resultPurchaseOrder = @file_get_contents("$sapiUrl?VERSION=$sapiVersion&ENCODING=$sapiEncoding&METHOD=CREATE_PURCHASE_ORDER&USERID=$user_mobile&SESSIONID=$session_id&product_id=$productid&region=in&currency=$currency&sales_price=$final_product_price&product_qty=$final_product_qty&promo_code=$promo_code&payid=$payid&referer=".urlencode($http_referer))) 
    {
        // echo $payid;exit;
    $dataPurchaseOrder=json_decode($resultPurchaseOrder, true);
      
    if($dataPurchaseOrder['RESULT']=="SUCCESS")
        {
        $purchaseOrderDetails = $dataPurchaseOrder['RESPONSE'];
        $order_id = $purchaseOrderDetails['order_id'];
        $key_id = $purchaseOrderDetails['key_id'];


        $processingBtn = '<a><button type="submit" class="btn btn--add-to-cart" id="confirmToPayBtn" style="padding: 11px 14px 20px 14px !important;background-color: #2b4f68;"><span>Confirm to Pay ₹ '.$final_product_price.'</span></button></a>';
        
        
# USER_LOG
if ($result_log = @file_get_contents("https://api.flickstree.com/video_commerce/user/process_request.php?do_action=USER_LOG&user_id=$user_mobile&product_id=$product_id&partner=$partner&type=$type&status=ORDER_CREATED")) {
    $log_array = json_decode($result_log,true);
    
    if($log_array['RESULT']=="SUCCESS")
    {
    //echo $log_array['RESPONSE'];
    }else{
    //echo "not done."; 
    }   
    
} // USER_LOG
        
        
        //*****Send Order Creation Confirmation to Seller*****//

        if($track_order_url!="" && count($product_tag)>0){
        //The data you want to send via POST
        $postData = [
            'name' => $user_name,
            'email' => $email,
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
        curl_setopt($ch,CURLOPT_URL, $track_order_url);
        curl_setopt($ch,CURLOPT_POST, true);
        curl_setopt($ch,CURLOPT_POSTFIELDS, $postfields_string);

        //So that curl_exec returns the contents of the cURL; rather than echoing it
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 

        //execute post
        $output = curl_exec($ch);
        $result = json_decode($output, true);
         if($result['status'] == "error") {
        $trackOrderUrlMsg = "Please contact us with above order id.";
         }else{
        $trackOrderUrlMsg = "";  
         }   
        //echo $result;

        } // if($track_order_url!="" && count($product_tag)>0)
        //*****Send Order Creation Confirmation to Seller*****//
        
        
        } // if($dataPurchaseOrder['RESULT']=="SUCCESS")
        else {
          $errorMsg = "Oops! Something went wrong. Please process your order again.";
          $processingBtn = '<a href="product.php?'.$urlParams.'&product_id='.$product_id.'" class="col button button-big button-fill" style="padding: 0 5px; font-size: 16px; width: 100%;">Back to Product</a>';   
        }           

} // CREATE_PURCHASE_ORDER  

if(stristr($url, 'www.') === FALSE) {
    $url = "https://ketan.flickstree.com/affiliate_new/affiliate_speed/";
  }else{
    $url = "https://ketan.flickstree.com/affiliate_new/affiliate_speed/";
    
  }
$callback_url = $url."thank_you.php?partner=".$partner."&type=".$type."&product_id=".$product_id;

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Confirm Order</title>
    <link rel="shortcut icon" type="image/x-icon" href="images/logo/favicon.ico">
    <link href="js/vendor/bootstrap/bootstrap.min.css" rel="stylesheet">
    <link href="css/style-light.css" rel="stylesheet">
    <link href="fonts/icomoon/icomoon.css" rel="stylesheet">
    <link href="css/custom.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i,800,800i" rel="stylesheet">

    <!-- Global site tag (gtag.js) - Google Analytics -->

        <script async src="https://www.googletagmanager.com/gtag/js?id=UA-55810795-26"></script>

        <script>

        window.dataLayer = window.dataLayer || [];

        function gtag(){dataLayer.push(arguments);}

        gtag('js', new Date());

        gtag('config', 'UA-55810795-26');

        </script>

    <style>
        @media screen and (max-width: 991px){
            .col-auto.hdr-content-right {
                    margin-left: 80px;
                    line-height: 11px;
                }
                .footer-block {
                    margin: 0 -15px;
                    margin-bottom: 47px !important;
                } 
                .hdr-mobile-style2 .hdr-mobile .logo-holder {
                    margin-left: 15px;
                }
            }
            .hdr-desktop .row > *:last-child, .sticky-holder .row > *:last-child {
                    margin-left: unset;
                }
                @media screen and (max-width: 767px){
                  .shop-features-style4 .shop-feature-text .text1 {
                        font-size: 11px !important;
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
    </style>
</head>

<body class="is-dropdn-click">
    <div class="body-preloader">
        <div class="loader-wrap">
            <div class="dots">
                <div class="dot one"></div>
                <div class="dot two"></div>
                <div class="dot three"></div>
            </div>
        </div>
    </div>
<header class="hdr global_width hdr-style-3 hdr_sticky minicart-icon-style-4 hdr-mobile-style2" style="border: 1px solid #f7f7f7;">

            <div class="hdr-mobile show-mobile">
                <div class="hdr-content">
                    <div class="container">
                    <?php echo'<div class="logo-holder"><a href="products.php?partner=' . $partner . '&type=' . $type . '" class="logo"><img src="images/logo/shop369_logo.png" srcset="images/logo/shop369_logo.png" alt=""></a></div>';?>
                        <div class="col-auto hdr-content-right">
                                <div class="hdr-mobile-right">
                                    <div class="hdr-topline-right links-holder"><div class="dropdn dropdn_account only-icon"  style="text-align: center;">
                                            <?php
                                            if ($session_id) {
                                                echo '<a href="profile.php?partner=' . $partner . '&type=' . $type . '" style="font-size: 9px !important;color: #30282b;padding-right: 0px !important;"><i class="icon icon-person" style="font-size: 13px;"></i><br>' . strtoupper($user_name) . '<br>' . $user_mobile . '</a>';
                                            }
                                            ?>
                                        </div></div>

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
                            </div>';?>
                                <div class="col search-holder nav-holder" style="max-width: 61%;height: 28px;">
                                </div>
                                <?php
                                if ($session_id) {
                                    echo'<div class="col-auto minicart-holder">
                                                        <div class=""><a href="javascript:void(0);" class="minicart-link" onclick="profile()" style="text-decoration:none;"><i class="icon icon-person" style="text-align: center;"></i>  <span class="minicart-title" style="text-align: center;"><b>' . strtoupper($user_name) . '</b></span> <span class="minicart-title" style="text-align: center;">' . $user_mobile . '</span></a>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="sticky-holder compensate-for-scrollbar">
                    <div class="container">
                        <div class="row"></a>
                        <?php echo'<div class="col-auto logo-holder-s"><a href="products.php?partner=' . $partner . '&type=' . $type . '" class="logo"><img src="images/logo/shop369_logo.png" srcset="images/logo/shop369_logo.png" alt=""></a></div>';?>
                        
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
                <ul class="breadcrumbs">
                    
                </ul>
            </div>
        </div>

        <h2 id="note" style="color: #ff0000; font-size: 14px; letter-spacing: 0px;"><?php echo $errorMsg; ?></h2>
        <div class="holder mt-0">
            <div class="container">
                <div class="row prd-block prd-block--mobile-image-first js-prd-gallery" id="prdGallery100">
                    <div class="col-md-5 fixed-col fixed aside js-product-fixed-col">
                        <div class="fixed-col_container">
                            <div class="fstart"></div>
                            <div class="fixed-wrapper">
                                <div class="fixed-scroll">
                                    <div class="fixed-col_content">
                                        <div class="prd-block_info js-prd-m-holder mb-2 mb-md-0"></div>
                                        <div class="prd-block_main-image main-image--slide js-main-image--slide">
                                            <div class="prd-block_main-image-holder js-main-image-zoom" data-zoomtype="inner">
                                                <div class="prd-block_main-image-video js-main-image-video"><video loop muted preload="metadata" controls="controls">
                                                        <source src="#"></video>
                                                    <div class="gdw-loader"></div>
                                                </div>
                                                <div class="prd-has-loader">
                                                <div class="gdw-loader"></div><img src="<?php echo $product_images; ?>" class="zoom" alt="" data-zoom-image="<?php echo $product_images; ?>">
                                                </div>
                                                
                                            </div>
                                        
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="fend"></div>
                        </div>
                    </div>
                    <?php

                            if ($result_rate = @file_get_contents("https://www.flickstree.com/video_commerce/admin/product_rating.json")) {
                                $rating_array = json_decode($result_rate,true);
                            }
                            $rating= $rating_array[$product_id];

                        ?>
                    <div class="col-md-7 aside">
                        <div class="prd-block_info">
                            <div class="js-prd-d-holder prd-holder">
                                <div class="prd-block_title-wrap">
                                    <h1 class="prd-block_title"><?php echo $product_title; ?></h1>
                                    <div class="prd-block__labels"><span class="prd-label--new" style="background-color: #ffff00;border-color: #ffff00 !important;color: #000 !important;"><?php echo $offer;?>% Off</span></div>
                                </div>
                                <div class="prd-block_info-top">
                                        <div class="product-sku" style="font-size: 13px;">Product Code: <span><?php echo $product_code; ?></span></div>

                                        <div class="prd-availability" style="font-size: 13px;">Seller: <span><?php echo $product_sold_by; ?></span></div>
                                        <div class="prd-rating" style="font-size: 16px;font-weight: 700;"><a href="#" class="prd-review-link" style="color: #2b4f68;"><span>Rating: <?php echo $rating;?></span><i class="icon-star fill"></i></a></div>
                                    </div>
                                <div class="prd-block_description topline">
                                    <p><?php echo $product_short_description; ?></p>
                                </div>
                            </div>
                            <div class="prd-block_actions topline">
                                 <div class="prd-block_price"><span class="prd-block_price--actual" style="font-size: 23px;">&#8377;<?php echo $product_sales_price; ?> &nbsp; <span class="prd-block_price--old">&#8377;<?php echo $product_mrp; ?></span> &nbsp; <span style="font-size: 13px;font-weight: 400;"> You Save &#8377;<?php echo $save_price; ?></span> </span> </div>
                              

                            </div>
                            
                            <div class="topline">
                                <div class="table-responsive">
                                    <table class="table table-striped table-borderless">
                                        <tbody>
                                            <tr>
                                                <td>Quantity</td>
                                                <td><?php echo $_COOKIE["AFF_PRODUCT_QTY"]; ?></td>
                                            </tr>
                                            <tr>
                                                <td>Name</td>
                                                <td><?php echo $user_name;?></td>
                                            </tr>
                                            <tr>
                                                <td>Mobile</td>
                                                <td><?php echo $user_mobile;?></td>
                                            </tr>
                                            <tr>
                                                <td>Address</td>
                                                <td><?php echo $payee_account_address;?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                 <div class="hdr-content hide-mobile">
                                 <form method="POST" action="https://api.razorpay.com/v1/checkout/embedded">
                                  <input type="hidden" name="key_id" value="rzp_live_hT0Mud51WLKTVN">
                                  <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
                                  <input type="hidden" name="name" value="<?php echo $product_title; ?>">
                                  <input type="hidden" name="description" value="<?php echo $product_short_description; ?>">
                                  <input type="hidden" name="image" value="<?php echo $product_images; ?>">
                                  <input type="hidden" name="prefill[name]" value="<?php echo $user_name; ?>">
                                  <input type="hidden" name="prefill[contact]" value="<?php echo $user_mobile; ?>">
                                  <input type="hidden" name="prefill[email]" value="<?php echo $email; ?>">
                                  <input type="hidden" name="notes[shipping address]" value="<?php echo $payee_account_address; ?>">
                                  <input type="hidden" name="callback_url" value="<?php echo $callback_url; ?>">
                                  <div class="btn-wrap">
                                    <?php echo $processingBtn; ?>
                                </div>

                                </form> 
      
                                   </div>
                            </div>
                            <div  style="display: inline-flex;width:100%;">
                            <div class="prd-safecheckout topline" style="width:45%;">
                                    <div class="shop-features-style4">

                                <a href="#" class="shop-feature" style="border-color: #fff;">
                                    <div class="shop-feature-icon"><i class="icon-lock"></i></div>
                                    <div class="shop-feature-text">
                                        <div class="text1" style="font-size: 15px;">Secure Payment</div>
                                    </div>
                                </a>
                                
                            </div>
                                </div>
                                <div class="prd-safecheckout topline" style="width:55%;margin-top: 0px !important;padding-top: 0px !important;">
                                    <div class="shop-features-style4">

                                <a href="#" class="shop-feature" style="border-color: #fff;">
                                    <div class="shop-feature-icon"><i class="icon-call"></i></div>
                                    <div class="shop-feature-text">
                                        <div class="text1" style="font-size: 15px;">24/7 customer support</div>
                                    </div>
                                </a>
                                
                            </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>

                <div class="ymax"></div>
            </div>
            <div class="holder mt-5">
                <div class="container">
                   
                </div>
            </div>
        </div>
        
    </div>
    <footer class="page-footer footer-style-5 global_width @@classes" style="margin-top:0px;background-color: #161717">
            <div class="footer-top container">
                <div class="footer-block py-1 py-md-0 mt-0 text-center">
                    <div class="footer-menu">
                        <ul>
                            <li><a href="about.php" target="_blank">About Us</a></li>
                            <li><a href="cookie_policy.php" target="_blank">Cookie policy</a></li>
                            <li><a href="faq.php" target="_blank"  style="text-transform: none;">FAQs</a></li>
                            <li><a href="terms_and_condition.php" target="_blank">Terms And Conditions</a>
                            </li>
                            <li><a href="privacy_policy.php" target="_blank">Privacy Policy</a>
                            </li>
                        </ul>
                    </div>
                </div>

            </div>
            <div class="hdr-mobile show-mobile" style="position: fixed;height: 60px;left:0;bottom: 8px;float:left;width:100%;z-index: 100;">
                    <div class="footer-block py-1 py-md-0 mt-0 text-center">
                        <div class="footer-menu">
                            <div class="btn-wrap">
                                
                                  <div class="btn-wrap">
                                    <form method="POST" action="https://api.razorpay.com/v1/checkout/embedded">
                                  <input type="hidden" name="key_id" value="rzp_live_hT0Mud51WLKTVN">
                                  <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
                                  <input type="hidden" name="name" value="<?php echo $product_title; ?>">
                                  <input type="hidden" name="description" value="<?php echo $product_short_description; ?>">
                                  <input type="hidden" name="image" value="<?php echo $product_images; ?>">
                                  <input type="hidden" name="prefill[name]" value="<?php echo $user_name; ?>">
                                  <input type="hidden" name="prefill[contact]" value="<?php echo $user_mobile; ?>">
                                  <input type="hidden" name="prefill[email]" value="<?php echo $email; ?>">
                                  <input type="hidden" name="notes[shipping address]" value="<?php echo $payee_account_address; ?>">
                                  <input type="hidden" name="callback_url" value="<?php echo $callback_url; ?>">
                                  <div class="btn-wrap">
                                    <?php echo $processingBtn; ?>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
        </footer><a class="back-to-top js-back-to-top" href="#" title="Scroll To Top"><i class="icon icon-angle-up"></i></a>
   
    <script src="js/vendor/jquery/jquery.min.js"></script>
    <script src="js/vendor/bootstrap/bootstrap.bundle.min.js"></script>
    <script src="js/vendor/scrollLock/jquery-scrollLock.min.js"></script>
    <script src="js/vendor/imagesloaded/imagesloaded.pkgd.min.js"></script>
    <script src="js/vendor/ez-plus/jquery.ez-plus.min.js"></script>
    <script src="js/vendor/bootstrap-tabcollapse/bootstrap-tabcollapse.min.js"></script>
    <script src="js/vendor/fancybox/jquery.fancybox.min.js"></script>

    <script src="js/vendor/form/validator.min.js"></script>
    <script src="js/app.min.js"></script>
</body>
<script>

    $(function(){
    var calcNewYear = setInterval(function(){
        date_future = new Date(new Date().getFullYear() +1, 0, 1);
        date_now = new Date();

        seconds = Math.floor((date_future - (date_now))/1000);
        minutes = Math.floor(seconds/60);
        hours = Math.floor(minutes/60);
        days = Math.floor(hours/24);
        
        hours = hours-(days*24);
        minutes = minutes-(days*24*60)-(hours*60);
        seconds = seconds-(days*24*60*60)-(hours*60*60)-(minutes*60);

        $("#time").text("" + hours + "H " + minutes + "M " + seconds +"S");
    },1000);
});
 function profile() {
    var partner_id = '<?php echo $partner; ?>';
    var type = '<?php echo $type; ?>';
    window.location.href = 'profile.php?partner=' + partner_id + '&type=' + type;
}
        </script>
</html>
<?php } else { ?>
<!DOCTYPE html>
<html>
  <head>
  <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no, minimal-ui, viewport-fit=cover">
    <meta name="apple-mobile-web-app-capable" content="yes">
  <title>Incorrect Request</title>
  </head>
  <body>

  Incorrect Request!<br /><br /> Back to <a href="products.php?partner=<?php echo $partner; ?>&type=<?php echo $type; ?>">Products</a>
  </body>
</html>
<?php } ?>