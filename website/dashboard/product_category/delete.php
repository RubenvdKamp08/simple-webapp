<?php
    include_once($_SERVER['DOCUMENT_ROOT'] . '/dashboard/header.php');
    $user = new User($conn);

    //Controleer als de gebruiker de rol Eigenaar, administrator of medewerker heeft.
    if($user->has_role('Eigenaar') || $user->has_role('Administrator') || $user->has_role('Medewerker')) {
        //Controleer als er een GET parameter van ID wordt meegegeven in de URL.
        if (isset($_GET['id'])) {
            $id = $_GET['id'];

            //Controleer als er een product bestaat met de opgevraagde productcategorie ID.
            $products = $conn->prepare("SELECT categoryID FROM products WHERE categoryID = :CategoryID");
            $products->bindParam(":CategoryID", $id);
            $products->execute();

            //Controleer als er minimaal 1 product bestaat met het opgevraagde categorie ID, zoja geef een foutmelding en anders verwijder de productcategorie.
            if ($products->rowCount() > 0) {
                echo '<p>Het verwijderen van de categorie is mislukt, er is een product aanwezig die deze categorie al bevat, verwijder eerst het product of wijzig de categorie voordat u de categorie kan verwijderen. Klik <a href="/dashboard/product_category/">hier</a> om terug te gaan</p>';
            } else {
                $delete_productcategory = $conn->prepare("DELETE FROM productcategory WHERE ID = :productcategoryID");
                $delete_productcategory->execute(array(
                    ':productcategoryID' => $id
                ));
                $user->redirect('/dashboard/product_category');
            }
        } else {
            echo 'Productcategorie niet gevonden';
        }
    } else {
        $user->redirect('/dashboard');
    }