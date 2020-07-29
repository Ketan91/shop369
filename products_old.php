<?php
include "functions.php"; // Get Configuration
$session_id = $_COOKIE["FTAFF_SESSIONID"];
$user_id = $_COOKIE["FTAFF_ID"];
$user_name = $_COOKIE["FTAFF_NAME"];
$user_mobile = $_COOKIE["FTAFF_MOBILE"];
$partner = sanitize_string($_REQUEST['partner']);
$cat = sanitize_string($_REQUEST['cat']);
$type = sanitize_string($_REQUEST['type']);
$id = sanitize_string($_REQUEST['id']);
$url = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
if (empty($partner)) {
    header('location: https://www.shop369.org/products.php?partner=com.flickstree.official&type=' . $type);
}
if (empty($type)) {
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
                                                    <a href="javascript:void(0);" onclick="get_category_product('<?php echo $categories ?>');"><?php echo $categories ?></a>
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
                                        <!--                                    <li class="mmenu-item--mega">
                                                                                <a href="javascript:void(0);">All Categories</a>
                                                                            </li>    -->
                                        <li class="mmenu-item--mega">
                                            <a href="javascript:void(0);" onclick="get_category_product('<?php echo $categories ?>');"><?php echo $categories ?></a>
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
                    <p id="showMsg" style="text-align:center;font-size: 19px;color: #da222b;display: none;"></p>
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
                                                <input type="tel" placeholder="Mobile No.*" name="su_mobile" id="su_mobile" class="form-control" required="">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="control-group form-group">
                                    <div class="controls">
                                        <label><b>Email</b><span class="text-danger">*</span> </label>
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
                            <input type="hidden" id="hdnid" value="">
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
                                                <input type="tel" placeholder="Mobile No.*" name="si_mobile" id="si_mobile" class="form-control" required />
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

    $(document).ready(function () {
        $('#divLoading').hide();
        var id = '<?php echo $id; ?>';
        if (id) {
            $('#signupBox').modal('show');
        } else {

        }

        var category = '<?php echo $cat; ?>';
        if (category) {
        get_category_product(category)
        }else{
              $('#showMsg').hide();
//                                               document.getElementById("showMsg").style.display = "none";
        var apiVersion = '<?php echo $sapiVersion; ?>';
        var ENCODING = '<?php echo $sapiEncoding; ?>';
        var access_code = '<?php echo $sapiAccessCode; ?>';
        var partner_id = '<?php echo $partner; ?>';
        var user_id = '<?php echo $user_mobile; ?>';
        var type = '<?php echo $type; ?>';
        var session_id = '<?php echo $session_id; ?>';
        var influencer_commission_percentage = '<?php echo $influencer_commission_percentage; ?>';
        var st_val = 6;
        $("#start_val").val(st_val);
        $.ajax({
            url: '<?php echo $sapiUrl; ?>',
            method: "POST",
//                                                async: false,
            data: {VERSION: apiVersion, ENCODING: ENCODING, access_code: access_code, METHOD: "GET_PUBLISHER_PRODUCTS", partner_id: partner_id, start_from: 0, max_results: 6},
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
        } 


    });

    function enter_key_search(e) {
   //                                        $('#showMsg').html("");
   //                                        document.getElementById("showMsg").style.display = "none";
    $('#showMsg').hide();
       var search_title = $('#search_title').val();
       var search_title = search_title.trim();
       if (e.keyCode == 13) {
           if (search_title == "") {

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
               desktop_partnerwise_search();
               return false;
           }

       }
   }

    function desktop_partnerwise_search() {
     $('#showMsg').remove();
//                                            document.getElementById("showMsg").style.display = "none";

    document.getElementById("desk_search_form").style.borderColor = "";
    var partner_id = '<?php echo $partner; ?>';
    var type = '<?php echo $type; ?>';
    var session_id = '<?php echo $session_id; ?>';
    var user_id = '<?php echo $user_mobile; ?>';
    if ($('#search_title').val() == "") {
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


    function mobile_partnerwise_search() {
        $('#showMsg').remove();
//                                            document.getElementById("showMsg").style.display = "none";
//                                            document.getElementById("desk_search_form").style.borderColor = "";
        var partner_id = '<?php echo $partner; ?>';
        var type = '<?php echo $type; ?>';
        var session_id = '<?php echo $session_id; ?>';
        var user_id = '<?php echo $user_mobile; ?>';
        if ($('#mobile_search').val() == "") {
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

                        $('#hdn_bar').hide();
                        $('#product_list').hide();
                        $('#category_list').hide();
                        $('#all_product').hide();
                        $('#search_result').show();
                        $('#search_list').show();
                        }
                    }
                } else {
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
                                       

    function get_category_product(category) {
         $('#showMsg').remove();
        isDataAva = false;
        $('#divLoading').show();
        $('#category_list').show();
        $('html, body').animate({
            scrollTop: $("#category_list").offset().top
        }, 3000);

        // document.getElementById("divLoading").classList.add("show");
        $("#clmenu").removeClass("active");
        $("#closemenu").removeClass("active");
        $("body").removeClass("slidemenu-open is-fixed");
        $("#product_list").hide();
        $("#category_list").empty();
        $("#search_list").empty();

        document.getElementById('hdn_category').value = category;
        var apiVersion = '<?php echo $sapiVersion; ?>';
        var ENCODING = '<?php echo $sapiEncoding; ?>';
        var access_code = '<?php echo $sapiAccessCode; ?>';
        var partner_id = '<?php echo $partner; ?>';
        var user_id = '<?php echo $user_mobile; ?>';
        var type = '<?php echo $type; ?>';
        var session_id = '<?php echo $session_id; ?>';
        var influencer_commission_percentage = '<?php echo $influencer_commission_percentage; ?>';

        $.ajax({
            url: '<?php echo $sapiUrl; ?>',
            method: "POST",
            data: {VERSION: apiVersion, ENCODING: ENCODING, access_code: access_code, METHOD: "GET_PUBLISHER_PRODUCTS", partner_id: partner_id, show_category: category},
            success: function (data)
            {
                data = JSON.parse(data);
                if (data['RESULT'] == 'SUCCESS') {
                    var response = data['RESPONSE'];
                    console.log(response);
                    var active_product_status_count = 0;
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
                        $('#category_list').show();
                        $('#category_list').append(categorywise_product);
                        $('#search_result').hide();
                        $('#no_data').hide();
                        $('#search_list').hide();
                        $('#divLoading').hide();
                        $('#all_product').show();
                        $('#hdn_container').show();
                        var imgDefer = document.getElementsByTagName('img');
                        for (var i = 0; i < imgDefer.length; i++) {
                            if (imgDefer[i].getAttribute('data-src')) {
                                imgDefer[i].setAttribute('src', imgDefer[i].getAttribute('data-src'));
                            }
                        }
                        active_product_status_count++;
                        }else{
            if(active_product_status_count<1){
            $('#no_data').show();
            }

            $('#divLoading').hide();

            }
                    }

                }
            }

        });
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
            var partner_id = '<?php echo $partner; ?>';
            var type = '<?php echo $type; ?>';
            var p_id = $("#hdnid").val();
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

                            setTimeout(function () {
                                $('#divLoading').show();
                                if (p_id) {
                                    window.location.href = 'product.php?partner=' + partner_id + '&type=' + type + '&product_id=' + p_id;
                                    $('#divLoading').hide();
                                } else {
                                    window.location.href = 'products.php?partner=' + partner_id + '&type=' + type;
                                    $('#divLoading').hide();
                                }
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
            var p_id = $("#hdnid").val();
            var flag = 0;

            if (otp_number == '') {
                flag = 1;
                $('#signinVerifyBox').modal('show');
                $('.alert-signinverify-otp').css('display', 'block');
                $('.alert-signinverify-otp').html('Please enter the OTP number.');
                setTimeout(function () {
                    $('.alert-signinverify-otp').css('display', 'none');
                    $('.alert-signinverify-otp').html('');
                }, 4000);
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
                            $('#signinVerifyBox').modal('hide');

                            setTimeout(function () {
                                if (p_id) {
                                    window.location.href = 'product.php?partner=' + partner_id + '&type=' + type + '&product_id=' + p_id;
                                    $('#divLoading').hide();
                                } else {
                                    window.location.href = 'products.php?partner=' + partner_id + '&type=' + type;
                                    $('#divLoading').hide();
                                }
                            }, 4000);
                            //window.location.href = "dashboard.php";
                        } else if (data['RESULT'] == 'FAILED') {
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
// document.getElementById("divLoading").classList.add("show");    
            var request = new XMLHttpRequest();

            request.open('GET', 'process_request.php?do_action=RETRY_OTP', true);
            request.onload = function () {

                console.log(this.response);

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
                console.log(this.response);

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
        function openSignupModal(product_id) {
            var pid = product_id;
            $("#hdnid").val(pid);
            $('#signupBox').modal('show');

        }
        function profile() {
        var partner_id = '<?php echo $partner; ?>';
        var type = '<?php echo $type; ?>';
        window.location.href = 'profile.php?partner=' + partner_id + '&type=' + type;
        }
    </script>
    <script>

        var isPreEventComp = true;
        var isDataAva = true;
        var recentScroll = false;
        var position = $(window).scrollTop();
        $(window).on('scroll', function () {
            var scroll = $(window).scrollTop();
            if (scroll > position) {
                if (isPreEventComp && isDataAva && $(window).scrollTop() >= ($(document).height() - $(window).height()) * 0.7) {

                    isPreEventComp = false;
                    load_products();

                }
            } else {

            }
            position = scroll;
        });
        function load_products() {

            var apiVersion = '<?php echo $sapiVersion; ?>';
            var ENCODING = '<?php echo $sapiEncoding; ?>';
            var access_code = '<?php echo $sapiAccessCode; ?>';
            var partner_id = '<?php echo $partner; ?>';
            var user_id = '<?php echo $user_mobile; ?>';
            var type = '<?php echo $type; ?>';
            var session_id = '<?php echo $session_id; ?>';
            var influencer_commission_percentage = '<?php echo $influencer_commission_percentage; ?>';
            var start_from = $('#start_val').val();
            start_from = parseInt(start_from);
            var end_val = parseInt(start_from) + 6;

            $("#start_val").val(end_val);
            
            $.ajax({
                url: '<?php echo $sapiUrl; ?>',
                method: "POST",
                data: {VERSION: apiVersion, ENCODING: ENCODING, access_code: access_code, METHOD: "GET_PUBLISHER_PRODUCTS", partner_id: partner_id, start_from: start_from, max_results: 6},
                success: function (data)
                {
//                    document.getElementById("showMsg").style.display = "block";
                    $('#showMsg').show();
                    $('#showMsg').html("Loading more products...");
                    data = JSON.parse(data);
                    if (data['RESULT'] == 'SUCCESS') {
                        isPreEventComp = true;
                        
                        var response = data['RESPONSE'];
                        console.log(response);
                        if (response.length == '') {
                            isDataAva = false;
                            $('#showMsg').html("That's It");
                        }
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
        }
    </script>
</body>

</html>