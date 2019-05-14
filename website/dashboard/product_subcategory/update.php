<?php
    include_once($_SERVER['DOCUMENT_ROOT'] . '/dashboard/header.php');
    $user = new User($conn);

    //Controleer als de gebruiker de rol Eigenaar, administrator of medewerker heeft.
    if($user->has_role('Eigenaar') || $user->has_role('Administrator') || $user->has_role('Medewerker')) {
        //Controleer als er een ID als GET parameter wordt meegegeven aan de URL.
        if (isset($_GET['id'])) {
            $id = $_GET["id"];
            $changed = false;
            //Controleer als er een POST request naar de server wordt gestuurd.
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $changed = true;
                //Update de productsubcategorie.
                $update_productsubcategory = $conn->prepare("UPDATE productsubcategory SET name=:name, checked=:active WHERE ID= :id");
                $update_productsubcategory->execute(array(
                    ':name' => ucfirst($_POST["name"]),
                    ':active' => $_POST["active"],
                    ':id' => $id
                ));
            }

            //Haal de productsubcategorie op voor de nieuwe data.
            $productcategory = $conn->prepare("SELECT * FROM productsubcategory WHERE ID= :id");
            $productcategory->execute(array(
                ':id' => $id
            ));

            //Loop door de subcategorie data heen.
            while ($row = $productcategory->fetch()) {
                $productcategory_name = $row["name"];
                $active = $row["checked"];
            }

            ?>

            <a href="/dashboard/product_category" class="back-btn"><i class="fa fa-arrow-left" aria-hidden="true"></i>&nbsp;
                Terug</a>
            <div class="content">
                <div class="dashboard-left">
                    <form method="post">
                        <label>Categorienaam</label>
                        <input type="text" name="productcategoryname" value="<?php echo "$productcategory_name" ?>">
                        <span> Actief: </span><br>
                        <input type="radio" name="active" value="true" class="radio-btn" <?php if ($active == "true") {
                            echo "checked='true'";
                        } ?>> Ja
                        <input type="radio" name="active" value="false"
                               class="radio-btn" <?php if ($active == "false") {
                            echo "checked='true'";
                        } ?>> Nee <br> <br>
                        <input type="submit" name="submit" value="Wijzigen">
                    </form>
                </div>
            </div>

            <?php
            if ($changed) {
                echo "Uw wijzigingen zijn opgeslagen!";
            }
        } else {
            echo 'Er kon geen productsubcategorie worden gevonden';
        }
    } else {
        $user->redirect('/dashboard');
    }

    include_once($_SERVER['DOCUMENT_ROOT'] . '/dashboard/footer.php');
?>
