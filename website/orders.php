<?php
include('header.php');

//Controleer als gebruiker wel is ingelogd.
if($user->is_loggedin()) {
    if (isset($_SESSION['user_session'])) {
        //Haal UserID op uit de sessie.
        $userid = $_SESSION['user_session'];

        //Haal alle orders op.
        $order = $conn->prepare("SELECT o.ID, o.date, o.Status, SUM(be.price) as totaal, be.amount FROM orders as o JOIN orderlines as be ON o.ID = be.OrderID JOIN products as p ON be.ProductID = p.ID WHERE o.CustomerID = :id GROUP BY o.ID");
        $order->bindparam(":id", $userid);
        $order->execute();

        function datetime() {
            return date('d-m-Y', time());
        }
    }
    ?>
    <section class="content main-info">

        <div class="allcontent">

            <div class="orderleft">
                <h2>Mijn account</h2>
                <ul>
                    <li><a href="/orders">Bestellingen</a></li>
                    <li><a href="/logout">Uitloggen</a></li>
                </ul>
            </div>

            <div class="orderright">
                <h1>Mijn bestellingen</h1>

                <?php
                    if ($order->rowCount() > 0) {
                        foreach ($order as $bestelling) {
                            ?>
                            <div class="fullorder">
                                <div class="ordercontent">
                                    <div class="contentleft">
                                        <p><?php echo "Bestelnummer: <b>" . $bestelling['ID'] . "</b>" ?></p>
                                        <a><?php echo datetime(); ?></a>
                                    </div>
                                    <div class="contentright">
                                        <div class="status">
                                            <a>Status: <b><?php echo $bestelling['Status']; ?></b></a>
                                        </div>
                                        <div class="ordertotal">
                                            <a>Totaal €<?php echo $bestelling['totaal']; ?></a>
                                        </div>
                                        <div class="seeorder">
                                            <a href="/vieworder?id=<?php echo $bestelling["ID"]; ?>"><b>Bekijk</b></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                    } else {
                        echo 'U heeft nog geen bestellingen geplaatst';
                    }
                ?>
        </div>

    </section>
    <?php
} else {
    $user->redirect('/404');
}
    include('footer.php'); ?>
