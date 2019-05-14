<?php
    include_once($_SERVER['DOCUMENT_ROOT'] . '/dashboard/header.php');
    $user = new User($conn);

    //Controleer als de gebruiker de rol Eigenaar, administrator of medewerker heeft.
    if($user->has_role('Eigenaar') || $user->has_role('Administrator') || $user->has_role('Medewerker')) {
        //Controleer als er een GET parameter aan de URL wordt toegevoegd van ID
        if (isset($_GET['id'])) {
            $id = $_GET["id"];
            //Verwijder de order uit de database.
            $deleteOrder = $conn->prepare("DELETE FROM orders WHERE ID=:id");
            $deleteOrder->execute(array(
                ":id" => $id
            ));

            //Verwijder alle orderlines die het opgevraagde order ID bevatten.
            $deleteOrderlines = $conn->prepare("DELETE FROM orderlines WHERE OrderID=:id");
            $deleteOrderlines->execute(array(
                ":id" => $id
            ));

            $user->redirect('/dashboard/orders');
        } else {
            echo 'Order niet gevonden!';
        }
    } else {
        $user->redirect('/dashboard');
    }
