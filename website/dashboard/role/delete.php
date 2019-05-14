<?php
    include_once($_SERVER['DOCUMENT_ROOT'] . '/dashboard/header.php');
    $user = new User($conn);

    //Controleer als de gebruiker de rol Eigenaar of administrator heeft.
    if($user->has_role('Eigenaar') || $user->has_role('Administrator')) {
        //Controleer als er een ID als GET parameter wordt meegegeven aan de URL.
        if (isset($_GET['id'])) {
            $id = $_GET["id"];
            //Verwijder gebruiker uit de database.
            $customer_delete = $conn->prepare("DELETE FROM customer WHERE ID = :customerID");
            $customer_delete->execute(array(
                ':customerID' => $id
            ));
            $user->redirect('/dashboard/role');
        } else {
            echo 'Gebruiker niet gevonden';
        }
    } else {
        $user->redirect('/dashboard');
    }

    include_once($_SERVER['DOCUMENT_ROOT'] . '/dashboard/footer.php');
?>