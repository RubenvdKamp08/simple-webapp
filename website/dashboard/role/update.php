<?php
    include_once($_SERVER['DOCUMENT_ROOT'] . '/dashboard/header.php');
    $user = new User($conn);

    //Controleer als de gebruiker de rol Eigenaar of administrator heeft.
    if($user->has_role('Eigenaar') || $user->has_role('Administrator')) {
        //Controleer als er een ID als GET parameter wordt meegegeven aan de URL.
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $user = new User($conn);
            $message = '';

            //Functie om account te bewerkern.
            function editAccount($firstname, $middlename, $lastname, $email, $phonenumber, $address, $city, $postalcode, $roleid, $password, $repassword, $validationpassword)
            {
                global $conn, $id;
                //Controleer als het wachtwoord wordt geupdated of niet.
                if ($password == $repassword && $validationpassword == true) {
                    try {
                        //Use options that denotes the algorithmic cost, salt is Deprecated in PHP 7.0 so don't use that!
                        $options = [
                            'cost' => 16
                        ];
                        $hashed_pass = password_hash($password, PASSWORD_BCRYPT, $options);

                        //Voer update query uit.
                        $stmt = $conn->prepare("UPDATE customer SET firstname = :firstname, middlename = :middlename, lastname = :lastname, email = :email, phonenumber = :phonenumber, address = :address, city = :city, postalcode = :postalcode, RoleID = :roleid, password = :password WHERE ID = " . $id);

                        $stmt->bindparam(":firstname", $firstname);
                        $stmt->bindparam(":middlename", $middlename);
                        $stmt->bindparam(":lastname", $lastname);
                        $stmt->bindparam(":email", $email);
                        $stmt->bindparam(":phonenumber", $phonenumber);
                        $stmt->bindparam(":address", $address);
                        $stmt->bindparam(":city", $city);
                        $stmt->bindparam(":postalcode", $postalcode);
                        $stmt->bindparam(":roleid", $roleid);
                        $stmt->bindparam(":password", $hashed_pass);
                        $stmt->execute();

                        return true;
                    } catch (PDOException $e) {
                        echo $e->getMessage();
                    }
                } else {
                    try {
                        //Voer update query uit.
                        $stmt = $conn->prepare("UPDATE customer SET firstname = :firstname, middlename = :middlename, lastname = :lastname, email = :email, phonenumber = :phonenumber, address = :address, city = :city, postalcode = :postalcode, RoleID = :roleid WHERE ID = " . $id);

                        $stmt->bindparam(":firstname", $firstname);
                        $stmt->bindparam(":middlename", $middlename);
                        $stmt->bindparam(":lastname", $lastname);
                        $stmt->bindparam(":email", $email);
                        $stmt->bindparam(":phonenumber", $phonenumber);
                        $stmt->bindparam(":address", $address);
                        $stmt->bindparam(":city", $city);
                        $stmt->bindparam(":postalcode", $postalcode);
                        $stmt->bindparam(":roleid", $roleid);
                        $stmt->execute();

                        return true;
                    } catch (PDOException $e) {
                        echo $e->getMessage();
                    }
                }
            }

            //Haal alle user roles op.
            $roles = $conn->prepare("SELECT * FROM roles");
            $roles->execute();
            $role = $roles->fetchAll();
            $roles = NULL;

            //Haal de gegevens van de gebruiker op.
            function customer($conn, $id)
            {
                $customers = $conn->prepare("SELECT c.ID, firstname, middlename, lastname, email, phonenumber, address, city, postalcode, RoleID, r.ID name FROM customer AS c INNER JOIN roles AS r ON c.RoleID=r.ID WHERE c.ID =" . $id);
                $customers->execute();
                $customer = $customers->fetchAll();
                $customers = NULL;
                return $customer;
            }

            //controleer als er een POST request naar de server wordt gestuurd.
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $firstname = $_POST["firstname"];
                $middlename = $_POST["middlename"];
                $lastname = $_POST["lastname"];
                $email = $_POST["email"];
                $phonenumber = $_POST["phonenumber"];
                $address = $_POST["address"];
                $city = $_POST["city"];
                $postalcode = $_POST["postalcode"];
                $roleid = $_POST["role"];
                $customerrole = $_POST["role"];

                $password = $_POST["password"];
                $repassword = $_POST["repassword"];

                //Controleer als het wachtwoord voldoet aan de eisen.
                if ($password) {
                    if (7 < strlen($password)) {
                        if (preg_match("#[0-9]+#", $password) || preg_match("#\W+#", $password)) {
                            if (preg_match("#[a-z]+#", $password)) {
                                if (preg_match("#[A-Z]+#", $password)) {
                                    $validationpassword = true;
                                }
                            }
                        }
                    }
                } else {
                    $validationpassword = true;
                }

                //Controleer als de gebruiker wel gewijzigd is.
                if (editAccount($firstname, $middlename, $lastname, $email, $phonenumber, $address, $city, $postalcode, $roleid, $password, $repassword, $validationpassword) === true) {
                    $message = 'Gebruiker is succesvol gewijzigd!';
                } else {
                    $message = 'Gebruiker kon niet worden aangepast, controleer of alles goed is ingevuld!';
                }
            }
            if (isset($_POST['submit'])) {
                ?>
                <a href="/dashboard/role" class="back-btn"><i class="fa fa-arrow-left" aria-hidden="true"></i>&nbsp;
                    Terug</a>
                <?php
            } else {
                ?>
                <a href="/dashboard/role" class="back-btn"
                   onclick="return confirm('Weet u zeker dat u het bewerken van het account wil annuleren?');"><i
                            class="fa fa-arrow-left" aria-hidden="true"></i>&nbsp; Terug</a>
                <?php
            }
            $customer = customer($conn, $id);
            //Loop door alle gegevens van de gebruikers.
            foreach ($customer as $item) {
                $customerrole = $item["RoleID"];
                ?>
                <div class="content">
                    <div class="dashboard-left">
                        <form method="post" enctype="multipart/form-data">
                            <label>Voornaam</label>
                            <input type="text" name="firstname" value="<?php echo $item["firstname"]; ?>"
                                   placeholder="Voornaam"
                                   required>
                            <label>Tussenvoegsel</label>
                            <input type="text" name="middlename" value="<?php echo $item["middlename"]; ?>"
                                   placeholder="Tussenvoegsel">
                            <label>Achternaam</label>
                            <input type="text" name="lastname" value="<?php echo $item["lastname"]; ?>"
                                   placeholder="Achternaam"
                                   required>
                            <label>E-mail</label>
                            <input type="text" name="email" value="<?php echo $item["email"]; ?>" placeholder="E-mail"
                                   required>
                            <label>Telefoonnummer</label>
                            <input type="text" name="phonenumber" value="<?php echo $item["phonenumber"]; ?>"
                                   placeholder="Telefoonnummer">
                            <label>Rol</label>
                            <select name="role">
                                <?php
                                //Loop door alle rollen.
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
                            <input type="submit" name="submit" value="Wijzigen">
                    </div>
                    <div class="dashboard-right">
                        <label>Plaatsnaam</label>
                        <input type="text" name="city" value="<?php echo $item["city"]; ?>"
                               placeholder="Plaatsnaam">
                        <label>Postcode</label>
                        <input type="text" name="postalcode" value="<?php echo $item["postalcode"]; ?>"
                               placeholder="Postcode"
                               required>
                        <label>Adres</label>
                        <input type="text" name="address" value="<?php echo $item["address"]; ?>"
                               placeholder="Straatnaam + nummer"
                               required>
                        <label>Nieuw wachtwoord</label>
                        <input class="password" type="password" id="password1" name="password"
                               placeholder="Wachtwoord*">
                        <label>Bevestig nieuw wachtwoord</label>
                        <input class="password" type="password" id="password2" name="repassword"
                               placeholder="Bevestig wachtwoord*">
                        </form>
                    </div>
                </div>

                <?php
                echo $message;
            }
        }
    } else {
        $user->redirect('/dashboard');
    }

    include_once($_SERVER['DOCUMENT_ROOT'] . '/dashboard/footer.php');
?>