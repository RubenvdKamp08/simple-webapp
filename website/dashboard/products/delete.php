<?php
    include_once($_SERVER['DOCUMENT_ROOT'] . '/dashboard/header.php');
    include_once($_SERVER['DOCUMENT_ROOT'] . '/functions/products.php');
    $user = new User($conn);
    $product = new Product($conn);

    //Controleer als de gebruiker de rol Eigenaar, administrator of medewerker heeft.
    if($user->has_role('Eigenaar') || $user->has_role('Administrator') || $user->has_role('Medewerker')) {
        //Controleer als er een ID als GET parameter wordt meegegeven aan de URL.
        if (isset($_GET['id'])) {
            $id = $_GET['id'];

            //Controleer als er een Order bestaat met dit product.
            $orderlines = $conn->prepare("SELECT ProductID FROM orderlines WHERE ProductID = :ProductID");
            $orderlines->bindParam(":ProductID", $id);
            $orderlines->execute();

            //Zodra er al een order bestaat met dit product geef een error bericht, anders verwijder het product uit de database.
            if ($orderlines->rowCount() > 0) {
                echo '<p>Het verwijderen van het product is mislukt, dit product is al een keer besteld. Om het product niet meer te tonen moet je terug gaan naar producten en zet de status op "Niet beschikbaar". Klik <a href="/dashboard/products">hier</a> om terug te gaan</p>';
            } else {
                $product->deleteProduct($id);
                $user->redirect('/dashboard/products');
            }
        } else {
            echo 'Product niet gevonden';
        }
    } else {
        $user->redirect('/dashboard');
    }