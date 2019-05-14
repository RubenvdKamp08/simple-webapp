<?php
    include_once($_SERVER['DOCUMENT_ROOT'] . '/dashboard/header.php');
    include_once($_SERVER['DOCUMENT_ROOT'] . '/functions/products.php');
    $user = new User($conn);
    $products = new Product($conn);

    //Controleer als de gebruiker de rol Eigenaar, administrator of medewerker heeft.
    if($user->has_role('Eigenaar') || $user->has_role('Administrator') || $user->has_role('Medewerker')) {
        //Haal de product categorieeen en subcategorieeen op.
        $categories = $products->getCategories();
        $subcategories = $products->getSubcategories();

        $message = '';
        //Controleer als er een ID als GET parameter wordt meegegeven aan de URL.
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            //Haal het product op uit de database.
            $product = $products->getProduct($id);
            //Controleer als er een POST request naar de server wordt gestuurd.
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $title = $_POST['title'];
                $description = $_POST['description'];
                $price = $_POST['price'];
                $categoryID = $_POST['category'];
                $subcategoryID = $_POST['subcategory'];
                $available = $_POST['available'];
                $image = uniqid() . "-" . $_FILES['image']['name'];
                $imagefile = $_FILES['image'];
                //Controleer als er een subcategorie is ingesteld of niet.
                if ($subcategoryID === 'none') {
                    $subcategoryID = NULL;
                }

                //Controleer als het bestand wel een afbeelding is.
                if ($products->editProduct($title, $description, $price, $categoryID, $subcategoryID, $available, $image, $imagefile, $id) === true) {
                    $message = 'Product is succesvol gewijzigd!';
                } else {
                    $message = 'Product kon niet worden gewijzigd, controleer als de geuploade afbeelding wel een jpg, png of jpeg bestand is!';
                }
                //Get product again to update values
                $product = $products->getProduct($id);
            }
            //Controleer als er wel een product wordt opgehaald uit de database.
            if ($product->rowCount() > 0) {
                foreach ($product as $item) {
                    ?>
                    <a href="/dashboard/products" class="back-btn"><i class="fa fa-arrow-left" aria-hidden="true"></i>&nbsp;
                        Terug</a>
                    <div class="content">
                        <div class="dashboard-left">
                            <form method="post" enctype="multipart/form-data">
                                <label>Product titel</label>
                                <input type="text" name="title" value="<?php echo $item["title"]; ?>"
                                       placeholder="Productnaam" required>
                                <label>Product beschrijving</label>
                                <textarea name="description" placeholder="Beschrijving"
                                          required><?php echo $item["description"]; ?></textarea>
                                <label>Product prijs</label>
                                <input type="number" name="price" value="<?php echo $item["price"]; ?>"
                                       placeholder="Prijs" step=".01" required>
                                <label>Product afbeelding</label>
                                <input type="file" name="image" id="image" value="<?php echo $item["image"]; ?>"
                                       onchange="readURL(this);">
                                <label>Productcategorie</label>
                                <select name="category" id="category">
                                    <?php
                                    //Loop door alle productcategorieen heen en selecteer de productcategorie die gekoppeld staat aan het product.
                                    foreach ($categories as $category) {
                                        if ($item["categoryID"] == $category['ID']) {
                                            ?>
                                            <option value="<?php echo $category['ID'] ?>"
                                                    class="<?php echo $category['ID']; ?>"
                                                    selected><?php echo $category['name']; ?></option>
                                            <?php
                                        } else {
                                            ?>
                                            <option value="<?php echo $category['ID'] ?>"
                                                    class="<?php echo $category['ID']; ?>"><?php echo $category['name']; ?></option>
                                            <?php
                                        }
                                    }
                                    ?>
                                </select>
                                <div id="sub-cat">
                                    <label>Product subcategorie</label>
                                    <select name="subcategory" id="subcategory">
                                        <?php
                                        if ($item["subcategoryID"] === NULL) {
                                            ?>
                                            <option value="none" id="none" selected>Geen subcategorie</option>
                                            <?php
                                        } else {
                                            ?>
                                            <option value="none" id="none">Geen subcategorie</option>
                                            <?php
                                        }
                                        //Loop door alle subcategorieeen heen en selecteer de subcategorie die aan het product gekoppeld is.
                                        foreach ($subcategories as $subcategory) {
                                            if ($item["subcategoryID"] === $subcategory["ID"]) {
                                                ?>
                                                <option value="<?php echo $subcategory['ID'] ?>"
                                                        class="<?php echo $subcategory['categoryID']; ?>"
                                                        selected><?php echo $subcategory['name']; ?></option>
                                                <?php
                                            } else {
                                                ?>
                                                <option value="<?php echo $subcategory['ID'] ?>"
                                                        class="<?php echo $subcategory['categoryID']; ?>"><?php echo $subcategory['name']; ?></option>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <span>Product beschikbaar:</span><br>
                                <input type="radio" class="radio-btn" name="available"
                                       value="1" <?php echo ($item['available'] == 1) ? 'checked="checked"' : ''; ?>> Ja
                                <input type="radio" class="radio-btn" name="available"
                                       value="0" <?php echo ($item['available'] == 0) ? 'checked="checked"' : ''; ?>>
                                Nee
                                <input type="submit" value="Wijzigen">
                            </form>
                        </div>
                        <div class="dashboard-right">
                            <img id="product-image" src="/assets/images/products/<?php echo $item['image']; ?>">
                        </div>
                    </div>
                    <?php
                    echo $message;
                }
            } else {
                $user->redirect('/dashboard/products');
            }
        }
    } else {
        $user->redirect('/dashboard');
    }

    include($_SERVER['DOCUMENT_ROOT'] . '/dashboard/footer.php');