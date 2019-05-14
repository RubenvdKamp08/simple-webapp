<?php include('header.php');

$succes = false;
$emailerror = false;
$loginerror = false;
$logincaptcha = false;

//Haal siteurl op.
$siteurl = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]";

//Controleer als gebruiker is ingelogd, als het klant is stuur klant naar order pagina, anders naar dashboard.
if($user->is_loggedin() != "") {
    if(!$user->has_role('Klant')) {
        $user->redirect('/dashboard');
    } elseif($user->has_role('Klant')) {
         $user->redirect('/orders');
    }
}

//Controleer als er een POST request naar de server wordt gestuurd van login.
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submitlogin'])) {
    if (isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])) {
        //Recaptcha key
        $secret = '6LeESTkUAAAAAJ7wfXVne6e9rBBdquHvF2alnBkU';
        //Response data
        $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . $secret .
            '&response=' . $_POST['g-recaptcha-response']);
        $responseData = json_decode($verifyResponse);
        //Controleer als Recaptcha een succes response terug geeft.
        if ($responseData->success) {
            //Gebruik striptags functie op alle postdata tegen XSS aanvallen.
            $_POST = array_map('strip_tags', $_POST);
            $email = $_POST['loginemail'];
            $password = $_POST['loginpassword'];

            //Redirect gebruiker naar de desbetreffende pagina van de goede userrole.
            if ($user->login($email, $password)) {
                if (isset($_GET['redirectUrl'])) {
                    $redirecturl = $_GET['redirectUrl'];
                    $user->redirect('/' . $redirecturl);
                } elseif (!$user->has_role('Klant')) {
                    $user->redirect('/dashboard');
                } else {
                    $user->redirect('/orders');
                }
                $succeslogin = true;
            } else {
                $loginerror = true;
            }
        }
        $logincaptcha = false;
    } else {
        $logincaptcha = true;
    }
}

$firstname = '';
$middlename = '';
$lastname = '';
$email = '';
$phonenumber = '';
$address = '';
$city = '';
$postalcode = '';
$repassword = '';

$validationpassword = false;

