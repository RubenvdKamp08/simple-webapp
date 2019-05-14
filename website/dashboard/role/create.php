<?php
    include_once($_SERVER['DOCUMENT_ROOT'] . '/dashboard/header.php');
    $user = new User($conn);

    //Controleer als de gebruiker de rol Eigenaar of administrator heeft.
    if($user->has_role('Eigenaar') || $user->has_role('Administrator')) {
        $succes = false;
        $emailerror = false;
        $loginerror = false;

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

        //Controleer als er een POST request naar de server wordt gestuurd.
        if (($_SERVER['REQUEST_METHOD'] == 'POST') && isset($_POST['register'])) {

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

            //Controleer als het wachtwoord voldoet aan alle eisen.
            if (7 < strlen($password)) {
                if (preg_match("#[0-9]+#", $password) || preg_match("#\W+#", $password)) {
                    if (preg_match("#[a-z]+#", $password)) {
                        if (preg_match("#[A-Z]+#", $password)) {
                            $validationpassword = true;
                        }
                    }
                }
            }

            //Controleer als het wachtwoord voldoet aan alle eisen.
            if ($password == $repassword && $validationpassword == true) {
                try {
                    //Controleer als er al een email adres bestaat met het opgegeven emailadres.
                    $useremail = $conn->prepare("SELECT email FROM customer WHERE email = :email");
                    $useremail->execute(array(':email' => $email));
                    $emailexists = $useremail->fetch(PDO::FETCH_ASSOC);

                    //Controleer als het emailadres al voorkomt in de database, geef anders een error.
                    if ($emailexists['email'] == $email) {
                        $emailerror = true;
                    } else {
                        //Registreer de gebruiker.
                        $user->register($firstname, $middlename, $lastname, $email, $phonenumber, $address, $city, $postalcode, $password);

                        //Haal de website URL op.
                        $siteurl = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
                        //Stuur de gebruiker een email
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

        //Haal alle rollen op.
        $roles = $conn->prepare("SELECT * FROM roles");
        $roles->execute();
        $role = $roles->fetchAll();
        $roles = NULL;

        if (isset($_POST['submit'])) {
            ?>
            <a href="/dashboard/role" class="back-btn"><i class="fa fa-arrow-left" aria-hidden="true"></i>&nbsp;
                Terug</a>
            <?php
        } else {
            ?>
            <a href="/dashboard/role" class="back-btn"
               onclick="return confirm('Weet u zeker dat u het aanmaken van het account wil annuleren?');"><i
                        class="fa fa-arrow-left" aria-hidden="true"></i>&nbsp; Terug</a>
            <?php
        }
        ?>
        <div class="content createcontent displayflex">
            <div class="leftcreate">
                <form method="post" enctype="multipart/form-data">
                    <label>Voornaam</label>
                    <input type="text" name="firstname" placeholder="Naam*" value="<?php echo $firstname ?>" required>
                    <label>Tussenvoegsel</label>
                    <input type="text" name="middlename" placeholder="Tussenvoegsel" value="<?php echo $middlename ?>">
                    <label>Achternaam</label>
                    <input type="text" name="lastname" placeholder="Achternaam*" value="<?php echo $lastname ?>"
                           required>
                    <label>Email</label>
                    <input type="email" name="email" placeholder="E-mail*" value="<?php echo $email ?>" required>
                    <label>Telefoonnummer</label>
                    <input type="text" name="phonenumber" placeholder="Telefoonnummer"
                           value="<?php echo $phonenumber ?>">
                    <label>Rol</label>
                    <select name="role">
                        <?php
                        foreach ($role as $rol) {
                            if ($customerrole == $rol["ID"]) {
                                ?>
                                <option value="<?php echo $rol["ID"]; ?>"
                                        selected><?php echo $rol["name"]; ?></option>
                                <?php
                            } else {
                                ?>
                                <option value="<?php echo $rol["ID"]; ?>"><?php echo $rol["name"]; ?></option>
                                <?php
                            }

                        }
                        ?>
                    </select>
                    <input type="submit" name="register" value="wijzigen">
            </div>
            <div class="rightcreate">
                <label>Adres</label>
                <input type="text" name="address" placeholder="Straat + Huisnummer*" value="<?php echo $address ?>"
                       required>
                <label>Plaats</label>
                <input type="text" name="city" placeholder="Woonplaats*" value="<?php echo $city ?>" required>
                <label>Postcode</label>
                <input type="text" name="postalcode" placeholder="Postcode*" value="<?php echo $postalcode ?>" required>
                <label>Wachtwoord</label>
                <input class="password" type="password" id="password1" name="password" placeholder="Wachtwoord*"
                       required>
                <label>Bevestig wachtwoord</label>
                <input class="password" type="password" id="password2" name="repassword"
                       placeholder="Bevestig wachtwoord*"
                       required>
                </form>
                <div class="helper-text-createacc">
                    <ul>
                        <li class="length">&bull; Minimaal 8 karakters</li>
                        <li class="lowercase">&bull; Minimaal 1 kleine letter</li>
                        <li class="uppercase">&bull; Minimaal 1 hoofdletter</li>
                        <li class="special">&bull; Minimaal 1 getal of speciaal karakter</li>
                        <li class="same-pass">&bull; Wachtwoorden komen overeen</li>
                    </ul>
                </div>
                <br>
            </div>
        </div>

        <?php
        //Controleer als er een POST request is gestuurd en geef eventuele foutmeldingen weer.
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
            echo "<div class='sentregister'>";
            if ($succes === true) {
                echo $_POST['firstname'] . " is nu registrereerd. Hij/zij kan nu inloggen!";
            } elseif ($emailerror === true) {
                echo "Er is al een account met het door uw ingevoerde emailadres!";
            } else {
                echo "Het wachtwoord voldoet niet aan de eisen!";
            }
            echo "</div>";
        }
    } else {
        $user->redirect('/dashboard');
    }

    include_once($_SERVER['DOCUMENT_ROOT'] . '/dashboard/footer.php');
?>