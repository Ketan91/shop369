<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
include "functions.php"; // Get Configuration


$partner = sanitize_string($_REQUEST['partner']);
$type = sanitize_string($_REQUEST['type']);
$urlParams = "partner=$partner&type=$type";

if (isset($_COOKIE["FTAFF_MOBILE"])) {

    $session_id = $_COOKIE["FTAFF_SESSIONID"];
    $user_id = $_COOKIE["FTAFF_ID"];
    $user_name = $_COOKIE["FTAFF_NAME"];
    $user_mobile = $_COOKIE["FTAFF_MOBILE"];


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

    $name = $order_details_result['RESPONSE']['name'];
    $email = $order_details_result['RESPONSE']['email'];
    $mobile = $order_details_result['RESPONSE']['mobile'];
    $address = $order_details_result['RESPONSE']['payee_account_address'];
    ?>
    <!DOCTYPE html>
    <html lang="en">

        <head>
            <meta charset="utf-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1">
            <meta name="description" content="">
            <meta name="author" content="">
            <title>Profile </title>
            <link rel="shortcut icon" type="image/x-icon" href="images/favicon.ico">
            <!-- Vendor CSS -->
            <link href="js/vendor/bootstrap/bootstrap.min.css" rel="stylesheet">
            <link async href="css/custom_style_light.css" rel="stylesheet">

            <!--icon font-->
            <link async href="fonts/icomoon/font.css" rel="stylesheet">
            <link async href="css/custom.css" rel="stylesheet">
            <!--custom font-->
            <!--            <link href="https://fonts.googleapis.com/css?family=Montserrat:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
                        <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i,800,800i" rel="stylesheet">-->

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
                ::-webkit-scrollbar {
                    -webkit-appearance: none;
                    width: 7px;
                }

                ::-webkit-scrollbar-thumb {
                    border-radius: 4px;
                    background-color: #eae6e6;
                    box-shadow: 0 0 1px rgba(255, 255, 255, .5);
                }
                table {
                    margin: auto;
                    border-collapse: collapse;
                    overflow-x: auto;

                    max-width: 100%;
                    box-shadow: 0 0 1px 1px rgba(0, 0, 0, .1);
                }

                td, th {
                    text-align: center;
                    border: solid rgb(200, 200, 200) 1px;
                    padding: .5rem;
                }

                th {
                    text-align: left;
                    padding-top: 1rem;
                    padding-bottom: 1rem;
                    border-bottom: rgb(50, 50, 100) solid 2px;
                    border-top: none;
                }

                td {
                    white-space: nowrap;
                    border-bottom: none;
                    color: rgb(20, 20, 20);
                }

                td:first-of-type, th:first-of-type {
                    border-left: none;
                }

                td:last-of-type, th:last-of-type {
                    border-right: none;
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
                .table-order-history td:last-child {
                    text-align: left;
                }
                .minicart-title {
                    padding-left: 37px !important;
                }
                @media screen and (max-width: 991px){
                    .col-sm-3 {
                        margin-bottom: 5px;
                    }
                    .footer-block {
                        margin: 0 -15px;
                        margin-bottom: 47px !important;
                    } 
                    .hdr-mobile-style2 .hdr-mobile .logo-holder {
                        /*margin-left: 267px;*/
                    }
                    .hdr-content .dropdn-link:hover [class*='icon-'] {
                        color: #DA222B;
                    }
                    .hdr-content .dropdn-link > .icon, .hdr-bottom .dropdn-link > .icon {
                        font-size: 18px;
                    }
                    .col-auto.hdr-content-right {
                        margin-left: 80px;
                    }
                    .col-auto.hdr-content-right {
                        line-height: 11px;
                    }
                    .hdr-mobile-style2 .hdr-mobile.is-sticky {
                        position: fixed;
                        top: 0px;
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

                .sticky-holder {
                    top: 0px;
                }
                #divLoading{display:none}
                #divLoading{
                    position:fixed;
                    z-index:100;
                    left:0;bottom:0;right:0;top:200px;z-index:999
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

                <!-- Mobile Menu -->
                
                <!-- /Mobile Menu -->
                <div class="hdr-mobile show-mobile">
                    <div class="hdr-content">
                        <div class="container">
                            <!-- Menu Toggle -->
                            <!--<div class="menu-toggle"><a href="#" class="mobilemenu-toggle"><i class="icon icon-menu"></i></a></div>-->
                            <!-- /Menu Toggle -->
                            <?php echo'<div class="logo-holder"><a href="products.php?partner=' . $partner . '&type=' . $type . '" class="logo"><img src="images/logo/shop369_logo.png" srcset="images/logo/shop369_logo.png" alt=""></a></div>'; ?>
                            <div class="col-auto hdr-content-right">

                                <div class="hdr-mobile-right">
                                    <div class="hdr-topline-right links-holder"><div class="dropdn dropdn_account only-icon"  style="text-align: center;">
                                            <?php echo '<a href="profile.php?partner=' . $partner . '&type=' . $type . '" style="font-size: 9px !important;color: #30282b;padding-right: 0px !important;"><i class="icon icon-person" style="font-size: 13px;"></i><br>' . strtoupper($user_name) . '<br>' . $user_mobile . '</a>'; ?>
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
                                    <?php echo'<div class="menu-toggle hide-mobile"></div>
                                <a href="products.php?partner=' . $partner . '&type=' . $type . '" class="logo"><img src="images/logo/shop369_logo.png" srcset="images/logo/shop369_logo.png" alt=""></a>
                            </div>'; ?>
                                    <div class="col search-holder nav-holder" style="max-width: 61%;height: 28px;">

                                    </div>

                                    <?php echo'<div class="col-auto minicart-holder">
                            <div class=""><a href="javascript:void(0);" class="minicart-link" onclick="profile()" style="text-decoration:none;"><i class="icon icon-person" style="text-align: center;"></i>  <span class="minicart-title" style="text-align: center;"><b>' . strtoupper($user_name) . '</b></span> <span class="minicart-title" style="text-align: center;">' . $user_mobile . '</span></a>'; ?>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="sticky-holder compensate-for-scrollbar">
                <div class="container">
                    <!--<div class="row"><a href="#" class="mobilemenu-toggle show-mobile"><i class="icon icon-menu"></i></a>-->
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
            <div class="page-content" style="height: 500px;">
            <div class="holder mt-0">
                <div class="container">
                    <ul class="breadcrumbs">

                    </ul>
                </div>
            </div>

            <div class="holder mt-0">
                <br><br>
                <div class="container">
                    <div class="row">

                        <div class="col-md-12 aside"  id="sales-section">
                           
                            <!-- <h2>Order History</h2> -->
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-order-history">
                                   
                                    <tbody id="sales_data">
                                         <tr>
                                            <td>Name :</td>
                                           <td><?php echo $name;?></td>
                                          </tr>
                                          <tr>
                                            <td>Mobile :</td>
                                            <td><?php echo $mobile;?></td>
                                          </tr>
                                          <tr>
                                            <td>Email :</td>
                                            <td><?php echo $email;?></td>
                                          </tr>
                                          <tr>
                                            <td>Address :</td>
                                            <td><?php echo $address;?></td>
                                          </tr>
                                       
                                    </tbody>
                                </table>
                            </div>

                        </div>
                       
                    </div>
                </div>
            </div>

        </div>
        <?php include('footer.php'); ?>

        <script src="js/vendor/jquery/jquery.min.js"></script>
        <script src="js/vendor/bootstrap/bootstrap.bundle.min.js"></script>
        <script defer src="js/vendor/slick/slick.min.js"></script>
        <script defer src="js/vendor/scrollLock/jquery-scrollLock.min.js"></script>
        <script  src="js/vendor/imagesloaded/imagesloaded.pkgd.min.js"></script>
        <script async src="js/vendor/ez-plus/jquery.ez-plus.min.js"></script>
        <script src="js/vendor/bootstrap-tabcollapse/bootstrap-tabcollapse.min.js"></script>
        <script defer src="js/customize_app.min.js"></script>
        <script>
            function profile() {
                var partner_id = '<?php echo $partner; ?>';
                var type = '<?php echo $type; ?>';
                window.location.href = 'profile.php?partner=' + partner_id + '&type=' + type;
            }

        </script>
    </body>

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
            Incorrect Request! <br /><br /> Back to <a href="products.php?<?php echo $urlParams; ?>">Products</a>
        </body>
    </html>
<?php } ?>