//Controleer als er een POST request naar de server wordt gestuurd van registreren.
if (($_SERVER['REQUEST_METHOD'] == 'POST') && isset($_POST['register'])) {
    $_POST = array_map('strip_tags', $_POST);
    $firstname = $_POST["firstname"];
    $middlename = $_POST["middlename"];
    $lastname = $_POST["lastname"];
    $email = $_POST["email"];
    $phonenumber = $_POST["phonenumber"];
    $address = $_POST["address"];
    $city = $_POST["city"];
    $postalcode = $_POST["postalcode"];
    $password = $_POST["password"];
    $repassword = $_POST["repassword"];

    //Controleer als het wachtwoord voldoet aan de eisen.
    if (7 < strlen($password)){
        if( preg_match("#[0-9]+#", $password) || preg_match("#\W+#", $password) ) {
            if( preg_match("#[a-z]+#", $password) ) {
                if( preg_match("#[A-Z]+#", $password) ) {
                    $validationpassword = true;
                }
            }
        }
    }

    if ($password == $repassword && $validationpassword == true) {
        try {
            //Controleer als er al een gebruiker bestaat met het emailadres.
            $useremail = $conn->prepare("SELECT email FROM customer WHERE email = :email");
            $useremail->execute(array(':email'=>$email));
            $emailexists = $useremail->fetch(PDO::FETCH_ASSOC);

            if($emailexists['email'] == $email) {
                $emailerror = true;
            } else {
                //Voeg gebruiker toe aan de database.
                $user->register($firstname, $middlename, $lastname, $email, $phonenumber, $address, $city, $postalcode, $password);

                //Verstuur succesemail naar de gebruiker.
                $toklant = $email;
                $subjectklant = 'Bevestiging registratie';
                $messageklant = 'Hallo ' . $firstname . ', <br><br>
                                                bedankt voor het registreren op onze site.<br>
                                                Vanaf nu kunt u inloggen en bestellingen maken.<br><br><br>
                                                de Plantage<br>
                                                Bloemstraat 22<br>
                                                8081 CW, Elburg<br>
                                                0525-842787<br>
                                                info@deplantage-elburg.nl<br><br>
                                                <img width="250" src="'. $siteurl .'/assets/images/logo.png" alt="de Plantage"><br>';
                $headers[] = 'From: de Plantage Elburg <no-reply@plantagedevelopment.nl>' . "\r\n" .
                    'Reply-To: info@plantagedevelopment.nl' . "\r\n" .
                    'X-Mailer: PHP/' . phpversion();
                $headers[] = 'MIME-Version: 1.0';
                $headers[] = 'Content-type: text/html; charset=iso-8859-1';

                mail($toklant, $subjectklant, $messageklant, implode("\r\n", $headers));

                $firstname = '';
                $middlename = '';
                $lastname = '';
                $email = '';
                $phonenumber = '';
                $address = '';
                $city = '';
                $postalcode = '';
                $repassword = '';

                $succes = true;
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
}

?>

<section class="content main-content">
    <div class="full-content">
        <div class="login">
            <div class="headertext">
                <h2>Login</h2>
            </div>
            <form method="post">
                <input type="email" name="loginemail" placeholder="E-mail*" required><br><br>
                <input type="password" name="loginpassword" placeholder="Wachtwoord*"><br><br>
                <div class="g-recaptcha" data-sitekey="6LeESTkUAAAAAIpBfp_ocb0-21UbKJzthvPaIX3r"></div>
                <div class="loginsubmit">
                    <button name="submitlogin" type="submit" value="verzend">Inloggen</button>
                </div>
            </form>
            <?php
                if ($loginerror === true) {
                    echo "<div class='sentregister'>";
                    echo "De door u ingevoerde inloggegevens kloppen niet!";
                    echo "</div>";
                } elseif($logincaptcha === true) {
                    echo "<div class='sentregister'>";
                    echo "Vul de captcha in!";
                    echo "</div>";
                }
            ?>
        </div>

        <div class="register">
            <div class="headertext">
                <h2>Registreren</h2>
            </div>

            <form method="post">
                <input type="text" name="firstname" placeholder="Naam*" value="<?php echo $firstname ?>" required><br><br>
                <input type="text" name="middlename" placeholder="Tussenvoegsel" value="<?php echo $middlename ?>"><br><br>
                <input type="text" name="lastname" placeholder="Achternaam*" value="<?php echo $lastname ?>" required><br><br>
                <input type="email" name="email" placeholder="E-mail*" value="<?php echo $email ?>" required><br><br>
                <input type="text" name="phonenumber" placeholder="Telefoonnummer" value="<?php echo $phonenumber ?>"><br><br>
                <input type="text" name="address" placeholder="Straat + Huisnummer*" value="<?php echo $address ?>" required><br><br>
                <input type="text" name="city" placeholder="Woonplaats*" value="<?php echo $city ?>" required><br><br>
                <input type="text" name="postalcode" placeholder="Postcode*" value="<?php echo $postalcode ?>" required><br><br>
                <input class="password" type="password" id="password1" name="password" placeholder="Wachtwoord*" required><br><br>
                <input class="password" type="password" id="password2" name="repassword" placeholder="Bevestig wachtwoord*" required><br><br>
                <div class="helper-text">
                    <ul>
                        <li class="length">Minimaal 8 karakters</li>
                        <li class="lowercase">Minimaal 1 kleine letter</li>
                        <li class="uppercase">Minimaal 1 hoofdletter</li>
                        <li class="special">Minimaal 1 getal of speciaal karakter</li>
                        <li class="same-pass">Wachtwoorden komen overeen</li>
                    </ul>
                </div><br>

                <div class="loginsubmit">
                    <button name="register" type="submit" value="verzend">Registreren</button>
                </div>

                <?php
                if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
                    echo "<div class='sentregister'>";
                    if ($succes === true) {
                        echo "U bent geregistreerd, u kunt vanaf nu inloggen!";
                    } elseif($emailerror === true) {
                        echo "Er is al een account met het door uw ingevoerde emailadres!";
                    } else {
                        echo "Het wachtwoord voldoet niet aan de eisen!";
                    }
                    echo "</div>";
                }
                ?>
            </form>
        </div>
    </div>
</section>

<?php include('footer.php'); ?>
