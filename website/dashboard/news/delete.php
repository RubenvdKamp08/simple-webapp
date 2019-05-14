<?php
    include_once($_SERVER['DOCUMENT_ROOT'] . '/dashboard/header.php');
    $user = new User($conn);

    //Controleer als de gebruiker de rol Eigenaar of administrator heeft.
    if($user->has_role('Eigenaar') || $user->has_role('Administrator')) {
        //Controleer als er een ID als GET parameter in de URL wordt meegegeven.
        if (isset($_GET['id'])) {
            $id = $_GET['id'];

            //Haal de afbeelding op van het nieuwsbericht.
            $newsitem = $conn->prepare("SELECT image FROM news WHERE ID = " . $id);
            $newsitem->execute();
            $newsitemimage = $newsitem->fetch(PDO::FETCH_ASSOC);

            //Verwijder nieuwsbericht uit de database en verwijder de afbeelding van de server.
            $stmt = $conn->prepare("DELETE FROM news WHERE ID = " . $id);
            $stmt->execute();
            unlink($_SERVER['DOCUMENT_ROOT'] . '/assets/images/news/' . $newsitemimage['image']);

            $user->redirect('/dashboard/news');
        } else {
            echo 'Product niet gevonden';
        }
    } else {
        $user->redirect('/dashboard');
    }