<?php include('header.php');
$firstname = '';
$middlename = '';
$lastname = '';
$email = '';
$phonenumber = '';
$subject = '';
$message = '';
$contacterror = false;

//Controleer als er een POST request is gestuurd.
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $_POST = array_map('strip_tags', $_POST);
    $firstname = $_POST["firstname"];
    $middlename = $_POST["middlename"];
    $lastname = $_POST["lastname"];
    $email = $_POST["email"];
    $phonenumber = $_POST["phonenumber"];
    $subject = $_POST["subject"];
    $message = $_POST["message"];

    //Controleer als alle velden zijn ingevuld.
    if(isset($firstname, $lastname, $email, $phonenumber, $subject, $message) && !empty($firstname && $lastname && $email && $phonenumber && $subject && $message)) {
            //Controleer als emailadres daadwerkelijk een emailadres is.
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                //Controleer als Recaptcha is ingevuld.
                if (isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])) {
                    //Recaptcha key
                    $secret = '6LeESTkUAAAAAJ7wfXVne6e9rBBdquHvF2alnBkU';
                    //Response data
                    $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . $secret . '&response=' . $_POST['g-recaptcha-response']);
                    $responseData = json_decode($verifyResponse);
                    if ($responseData->success) {
                        //Zet contactinzending in de database.
                        $sql = "INSERT INTO contact (firstname, middlename, lastname, email, phonenumber, subject, message) VALUES (:firstname, :middlename, :lastname, :email, :phonenumber, :subject, :message)";
                        $stm = $conn->prepare($sql);
                        $stm->execute(array(
                            ':firstname' => $firstname,
                            ':middlename' => $middlename,
                            ':lastname' => $lastname,
                            ':email' => $email,
                            ':phonenumber' => $phonenumber,
                            ':subject' => $subject,
                            ':message' => $message,
                        ));

                        //Haal de website URL op.
                        $siteurl = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
                        //Verstuur bevestigingsemail naar de klant.
                        $toklant = $email;
                        $subjectklant = 'Bedankt voor uw bericht';
                        $messageklant = 'Hallo ' . $firstname . ', <br><br>
                                    we hebben uw bericht ontvangen en zullen zo spoedig mogelijk bericht met u opnemen.<br><br><br>
                                    de Plantage<br>
                                    Bloemstraat 22<br>
                                    8081 CW, Elburg<br>
                                    0525-842787<br>
                                    info@deplantage-elburg.nl<br><br>
                                    <img width="250" src="' . $siteurl . '/assets/images/logo.png" alt="de Plantage"><br>
                                    ';
                        $headers[] = 'From: de Plantage Elburg <no-reply@plantagedevelopment.nl>' . "\r\n" .
                            'Reply-To: info@plantagedevelopment.nl' . "\r\n" .
                            'X-Mailer: PHP/' . phpversion();
                        $headers[] = 'MIME-Version: 1.0';
                        $headers[] = 'Content-type: text/html; charset=iso-8859-1';

                        mail($toklant, $subjectklant, $messageklant, implode("\r\n", $headers));

                        //Verstuur email naar de plantage.
                        $tomail = 'info@plantagedevelopment.nl';
                        $subjectmail = 'Contactformulier - Onderwerp: ' . $subject;
                        $messagemail = 'Automatisch bericht (ingevuld contactformulier)<br>
                                    Van: ' . $firstname . ' ' . $middlename . ' ' . $lastname . '<br>
                                    Onderwerp: ' . $subject . '<br>
                                    E-mail: ' . $email . '<br>
                                    Telefoonnummer: ' . $phonenumber . '<br><br>
                                    Bericht:<br> ' . $message . '
                                                                
                                    ';
                        $headersmail[] = 'From: ' . $firstname . ' ' . $middlename . ' ' . $lastname . '<no-reply@plantagedevelopment.nl>' . "\r\n" .
                            'Reply-To: ' . $email . "\r\n" .
                            'X-Mailer: PHP/' . phpversion();
                        $headersmail[] = 'MIME-Version: 1.0';
                        $headersmail[] = 'Content-type: text/html; charset=iso-8859-1';

                        mail($tomail, $subjectmail, $messagemail, implode("\r\n", $headersmail));

                        $firstname = '';
                        $middlename = '';
                        $lastname = '';
                        $email = '';
                        $phonenumber = '';
                        $subject = '';
                        $message = '';
                    }
                }
            } else {
                $contacterror = true;
            }
    } else {
        $contacterror = true;
    }
}
?>

<section class="content main-content">
    <div class="heading">
        <h2>Contactformulier</h2>
    </div>
    <div class="main-contact">
        <div class="contact-form">
            <form method="post">

                <input type="text" name="firstname" placeholder="Naam*" value="<?php echo $firstname ?>" required><br><br>

                <input type="text" name="middlename" placeholder="Tussenvoegsel" value="<?php echo $middlename ?>"><br><br>

                <input type="text" name="lastname" placeholder="Achternaam*" value="<?php echo $lastname ?>" required><br><br>

                <input type="email" name="email" placeholder="E-mail*" value="<?php echo $email ?>" required><br><br>

                <input type="text" name="phonenumber" placeholder="Telefoonnummer" value="<?php echo $phonenumber ?>"><br><br>

                <input type="text" name="subject" placeholder="Onderwerp*" value="<?php echo $subject ?>" required><br><br>
                <div class="message">
                    <textarea name="message" placeholder="Bericht*" required maxlength="2000" required><?php echo $message ?></textarea><br><br>
                </div>
                <div class="g-recaptcha" data-sitekey="6LeESTkUAAAAAIpBfp_ocb0-21UbKJzthvPaIX3r"></div>
                <div class="submit">
                    <button name="submit" type="submit" value="verzend">Verzend</button>
                </div>
            </form>
            <?php
                //Controleer als er een POST request is gestuurd naar de server.
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    //Controleer als er errors aanwezig zijn, zoja geef de error melding.
                    if($contacterror === false) {
                        if (isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])) {
                            if ($responseData->success) {
                                echo "<div class='sent'>";
                                echo "Bedankt voor uw bericht! We proberen zo spoedig mogelijk te reageren.";
                                echo "</div>";
                            }
                        } else {
                            echo "<div class='sent'>";
                            echo "Vul de CAPTCHA in!";
                            echo "</div>";
                        }
                    } else {
                        echo "<div class='sent'>";
                        echo 'Vul alle velden in!';
                        echo "</div>";
                    }
                }
            ?>
        </div>

        <div class="contact-info">
            <div class="info-block">
                <h3>Adres</h3>
                <div class="address">
                    <p>Bloemstraat 22<br>
                        8081 CW, Elburg<br>
                        <a href="tel:+31525842787">0525-842787</a><br>
                        <a href="mailto:info@deplantage-elburg.nl">info@deplantage-elburg.nl</a>
                    </p>
                </div>
            </div>
            <div class="info-block">
                <h3>Openingstijden</h3>
                <div class="opening-hours">
                    <div class="left">
                        <p>
                            Maandag<br>
                            Dinsdag<br>
                            Woensdag<br>
                            Donderdag<br>
                            Vrijdag<br>
                            Zaterdag<br>
                            Zondag
                        </p>
                    </div>
                    <div class="right">
                        <p>
                            gesloten<br>
                            9:30 - 18:00<br>
                            9:30 - 18:00<br>
                            9:30 - 18:00<br>
                            9:30 - 21:00<br>
                            9:30 - 18:00<br>
                            gesloten
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<div id="map">

</div>

<?php include('footer.php'); ?>
