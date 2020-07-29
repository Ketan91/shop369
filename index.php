<?php

if(!isset($_REQUEST['partner'])){
header('location: https://www.shop369.org/products.php?partner=com.flickstree.official&type=buyer');
}else{
    if(!isset($_REQUEST['type'])){
        header('location: https://www.shop369.org/products.php?partner='.$_REQUEST['partner'].'&type=buyer');
    }else{
        header('location: https://www.shop369.org/products.php?partner='.$_REQUEST['partner'].'&type='.$_REQUEST['type']); 
    }
}


?>