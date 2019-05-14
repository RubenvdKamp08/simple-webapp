<?php
    include_once($_SERVER['DOCUMENT_ROOT'] . '/dashboard/header.php');
    include_once($_SERVER['DOCUMENT_ROOT'] . '/functions/products.php');
    $user = new User($conn);
    //Controleer als gebruiker rol Eigenaar of Admin heeft, vervolgens kijken als ID opgevraagd kan worden en daarna contactformulier verwijderen en doorsturen naar contact overzicht.
    if($user->has_role('Eigenaar') || $user->has_role('Administrator')) {
        if(isset($_GET['id'])) {
            $id = $_GET['id'];
            $stmt = $conn->prepare("DELETE FROM contact WHERE ID = " . $id);
            $stmt->execute();
            $user->redirect('/dashboard/contact');
        } else {
            echo 'Contactformulier niet gevonden';
        }
    } else {
        $user->redirect('/dashboard');
    }
