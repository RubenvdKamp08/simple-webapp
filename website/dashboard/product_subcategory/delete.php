<?php
    include_once($_SERVER['DOCUMENT_ROOT'] . '/dashboard/header.php');
    $user = new User($conn);

    //Controleer als de gebruiker de rol Eigenaar, administrator of medewerker heeft.
    if($user->has_role('Eigenaar') || $user->has_role('Administrator') || $user->has_role('Medewerker')) {
        //Controleer als er een ID als GET parameter wordt meegegeven aan de URL.
        if (isset($_GET['id'])) {
            $id = $_GET['id'];

            //Controleer als het ID in een product voorkomt.
            $products = $conn->prepare("SELECT subcategoryID FROM products WHERE subcategoryID = :subcategoryID");
            $products->bindParam(":subcategoryID", $id);
            $products->execute();

            ///Zodra er een product bestaat met het subcategorieID geef een foutmelding, anders verwijder de subcategorie.
            if ($products->rowCount() > 0) {
                echo '<p>Het verwijderen van de subcategorie is mislukt, er is een product aanwezig die deze subcategorie al bevat, verwijder eerst het product of wijzig de subcategorie voordat u de categorie kan verwijderen. Klik <a href="/dashboard/product_subcategory/">hier</a> om terug te gaan</p>';
            } else {
                $delete_productsubcategory = $conn->prepare("DELETE FROM productsubcategory WHERE ID = :productsubcategoryID");
                $delete_productsubcategory->execute(array(
                    ':productsubcategoryID' => $id
                ));
                $user->redirect('/dashboard/product_subcategory');
            }
        } else {
            echo 'Productsubcategorie niet gevonden';
        }
    } else {
        $user->redirect('/dashboard');
    }