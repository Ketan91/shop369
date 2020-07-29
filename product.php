<?php
include "functions.php"; // Get Configuration
$product_id = sanitize_string($_REQUEST['product_id']);
$partner = sanitize_string($_REQUEST['partner']);
$type = sanitize_string($_REQUEST['type']);
$url = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

# Influencer Commission
$inf_url = "https://api.flickstree.com/video_commerce/admin/utils/get_influencer_commission.php";
$result_inf_commission = $inf_url . "?publisher_id=" . $partner . "&influencer_id=" . $user_mobile . "&product_id=" . $product_id;
$influencer_comm = json_decode(file_get_contents($result_inf_commission), true);
$influencer_commission_value = $influencer_comm['influencer_commission'];
$influencer_commission_type = $influencer_comm['influencer_commission_type'];
if ($influencer_commission_type == 'percent') {

    $influencer_commission_value = $influencer_commission_value . '%';
} else {
    $influencer_commission_value = 'INR' . $influencer_commission_value;
}


if (empty($partner) || empty($type) || empty($product_id)) {
    header('location: https://www.shop369.org/products.php');
}

if (isset($_REQUEST['referer'])) {
    $referer = sanitize_string($_REQUEST['referer']);
    //$_SESSION["AFF_REFERER"] = $referer;
    setcookie("AFF_REFERER", $referer, time() + (86400 * 30), "/");
}


if (isset($_REQUEST['sub2'])) {
    $referer = sanitize_string($_REQUEST['sub2']);
    //$_SESSION["AFF_REFERER"] = $referer;
    setcookie("AFF_REFERER", $referer, time() + (86400 * 30), "/");
}

// Get & Set HTTP_REFERER
if (!isset($_COOKIE['AFF_HTTP_REFERER'])) {
    if (isset($_SERVER['HTTP_REFERER'])) {
        setcookie("AFF_HTTP_REFERER", $_SERVER['HTTP_REFERER'], time() + (86400 * 30), "/");
    }
} // AFF_HTTP_REFERER 



if (isset($_COOKIE["FTAFF_ID"], $_COOKIE["FTAFF_MOBILE"])) {
    $session_id = $_COOKIE["FTAFF_SESSIONID"];
    $user_id = $_COOKIE["FTAFF_ID"];
    $user_name = $_COOKIE["FTAFF_NAME"];
    $user_mobile = $_COOKIE["FTAFF_MOBILE"];
    $PAYID = $_COOKIE["FTAFF_PAYID"];


    $curl = curl_init($sapiUrl);

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



    $dataUserPayIdD = get_user_payid($_COOKIE["FTAFF_MOBILE"], $partner);

    if ($dataUserPayIdD['RESULT'] == "SUCCESS") {
        $payid = $dataUserPayIdD['RESPONSE']['payid'];

        setcookie("FTAFF_PAYID", $payid, time() + (86400 * 30), "/");

        $shareable_link = 'https://www.shop369.org/product.php?' . $urlParams . '&referer=' . $payid;

        $facebook_share = 'https://www.facebook.com/sharer/sharer.php?u=' . urlencode($shareable_link);
        $twitter_share = 'https://twitter.com/share?url=' . urlencode($shareable_link) . '&text=' . urlencode($product_title . ' - Buy this Now! ');
        $whatsapp_share = 'https://api.whatsapp.com/send?text=' . urlencode($product_title . ' - Buy this Now! ') . ' ' . urlencode($shareable_link);
        $copy_link = $shareable_link;
    } // get_user_payid
# USER_LOG
    $user_actual_mobile = $_COOKIE["FTAFF_MOBILE"];
    if ($result_log = @file_get_contents("https://api.flickstree.com/video_commerce/user/process_request.php?do_action=USER_LOG&user_id=$user_actual_mobile&product_id=$product_id&partner=$partner&type=$type&status=PRODUCT_PAGE")) {
        $log_array = json_decode($result_log, true);

        if ($log_array['RESULT'] == "SUCCESS") {
            
        } else {
            
        }
    } // USER_LOG
}



$curl = curl_init($sapiUrl); //product details

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
//echo "<pre>";
// print_r($result);exit;
foreach ($result as $response) {
    $product_title = $response[$product_id]['details']['product_title'];
    $product_description = $response[$product_id]['details']['product_description'];
    $product_short_description = $response[$product_id]['details']['product_short_description'];
    $product_code = $response[$product_id]['details']['product_code'];
    $product_type = $response[$product_id]['details']['product_type'];
    $affise_description = $response[$product_id]['details']['affise_description'];
    $product_images = $response[$product_id]['details']['product_images'][0];
    $product_description_bullets = $response[$product_id]['details']['product_description_bullets'];
    $product_testimonial = $response[$product_id]['details']['product_testimonial'];
    $product_tags = $response[$product_id]['details']['product_tags'];
    $product_tnc = $response[$product_id]['details']['product_tnc'];
    $product_sold_by = $response[$product_id]['details']['product_sold_by'];
    $affise_tracking_url = $response[$product_id]['details']['affise_tracking_url'];
    $product_mrp = $response[$product_id]['pricing']['pricing']['in']['product_mrp'];
    $product_sales_price = $response[$product_id]['pricing']['pricing']['in']['product_sales_price'];
    $product_images_count = $response[$product_id]['details']['product_images'];
    $product_sales_price = $response[$product_id]['pricing']['pricing']['in']['product_sales_price'];

    $save_price = $product_mrp - $product_sales_price;
}
$images_count = count($product_images_count);
$offer = round((($product_mrp - $product_sales_price) / $product_mrp) * 100);

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
$result_product_list = json_decode($curl_response, true);

$response_product_list = $result_product_list['RESPONSE'];

//s2s- affise postback
if (!empty($_REQUEST['clickid'])) {
    $clickid = sanitize_string($_REQUEST['clickid']);
    $sub1 = sanitize_string($_REQUEST['sub1']);
    $sub2 = sanitize_string($_REQUEST['sub2']);
    $pid = sanitize_string($_REQUEST['pid']);
    $offer_id = sanitize_string($_REQUEST['offer_id']);
    $advertiser_id = sanitize_string($_REQUEST['advertiser_id']);
    //$status = 1;

    $affise_postdata = "clickid=" . $clickid . "&sub1=" . $sub1 . "&sub2=" . $sub2 . "&pid=" . $pid . "&offer_id=" . $offer_id . "&advertiser_id=" . $advertiser_id;
    setcookie("affise_postdata", $affise_postdata, time() + (86400 * 30), "/");
} // //s2s- affise postback
?>
<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">
        <title>Product Detail -<?php echo $product_title; ?></title>
        <link rel="shortcut icon" type="image/x-icon" href="images/logo/favicon.ico">
        <!-- Vendor CSS -->
        <link href="js/vendor/bootstrap/bootstrap.min.css" rel="stylesheet">
        <link href="js/vendor/slick/slick.min.css" rel="stylesheet">
        <!--<link href="js/vendor/fancybox/jquery.fancybox.min.css" rel="stylesheet">-->
        <!--<link href="js/vendor/animate/animate.min.css" rel="stylesheet">-->
        <!-- Custom styles for this template -->
        <link async href="css/custom_style_light.css" rel="stylesheet">
        <!--icon font-->
        <link async href="fonts/icomoon/font.css" rel="stylesheet">
        <link async href="css/custom.css" rel="stylesheet">
        <!--custom font-->
        <!--        <link href="https://fonts.googleapis.com/css?family=Montserrat:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
                <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i,800,800i" rel="stylesheet">-->
        <!-- Global site tag (gtag.js) - Google Analytics -->

        <script async src="https://www.googletagmanager.com/gtag/js?id=UA-55810795-26"></script>

        <script async>

            window.dataLayer = window.dataLayer || [];

            function gtag() {
                dataLayer.push(arguments);
            }

            gtag('js', new Date());



            gtag('config', 'UA-55810795-26');



        </script>
        <style>
            @media screen and (max-width: 991px){
                .panel-body.js-tabcollapse-panel-body {
                margin-top: 10px;
            }
                .panel.panel-default {
                    border: 0;
                    background: transparent;
                    box-shadow: none;
                }
                .panel > .panel-heading {
                position: relative;
                border-width: 1px 0 1px 0;
                border-style: solid;
                border-color: #f7f7f7;
                background-color: transparent;
            }
                    .panel > .panel-heading:before {
                        position: absolute;
                        content: '';
                        top: 50%;
                        right: 0;
                        margin-top: -15px;
                        padding-left: 2px;
                        width: 30px;
                        height: 30px;
                        border-width: 1px;
                        border-style: solid;
                        border-color: #e2e2e2;
                        pointer-events: none;
                        transition: 0.2s;
                    }
                    .panel-title {
                        margin-bottom: 0;
                        font-size: 14px;
                        line-height: 24px;
                    }
                    .panel-heading a {
                        display: block;
                        padding: 13px 40px 13px 0;
                        color: #000;
                    }
                    .panel > .panel-heading:after {
                        position: absolute;
                        top: 50%;
                        right: 0;
                        margin-top: -14px;
                        padding-left: 2px;
                        width: 30px;
                        height: 30px;
                        content: "\e919";
                        text-align: center;
                        font-size: 14px;
                        font-family: 'icomoon';
                        line-height: 28px;
                        pointer-events: none;
                        transition: 0.2s;
                    }
                .prd-block_main-image-video {
                    z-index: 99;
                }
                .col-auto.hdr-content-right {
                    margin-left: 80px;
                    line-height: 11px;
                }
                div#page_detail {
                    margin-top: 60px !important;
                }
                .hdr-mobile-style2 .hdr-mobile.is-sticky {
                    position: fixed;
                    top: 9px;
                }
                .prd-grid:not([class*='arrows-']) .slick-prev {
                    right: 333px !important;
                }
                div#video_holder {
                     margin-top: 58px !important;
                }
                .footer-block {
                    margin: 0 -15px;
                    margin-bottom: 94px !important;
                } 
                video#video_div {
                    height: 180px !important;
                }
                span#close_modal {
                    margin-right: 0px !important;
                }
