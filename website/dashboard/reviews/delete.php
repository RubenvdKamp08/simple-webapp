<?php
    include_once($_SERVER['DOCUMENT_ROOT'] . '/dashboard/header.php');
    $user = new User($conn);

    //Controleer als de gebruiker de rol Eigenaar, administrator of medewerker heeft.
    if($user->has_role('Eigenaar') || $user->has_role('Administrator') || $user->has_role('Medewerker')) {
        //Controleer als er een ID als GET parameter wordt meegegeven aan de URL.
        if (isset($_GET['id'])) {
            $id = $_GET["id"];

            //Verwijder de opgevraagde review.
            $delete_reviews = $conn->prepare('DELETE FROM reviews WHERE ID = :reviewid');
            $delete_reviews->execute(array(
                ':reviewid' => $id));
            $user->redirect('/dashboard/reviews');
        } else {
            echo 'Review kon niet worden gevonden';
        }
    } else {
        $user->redirect('/dashboard');
    }
?>