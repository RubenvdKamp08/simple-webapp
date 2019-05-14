<?php
    include_once($_SERVER['DOCUMENT_ROOT'] . '/dashboard/header.php');
    $user = new User($conn);

    //Controleer als de gebruiker de rol Eigenaar of administrator heeft.
    if($user->has_role('Eigenaar') || $user->has_role('Administrator')) {
        //Controleer als er een ID als GET parameter wordt meegegeven aan de URL.
        if (isset($_GET['id'])) {
            $id = $_GET['id'];

            //Controleer als er een nieuwsbericht bestaat met de opgevraagde categorie.
            $news = $conn->prepare("SELECT categoryID FROM news WHERE categoryID = :CategoryID");
            $news->bindParam(":CategoryID", $id);
            $news->execute();


            //Als er een nieuwsbericht met de opgevraagde categorie is geef een foutmelding, anders verwijder nieuwscategorie.
            if ($news->rowCount() > 0) {
                echo '<p>Het verwijderen van de categorie is mislukt, er is een nieuwsbericht aanwezig die deze categorie al bevat, verwijder eerst het nieuwsbericht of wijzig de categorie voordat u de categorie kan verwijderen. Klik <a href="/dashboard/news_category/">hier</a> om terug te gaan</p>';
            } else {
                //Verwijder nieuwscategorie uit de database.
                $delete_newscategory = $conn->prepare("DELETE FROM newscategory WHERE ID = :newscategoryID");
                $delete_newscategory->execute(array(
                    ':newscategoryID' => $id
                ));
                $user->redirect('/dashboard/news_category/');
            }
        } else {
            echo 'Nieuwsitem niet gevonden';
        }
    } else {
        $user->redirect('/dashboard');
    }