/*                .modal-body {
                    height: 185px !important;
                }*/
                iframe#iframe_height {
                    height: 190px !important;
                }
               
            }
            .form-control:disabled, .form-control[readonly] {
                background-color: #dddddd;
                opacity: 1;
            }
            select.minimal {
                background-image:
                    linear-gradient(45deg, transparent 50%, gray 50%),
                    linear-gradient(135deg, gray 50%, transparent 50%),
                    linear-gradient(to right, #ccc, #ccc);
                background-position:
                    calc(100% - 20px) calc(1em + 2px),
                    calc(100% - 15px) calc(1em + 2px),
                    calc(100% - 2.5em) 0.5em;
                background-size:
                    5px 5px,
                    5px 5px,
                    1px 1.5em;
                background-repeat: no-repeat;
            }

            select.minimal:focus {
                background-image:
                    linear-gradient(45deg, green 50%, transparent 50%),
                    linear-gradient(135deg, transparent 50%, green 50%),
                    linear-gradient(to right, #ccc, #ccc);
                background-position:
                    calc(100% - 15px) 1em,
                    calc(100% - 20px) 1em,
                    calc(100% - 2.5em) 0.5em;
                background-size:
                    5px 5px,
                    5px 5px,
                    1px 1.5em;
                background-repeat: no-repeat;
                outline: 0;
            }

            ul#tick {
                list-style: none;
            }

            ul#tick  li:before {
                content: '✓';
            }
            .hdr {
                border-color: #fff;
            }
            .header {
                height: 18px !important;
            }
            #divLoading{display:none}
            #divLoading{
                position:fixed;
                z-index:100;
                left:0;bottom:0;right:0;top:200px;z-index:999
            }
            iframe#iframe_height {
                height: 450px;
            }
            .modal-body {
                height: 450px;
            }
            .close {
                float: right;
                font-size: 1.7rem;
                font-weight: 700;
                line-height: 1;
                color: #fff;
                text-shadow: 0 1px 0 #fff;

            }
            span#close_modal {
                margin-right: 255px;
            }
