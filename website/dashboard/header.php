<?php
    include($_SERVER['DOCUMENT_ROOT'] . '/lib/connection.php');
    include($_SERVER['DOCUMENT_ROOT'] . '/functions/user.php');

    $user = new User($conn);
    if(!$user->is_loggedin()) {
        $user->redirect('/404');
    } else {
        if($user->has_role('Klant')) {
            $user->redirect('/');
        }
    }

?>
<!DOCTYPE HTML>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width"/>
    <title>De Plantage</title>
    <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
    <link rel="icon" href="/assets/images/favicon.png">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="//netdna.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="/dashboard/assets/css/dashboard.css">
    <link rel="stylesheet" type="text/css" href="/assets/css/slicknav.css">
    <link rel="stylesheet" type="text/css" href="/assets/css/slick.css">
    <link rel="stylesheet" type="text/css" href="/dashboard/assets/css/review.css">
    <link rel="stylesheet" type="text/css" href="/dashboard/assets/css/newscategory.css">
    <link rel="stylesheet" type="text/css" href="/dashboard/assets/css/monthlyproduct.css">
    <link rel="stylesheet" type="text/css" href="/dashboard/assets/css/news.css">
    <link rel="stylesheet" type="text/css" href="/dashboard/assets/css/lookcontact.css">
    <link rel="stylesheet" type="text/css" href="/dashboard/assets/css/orders.css">
    <link rel="stylesheet" type="text/css" href="/dashboard/assets/css/edit.css">
</head>
<body>
<div class="dashcontent">
    <div class="dashleft">
        <div class="logo">
            <a href="/"><img src="/assets/images/dashlogo.png"></a>
        </div>
        <div class="dashnav">
            <div class="dropdown">
                <button class="dropbtn"><i class="fa fa-bars" aria-hidden="true"></i></button>
                <div class="dropdown-content">
                    <a href="/dashboard">Home</a>
                    <a href="/dashboard/orders">Bestellingen</a>
                    <a href="/dashboard/products">Producten</a>
                    <a href="/dashboard/reviews">Reviews</a>
                    <?php
                    if($user->has_role('Eigenaar') || $user->has_role('Administrator')) {
                        ?>
                        <a href="/dashboard/role/">Accounts</a>
                        <a href="/dashboard/news">Nieuws</a>
                        <a href="/dashboard/contact">Contact</a>
                        <a href="/dashboard/slider">Slider</a>
                        <?php
                    }
                    ?>

                    <a href="/dashboard/montlyproduct">Product van de maand</a>

                </div>
            </div>
            <ul id="dashmenu">
                <li><i class="fa fa-home" aria-hidden="true"></i><a href="/dashboard">Home</a></li>
                <li><i class="fa fa-shopping-basket" aria-hidden="true"></i><a href="/dashboard/orders/">Bestellingen</a></li>
                <li><i class="fa fa-shopping-bag" aria-hidden="true"></i><a href="/dashboard/products">Producten</a></li>
                <li><i class="fa fa-star" aria-hidden="true"></i><a href="/dashboard/reviews">Reviews</a></li>
                <?php
                if($user->has_role('Eigenaar') || $user->has_role('Administrator')) {
                    ?>
                    <li><i class="fa fa-users" aria-hidden="true"></i><a href="/dashboard/role">Accounts</a></li>
                    <li><i class="fa fa-newspaper-o" aria-hidden="true"></i><a href="/dashboard/news">Nieuws</a></li>
                    <li><i class="fa fa-pencil-square-o" aria-hidden="true"></i><a href="/dashboard/contact">Contact</a></li>
                    <li><i class="fa fa-cog" aria-hidden="true"></i><a href="/dashboard/slider">Slider</a></li>
                    <?php
                }
                ?>
                <li><i class="fa fa-coffee" aria-hidden="true"></i><a href="/dashboard/montlyproduct">Product v/d maand</a></li>
            </ul>
        </div>
        <div class="header-mobile"></div>
    </div>
    <div class="dashright">
        <div class="dashrightupper">
            <a>DASHBOARD</a>
        </div>
        <div class="dashrightunder">
            <div class="dashheader">
                <a href="/dashboard">Dashboard</a>
            </div>
            <div class="dashbody">