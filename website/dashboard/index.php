<?php include('header.php');
    $user = new User($conn);
    //Haal alle variabelen op voor het dashboard.
    $countUsers = $conn->prepare("select count(*) from customer");
    $countContact = $conn->prepare("select count(*) from contact");
    $countProducten = $conn->prepare("select count(*) from products");
    $countNews = $conn->prepare("select count(*) from news");
    $countReviews = $conn->prepare("select count(*) from reviews");
    $countOrders = $conn->prepare("select count(*) from orders");
?>
<div class="dashblocks">
    <div class="dashblock">
        <div class="innerdashblock">
            <i class="fa fa-shopping-basket" aria-hidden="true"></i>
            <?php
                $countOrders->execute();
                while ($row = $countOrders->fetch()) {
                    $orderAmount = $row["count(*)"];
                }
                echo "<a> $orderAmount</a>";
            ?>
        </div>
        <div class="dashblocktitle">
            <h2>Bestellingen</h2>
        </div>
    </div>
    <div class="dashblock">
        <div class="innerdashblock">
            <i class="fa fa-users" aria-hidden="true"></i>
            <?php
                $countUsers->execute();
                while ($row = $countUsers->fetch()) {
                    $hoeveelheidUsers = $row["count(*)"];
                }
                echo "<a> $hoeveelheidUsers</a>";
            ?>
        </div>
        <div class="dashblocktitle">
            <h2>Accounts</h2>
        </div>
    </div>
    <div class="dashblock">
        <div class="innerdashblock">
            <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
            <?php
                $countContact->execute();
                while ($row = $countContact->fetch()) {
                    $hoeveelheidContact = $row["count(*)"];
                }
                echo "<a> $hoeveelheidContact </a>";
            ?>
        </div>
        <div class="dashblocktitle">
            <h2>Contactformulieren</h2>
        </div>
    </div>
    <div class="dashblock">
        <div class="innerdashblock">
            <i class="fa fa-shopping-bag" aria-hidden="true"></i>
            <?php
                $countProducten->execute();
                while ($row = $countProducten->fetch()) {
                    $hoeveelheidProducten = $row["count(*)"];
                }
                echo "<a href='/dashboard/products'> $hoeveelheidProducten </a>";
            ?>
        </div>
        <div class="dashblocktitle">
            <h2>Producten</h2>
        </div>
    </div>
    <div class="dashblock">
        <div class="innerdashblock">
            <i class="fa fa-newspaper-o" aria-hidden="true"></i>
            <?php
                $countNews->execute();
                while ($row = $countNews->fetch()) {
                    $hoeveelheidNieuws = $row["count(*)"];
                }
                echo "<a> $hoeveelheidNieuws </a>";
            ?>
        </div>
        <div class="dashblocktitle">
            <h2>Nieuwsartikelen</h2>
        </div>
    </div>
    <div class="dashblock">
        <div class="innerdashblock">
            <i class="fa fa-star" aria-hidden="true"></i>
            <?php
                $countReviews->execute();
                while ($row = $countReviews->fetch()) {
                    $hoeveelheidReviews = $row["count(*)"];
                }
                echo "<a> $hoeveelheidReviews </a>";
            ?>
        </div>
        <div class="dashblocktitle">
            <h2>Reviews</h2>
        </div>
    </div>
</div>

<?php include('footer.php'); ?>
