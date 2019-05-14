<?php
    include_once($_SERVER['DOCUMENT_ROOT'] . '/dashboard/header.php');
    include_once($_SERVER['DOCUMENT_ROOT'] . '/functions/slide.php');
    $user = new User($conn);
    $slide = new Slide($conn);

    //Controleer als de gebruiker de rol Eigenaar of administrator heeft.
    if($user->has_role('Eigenaar') || $user->has_role('Administrator')) {
        //Controleer als er een GET parameter aan de URL wordt toegevoegd van ID
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            //Verwijder de slide en redirect de user terug naar slider overzicht.
            $slide->deleteSlide($id);
            $user->redirect('/dashboard/slider');
        } else {
            echo 'Slide niet gevonden';
        }
    } else {
        $user->redirect('/dashboard');
    }