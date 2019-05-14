<?php
    include_once($_SERVER['DOCUMENT_ROOT'] . '/dashboard/header.php');
    $user = new User($conn);

    //Controleer als de gebruiker de rol Eigenaar, administrator of medewerker heeft.
    if($user->has_role('Eigenaar') || $user->has_role('Administrator') || $user->has_role('Medewerker')) {
        //Controleer als er een GET parameter aan de URL wordt toegevoegd van ID
        if (isset($_GET["id"])) {
            $id = $_GET["id"];

            //Update order status.
            $status = $conn->prepare("UPDATE orders SET status='afgerond' WHERE ID=:id");
            $status->execute(array(
                ":id" => $id
            ));

            //Vraag de klant ID op uit de database
            $order = $conn->prepare("SELECT CustomerID FROM orders WHERE ID = :id");
            $order->execute(array(
                ":id" => $id
            ));
            $customerID = $order->fetch(PDO::FETCH_ASSOC);

            //Vraag alle klantgegevens op uit de database.
            $customer = $conn->prepare("SELECT * FROM customer WHERE ID = :id");
            $customer->execute(array(
                ":id" => $customerID['CustomerID']
            ));
            $siteurl = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
            //Loop door de klantgegevens heen en stuur de klant een bevestigingsemail zodat de klant weet dat hij zijn bestelling kan komen ophalen in de winkel.
            foreach ($customer as $item) {
                $subjectklant = 'Uw bestelling staat klaar!';
                $messageklant = 'Beste ' . $item['firstname'] . ', <br><br>
                                                    Uw bestelling met het ordernummer: ' . $id . ' staat klaar in onze winkel en die kunt u vanaf nu afhalen.<br>
                                                    Klik <a href="' . "http://" . $_SERVER['SERVER_NAME'] . '/vieworder?id=' . $id . '">hier</a> om uw bestelling te bekijken.<br><br><br>
                                                    de Plantage<br>
                                                    Bloemstraat 22<br>
                                                    8081 CW, Elburg<br>
                                                    0525-842787<br>
                                                    info@deplantage-elburg.nl<br><br>
                                                    <img width="250" src="'. $siteurl .'/assets/images/logo.png" alt="de Plantage"><br>';
                $headers[] = 'From: de Plantage Elburg <no-reply@plantagedevelopment.nl>' . "\r\n" .
                    'Reply-To: info@plantagedevelopment.nl' . "\r\n" . 'X-Mailer: PHP/' . phpversion();
                $headers[] = 'MIME-Version: 1.0';
                $headers[] = 'Content-type: text/html; charset=iso-8859-1';

                mail($item['email'], $subjectklant, $messageklant, implode("\r\n", $headers));
            }

            $user->redirect('/dashboard/orders');
        }
    } else {
        $user->redirect('/dashboard');
    }