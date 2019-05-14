<?php
    class Shop {

        private $db;

        function __construct($conn) {
            //Use construct so we can use the current connection easily.
            $this->db = $conn;
        }

        public function createOrder($CustomerID, $status) {
            //Maak nieuwe order aan.
            $stmt = $this->db->prepare("INSERT INTO orders(CustomerID, status) 
                                                       VALUES(:CustomerID, :status)");

            $stmt->bindparam(":CustomerID", $CustomerID);
            $stmt->bindparam(":status", $status);
            $stmt->execute();

            return $stmt;
        }

        public function createOrderLine($OrderID, $ProductID, $amount, $price) {
            //Maak nieuwe orderregel aan.
            $stmt = $this->db->prepare("INSERT INTO orderlines(OrderID, ProductID, Amount, price) 
                                                       VALUES(:OrderID, :ProductID, :Amount, :Price)");

            $stmt->bindparam(":OrderID", $OrderID);
            $stmt->bindparam(":ProductID", $ProductID);
            $stmt->bindparam(":Amount", $amount);
            $stmt->bindparam(":Price", $price);
            $stmt->execute();

            return $stmt;
        }

        public function comfirmationMail($CustomerID, $ordernumber) {
            //Verstuur bevestigingsmail voor bestelling.
            $siteurl = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]";

            //Haal gebruiker op
            $stmt = $this->db->prepare("SELECT firstname, email FROM customer WHERE ID = :customerID");
            $stmt->bindparam(":customerID", $CustomerID);
            $stmt->execute();

            foreach ($stmt as $customer) {
                $subjectklant = 'Bevestiging order ' . $ordernumber;
                $messageklant = 'Beste ' . $customer['firstname'] . ', <br><br>
                                                    Bedankt voor uw bestelling, uw ordernummer is ' . $ordernumber . '.<br>
                                                    Klik <a href="' . "http://" . $_SERVER['SERVER_NAME'] .
                    '/vieworder?id=' . $ordernumber . '">hier</a> om uw bestelling te bekijken.<br><br><br>
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

                mail($customer['email'], $subjectklant, $messageklant, implode("\r\n", $headers));
            }

            $email = 'info@plantagedevelopment.nl';
            $subject = 'Nieuwe bestelling: ' . $ordernumber;
            $message = 'Nieuwe bestelling, ' . ', <br><br>
                                                    Er is een nieuwe bestelling geplaatst met het ordernummer: ' . $ordernumber . '.<br>
                                                    Klik <a href="' . "http://" . $_SERVER['SERVER_NAME'] .
                '/dashboard/orders/order?id=' . $ordernumber . '">hier</a> om de nieuwe order te bekijken.<br><br><br>
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

            mail($email, $subject, $message, implode("\r\n", $headers));
        }
    }