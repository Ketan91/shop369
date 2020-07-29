<?php
include "functions.php"; // Get Configuration
$session_id = $_COOKIE["FTAFF_SESSIONID"];
$user_id = $_COOKIE["FTAFF_ID"];
$user_name = $_COOKIE["FTAFF_NAME"];
$user_mobile = $_COOKIE["FTAFF_MOBILE"];
$partner = sanitize_string($_REQUEST['partner']);
$referer = sanitize_string($_REQUEST['referer']);
$product_cat = sanitize_string($_REQUEST['cat']);
$type = 'buyer';
$id = sanitize_string($_REQUEST['id']);
$url = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
if (empty($partner)) {
    header('location: https://www.shop369.org/products.php?partner=com.flickstree.official&type=' . $type);
}
if (empty($referer)) {
    header('location: https://www.shop369.org/products.php?partner=' . $partner . '&type=buyer');
}


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
$result = json_decode($curl_response, true);

$response = $result['RESPONSE'];

if ($result_banner = @file_get_contents("banner.json")) {
    $banner_array = json_decode($result_banner, true);
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
        <title>Products</title>
        <link rel="shortcut icon" type="image/x-icon" href="images/logo/favicon.ico">
        <link async href="js/vendor/bootstrap/bootstrap.min.css" rel="stylesheet">
        <link async href="js/vendor/slick/slick.min.css" rel="stylesheet">
        <link async href="css/product_style_light.min.css" rel="stylesheet">
        <link async href="fonts/icomoon/font.min.css" rel="stylesheet">
        <link async href="css/custom.min.css" rel="stylesheet">
        <!--        <link async href="https://fonts.googleapis.com/css?family=Montserrat:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
                <link async href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i,800,800i" rel="stylesheet">-->
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
            @media screen and (max-width: 991px){
                .hdr-mobile-style2 .hdr-mobile.is-sticky {
                    position: fixed;
                    top: 0px;
                }
                .hdr-mobile-style2 .hdr-mobile .logo-holder {
                    margin-left: 0px;
                }
            }
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
            
            .sticky-holder {
                top: 0px;
            }
            #divLoading{display:none}
            #divLoading{
                position:fixed;
                z-index:100;
                left:0;bottom:0;right:0;top:200px;z-index:999
            }
            h2.prd-title {
                height: 42px !important;
            }
            .prd-tag {
                height: 42px !important;
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

    <body class="home-page is-dropdn-click">


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
                                        $category_array = [];
                                        foreach ($response as $index) {
                                            $product_category = $index['details']['product_category'];
                                            $category_array[] = $product_category;
                                        }
                                        $all_categories = array_values(array_unique($category_array));
                                        for ($j = 0; $j < count($all_categories); $j++) {
                                            $categories = $all_categories[$j];
                                            if ($categories != 'null' && $categories != '') {
                                                ?>

                                                <li>
                                                   <?php echo '<a href="influencer_products.php?partner='.$partner.'&type='.$type.'&cat='.$categories.'&referer='.$referer.'">'.$categories.'</a>'; ?>
                                                </li>
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
                        <?php echo'<div class="logo-holder"><a href="products.php?partner=' . $partner . '&type=' . $type . '&referer='.$referer.'" class="logo"><img src="images/logo/shop369_logo.png" srcset="images/logo/shop369_logo.png" alt=""></a></div>'; ?>
                    
                        <div class="col-auto hdr-content-right">
                            <div class="search-holder">
                                <form action="#" class="search" style="padding: 0px 0px 0px 0px !important;border: 1px solid #00000014;margin-top: 10px;" id="mobile_search_form">
                                    <a href="javascript:void(0);" onclick="mobile_search();"><p class="search-button" style="width: 30px;line-height: 35px;background-color: #2b4f68;">
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
                                        <a href="javascript:void(0);" onclick="desktop_search();"><p class="search-button" style="background-color: #2b4f68;"><i class="icon-search2" style="color: #fff;"></i>
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
                            <?php echo'<div class="col-auto logo-holder-s"><a href="products.php?partner=' . $partner . '&type=' . $type . '&referer='.$referer.'" class="logo"><img src="images/logo/shop369_logo.png" srcset="images/logo/shop369_logo.png" alt=""></a></div>'; ?>

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

            <div class="container" id="hdn_container" >
                <div class="row">
                    <div class="col-md-9 aside centerColumn">
                        <!-- BN Slider 1 -->
                        <div class="bnslider-wrapper">
                            <div class="bnslider bnslider--md keep-scale" id="bnslider-001" data-slick='{"arrows": true, "dots": true}' data-autoplay="false" data-speed="5000" data-start-width="848" data-start-height="667" data-start-mwidth="848" data-start-mheight="467">
                                <?php
                                foreach ($banner_array as $banner) {
                                    $banner_title = $banner['banner_title'];
                                    $banner_img_path = $banner['banner_img_path'];
                                    $banner_link = $banner['banner_url'];

                                    $banner_url_type = $banner['banner_url_type'];
//                                    if ($banner_url_type == 'External') {
//                                        $banner_link = $banner_link . '&sub1=' . $partner . '&sub2=' . $user_mobile;
//                                    } else {
//                                        $banner_link = $banner['banner_url'];
//                                    }
//                                    if ($type == 'buyer') {
//
//                                        echo '<a href="' . $banner_link . '" class="bnslider-slide">';
//                                    } else {
//                                        if ($user_mobile) {
//                                            echo '<a href="' . $banner_link . '" class="bnslider-slide">';
//                                        } else {
//                                            echo '<a data-toggle="modal" data-target="#signupBox" class="bnslider-slide">';
//                                        }
//                                    }
                                    ?>  
                                <div class="bnslider-slide">
                                    <div class="bnslider-image" style="background-image: url('<?php echo $banner_img_path; ?>');"></div>
                                    <div class="bnslider-text-wrap bnslider-overlay">
                                        <div class="bnslider-text-content txt-middle txt-left">
                                            <div class="bnslider-text-content-flex container">
                                                <div class="bnslider-vert">

                                                    <div class="btn-wrap" data-animation="fadeIn" data-animation-delay="1s"><div class="btn-decor">shop now<span class="btn-line"></span></div></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                    <!--</a>-->

                                    <?php
                                }
                                ?>
                            </div>

                            <div class="bnslider-arrows container">
                                <div></div>
                            </div>
                            <div class="bnslider-dots vert-dots container"></div>
                        </div>

                    </div>
                    <div class="col-md-3 aside aside--left mt-0 sideColumn">
                        <div class="d-lg-block d-none">
                            <h2 class="h-category">CATEGORIES</h2>
                            <ul class="mmenu vmmenu-js mmenu--vertical" style="overflow: auto;overflow-x: hidden;height: 615px">

                                <?php
                                $category_array = [];
                                foreach ($response as $index) {
                                    $product_category = $index['details']['product_category'];
                                    $category_array[] = $product_category;
                                }
                                $all_categories = array_values(array_unique($category_array));
                                for ($j = 0; $j < count($all_categories); $j++) {
                                    $categories = $all_categories[$j];
                                    if ($categories != 'null' && $categories != '') {
                                        ?>
                                        <li class="mmenu-item--mega">
                                            <?php echo '<a href="influencer_products.php?partner='.$partner.'&type='.$type.'&cat='.$categories.'&referer='.$referer.'">'.$categories.'</a>'; ?>
                                        </li>
                                        <input type="hidden" id="hdn_category">
                                        <?php
                                    }
                                }
                                ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="holder py-3 py-md-6 fullboxed aside--bg-none holder-bg-09" style="margin-top: 0px !important;">
                <div class="container">
                    <div class="text-center" id="all_product">
                        <h2 class="h1-style">Products</h2>
                    </div>
                    <div class="text-center" id="search_result" style="display:none">
                        <h2 class="h1-style">Search Result</h2>
                    </div>
                    <div class="prd-grid prd-grid--nopad data-to-show-3 data-to-show-md-2 data-to-show-sm-2 data-to-show-xs-1 js-product-isotope mt-4"  id="product_list">

                    </div>
                    <p id="showMsg" style="text-align:center;font-size: 19px;color: #da222b;"></p>
                    <div class="prd-grid prd-grid--nopad data-to-show-3 data-to-show-md-2 data-to-show-sm-2 data-to-show-xs-1 js-product-isotope mt-4"  id="search_list" style="display:none">

                    </div>
                    <div class="prd-grid prd-grid--nopad data-to-show-3 data-to-show-md-2 data-to-show-sm-2 data-to-show-xs-1 js-product-isotope mt-4"  id="category_list" style="display:none">

                    </div>
                    <div id="no_data" style="display:none;text-align: center;height: 350px;">
                        <h5>No Data Found</h5>
                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" id="hdn_partner" value="<?php echo $partner; ?>">
        <input type="hidden" id="hdn_type" value="<?php echo $type; ?>">   
        <input type="hidden" id="start_val" value="">
        <input type="hidden" id="otp_mobile">

        <!-----------    signupVerifyBox end       ------------->

    </div> 
    <?php include('footer.php'); ?>

    <script src="js/vendor/jquery/jquery.min.js"></script>
    <script defer src="js/vendor/bootstrap/bootstrap.bundle.min.js"></script>
    <script defer src="js/vendor/slick/slick.min.js"></script>
    <script async src="js/vendor/scrollLock/jquery-scrollLock.min.js"></script>
    <script  src="js/vendor/bootstrap-tabcollapse/bootstrap-tabcollapse.min.js"></script>
    <script async src="js/vendor/fancybox/jquery.fancybox.min.js"></script>
    <script async src="js/vendor/cookie/jquery.cookie.min.js"></script>
    <script async src="js/vendor/bootstrap-select/bootstrap-select.min.js"></script>
    <script async src="js/vendor/slider/banner_slider.min.js"></script>
    <script defer src="js/customize_app.min.js"></script>

    <script>
    window.setInterval(function () {

        $(".slick-next").click();
    }, 5000);
    
    var search_array = <?php echo json_encode($result['RESPONSE']) ?>;
    if (!!window.performance && window.performance.navigation.type == 2)
    {

        window.location.reload();
    }
    
    
    var apiVersion = '<?php echo $sapiVersion; ?>';
    var ENCODING = '<?php echo $sapiEncoding; ?>';
    var access_code = '<?php echo $sapiAccessCode; ?>';
    var partner_id = '<?php echo $partner; ?>';
    var user_id = '<?php echo $user_mobile; ?>';
    var type = '<?php echo $type; ?>';
    var referer = '<?php echo $referer; ?>';
    var product_category = '<?php echo $product_cat; ?>';
    if(product_category){
        $('html, body').animate({
            scrollTop: $("#all_product").offset().top
        }, 3000);
    }
//    var influencer_commission_percentage = '<?php echo $influencer_commission_percentage; ?>';
    var startfrom = 0;
    var maxresults = 6;
    var is_working = 0; 
    var isDataAva = true;
   $(document).ready(function () {
        getInfluencerProducts(apiVersion,ENCODING,access_code,partner_id,type,product_category,referer, startfrom,maxresults);
    });
    
    $(window).on('scroll', function () {

    if (isDataAva && !is_working && $(window).scrollTop() >= (($(document).height() - $(window).height()) - 150)) {
            is_working = 1;
            getInfluencerProducts(apiVersion,ENCODING,access_code,partner_id,type,product_category,referer, startfrom,maxresults);

        }
    
    });

    function getInfluencerProducts(apiVersion,ENCODING,access_code,partner_id,type,product_category,referer, start_from,max_results){
        if(start_from==0){
            $('#showMsg').html('Loading Products');
        }else{
            $('#showMsg').html('Loading More Products..');
        }
        
       
var categorywise_product = '';
var response = [];
 $.ajax({
	url: '<?php echo $sapiUrl; ?>',
        method: "POST",
        data: {VERSION: apiVersion, ENCODING: ENCODING, access_code: access_code, METHOD: "GET_PUBLISHER_PRODUCTS", partner_id: partner_id,show_category:product_category, start_from: start_from, max_results: max_results},
	dataType: 'json'
})
.done(function(data) {
	
//data = JSON.parse(data);
                if (data['RESULT'] == 'SUCCESS') {
                    var response = data['RESPONSE'];
                    if (response.length == '') {
                            console.log("no data");
                             $('#showMsg').html("Thats it");
                            is_working = 1;
                           
                        }else{
                           console.log(response);
                    
                    for (var index in response) {
                        
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
                            var url = 'product.php?partner=' + partner_id + '&type=' + type + '&product_id=' + product_id + '&referer=' + referer;
                        } else {
                            var product_mrp = "";
                            var url = 'product.php?partner=' + partner_id + '&type=' + type + '&product_id=' + product_id + '&referer=' + referer;
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

//                        if (response[index]['pricing']) {
//                            var influencer_commission_value = (product_sales_price * influencer_commission_percentage) / 100;
//
//                        } else {
//                            var influencer_commission_value = "";
//                        }

                        var product_type = response[index]['details']['product_type'];

                        var product_status = response[index]['details']['product_status'];

                        if(product_status== 'Active'){

                        categorywise_product += '<div class="prd prd-horizontal-simple prd-popular prd-new">';
                        categorywise_product += '<div class="prd-inside">\n\
                        <div class="prd-img-area" style="height: 230px;box-shadow: -3px -3px 5px #888888ab;">';
                       
                        categorywise_product += '<a href="' + url + '" class="prd-img"><img src="images/default_img.jpg" data-src="' + product_image + '" alt="" id="tile_img">\n\
                        ' + offer + '<p class="label-wishlist icon-heart js-label-wishlist"></p></a>';
                           
                        categorywise_product += '</div>\n\
                            <div class="prd-info" style="display:block !important;">';

                        categorywise_product += '<a href="' + url + '"  style="text-transform: none;text-decoration: none;"><h2 class="prd-title" style="color: #282c3f;">' + product_title + '</h2>\n\
                            <div class="prd-tag" style="text-transform: none;color: #7e818c;line-height: 1.2em;">' + product_short_description + '</div>\n\
                            <div class="prd-rating">' + rate + '</div>\n\
                            ' + pricing + '</a>';
                           
                        categorywise_product += '<div class="prd-action" style="width: 90%;">\n\
                            <form action="#"><input type="hidden">\n\
                                <a href="product.php?partner=' + partner_id + '&type=' + type + '&product_id=' + product_id + '&referer=' + referer +'" class="btn" style="color: #fff;background-color: #2b4f68;background-color: #2b4f68;"><span>Buy Now!</span></a>\n\
                            </form>\n\
                            </div>';
                           
                        categorywise_product += '</div>\n\
                        </div>\n\
                        </div>';
//                        $('#product_list').append(categorywise_product);
                       
                        }
                    }
                    if(start_from==0){
                        $('#product_list').html(categorywise_product);
                    }else{
                        $('#product_list').append(categorywise_product);
                    }	
                    startfrom = start_from+max_results;
                    var imgDefer = document.getElementsByTagName('img');
                        for (var i = 0; i < imgDefer.length; i++) {
                            if (imgDefer[i].getAttribute('data-src')) {
                                imgDefer[i].setAttribute('src', imgDefer[i].getAttribute('data-src'));
                            }
                        }
                    $('#showMsg').html("");
                    is_working = 0; 
                        }
                    
                    }	
	
   
        }).fail(function(data) {
            $('#showMsg').html("");  

        }).always(function(data) {

            $('#showMsg').html("");
        });
    }


    function enter_key_search(e) {
   
    
       var search_title = $('#search_title').val();
       var search_title = search_title.trim();
       if (e.keyCode == 13) {
           if (search_title == "") {
               $('#showMsg').html('Loading More Products..');
               isDataAva = true;
               $('#product_list').show();
               $("#search_list").empty();
               $('#hdn_container').show();
               $('#all_product').show();
               $('#search_result').hide();
               $('#no_data').hide();
               return false;
           } else {
               $('#showMsg').html("");
               isDataAva = false;
               desktop_search();
               return false;
           }

       }
   }

   function desktop_search(){
       if ($('#search_title').val()) {
           var search_title = $('#search_title').val();
       }else{
           var search_title = "";
       }

       partnerwise_search(search_title);
    }
    function mobile_search(){
       if ($('#mobile_search').val()){
           var search_title = $('#mobile_search').val();
       }else{
           var search_title = "";
       }

       partnerwise_search(search_title);
    }                                        

    function partnerwise_search(searchTitle) {
    $('#showMsg').html("");
    document.getElementById("desk_search_form").style.borderColor = "";
    var partner_id = '<?php echo $partner; ?>';
    var type = '<?php echo $type; ?>';
    var session_id = '<?php echo $session_id; ?>';
    var user_id = '<?php echo $user_mobile; ?>';
    var referer = '<?php echo $referer; ?>';
    if (searchTitle == "") {
        isDataAva = true;
        $('#product_list').show();
        $("#search_list").empty();
        $('#hdn_container').show();
        $('#all_product').show();
        $('#search_result').hide();

        $('#no_data').hide();
        return false;
    } else {
        isDataAva = false;
        var search_title = searchTitle;
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
                    var atag = 'product.php?partner=' + partner_id + '&type=' + type + '&product_id=' + product_array['product_id'] + '&referer=' + referer;

                }

            }

            if (counter >= 1) {

                if (product_array['product_id']) {

                    product_array['product_status'] = search_array[index]['details']['product_status'];

                if(product_array['product_status'] == 'Active'){

                    search_product += '<div class="prd prd-horizontal-simple prd-popular prd-new">\n\
                <div class="prd-inside">\n\
                <div class="prd-img-area" style="height: 230px;box-shadow: -3px -3px 5px #888888ab;">';
                    
                search_product += '<a href="' + atag + '" class="prd-img"><img src=' + product_array['product_image'] + ' alt="" id="tile_img"></a>';
                search_product += '<div class="label-new"></div><a href="#" class="label-wishlist icon-heart js-label-wishlist"></a>\n\
                </div>\n\
                <div class="prd-info" style="display:block !important;">';

                search_product += '<a href="' + atag + '"  style="text-transform: none;text-decoration: none;"><h2 class="prd-title" style="color: #282c3f;">' + product_array['product_title'] + '</h2>\n\
                <div class="prd-tag" style="text-transform: none;color: #7e818c;line-height: 1.2em;">' + product_array['product_short_description'] + '</div>\n\
                <div class="prd-rating"></div>\n\
                <div class="prd-price">\n\
                <div class="price-new"></div>\n\
                <div class="price-old"></div>\n\
                <div class="price-comment" style="color: #7e818c;"></div></a>';
                     
                search_product += '</div>';
                search_product += '<div class="prd-action" style="width: 90%;">\n\
                <form><input type="hidden">';
              
                search_product += '<a href="product.php?partner=' + partner_id + '&type=' + type + '&product_id=' + product_array['product_id'] + '&referer=' + referer +'" class="btn" style="color: #fff;background-color: #2b4f68;"><span>Buy Now!</span></a>';
                   
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

                    $('#hdn_bar').hide();
                    $('#product_list').hide();
                    $('#category_list').hide();
                    $('#all_product').hide();
                    $('#search_result').show();
                    $('#search_list').show();
                    }
                }
            } else {
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

    </script>

    <script>
        function profile() {
        var partner_id = '<?php echo $partner; ?>';
        var type = '<?php echo $type; ?>';
        window.location.href = 'profile.php?partner=' + partner_id + '&type=' + type;
        }
    </script>

</body>

</html>