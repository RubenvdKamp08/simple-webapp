<?php
    include('header.php');
    include('functions/products.php');
    include('functions/shop.php');

    $product = new Product($conn);
    $user = new User($conn);
    $shop = new Shop($conn);

    //Controleer als er een POST request naar de server is gestuurd.
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        //Controleer als de gebruiker wel is ingelogd.
        if($user->is_loggedin()) {
            //Haal User ID op uit PHP Sessie
            $UserID = $_SESSION['user_session'];
            //Maak order aan.
            $shop->createOrder($UserID, 'verwerken');
            //Haal Order ID op.
            $OrderID = $conn->lastInsertId();
            //Loop door alle producten heen die in de winkelwagen staan.
            foreach($_SESSION["shopping_cart"] as $keys => $values) {
                $ProductID = $values['item_id'];
                //Controleer als het aantal van het product minimaal 1 is, zet het product aantal anders op 1.
                if(isset($_POST['amount-' . $ProductID])) {
                    if(($_POST['amount-' . $ProductID] === 0) || ($_POST['amount-' . $ProductID] < 0)) {
                        $amount = 1;
                    } else {
                        $amount = $_POST['amount-' . $ProductID];
                    }
                } else {
                    $amount = 1;
                }
                $price = $amount * $values['item_price'];
                //Voeg orderregel toe aan de database.
                $shop->createOrderLine($OrderID, $ProductID, $amount, $price);
            }
            //Leeg de winkelwagen PHP Sessie
            unset($_SESSION["shopping_cart"]);
            //Maak sessie aan met nieuw ordernummer en een unieke succes pagina.
            $_SESSION['order_succes'] = uniqid();
            $_SESSION['order_number'] = $OrderID;

            //Verstuur bevestigingsmail naar de klant en redirect de gebruiker vervolgens naar de succespagina.
            $shop->comfirmationMail($UserID, $OrderID);
            $user->redirect('/succes?id=' . $_SESSION['order_succes']);
        }
    }
?>

    <section class="content main-content">
        <div class="cart-header">
             <h1>Winkelwagen</h1>
        </div>
        <form method="post" action="" id="cart">
        <table class="cart-table">
            <?php
                //Controleer als er een PHP sessie is aangemaakt voor de winkelwagen.
                if(!empty($_SESSION["shopping_cart"]))
                {
                    ?>
                    <thead>
                    <tr>
                        <th>Product</th>
                        <th>Naam</th>
                        <th>Aantal</th>
                        <th>Prijs</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $total = 0;
                    $i = 1;
                    //Loop door alle producten heen die in de sessie staan opgeslagen.
                    foreach($_SESSION["shopping_cart"] as $keys => $values)
                    {
                        if(isset($_GET['action'])) {
                            if($values['item_id'] == $_GET["id"]) {
                                unset($_SESSION["shopping_cart"][$keys]);
                                $count = count($_SESSION["shopping_cart"]);
                                $user->redirect('/cart');
                            }
                        }
                        ?>
                        <tr class="cart-row">
                            <td><img src="/assets/images/products/<?php echo $values["item_image"]; ?>"></td>
                            <td><?php echo $values["item_name"]; ?></td>
                            <td><input type="number" class="cart-amount-<?php echo $i; ?>" id="cart-amount" min="1" name="amount-<?php echo $values["item_id"]; ?>" value="<?php echo $values["item_amount"]; ?>"></td>
                            <td>&euro; <span class="item-number-<?php echo $i; ?>" data-name="total-price" data-price="<?php echo number_format( $values["item_price"], 2); ?>"><?php echo number_format($values["item_amount"] * $values["item_price"], 2); ?></span></td>
                            <td><a href="/cart?action=delete&id=<?php echo $values["item_id"]; ?>">Verwijder <span class="text-danger"><i class="fa fa-times" aria-hidden="true"></i></span></a></td>
                        </tr>
                        <?php
                        $total = $total + ($values["item_amount"] * $values["item_price"]);
                        $i++;
                    }
                    ?>
                    <tr>
                        <td>Totaal bedrag</td>
                        <td>&euro; <span class="item-total"><?php echo number_format($total, 2); ?></span></td>
                        <td></td>
                    </tr>
                    </tbody>
                    <?php
                } else {
                    ?>
                    <p>Uw winkelmandje is leeg, klik <a href="/shop">hier</a> om naar de shop te gaan.</p>
                    <?php
                }
            ?>
        </table>
        </form>
        <?php
        //Controleer als er een PHP sessie is aangemaakt voor de winkelwagen.
        if(!empty($_SESSION["shopping_cart"])) {
            if($user->is_loggedin()) {
                ?>
                   <button type="submit" class="btn" form="cart">Bestelling plaatsen</button>
                <?php
            } else {
                ?>
                <a href="/inloggen?redirectUrl=cart" class="btn">Verder naar bestellen</a>
                <?php
            }
            ?>

            <?php
        }
        ?>
    </section>

<?php include('footer.php'); ?>