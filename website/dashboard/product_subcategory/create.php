<?php
    include_once($_SERVER['DOCUMENT_ROOT'] . '/dashboard/header.php');
    include_once($_SERVER['DOCUMENT_ROOT'] . '/functions/products.php');
    $user = new User($conn);
    $categories = $product->getCategories();

    //Controleer als de gebruiker de rol Eigenaar, administrator of medewerker heeft.
    if($user->has_role('Eigenaar') || $user->has_role('Administrator') || $user->has_role('Medewerker')) {
        $insert = false;
        //Controleer als er een POST request naar de server is gestuurd.
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $insert = true;

            //Insert POST values in de database naar de productsubcategorieeen.
            $stmt = $conn->prepare("INSERT INTO productsubcategory(name, checked, categoryID) 
                                                       VALUES(:subcategory_name, :checked, :categoryID)");

            $stmt->bindparam(":subcategory_name", $_POST["subcategory_name"]);
            $stmt->bindparam(":checked", $_POST["active"]);
            $stmt->bindparam(":categoryID", $_POST["category"]);
            $stmt->execute();
        }
        ?>

        <a href="/dashboard/product_subcategory" class="back-btn"><i class="fa fa-arrow-left" aria-hidden="true"></i>&nbsp;
            Terug</a>
        <div class="content">
            <div class="dashboard-left">
                <form method="post">
                    <label>Subcategorie</label>
                    <input type="text" name="subcategory_name" placeholder="Subcategorienaam" required>
                    <label>Voeg toe aan categorie:</label>
                    <select name="category">
                        <?php
                        foreach ($categories as $category) {
                            ?>
                            <option value="<?php echo $category['ID'] ?>"><?php echo $category['name']; ?></option>
                            <?php
                        }
                        ?>
                    </select>
                    <span> Actief of non-actief: </span> <br>
                    <input type="radio" name="active" class="radio-btn" value="1" checked="checked" required> Actief
                    <input type="radio" name="active" class="radio-btn" value="0"> Non-actief <br>
                    <input type="submit" name="submit" value="Toevoegen">
                </form>
            </div>
        </div>

        <?php
        if ($insert) {
            echo "Subcategorie is toegevoegd!";
        }
    } else {
        $user->redirect('/dashboard');
    }

    include_once($_SERVER['DOCUMENT_ROOT'] . '/dashboard/footer.php');
?>