/*            li#img_width {
                width: 79px !important;
            }*/
         .prd-carousel .prd {
            margin-top: 8px !important;
        }
        .prd-grid:not([class*='arrows-']) .slick-prev {
                right: 389px;
                top:50px;
        }
        .prd-grid:not([class*='arrows-']) .slick-arrow {
            top: 50px;
           
            left: auto;
        }
        div#video_holder {
                     margin-top: 38px;
        }
        .modal-body {
        position: relative;
        overflow-y: auto;
        padding: 15px;
        height: auto;
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
        <div class="loader-wrap" id="divLoading" style="display: none;">
            <div class="dots">
                <div class="dot one"></div>
                <div class="dot two"></div>
                <div class="dot three"></div>
            </div>
        </div>

        <header class="hdr global_width hdr-style-3 hdr_sticky minicart-icon-style-4 hdr-mobile-style2" style="border: 1px solid #f7f7f7;">
            <div class="header">
                <div id="header-sroll" style="z-index: 100;"> <h1 style="color: #ff8000">Sales Ends In <span style="color: #ff8000" id="time" class="timmer"></span></h1></div>
            </div>
            <!-- Mobile Menu -->
            <div class="mobilemenu js-push-mbmenu"  id="clmenu">
                <div class="mobilemenu-content">
                    <div class="mobilemenu-close mobilemenu-toggle"  id="closemenu">CLOSE</div>
                    <div class="mobilemenu-scroll">
                        <div class="mobilemenu-search"></div>
                        <div class="nav-wrapper show-menu">
                            <div class="nav-toggle"><span class="nav-back"><i class="icon-arrow-left"></i></span> <span class="nav-title"></span></div>
                            <ul class="nav nav-level-1">
                                <li><a >Category</a><span class="arrow"></span>
                                    <ul class="nav-level-2">
                                        <?php
                                        $category_array = [];
                                        foreach ($response_product_list as $index) {
                                            $product_category = $index['details']['product_category'];
                                            $category_array[] = $product_category;
                                        }
                                        $all_categories = array_values(array_unique($category_array));
                                        for ($j = 0; $j < count($all_categories); $j++) {
                                            $categories = $all_categories[$j];
                                            ?>

                                            <li>
                                                <a href="javascript:void(0);" onclick="get_category_product('<?php echo $categories ?>');"><?php echo $categories ?></a>
                                            </li>
                                            <input type="hidden" id="hdn_category">
                                            <?php
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
                                    <div class="hdr-mobile show-mobile" style="margin-top: 30px;">
                                        <div class="hdr-content">
                                            <div class="container">
                                                <!-- Menu Toggle -->
                                                <!-- <div class="menu-toggle"><a href="#" class="mobilemenu-toggle"><i class="icon icon-menu"></i></a></div> -->
                                                <!-- /Menu Toggle -->
                                                <?php echo'<div class="logo-holder"><a href="products.php?partner=' . $partner . '&type=' . $type . '" class="logo"><img src="images/logo/shop369_logo.png" srcset="images/logo/shop369_logo.png" alt=""></a></div>'; ?>
                                                <div class="col-auto hdr-content-right">
                                                    <div class="hdr-mobile-right">
                                                        <div class="hdr-topline-right links-holder"><div class="dropdn dropdn_account only-icon"  style="text-align: center;">
                                                                <?php 
                                                                if($session_id){
                                                                   echo '<a href="profile.php?partner=' . $partner . '&type=' . $type . '" style="font-size: 9px !important;color: #30282b;padding-right: 0px !important;"><i class="icon icon-person" style="font-size: 13px;"></i><br>' . strtoupper($user_name) . '<br>' . $user_mobile . '</a>';
                                                                 
                                                                }
                                                                ?>
                                                            </div></div>

                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="hdr-desktop hide-mobile" style="margin-top: 30px;">

                                        <div class="hdr-content hide-mobile">
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col logo-holder">
                                                        <?php echo'<div class="menu-toggle hide-mobile"></div><a href="products.php?partner=' . $partner . '&type=' . $type . '" class="logo"><img src="images/logo/shop369_logo.png" srcset="images/logo/shop369_logo.png" alt=""></a>
                                                    </div>'; ?>
                                                        <div class="col search-holder nav-holder" style="max-width: 61%;height: 28px;">
                                                            <!-- Header Search -->
                                                            <!-- <form action="#" class="search" style="border: 1px solid #00000026;padding: 2px 0 2px 0px;height: 51px;">
                                                                <p class="search-button"><i class="icon-search2"></i>
                                                                </p> 
                                                                <input type="text" class="search-input" placeholder="search keyword" id="search_title" onkeyup="desktopsearch();" style="font-size: 17px;">
                                                            </form> -->
                                                            <!-- /Header Search -->
                                                        </div>
                                                        <?php
                                                        if($session_id){
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
        <div class="page-content" style="margin-top: 20px;">
                                            <div class="holder mt-0">
                                                <div class="container">
                                                    <ul class="breadcrumbs">

                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="holder mt-0" id="page_detail">
                                                <div class="container">
                                                    <div class="row prd-block prd-block--mobile-image-first prd-block--prv-left js-prd-gallery" id="prdGallery100">
                                                        <div class="col-md-6 col-xl-5">
                                                            <div class="prd-block_info js-prd-m-holder mb-2 mb-md-0"></div><!-- Product Gallery -->
                                                            <div class="prd-block_main-image main-image--slide js-main-image--slide">
                                                                <div class="prd-block_main-image-holder js-main-image-zoom" data-zoomtype="inner">
                                                                    <div class="prd-block_main-image-video js-main-image-video"><video loop muted preload="metadata" controls="controls">
                                                                            <source src="#"></video>
                                                                        <div class="gdw-loader"></div>
                                                                    </div>
                                                                    <div class="prd-has-loader">
                                                                        <div class="gdw-loader"></div><img src="<?php echo $product_images; ?>" class="zoom" alt="" data-zoom-image="<?php echo $product_images; ?>" id="main_image">
                                                                        
                                                                    </div>
                                                                    <?php
                                                                    if ($images_count > 1) {
                                                                        ?>
                                                                        <div class="prd-block_main-image-next slick-next js-main-image-next" id="Next">NEXT</div>
                                                                        <div class="prd-block_main-image-prev slick-prev js-main-image-prev" id="Prev">PREV</div>
                                                                        <?php
                                                                    } else {
                                                                        
                                                                    }
                                                                    ?>
                                                                    <!--<div class="prd-block_main-image-links"><a  data-width="900" data-toggle="modal" data-target="#sell-modal" class="prd-block_video-link"><i class="icon icon-play"></i></a> </div>-->
                                                                </div>
                                                            </div>
                                                            <div class="product-previews-wrapper" style="height:600px;">
                                                                <div class="product-previews-carousel" id="previewsGallery100">
                                                                    <?php
                                                                    for ($v = 0; $v < count($result); $v++) {
                                                                        $product_video = $result['RESPONSE'][$product_id]['videos'][0]['video_s3_url'];
                                                                        $product_video_watch_url = $result['RESPONSE'][$product_id]['videos'][0]['video_watch_url'];
                                                                        $product_video_poster = $result['RESPONSE'][$product_id]['videos'][0]['video_poster_url'];
                                                                    }

                                                                    for ($i = 0; $i < count($result); $i++) {
                                                                        $product_img = $result['RESPONSE'][$product_id]['details']['product_images'][$i];
                                                                        $left_img = "https://api.flickstree.com/resize_img.php?url=" . $result['RESPONSE'][$product_id]['details']['product_images'][$i] . "&width=82&height=146";
                                                                       if ($result['RESPONSE'][$product_id]['details']['product_images'][$i]) {
                                                                        echo '<a href="#" data-value="Silver" data-image="' . $product_img . '" data-zoom-image="' . $product_img . '">
                                                                                                                <img src="' . $left_img . '" alt="">
                                                                                                              </a>';
                                                                        }
                                                                    }
                                                                    if ($result['RESPONSE'][$product_id]['videos']) {
                                                                        if($product_video){
                                                                          echo '<a href="#" data-video="' . $product_video . '">
                                                                                <img src="' . $product_video_poster . '" alt="">
                                                                               </a>';
                                                                          
                                                                        }
                                                                       
                                                                    }
                                                                    ?>

                                                                </div>
                                                            </div>
                                                        </div>
                                                       
                                                        <?php
                                                        # Product Rating
                                                        $rating_url = "https://api.flickstree.com/video_commerce/admin/utils/get_product_rating.php";
                                                        $result_rate = $rating_url . "?product_id=" . $product_id;
                                                        $rating = json_decode(file_get_contents($result_rate), true);
                                                        ?>

                                                        <div class="col-md">
                                                            <div class="prd-block_info" style="margin-top: 15px;">
                                                                <div class="js-prd-d-holder prd-holder">
                                                                    <div class="prd-block_title-wrap">
                                                                        <h2 class="prd-block_title" style="overflow-wrap: anywhere !important;"><?php echo $product_title; ?></h2>
                                                                        <?php
                                                                        if ($product_mrp) {
                                                                            ?>
                                                                            <div class="prd-block__labels"><span class="prd-label--new" style="background-color: #ffff00;border-color: #ffff00 !important;color: #000 !important;font-size: 14px;"><?php echo $offer; ?>% Off</span></div>

                                                                        </div>
                                                                        <div class="prd-block_info-top">
                                                                            <div class="product-sku" style="font-size: 13px;">Product Code: <span><?php echo $product_code; ?></span></div>

                                                                            <div class="prd-availability" style="font-size: 13px;">Seller: <span><?php echo $product_sold_by; ?></span></div>
                                                                            <div class="prd-rating" style="font-size: 16px;font-weight: 700;"><a href="#" class="prd-review-link" style="color: #2b4f68;"><span>Rating: <?php echo $rating; ?></span><i class="icon-star fill"></i></a></div>
                                                                        </div>
                                                                        <?php
                                                                    } else {
                                                                        ?>

                                                                        <div class="prd-block__labels"></div>

                                                                    </div>
                                                                    <div class="prd-block_info-top">
                                                                        <div class="prd-rating" style="font-size: 16px;font-weight: 700;"><span>Rating: <?php echo $rating; ?></span><i class="icon-star fill"></i></a></div>

                                                                        <div class="prd-availability" style="font-size: 13px;"></div>
                                                                        <div class="prd-rating" style="font-size: 16px;font-weight: 700;"></div>
                                                                    </div>

                                                                    <?php
                                                                }
                                                                if ($product_short_description) {
                                                                    ?>
                                                                    <div class="prd-block_description topline">
                                                                        <p><?php echo $product_short_description; ?></p>
                                                                    </div>
                                                                    <?php
                                                                } else {
                                                                    ?>
                                                                    <div class="prd-block_description topline">
                                                                        <p><?php echo strip_tags($affise_description); ?></p>
                                                                    </div>
                                                                    <?php
                                                                }
                                                                ?>
                                                            </div>

                                                            <div class="prd-block_actions topline">

                                                                <?php
                                                                if ($product_sales_price) {
                                                                    ?>
                                                                    <div class="prd-block_price"><span class="prd-block_price--actual">&#8377;<?php echo $product_sales_price; ?></span> <span class="prd-block_price--old">&#8377;<?php echo $product_mrp; ?></span> You Save &#8377;<?php echo $save_price; ?></div>
                                                                    <?php
                                                                } else {
                                                                    ?>

                                                                    <div class="prd-block_price"><span class="prd-block_price--actual"></span> <span class="prd-block_price--old"></div>

                                                                    <?php
                                                                }
                                                                ?>

                                                                <div class="hdr-content hide-mobile" style="display: inline-flex;">
                                                                    <?php
                                                                    if ($type == 'buyer') {
                                                                        if ($session_id) {
                                                                            if ($product_type == 'External') {
                                                                                $traking_url = $affise_tracking_url . '&sub1=' . $partner . '&sub2=' . $PAYID;
                                                                                echo "<div class='btn-wrap'><a href='" . $traking_url . "' class='btn btn--add-to-cart'   style='padding: 11px 14px 11px 14px !important;background-color: #2b4f68;'><i class='icon icon-share'></i><span>Avail the Offer</span></a>
                                                                         </div>";
                                                                            } else {
                                                                                echo "<div class='btn-wrap'><button class='btn btn--add-to-cart'  onclick='proceedToPay()' style='padding: 11px 14px 11px 14px !important;background-color: #2b4f68;'><i class='icon icon-share'></i><span>Proceed To Pay</span></button>
                                                                         </div>";
                                                                            }

                                                                            echo '<div class="prd-block_link"><a onclick="shareModal()" class="icon-share"></a></div>';
                                                                        } else {
                                                                            echo '<div class="btn-wrap"><button class="btn btn--add-to-cart" data-toggle="modal" data-target="#signupBox" style="padding: 11px 14px 11px 14px !important;background-color: #2b4f68;"><i class="icon icon-handbag"></i><span>Buy Now!</span></button></div>';

                                                                            echo '<div class="prd-block_link"><a onclick="shareModal()" class="icon-share"></a></div>';
                                                                        }
                                                                    } else {
                                                                        if ($session_id) {
                                                                            echo "<div class='btn-wrap'><button class='btn btn--add-to-cart' onclick=sellModal('" . $partner . "','" . $product_id . "','" . $product_sales_price . "','" . $PAYID . "') style='padding: 11px 14px 11px 14px !important;background-color: #2b4f68;'><i class='icon icon-share'></i><span>Share And Earn</span></button>
                                                                        </div>";
                                                                        } else {

                                                                            echo '<div class="btn-wrap"><button class="btn btn--add-to-cart" data-toggle="modal" data-target="#signupBox" style="padding: 11px 14px 20px 14px !important;background-color: #2b4f68;"><i class="icon icon-share"></i><span>Share And Earn</span></button></div>';
                                                                        }
                                                                    }
                                                                    ?>
                                                                </div>
                                                            </div>


                                                        </div>
                                                    </div>
                                                    <div class="col-xl-3 mt-3 mt-xl-0 sidebar-product">
                                                        <div class="shop-features-style4"><a href="javascript:void(0)" class="shop-feature">
                                                                <div class="shop-feature-icon"><i class="icon-box3"></i></div>
                                                                <div class="shop-feature-text">
                                                                    <div class="text1">Genuine Product</div>
                                                                </div>
                                                            </a><a href="javascript:void(0)" class="shop-feature">
                                                                <div class="shop-feature-icon"><i class="icon-lock"></i></div>
                                                                <div class="shop-feature-text">
                                                                    <div class="text1">Secure Payment</div>
                                                                </div>
                                                            </a><a href="javascript:void(0)" class="shop-feature">
                                                                <div class="shop-feature-icon"><i class="icon-call"></i></div>
                                                                <div class="shop-feature-text">
                                                                    <div class="text1">24/7 customer support</div>
                                                                </div>
                                                            </a></div>

                                                    </div>

                                                </div>
                                            </div>

                                            <?php
                                            if ($result['RESPONSE'][$product_id]['videos']) {
                                                ?>
                                                <div class="holder" id="video_holder">
                                                    <div class="container" >
                                                        
                                                        <div class="row vert-margin-double">
                                                            <div class="col-md-1" style="margin-left: -5px;"></div>
                                                            <div class="col-md-4">
                                                            
                                                            <div class="prd-grid prd-text-center prd-carousel js-prd-carousel data-to-show-2" >
                                                                <?php
                                                                $video_count = count($result['RESPONSE'][$product_id]['videos']);
                                                            for ($i = 0; $i < count($result['RESPONSE'][$product_id]['videos']); $i++) {
                                                                $video_id = $result['RESPONSE'][$product_id]['videos'][$i]['video_poster_url'];
                                                                $product_video_poster_url = $result['RESPONSE'][$product_id]['videos'][$i]['video_poster_url'];
                                                                $product_video_watch_url = $result['RESPONSE'][$product_id]['videos'][$i]['video_watch_url'];
                                                                $product_video_s3_url = $result['RESPONSE'][$product_id]['videos'][$i]['video_s3_url'];
                                                                $product_video_poster_url = "https://api.flickstree.com/resize_img.php?url=" . $product_video_poster_url . "&width=165";
                                                                
                                                                if($product_video_watch_url){
                                                                     echo "<div class='prd prd-has-loader'>
                                                                    <div class='prd-inside'>
                                                                        <div class='prd-img-area'>
                                                                            <a href='javascript:void(0);' onclick=showModal('" . $product_video_watch_url . "','video_watch_url') class='prd-img'>
                                                                               <img src='" . $product_video_poster_url . "'  alt='Toilet Shelves' class='js-prd-img lazyload'>
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                </div>";
                                                                }
                                                                
                                                                    }
                                                            ?>
                                                            </div>
                                                        </div>
                                                      </div>
                                                        
                                                    </div>
                                                </div>

                                                <?php
                                            }
                                            ?>
                                            <div class="modal fade" id="video-modal" >
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true" id="close_modal">&times;</span>
                                                </button>
                                                <div class="modal-dialog" role="document" style="max-width: 800px !important;">
                                                    <div class="modal-content" style="padding: 0px 0px !important;">

                                                        <div class="modal-body" style="padding: 0px !important;">
                                                            <?php
                                                            if ($product_video) {
                                                                echo '<video  controls="controls" style="width: 100%;" id="video_div">
                                                                                                <source src=" "  type="video/mp4">
                                                                                           </video>';
                                                            } else {
                                                                echo "<iframe src=''  width='100%'  frameborder='0' allow='autoplay; encrypted-media' allowfullscreen   id='iframe_height'></iframe>";
                                                            }
                                                            ?>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div> 
                                            <div class="holder mt-5">
                                                <div class="container" style="margin-bottom: 20px;">
                                                    <!-- Nav tabs -->
                                                    <?php
                                                    if ($type == 'buyer') {
                                                        if ($product_type == 'External') {
                                                            
                                                        } else {

                                                            echo '<ul class="nav nav-tabs product-tab">
                                                                            <li class="nav-item"><a href="#Tab1" class="nav-link" data-toggle="tab" style="color:#2b4f68;">Description</a></li>
                                                                            <li class="nav-item"><a href="#Tab2" class="nav-link" data-toggle="tab" style="color:#2b4f68;">Terms & Conditions</a></li>
                                                                            <li class="nav-item"><a href="#Tab3" class="nav-link" data-toggle="tab" style="color:#2b4f68;">Reviews</a></li>
                                                                            <li class="nav-item"><a href="#Tab4" class="nav-link" data-toggle="tab" style="color:#2b4f68;">Tags</a></li>                       
                                                                                </ul>';
                                                        }
                                                    }


                                                    if ($type == 'seller') {

                                                        if ($product_type == 'External') {

                                                            echo '<ul class="nav nav-tabs product-tab"><li class="nav-item"><a href="#Tab5" class="nav-link" data-toggle="tab" style="color:#2b4f68;">Affise Description</a></li>
                                                                                </ul>';
                                                        } else {
                                                            echo '<ul class="nav nav-tabs product-tab">';

                                                            if ($affise_description) {
                                                                echo '<li class="nav-item"><a href="#Tab5" class="nav-link" data-toggle="tab" style="color:#2b4f68;">Affise 
                                                                                Description</a></li>
                                                                                <li class="nav-item"><a href="#Tab1" class="nav-link" data-toggle="tab" style="color:#2b4f68;">Description</a></li>
                                                                                <li class="nav-item"><a href="#Tab2" class="nav-link" data-toggle="tab" style="color:#2b4f68;">Terms & Conditions</a></li>
                                                                                <li class="nav-item"><a href="#Tab3" class="nav-link" data-toggle="tab" style="color:#2b4f68;">Reviews</a></li>
                                                                                <li class="nav-item"><a href="#Tab4" class="nav-link" data-toggle="tab" style="color:#2b4f68;">Tags</a></li>';
                                                            } else {
                                                                echo '<li class="nav-item"><a href="#Tab1" class="nav-link" data-toggle="tab" style="color:#2b4f68;">Description</a></li>
                                                                                <li class="nav-item"><a href="#Tab2" class="nav-link" data-toggle="tab" style="color:#2b4f68;">Terms & Conditions</a></li>
                                                                                <li class="nav-item"><a href="#Tab3" class="nav-link" data-toggle="tab" style="color:#2b4f68;">Reviews</a></li>
                                                                                <li class="nav-item"><a href="#Tab4" class="nav-link" data-toggle="tab" style="color:#2b4f68;">Tags</a></li>';
                                                            }

                                                            echo'</ul>';
                                                        }
                                                    }
                                                    ?>              




                                                    <div class="tab-content">
                                                        <div role="tabpanel" class="tab-pane fade" id="Tab1">

                                                            <?php
                                                            echo '<p>' . $product_description . '</p>';

                                                            if (count($result['RESPONSE'][$product_id]['details']['product_description_bullets'][0]) > 0) {
                                                                echo '<h5>What will you learn</h5>
                                                        <ul  class="" id="tick">';
                                                                for ($j = 0; $j < count($result); $j++) {
                                                                    $product_description_bullets = $result['RESPONSE'][$product_id]['details']['product_description_bullets'][$j];
                                                                    echo '<li>&nbsp;&nbsp;' . $product_description_bullets . '</li>';
                                                                }
                                                                echo '</ul>';
                                                            } else {
                                                                
                                                            }
                                                            ?>
                                                            <br><br><br><br>
                                                        </div>
                                                        <div role="tabpanel" class="tab-pane fade" id="Tab5">

                                                            <?php
                                                            echo '<p>' . $affise_description . '</p>';
                                                            ?>
                                                           
                                                        </div>
                                                        <div role="tabpanel" class="tab-pane fade" id="Tab2">
                                                            <div class="mt-3"></div>
                                                            <div class="row">

                                                                <div class="col-sm-12 mt-3 mt-sm-0">
                                                                    <ul class="list list--marker-squared">
                                                                        <li><?php echo $product_tnc; ?></li>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div role="tabpanel" class="tab-pane fade" id="Tab3">
                                                            <div id="productReviews">
                                                                <?php
                                                                for ($k = 0; $k < count($result); $k++) {

                                                                    $testimonial_given_by = $result['RESPONSE'][$product_id]['details']['product_testimonial'][$k]['testimonial_given_by'];
                                                                    $testimonial_text = $result['RESPONSE'][$product_id]['details']['product_testimonial'][$k]['testimonial_text'];
                                                                    $testimonial_rating = $result['RESPONSE'][$product_id]['details']['product_testimonial'][$k]['testimonial_rating'];
                                                                    if (count($testimonial_given_by) > 0) {

                                                                        echo '<div class="review-item" style="margin-top: 0px;";><img src="images/user.png" style="width:30px;height:30px;">
                                                                                                        <h4 class="review-item_author" style="display: initial;">' . $testimonial_given_by . '</h4>
                                                                                                        <div class="review-item_rating" style="font-size: 18px;"><b>' . $testimonial_rating . '</b><i class="icon-star fill" style="font-size: 22px;"></i></div>
                                                                                                        <p>' . $testimonial_text . '</p>
                                                                                                        </div>';
                                                                    }
                                                                }
                                                                ?>

                                                            </div>
                                                        </div>
                                                        <div role="tabpanel" class="tab-pane fade" id="Tab4">
                                                            <ul class="tags-list">
                                                                <?php
                                                                for ($h = 0; $h < count($result); $h++) {
                                                                    $tags = $result['RESPONSE'][$product_id]['details']['product_tag'][$h];
                                                                    if (count($tags) > 0) {
                                                                        echo '<li><a href="#">' . $tags . '</a></li>';
                                                                    }
                                                                }
                                                                ?>
                                                            </ul>
                                                        </div>

                                                        <div role="tabpanel" class="tab-pane fade" id="Tab5">       
                                                            <h3 class="custom-color">How to buy</h3>
                                                            <div class="row">
                                                                <div class="col-sm-6">

                                                                </div>
                                                                <div class="col-sm-6 mt-3 mt-sm-0">
                                                                    <p>But I must explain to you how all this mistaken idea of denouncing pleasure and praising pain was born and I will give you a complete account of the system, and expound the actual teachings of the great explorer of the truth, the master-builder of human happiness. No one rejects, dislikes, or avoids pleasure itself, because it is pleasure, but because those who do not know how to pursue pleasure rationally encounter consequences that are extremely painful</p>
                                                                    <ul class="list list--marker-squared">
                                                                        <li>Nam liberempore</li>
                                                                        <li>Cumsoluta nobisest</li>
                                                                        <li>Eligendptio cumque</li>
                                                                        <li>Nam liberempore</li>
                                                                        <li>Cumsoluta nobisest</li>
                                                                    </ul>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                        if ($type == 'buyer' && $product_type != 'External') {
                                            if (isset($_COOKIE["FTAFF_ID"], $_COOKIE["FTAFF_MOBILE"])) {
                                                ?>
                                                <div class="holder mt-0" id="order_details">
                                                    <div class="container">
                                                        <div class="row justify-content-center">
                                                            <div class="col-sm-12 col-md-12">
                                                                <h2 class="text-center">Order Details</h2>
                                                                <div class="form-wrapper">
                                                                    <div class="alert alert-danger alert-email" style="display:none;"></div>
                                                                    <form action="#">
                                                                        <div class="row">
                                                                            <div class="col-sm-6">
                                                                                <div class="control-group form-group">
                                                                                    <div class="controls">
                                                                                        <label><b>Quantity</b></label>
                                                                                        <select name="ge_quantity" id="ge_quantity" placeholder="Select Quantity" class="form-control minimal" style="height: calc(2.45rem + 2px);" onchange="quantityPrice(this.value);">

                                                                                            <option value="1">1</option>
                                                                                            <option value="2">2</option>
                                                                                            <option value="3">3</option>
                                                                                            <option value="4">4</option>
                                                                                            <option value="5">5</option>
                                                                                            <option value="6">6</option>
                                                                                            <option value="7">7</option>
                                                                                            <option value="8">8</option>
                                                                                            <option value="9">9</option>
                                                                                            <option value="10">10</option>

                                                                                        </select>
                                                                                    </div>
                                                                                </div> 
                                                                            </div>
                                                                            <div class="col-sm-6">
                                                                                <div class="form-group">
                                                                                    <label><b>Product Price</b></label>
                                                                                    <?php echo '<input type="text" id="ge_amount" class="form-control" placeholder="Amount" value="' . $product_sales_price . '" readonly="">'; ?>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-sm-6">
                                                                                <div class="form-group">
                                                                                    <label><b>Name</b></label>
                                                                                    <?php echo'<input type="text" id="ge_username" class="form-control" placeholder="Name" value="' . $user_name . '">'; ?>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-sm-6">
                                                                                <div class="form-group">
                                                                                    <label><b>Email</b></label>
                                                                                    <?php echo'<input type="email" id="ge_email" class="form-control" placeholder="Email" value="' . $email . '">'; ?>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <div class="row">

                                                                                <div class="col-sm-6">
                                                                                    <label><b>Mobile</b></label>
                                                                                    <div class="form-group">

                                                                                        <?php echo '<input type="tel" placeholder="Mobile No.*" name="ge_mobile" id="ge_mobile" class="form-control" required=""  value="' . $user_mobile . '" readonly="">'; ?>

                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-sm-6">
                                                                                    <div class="form-group">
                                                                                        <label><b>Address</b></label>
                                                                                        <?php echo '<textarea  id="ge_address" class="form-control textarea--height-100" placeholder="Address">' . $payee_account_address . '</textarea>'; ?>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                    </form>
                                                                    <div class="hdr-content hide-mobile">
                                                                        <?php
                                                                        $traking_url = $affise_tracking_url . '&sub1=' . $partner . '&sub2=' . $PAYID;

                                                                        if ($product_type == 'External') {
                                                                            echo '<div class="text-center"><a class="btn" href="' . $traking_url . '" style="background-color: #2b4f68;">Avail the Offer</a></div>';
                                                                        } else {
                                                                            echo '<div class="text-center"><button class="btn" onclick="proceedToPay()" style="background-color: #2b4f68;">Proceed to pay</button></div>';
                                                                        }
                                                                        ?>

                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php
                                            }
                                        }
                                        ?>

                                    </div>
                                    <br>
                                    <br>
                                    <br>

                                    <input type="hidden" id="hdn_productid" value="<?php echo $product_id; ?>">
                                    <input type="hidden" id="hdn_partner" value="<?php echo $partner; ?>">
                                    <input type="hidden" id="hdn_type" value="<?php echo $type; ?>">
                                    <input type="hidden" id="hdn_product_type" value="<?php echo $product_type; ?>">
                                    <input type="text" id="just_for_copy" style="position:absolute; left: -9999px;" />

                                    <!-----------    signupBox start       ------------->

                                    <div class="modal fade" id="signupBox" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                                         aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="exampleModalLabel">Sign up Now!</h5>

                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">

                                                    <div class="container">
                                                        <div class="alert alert-danger alert-signup" style="display:none;"></div>
                                                        <form class="form-horizontal" id="sign_up_form">
                                                            <div class="control-group form-group">
                                                                <div class="controls">
                                                                    <label><b>Name</b> <span class="text-danger">*</span></label>
                                                                    <input type="text" placeholder="Name" class="form-control" name="su_username" id="su_username" required="">
                                                                    <p class="help-block"></p>
                                                                </div>
                                                            </div>

                                                            <div class="row">
                                                                <div class="control-group form-group col-md-12">
                                                                    <label><b>Mobile No.</b><span class="text-danger">*</span></label>
                                                                    <div class="controls" style="display: inline-flex;width: 100%;">
                                                                        <div class="item-input-wrap" style="width: 20%; display: inline-block;">
                                                                            <select name="su_country_code" id="su_country_code" class="form-control" style="padding: .375rem .2rem;height: 40px !important;">
                                                                                <option value="91" selected="">+91</option>
                                                                            </select>
                                                                        </div>
                                                                        <div class="item-input-wrap" style="width: 80%; display: inline-block;">
                                                                            <!--<input type="tel" placeholder="Mobile No.*" name="su_mobile" id="su_mobile" class="form-control" required="">-->
                                                                            <input type="number" name="su_mobile" id="su_mobile" class="form-control" placeholder="Mobile No" autocomplete="off" oninput="this.value = this.value.replace(/[^0-9.]/g, ''); this.value = this.value.replace(/(\..*)\./g, '$1');"  required="">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="control-group form-group">
                                                                <div class="controls">
                                                                    <label><b>Email</b> <span class="text-danger">*</span></label>
                                                                    <input type="email" placeholder="E-mail" name="su_email" id="su_email" class="form-control">
                                                                </div>
                                                            </div>
                                                            <div class="control-group form-group">
                                                                <div class="controls">
                                                                    <label><b>Birth Year</b> </label>
                                                                    <select name="su_yob" id="su_yob" placeholder="Select Birth Year" class="form-control" style="height: calc(2.45rem + 2px);">
                                                                        <option value="" selected="">Select Birth Year</option>
                                                                        <option value="1950">1950</option>
                                                                        <option value="1951">1951</option>
                                                                        <option value="1952">1952</option>
                                                                        <option value="1953">1953</option>
                                                                        <option value="1954">1954</option>
                                                                        <option value="1955">1955</option>
                                                                        <option value="1956">1956</option>
                                                                        <option value="1957">1957</option>
                                                                        <option value="1958">1958</option>
                                                                        <option value="1959">1959</option>
                                                                        <option value="1960">1960</option>
                                                                        <option value="1961">1961</option>
                                                                        <option value="1962">1962</option>
                                                                        <option value="1963">1963</option>
                                                                        <option value="1964">1964</option>
                                                                        <option value="1965">1965</option>
                                                                        <option value="1966">1966</option>
                                                                        <option value="1967">1967</option>
                                                                        <option value="1968">1968</option>
                                                                        <option value="1969">1969</option>
                                                                        <option value="1970">1970</option>
                                                                        <option value="1971">1971</option>
                                                                        <option value="1972">1972</option>
                                                                        <option value="1973">1973</option>
                                                                        <option value="1974">1974</option>
                                                                        <option value="1975">1975</option>
                                                                        <option value="1976">1976</option>
                                                                        <option value="1977">1977</option>
                                                                        <option value="1978">1978</option>
                                                                        <option value="1979">1979</option>
                                                                        <option value="1980">1980</option>
                                                                        <option value="1981">1981</option>
                                                                        <option value="1982">1982</option>
                                                                        <option value="1983">1983</option>
                                                                        <option value="1984">1984</option>
                                                                        <option value="1985">1985</option>
                                                                        <option value="1986">1986</option>
                                                                        <option value="1987">1987</option>
                                                                        <option value="1988">1988</option>
                                                                        <option value="1989">1989</option>
                                                                        <option value="1990">1990</option>
                                                                        <option value="1991">1991</option>
                                                                        <option value="1992">1992</option>
                                                                        <option value="1993">1993</option>
                                                                        <option value="1994">1994</option>
                                                                        <option value="1995">1995</option>
                                                                        <option value="1996">1996</option>
                                                                        <option value="1997">1997</option>
                                                                        <option value="1998">1998</option>
                                                                        <option value="1999">1999</option>
                                                                        <option value="2000">2000</option>
                                                                        <option value="2001">2001</option>
                                                                        <option value="2002">2002</option>
                                                                        <option value="2003">2003</option>
                                                                        <option value="2004">2004</option>
                                                                        <option value="2005">2005</option>
                                                                        <option value="2006">2006</option>
                                                                        <option value="2007">2007</option>
                                                                        <option value="2008">2008</option>
                                                                        <option value="2009">2009</option>
                                                                        <option value="2010">2010</option>
                                                                        <option value="2011">2011</option>
                                                                        <option value="2012">2012</option>
                                                                        <option value="2013">2013</option>
                                                                        <option value="2014">2014</option>
                                                                        <option value="2015">2015</option>
                                                                    </select>
                                                                </div>
                                                            </div>      

                                                            <div class="form-check form-check-inline">
                                                                <input type="radio" id="male" name="su_gender" value="male" checked> <label for="male" class="light">Male</label>

                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input type="radio" id="female"  name="su_gender" value="female"> <label for="female" class="light">Female</label>
                                                            </div>

                                                            <div style="margin-top: 20px;">
                                                                <button type="button" class="btn btn-block btn-sm btn-inverse" onClick="signUp();" style="font-size: 10px;background-color: #f7bb2b;">Sign up</button>
                                                                <button type="button" class="btn btn-block btn-inverse btn-custom" onClick="goSignin();" style="padding: 10px 0px 10px 0px;font-size: 10px;background-color: #2b4f68;">Already have an Account? Sign in</button>
                                                            </div>

                                                        </form>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-----------    signupBox end       --------------->


                                    <!-----------    signinBox start       ------------->

                                    <div class="modal fade" id="signinBox" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                                         aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">

                                                    <h5 class="modal-title" id="exampleModalLabel">Sign in Now!</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="alert alert-danger alert-signin" style="display:none;"></div>
                                                    <div class="container">
                                                        <form class="form-horizontal" id="sign_in_form">
                                                            <div class="row">
                                                                <div class="control-group form-group col-md-12">
                                                                    <label><b>Mobile No.</b><span class="text-danger">*</span></label>
                                                                    <div class="controls">
                                                                        <div class="item-input-wrap" style="width: 20%; display: inline-block;">
                                                                            <select name="si_country_code" id="si_country_code" class="form-control" style="padding: .375rem .2rem;">
                                                                                <option value="91" selected="">+91</option>
                                                                            </select>
                                                                        </div>
                                                                        <div class="item-input-wrap" style="width: 70%; display: inline-block;">
                                                                            <!--<input type="tel" placeholder="Mobile No.*" name="si_mobile" id="si_mobile" class="form-control" required />-->
                                                                            <input type="number" name="si_mobile" id="si_mobile" class="form-control" placeholder="Mobile No" autocomplete="off" oninput="this.value = this.value.replace(/[^0-9.]/g, ''); this.value = this.value.replace(/(\..*)\./g, '$1');"  required="" maxlength="4">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <br>

                                                            <div class="form-group">
                                                                <div class="col-12 col-sm-12" style="width:90%"><a href="#" class="btn btn-block btn-inverse btn-custom" id="user_login" onclick="userLogin();" style="background-color: #2b4f68;"><b>Sign In</b></a></div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div> 

                                    <!-----------    signinBox end       ----------------->


                                    <!-----------    signinVerifyBox start       ------------->

                                    <div class="modal fade" id="signinVerifyBox" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                                         aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="exampleModalLabel">Sign in Now!</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="container">
                                                        <div class="alert alert-danger alert-signinverify-otp" style="display:none;"></div> 
                                                        <form class="form-horizontal" id="sign_in_form">
                                                            <div class="control-group form-group">
                                                                <div class="controls">
                                                                    <label><b>OTP Code</b> <span class="text-danger">*</span></label>
                                                                    <input type="number" placeholder="OTP Code" class="form-control" name="si_otp_number" id="si_otp_number" required="">
                                                                    <p class="help-block"></p>
                                                                </div>
                                                            </div>
                                                            <br>

                                                            <div class="form-group">
                                                                <div class="col-12 col-sm-12"><a href="#" class="btn  btn-lg btn-block" id="si_otp_submit"  onclick="signinverifyOTP();" style="background-color: #2b4f68;"><b>Verify OTP</b></a></div>

                                                            </div>
                                                            <div class="form-group">
                                                                <div class="col-12 col-sm-12"><a href="#" class="btn  btn-lg btn-block"  onclick="signinRetryOTP();" style="background-color: #2b4f68;"><b>Resend OTP</b></a></div>

                                                            </div>
                                                            <div class="form-group">
                                                                <div class="col-12 col-sm-12"><a href="#" class="btn  btn-lg btn-block" onclick="resetSignIn();" style="background-color: #2b4f68;"><b> Back</b></a></div>

                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div> 

                                    <!-----------    signinVerifyBox end        ------------->


                                    <!-----------    signupVerifyBox start       ------------->

                                    <div class="modal fade" id="signupVerifyBox" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                                         aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">

                                                    <h5 class="modal-title" id="exampleModalLabel">Sign up Now!</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="container">
                                                        <div class="alert alert-danger alert-signupverify-otp" style="display:none;"></div>
                                                        <form class="form-horizontal" id="sign_in_form">
                                                            <div class="control-group form-group">
                                                                <div class="controls">
                                                                    <label><b>OTP Code</b> <span class="text-danger">*</span></label>
                                                                    <input type="number" placeholder="OTP Code" class="form-control" name="su_otp_number" id="su_otp_number" required="">
                                                                    <p class="help-block"></p>
                                                                </div>
                                                            </div>
                                                            <br>

                                                            <div class="form-group">
                                                                <div class="col-12 col-sm-12"><a href="#" class="btn  btn-lg btn-block" id="su_otp_submit" onClick="signupverifyOTP();" style="background-color: #2b4f68;"><b>Verify OTP</b></a></div>

                                                            </div>
                                                            <div class="form-group">
                                                                <div class="col-12 col-sm-12"><a href="#" class="btn  btn-lg btn-block"  onclick="signupRetryOTP()" style="background-color: #2b4f68;"><b>Resend OTP</b></a></div>

                                                            </div>
                                                            <div class="form-group">
                                                                <div class="col-12 col-sm-12"><a href="#" class="btn  btn-lg btn-block" onclick="resetSignUp();" style="background-color: #2b4f68;"><b> Back</b></a></div>

                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div> 
                                    <input type="hidden" id="otp_mobile">

                                    <!-----------    signupVerifyBox end       ------------->




                                    <!---------------- share and earn modal------------------>
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
                                                    <p id="copy_link" style="display: none;"></p>
                                                    <div class="container" style="padding-left: 0px !important;">

                                                        <p style="margin: 15px 0px 0px 0px; font-weight: 600; font-size: 16px;" id="modal_product_title"></p>
                                                        <p id="modal_product_sales_price"></p>
                                                        <p id="modal_influencer_commission_value"></p>
                                                        <div class="social-buttons" style="justify-content: flex-start;" id="shareable_link"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div> 

                                    <!-----------------------share and earn end--------------------------------->



                                    <!-----------------------share modal---------------------------------------->
                                    <div class="modal fade" id="buy-modal">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="exampleModalLabel">Share</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="container">

                                                        <div class="social-buttons" style="justify-content: flex-start;text-align: center;">
                                                            <?php
                                                            $buyer_link = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

                                                            $facebook = 'https://www.facebook.com/sharer/sharer.php?u=' . rawurlencode($buyer_link);
                                                            $twitter = 'https://twitter.com/share?url=' . rawurlencode($buyer_link);
                                                            $whatsapp = 'https://api.whatsapp.com/send?text=' . rawurlencode($buyer_link);
                                                            echo '<a href="' . $twitter . '" target="_blank" class="col button button-fill twitter external" style="margin-right: 10px; padding: 0 10px;"><i class="icon icon-twitter"  style="font-size: 22px !important;"></i></a>
                                                                                            <a href="' . $facebook . '" target="_blank" class="col button button-fill facebook external" style="margin-right: 10px; padding: 0 10px;"><i class="icon icon-facebook" style="font-size: 22px !important;"></i></a>
                                                                                            <a href="' . $whatsapp . '" target="_blank" class="col button button-fill whatsapp external" style="margin-right: 10px; padding: 0 10px;"><img src="images/whatsapp.png" style="width:22px;height:22px;margin-bottom: 11px;"></a>
                                                                                           ';
                                                            ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div> 
                                     <?php
                                            if ($type == 'buyer') {

                                                if ($session_id) {

                                                    if ($product_type == 'External') {
                                                        $traking_url = $affise_tracking_url . '&sub1=' . $partner . '&sub2=' . $PAYID;
                                                        echo '<div class="hdr-mobile show-mobile" style="position: fixed;height: 116px;left:0;bottom: 8px;float:left;width:100%;z-index: 100;">
                                                                                <div class="footer-block py-1 py-md-0 mt-0 text-center">
                                                                                    <div class="footer-menu">
                                                                                        <div class="btn-wrap"><a class="btn btn--add-to-cart"  href=' . $traking_url . ' style="padding: 11px 14px 11px 14px !important;background-color: #2b4f68;"><i class="icon icon-handbag"></i><span>Avail the Offer</span></a></div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>';
                                                    } else {
                                                        echo '<div class="hdr-mobile show-mobile" style="position: fixed;height: 116px;left:0;bottom: 8px;float:left;width:100%;z-index: 100;">
                                                                            <div class="footer-block py-1 py-md-0 mt-0 text-center">
                                                                                <div class="footer-menu">
                                                                                    <div class="btn-wrap"><button class="btn btn--add-to-cart"  onclick="proceedToPay()" style="padding: 11px 14px 11px 14px !important;background-color: #2b4f68;"><i class="icon icon-handbag"></i><span>Proceed to Pay!</span></button></div>
                                                                                </div>
                                                                            </div>
                                                                        </div>';
                                                    }


                                                    echo '<div class="hdr-mobile show-mobile" style="position: fixed;height: 60px;left:0;bottom: 8px;float:left;width:100%;z-index: 100;">
                                                                            <div class="footer-block py-1 py-md-0 mt-0 text-center">
                                                                                <div class="footer-menu">';
                                                    echo "<div class='btn-wrap'><button class='btn btn--add-to-cart' onclick='shareModal()' style='padding: 11px 14px 11px 14px !important;background-color: #2b4f68;'><i class='icon icon-share'></i><span>Share</span></button>
                                                                                    </div>";
                                                    echo '</div>
                                                                            </div>
                                                                        </div>';
                                                } else {
                                                    echo '<div class="hdr-mobile show-mobile" style="position: fixed;height: 60px;left:0;bottom: 8px;float:left;width:100%;z-index: 100;">
                                                                            <div class="footer-block py-1 py-md-0 mt-0 text-center">
                                                                                <div class="footer-menu">
                                                                                    <div class="btn-wrap"><button class="btn btn--add-to-cart" data-toggle="modal" data-target="#signupBox" style="padding: 11px 14px 11px 14px !important;background-color: #2b4f68;"><i class="icon icon-handbag"></i><span>Buy Now!</span></button></div>
                                                                                </div>
                                                                            </div>
                                                                        </div>';
                                                }
                                            } else {
                                                if ($session_id) {
                                                    echo '<div class="hdr-mobile show-mobile" style="position: fixed;height: 50px;left:0;bottom: 8px;float:left;width:100%;z-index: 100;">
                                                            <div class="footer-block py-1 py-md-0 mt-0 text-center">
                                                            <div class="footer-menu">';
                                                    echo "<div class='btn-wrap'><button class='btn btn--add-to-cart' onclick=sellModal('" . $partner . "','" . $product_id . "','" . $product_sales_price . "','" . $PAYID . "') style='padding: 4px 14px 11px 14px !important;background-color: #2b4f68;'><i class='icon icon-share'></i><span>Share And Earn</span></button>
                                                          </div>";
                                                    echo '</div>
                                                        </div>
                                                        </div>';
                                                } else {
                                                    echo '<div class="hdr-mobile show-mobile" style="position: fixed;height: 50px;left:0;bottom: 8px;float:left;width:100%;z-index: 100;">
                                                                    <div class="footer-block py-1 py-md-0 mt-0 text-center">
                                                                        <div class="footer-menu">
                                                                            <div class="btn-wrap"><button class="btn btn--add-to-cart" data-toggle="modal" data-target="#signupBox" style="padding: 4px 14px 20px 14px !important;background-color: #2b4f68;"><i class="icon icon-share"></i><span>Share And Earn</span></button></div>
                                                                        </div>
                                                                    </div>
                                                          </div>';
                                                }
                                            }
                                            ?>
                                    
                                    <!-------------------------------------share modal end---------------------------------------------->
                                    <footer class="page-footer footer-style-5 global_width @@classes" style="margin-top:0px;background-color: #161717">
                                        <div class="footer-top container">
                                            <div class="footer-block py-1 py-md-0 mt-0 text-center" style="bottom: 8px;">
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
                                        <input type="hidden" id="hdn_sales_price" value='<?php echo $product_sales_price; ?>'>
                                        <input type="hidden" id="video_count" value='<?php echo $video_count; ?>'>
</footer><a class="back-to-top js-back-to-top" href="#" title="Scroll To Top"><i class="icon icon-angle-up"></i></a>

<script src="js/vendor/jquery/jquery.min.js"></script>
<script src="js/vendor/bootstrap/bootstrap.bundle.min.js"></script>
<script defer src="js/vendor/slick/slick.min.js"></script>
<script defer src="js/vendor/scrollLock/jquery-scrollLock.min.js"></script>
<!--<script src="js/vendor/countdown/jquery.countdown.min.js"></script>-->
<script  src="js/vendor/imagesloaded/imagesloaded.pkgd.min.js"></script>
<script async src="js/vendor/ez-plus/jquery.ez-plus.min.js"></script>
<!--<script async src="js/vendor/tocca/tocca.min.js"></script>-->
<script src="js/vendor/bootstrap-tabcollapse/bootstrap-tabcollapse.min.js"></script>
<script  src="js/vendor/fancybox/jquery.fancybox.min.js"></script>
<!--<script src="js/vendor/cookie/jquery.cookie.min.js"></script>-->
<!--<script src="js/vendor/bootstrap-select/bootstrap-select.min.js"></script>-->
<!--<script src="js/vendor/form/validator.min.js"></script>-->
<!--<script defer src="js/customize_app.min.js"></script>-->
<script async src="js/vendor/lazysizes/lazysizes.min.js"></script>
<!--<script src="js/vendor/lazysizes/ls.bgset.min.js"></script>-->
<script async src="js/vendor/form/jquery.form.min.js"></script>
<script  src="js/vendor/form/validator.min.js"></script>
<!--<script src="js/vendor/slider/slider.js"></script>-->
<script src="js/app.min.js"></script>
<script>
    //show video modal on clicking video tile                                                             
    function showModal(video_url, video_type) {
        $("#video-modal").modal({
            backdrop: 'static',
            keyboard: false
        });
        var video_url = video_url;
        var video_type = video_type;
        if (video_type == 'video_s3_url') {
            var vid = document.getElementById("video_div");
            isSupp = vid.canPlayType("video/mp4");
            vid.src = video_url;
            vid.load();
            $('#video-modal').modal('show');
            document.getElementById("video-modal").style.display = "block";
        } else {
            $('#iframe_height').attr('src', video_url);
            $('#video-modal').modal('show');
            document.getElementById("video-modal").style.display = "block";
        }
    }
    

    $('#video-modal').on('hidden.bs.modal', function () {
        $('#video_div').attr('src', '');
        $('#iframe_height').attr('src', '');
    })
    //show video modal on clicking video tile  


    // when user signup or signin scroll down to end   
    $(window).load(function () {
        var video_count = document.getElementById('video_count').value;
        if(video_count<=2){
           document.querySelector('style').textContent +=
        "@media screen and (max-width:991px) { .slick-track {width: 680px!important;}.prd.prd-has-loader {}}";

        }
        var type = document.getElementById('hdn_type').value;
        var hdn_product_type = document.getElementById('hdn_product_type').value;
        var session_mobile = '<?php echo $user_mobile; ?>';
        if (type == 'buyer' && hdn_product_type != 'External') {
            if (session_mobile) {
                $("html, body").animate({scrollTop: $(document).height()}, 3000);
            } else {

            }
        } else {

        }

    });// when user signup or signin scroll down to end  


    // sales timmer display on top 
    $(function () {
        var calcNewYear = setInterval(function () {
            date_future = new Date(new Date().getFullYear() + 1, 0, 1);
            date_now = new Date();

            seconds = Math.floor((date_future - (date_now)) / 1000);
            minutes = Math.floor(seconds / 60);
            hours = Math.floor(minutes / 60);
            days = Math.floor(hours / 24);

            hours = hours - (days * 24);
            minutes = minutes - (days * 24 * 60) - (hours * 60);
            seconds = seconds - (days * 24 * 60 * 60) - (hours * 60 * 60) - (minutes * 60);

            $("#time").text("" + hours + "H " + minutes + "M " + seconds + "S");
        }, 1000);
    });// sales timmer display on top 

    // page scroll adjust the header 
    $(window).scroll(function () {
        var sc = $(window).scrollTop()
        if (sc > 500) {
            $("#header-sroll").addClass("small")
        } else {
            $("#header-sroll").removeClass("small")
        }
    });// page scroll adjust the header 
    
//
//    function GeeksForGeeks() {
//        var copyGfGText = document.getElementById("GfGInput");
//        copyGfGText.select();
//        document.execCommand("copy");
//    }

    function profile() {
        var partner_id = '<?php echo $partner; ?>';
        var type = '<?php echo $type; ?>';
        window.location.href = 'profile.php?partner=' + partner_id + '&type=' + type;
    }
</script> 

<script>

function quantityPrice(qty) {
    var product_sales_price = document.getElementById('hdn_sales_price').value;
    final_product_price = product_sales_price * qty;
    console.log(final_product_price);
    console.log(qty);
    document.getElementById('ge_amount').value = final_product_price;
}

function proceedToPay() {
    $('#divLoading').show();
    var ge_quantity = document.getElementById('ge_quantity').value;
    var ge_username = document.getElementById('ge_username').value;

    var ge_mobile = document.getElementById('ge_mobile').value;
    var ge_email = document.getElementById('ge_email').value;
    var ge_address = document.getElementById('ge_address').value;
    var ge_promo_code = '';
    var final_sales_price = document.getElementById('ge_amount').value;
    var product_id = '<?php echo $product_id; ?>';
    var partner = '<?php echo $partner; ?>';

    var flag = 0;

    if (final_sales_price == '') {
        flag = 1;
    }

    if (ge_quantity == '') {
        flag = 1;
    }

    if (ge_username == '') {
        flag = 1;
        $('.alert-email').css('display', 'block');
        $('.alert-email').html('Error! Blank fields or incorrect details.');
        setTimeout(function () {
            $('.alert-email').css('display', 'none');
            $('.alert-email').html('');
        }, 4000);
    }

    if (ge_email == '') {
        flag = 1;
        $('.alert-email').css('display', 'block');
        $('.alert-email').html('Error! Blank fields or incorrect details.');
        setTimeout(function () {
            $('.alert-email').css('display', 'none');
            $('.alert-email').html('');
        }, 4000);
    }

    if (ge_mobile == '') {
        flag = 1;
    } else if (ge_mobile.length != 12) {
        flag = 1;
    }


    if (ge_email != '') {
        if (!validateEmail(ge_email)) {
            flag = 1;
            $('.alert-email').css('display', 'block');
            $('.alert-email').html('Please enter valid email format.');
            setTimeout(function () {
                $('.alert-email').css('display', 'none');
                $('.alert-email').html('');
            }, 4000);
        }
    }

    if (flag == 0) {
        ge_mobile = ge_mobile;
        sendPaymentDetails(ge_username, ge_mobile, ge_email, ge_address, ge_quantity, partner, product_id, ge_promo_code, final_sales_price);

    } else {

        $('.alert-email').css('display', 'block');
        $('.alert-email').html('Error! Blank fields or incorrect details.');
        setTimeout(function () {
            $('.alert-email').css('display', 'none');
            $('.alert-email').html('');
        }, 4000);

    } // flag==0 

} // proceedToPay    


function sendPaymentDetails(username, mobile, email, address, quantity, partner, product_id, promo_code, final_sales_price)
{
    var type = '<?php echo $type; ?>';
    console.log(sendPaymentDetails);

    var request = new XMLHttpRequest();

    request.open('GET', 'process_request.php?do_action=PROCEED_TO_PAY&user_name=' + username + '&user_mobile=' + mobile + '&user_email=' + email + '&user_address=' + address + '&partner=' + partner + '&product_id=' + product_id + '&product_qty=' + quantity + '&product_sales_price=' + final_sales_price + '&promo_code=' + promo_code, true);
    request.onload = function () {

        // Begin accessing JSON data here
        var data = JSON.parse(this.response);

        if (request.status >= 200 && request.status < 400) {

            if (data['RESULT'] == 'SUCCESS') {

                console.log(data['RESULT']);
                $('#divLoading').hide();
                window.location.href = 'confirm_order.php?partner=' + partner + '&type=' + type + '&product_id=' + product_id;

            } else {
                $('.alert-email').html('Sorry! Verification Issue (Code 1)');
                setTimeout(function () {
                    $('.alert-email').css('display', 'none');
                    $('.alert-email').html('');
                }, 4000);
            }

        } else {
            $('.alert-email').html('Oops! Something went wrong. Please try again.');
            setTimeout(function () {
                $('.alert-email').css('display', 'none');
                $('.alert-email').html('');
            }, 4000);

        }

    }

    request.send();

} // sendPaymentDetails()


function sellModal(partner_id, product_id, product_sales_price, payid) {
    $('#divLoading').show();
    var product_title = '<?php echo $product_title; ?>';
    // $('#sell-modal').modal('show');
    getProductDetails(partner_id, product_id, product_sales_price, payid);
}


function getProductDetails(partner_id, product_id, product_sales_price, payid) {
    var traking_url = '<?php echo $affise_tracking_url; ?>';
    var influencer_commission_value = '<?php echo $influencer_commission_value; ?>';
    console.log(influencer_commission_value);
    var product_title = '';
    document.getElementById("shareable_link").innerHTML = '';
    document.getElementById("modal_product_title").innerHTML = '';
    document.getElementById("modal_product_sales_price").innerHTML = '';
    document.getElementById("modal_influencer_commission_value").innerHTML = '';

    var sapiUrl = '<?php echo $sapiUrl; ?>';
    var sapiVersion = '<?php echo $sapiVersion; ?>';
    var sapiEncoding = '<?php echo $sapiEncoding; ?>';
    var USERID = '<?php echo $user_mobile; ?>';

    var response = [];
    var request = new XMLHttpRequest();
    request.open('POST', sapiUrl + '?VERSION=' + sapiVersion + '&ENCODING=' + sapiEncoding + '&METHOD=GET_USER_PAYID&USERID=' + USERID + '&partner_id=' + partner_id, true);
    request.onload = function () {

        // Begin accessing JSON data here
        var data = JSON.parse(this.response);

        if (request.status >= 200 && request.status < 400) {

            console.log(data);

            response = data['RESPONSE'];
            if (traking_url) {
                var shareable_link = traking_url + '&sub1=' + partner_id + '&sub2=' + response['payid'];
                document.getElementById("modal_influencer_commission_value").innerHTML = '<strong>Your Commission:</strong> ' + influencer_commission_value;
            } else {
                var shareable_link = 'https://www.shop369.org/product.php?partner=' + partner_id + '&type=buyer&product_id=' + product_id + '&referer=' + response['payid'];
                document.getElementById("modal_influencer_commission_value").innerHTML = '<strong>Your Commission:</strong>  ' + influencer_commission_value;
            }

            if (shareable_link) {
                console.log(shareable_link);
                $.ajax({
                    url: "https://www.flickstree.com/shorty.php?long_link=" + encodeURIComponent(shareable_link),
                    method: "GET",
                    contentType: "application/json",
                    crossDomain: true,
                    data: {},

                    success: function (data)
                    {
                        data = JSON.parse(data);
                        var short_url = data.short_url;
                        console.log(data);


                        var facebook_share = 'https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(short_url);
                        var twitter_share = 'https://twitter.com/share?url=' + encodeURIComponent(short_url) + '&text=' + encodeURIComponent(product_title + ' - Buy this Now! ');
                        var whatsapp_share = 'https://api.whatsapp.com/send?text=' + encodeURIComponent(product_title + ' - Buy this Now! ') + ' ' + encodeURIComponent(short_url);

                        var copy_link = short_url;

                        if (typeof response['payid'] != 'undefined') {
                            $('#divLoading').hide();
                            $('#sell-modal').modal('show');
                            document.getElementById("shareable_link").innerHTML = '<a href="' + twitter_share + '" target="_blank" class="col button button-fill twitter external" style="margin-right: 10px; padding: 0 10px;"><i class="icon icon-twitter"  style="font-size: 22px !important;"></i></a>\
                            <a href="' + facebook_share + '" target="_blank" class="col button button-fill facebook external" style="margin-right: 10px; padding: 0 10px;"><i class="icon icon-facebook" style="font-size: 22px !important;"></i></a>\
                            <a href="' + whatsapp_share + '" target="_blank" class="col button button-fill whatsapp external" style="margin-right: 10px; padding: 0 10px;"><img src="images/whatsapp.png" style="width:22px;height:22px;margin-bottom: 11px;"></a>\
                            <a onclick="copyTextToClipboard(\'' + product_id + '\')" class="col button button-fill link" style="padding: 0 10px;"><img src="images/link.png" style="width:22px;height:22px;margin-bottom: 11px;"></a>\
                            <input type="hidden" value="' + copy_link + '" id="' + product_id + '">';
                            document.getElementById('copy_link').innerHTML = copy_link;

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

} //getProductDetails

function copyTextToClipboard(box_id) {
    //just_for_copy is my invisible extra field
    document.getElementById('just_for_copy').value = document.getElementById(box_id).value;
    var copyText = document.getElementById('just_for_copy');
    document.getElementById("copy_link_text").style.display = "block";
    document.getElementById("copy_link").style.display = "block";
    console.log(copyText);

    copyText.select();
    document.execCommand("copy");
    //alert("Copied the text: " + copyText.value);
}
</script>


<script>
    function signUp() {
        console.log("signUp");

        var su_username = document.getElementById('su_username').value;
        var su_country_code = document.getElementById('su_country_code').value;
        var su_mobile = document.getElementById('su_mobile').value;
        var su_email = document.getElementById('su_email').value;
        var su_yob = document.getElementById('su_yob').value;

        var x = document.getElementsByName("su_gender");
        var i;
        for (i = 0; i < x.length; i++) {
            if (x[i].checked == true) {
                var gender = x[i].value;
            }
        }

        var flag = 0;

        if (su_username == '' || su_username.trim() == '') {
            flag = 1;
            $('.alert-signup').css('display', 'block');
            $('.alert-signup').html('Incomplete Form, Please fill the mandatory fields');
            setTimeout(function () {
                $('.alert-signup').css('display', 'none');
                $('.alert-signup').html('');
            }, 4000);
        }

        if (su_country_code == '') {
            flag = 1;
            $('.alert-signup').css('display', 'block');
            $('.alert-signup').html('Incomplete Form, Please fill the mandatory fields');
            setTimeout(function () {
                $('.alert-signup').css('display', 'none');
                $('.alert-signup').html('');
            }, 4000);
        }

        if (su_mobile == '') {
            flag = 1;
            $('.alert-signup').css('display', 'block');
            $('.alert-signup').html('Incomplete Form, Please fill the mandatory fields');
            setTimeout(function () {
                $('.alert-signup').css('display', 'none');
                $('.alert-signup').html('');
            }, 4000);
        }
        if (su_mobile.length != 10) {
            flag = 1;
            $('.alert-signup').css('display', 'block');
            $('.alert-signup').html('Please enter valid mobile number.');
            setTimeout(function () {
                $('.alert-signup').css('display', 'none');
                $('.alert-signup').html('');
            }, 4000);
        }
        if (su_email == '') {
            flag = 1;
            $('.alert-signup').css('display', 'block');
            $('.alert-signup').html('Incomplete Form, Please fill the mandatory fields');
            setTimeout(function () {
                $('.alert-signup').css('display', 'none');
                $('.alert-signup').html('');
            }, 4000);
        }

        if (su_email != '') {
            if (!validateEmail(su_email)) {
                flag = 1;
                $('.alert-signup').css('display', 'block');
                $('.alert-signup').html('Please enter valid email format.');
                setTimeout(function () {
                    $('.alert-signup').css('display', 'none');
                    $('.alert-signup').html('');
                }, 4000);
            }
        }

        if (flag == 0) {

            var user_mobile = su_country_code + su_mobile;

            var request = new XMLHttpRequest();
            request.open('GET', 'process_request.php?do_action=SEND_OTP&user_name=' + su_username + '&user_mobile=' + user_mobile + '&user_email=' + su_email + '&user_yob=' + su_yob + '&user_gender=' + gender, true);
            request.onload = function () {

                var data = JSON.parse(this.response);

                if (request.status >= 200 && request.status < 400) {
                    if (data['RESULT'] == 'SUCCESS') {
                        var response = data['RESPONSE'];
                        document.getElementById('otp_mobile').value = user_mobile;
                        $('#signupBox').modal('hide');
                        $('#signupVerifyBox').modal('show');

                    } else {

                        $('.alert-signup').css('display', 'block');
                        $('.alert-signup').html('Sorry! Process Failed. (Code 2)');
                        setTimeout(function () {
                            $('.alert-signup').css('display', 'none');
                            $('.alert-signup').html('');
                        }, 4000);

                    }
                } else {
                    $('.alert-signup').css('display', 'block');
                    $('.alert-signup').html('Sorry! Process Failed. (Code 1)');
                    setTimeout(function () {
                        $('.alert-signup').css('display', 'none');
                        $('.alert-signup').html('');
                    }, 4000);
                }

            }


            request.send();
        }
    }

    function goSignin() {
        $('#signupBox').modal('hide');
        $('#signinBox').modal('show');
    }


    function userLogin()
    {

        var si_country_code = document.getElementById('si_country_code').value;
        var si_mobile = document.getElementById('si_mobile').value;

        var flag = 0;
        if (si_mobile == '') {
            flag = 1;
            $('.alert-signin').css('display', 'block');
            $('.alert-signin').html('Please enter the mobile number.');
            setTimeout(function () {
                $('.alert-signin').css('display', 'none');
                $('.alert-signin').html('');
            }, 4000);
        } else if (si_mobile.length != 10) {
            flag = 1;
            $('.alert-signin').css('display', 'block');
            $('.alert-signin').html('Please enter valid mobile number.');
            setTimeout(function () {
                $('.alert-signin').css('display', 'none');
                $('.alert-signin').html('');
            }, 4000);
        }

        if (flag == 0) {
            var user_mobile = si_country_code + si_mobile;
            console.log(user_mobile);
            var request = new XMLHttpRequest();


            request.open('GET', 'process_request.php?do_action=CUSTOMER_LOGIN&user_mobile=' + user_mobile, true);
            request.onload = function () {

                console.log(this.response);

                // Begin accessing JSON data here
                var data = JSON.parse(this.response);
                console.log(data);

                if (request.status >= 200 && request.status < 400) {

                    if (data['RESULT'] == 'SUCCESS') {
                        // console.log(data['RESULT']);
                        document.getElementById('otp_mobile').value = user_mobile;
                        $('#signinBox').modal('hide');
                        $('#signinVerifyBox').modal('show');
                    } else {
                        $('.alert-signin').css('display', 'block');
                        $('.alert-signin').html('Sorry! Process Failed. (Code 2)');
                        setTimeout(function () {
                            $('.alert-signin').css('display', 'none');
                            $('.alert-signin').html('');
                        }, 4000);
                    }

                } else {
                    $('.alert-signin').css('display', 'block');
                    $('.alert-signin').html('Sorry! Process Failed. (Code 1)');
                    setTimeout(function () {
                        $('.alert-signin').css('display', 'none');
                        $('.alert-signin').html('');
                    }, 4000);
                }

            }

            request.send();

        } else {

        } // flag==0

    }

    function signupverifyOTP()
    {

        $('#divLoading').show();
        console.log("verifyOTP");
        var su_otp_number = document.getElementById('su_otp_number').value;
        var otp_mobile = document.getElementById('otp_mobile').value;
        var partner_id = $('#hdn_partner').val();
        var type = $('#hdn_type').val();
        var product_id = '<?php echo $product_id; ?>';
        var flag = 0;

        if (su_otp_number == '') {
            flag = 1;
            $('#signupVerifyBox').modal('show');
            $('.alert-signupverify-otp').css('display', 'block');
            $('.alert-signupverify-otp').html('Please enter the OTP number.');
            setTimeout(function () {
                $('.alert-signupverify-otp').css('display', 'none');
                $('.alert-signupverify-otp').html('');
            }, 4000);
        }

        if (otp_mobile == '') {
            flag = 1;
        }

        console.log(su_otp_number);
        console.log(su_otp_number);
        if (flag == 0) {
            var request = new XMLHttpRequest();

            request.open('GET', 'process_request.php?do_action=VERIFY_OTP&partner=' + partner_id + '&type=' + type + '&otp_number=' + su_otp_number, true);
            request.onload = function () {

                console.log(this.response);

                var data = JSON.parse(this.response);
                console.log(data);

                if (request.status >= 200 && request.status < 400) {
                    if (data['RESULT'] == 'SUCCESS') {
                        console.log("Signup in Successful");
                        $('#signupVerifyBox').modal('hide');
                        $('#signinVerifyBox').modal('hide');

                        $('#divLoading').show();
                        setTimeout(function () {
                            window.location.href = 'product.php?partner=' + partner_id + '&type=' + type + '&product_id=' + product_id;
                            $('#divLoading').hide();
                        }, 4000);
                        //window.location.href = "dashboard.php";
                    } else if (data['RESULT'] == 'FAILED') {
                        $('#divLoading').hide();
                        $('#signupVerifyBox').modal('show');
                        $('.alert-signupverify-otp').css('display', 'block');
                        $('.alert-signupverify-otp').html('You are already register member.Please Sign in');
                        setTimeout(function () {
                            $('.alert-signupverify-otp').css('display', 'none');
                            $('.alert-signupverify-otp').html('');
                        }, 4000);

                    }
                } else {

                    $('.alert-signupverify-otp').css('display', 'block');
                    $('.alert-signupverify-otp').html('Sorry! Verification Issue (Code 1)');
                    setTimeout(function () {
                        $('.alert-signupverify-otp').css('display', 'none');
                        $('.alert-signupverify-otp').html('');
                    }, 4000);
                }

            }
            request.send();

        }

    }

    function signinverifyOTP()
    {
        $('#divLoading').show();
        console.log("verifyOTP");
        var otp_number = document.getElementById('si_otp_number').value;
        var otp_mobile = document.getElementById('otp_mobile').value;
        var partner_id = $('#hdn_partner').val();
        var type = $('#hdn_type').val();
        var product_id = '<?php echo $product_id; ?>';

        var flag = 0;

        if (otp_number == '') {
            flag = 1;
            // $('#divLoading').hide();
            $('#signinVerifyBox').modal('show');

            $('.alert-signinverify-otp').css('display', 'block');
            $('.alert-signinverify-otp').html('Please enter the OTP number.');

            setTimeout(function () {
                $('.alert-signinverify-otp').css('display', 'none');
                $('.alert-signinverify-otp').html('');
            }, 6000);

        }

        if (otp_mobile == '') {
            flag = 1;
        }


        if (flag == 0) {

            var request = new XMLHttpRequest();

            request.open('GET', 'process_request.php?do_action=VERIFY_OTP&otp_number=' + otp_number, true);

            request.onload = function () {

                console.log(this.response);

                var data = JSON.parse(this.response);
                console.log(data);

                if (request.status >= 200 && request.status < 400) {
                    if (data['RESULT'] == 'SUCCESS') {
                        console.log("Signup in Successful");
                        $('#signupVerifyBox').modal('hide');
                        $('#signinVerifyBox').modal('hide');
                        $('#divLoading').show();

                        setTimeout(function () {
                            window.location.href = 'product.php?partner=' + partner_id + '&type=' + type + '&product_id=' + product_id;
                            $('#divLoading').hide();
                        }, 4000);
                        //window.location.href = "dashboard.php";
                    } else if (data['RESULT'] == 'FAILED') {
                        $('#divLoading').hide();
                        $('#signinVerifyBox').modal('show');
                        $('.alert-signinverify-otp').css('display', 'block');
                        $('.alert-signinverify-otp').html('Sorry! Verification Issue (Code 2)');
                        setTimeout(function () {
                            $('.alert-signinverify-otp').css('display', 'none');
                            $('.alert-signinverify-otp').html('');
                        }, 4000);

                    }
                } else {

                    $('.alert-signinverify-otp').css('display', 'block');
                    $('.alert-signinverify-otp').html('Sorry! Verification Issue (Code 1)');
                    setTimeout(function () {
                        $('.alert-signinverify-otp').css('display', 'none');
                        $('.alert-signinverify-otp').html('');
                    }, 4000);
                }

            }
            request.send();

        }

    }

    function signupRetryOTP()
    {
        var request = new XMLHttpRequest();

        request.open('GET', 'process_request.php?do_action=RETRY_OTP', true);
        request.onload = function () {

            // Begin accessing JSON data here
            var data = JSON.parse(this.response);
            console.log(data);

            if (request.status >= 200 && request.status < 400) {
                if (data['RESULT'] == 'SUCCESS') {

                    // document.getElementById("divLoading").classList.remove("show");
                    $('.alert-signupverify-otp').html('Successfully send OTP');
                    setTimeout(function () {
                        $('.alert-signupverify-otp').html('');
                    }, 4000);

                    $('#signupBox').modal('hide');
                    $('#signupVerifyBox').modal('show');

                } else {
                    // document.getElementById("divLoading").classList.remove("show");
                }
            } else {
                // document.getElementById("divLoading").classList.remove("show");

                $('.alert-signupverify-otp').html('Oops! Something went wrong. Please try again.');
                setTimeout(function () {
                    $('.alert-signupverify-otp').html('');
                }, 4000);
            }

        }
        request.send();

    }


    function signinRetryOTP()
    {

        var request = new XMLHttpRequest();

        request.open('GET', 'process_request.php?do_action=RETRY_OTP', true);
        request.onload = function () {
            // Begin accessing JSON data here
            var data = JSON.parse(this.response);
            console.log(data);

            if (request.status >= 200 && request.status < 400) {
                if (data['RESULT'] == 'SUCCESS') {


                    $('.alert-signinverify-otp').html('Successfully send OTP');
                    setTimeout(function () {
                        $('.alert-signinverify-otp').html('');
                    }, 4000);


                    $('#signinBox').modal('hide');
                    $('#signinVerifyBox').modal('show');

                } else {

                }
            } else {

                $('.alert-signinverify-otp').html('Oops! Something went wrong. Please try again.');
                setTimeout(function () {
                    $('.alert-signupverify-otp').html('');
                }, 4000);
            }

        }
        request.send();

    } // signinRetryOTP()   

    function validateEmail(email_id) {
        var invalidChars = '\/\'\\ ";:?!()[]\{\}^|';
        for (var i = 0; i < invalidChars.length; i++) {
            if (email_id.indexOf(invalidChars.charAt(i), 0) > -1) {
                // alert('email address contains invalid characters');
                return false;
            }
        }
        for (var i = 0; i < email_id.length; i++) {
            if (email_id.charCodeAt(i) > 127) {
                // alert("email address contains non ascii characters.");
                return false;
            }
        }

        var atPos = email_id.indexOf('@', 0);
        if (atPos == -1) {
            // alert('email address must contain an @');
            return false;
        }
        if (atPos == 0) {
            // alert('email address must not start with @');
            return false;
        }
        if (email_id.indexOf('@', atPos + 1) > -1) {
            alert('email address must contain only one @');
            return false;
        }
        if (email_id.indexOf('.', atPos) == -1) {
            // alert('email address must contain a period in the domain name');
            return false;
        }
        if (email_id.indexOf('@.', 0) != -1) {
            // alert('period must not immediately follow @ in email address');
            return false;
        }
        if (email_id.indexOf('.@', 0) != -1) {
            // alert('period must not immediately precede @ in email address');
            return false;
        }
        if (email_id.indexOf('..', 0) != -1) {
            // alert('two periods must not be adjacent in email address');
            return false;
        }
        var suffix = email_id.substring(email_id.lastIndexOf('.') + 1);
        if (suffix.length != 2 && suffix != 'com' && suffix != 'net' && suffix != 'org' && suffix != 'edu' && suffix != 'int' && suffix != 'mil' && suffix != 'gov' & suffix != 'arpa' && suffix != 'biz' && suffix != 'aero' && suffix != 'name' && suffix != 'coop' && suffix != 'info' && suffix != 'pro' && suffix != 'museum') {
            // alert('invalid primary domain in email address');
            return false;
        }
        return true;
    }

    function resetSignUp() {
        $('#signupBox').modal('show');
        $('#signupVerifyBox').modal('hide');
    } // resetSignUp

    function resetSignIn() {
        $('#signinVerifyBox').modal('hide');
        $('#signinBox').modal('show');

    } // resetSignIn

    function shareModal() {
        $('#buy-modal').modal('show');
    }

</script>
</body>

</html>