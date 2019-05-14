<?php
    include_once($_SERVER['DOCUMENT_ROOT'] . '/dashboard/header.php');
    include_once($_SERVER['DOCUMENT_ROOT'] . '/functions/products.php');
    $user = new User($conn);
    $product = new Product($conn);

    //Controleer als de gebruiker de rol Eigenaar, administrator of medewerker heeft.
    if($user->has_role('Eigenaar') || $user->has_role('Administrator') || $user->has_role('Medewerker')) {
        //Haal de product categorieeen en subcategorieeen op.
        $categories = $product->getCategories();
        $subcategories = $product->getSubcategories();
        ?>
        <a href="/dashboard/products" class="back-btn"><i class="fa fa-arrow-left" aria-hidden="true"></i>&nbsp;
            Terug</a>
        <div class="content">
            <div class="dashboard-left">
                <form method="post" enctype="multipart/form-data">
                    <label>Product titel</label>
                    <input type="text" name="title" placeholder="Productnaam" required>
                    <label>Product beschrijving</label>
                    <textarea name="description" placeholder="Beschrijving" required></textarea>
                    <label>Product prijs</label>
                    <input type="number" name="price" placeholder="Prijs" step=".01" required>
                    <label>Product afbeelding</label>
                    <input type="file" name="image" id="image" onchange="readURL(this);" required>
                    <label>Productcategorie</label>
                    <select name="category" id="category">
                        <?php
                        //Loop door de productcategorieeen.
                        foreach ($categories as $category) {
                            ?>
                            <option value="<?php echo $category['ID']; ?>"
                                    class="<?php echo $category['ID']; ?>"><?php echo $category['name']; ?></option>
                            <?php
                        }
                        ?>
                    </select>
                    <div id="sub-cat">
                        <label>Product subcategorie</label>
                        <select name="subcategory" id="subcategory">
                            <option value="none" id="none" selected>Geen subcategorie</option>
                            <?php
                            //Loop door de product subcategorieeen.
                            foreach ($subcategories as $subcategory) {
                                ?>
                                <option value="<?php echo $subcategory['ID'] ?>"
                                        class="<?php echo $subcategory['categoryID']; ?>"><?php echo $subcategory['name']; ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                    <input type="submit" value="Toevoegen">
                </form>
            </div>
            <div class="dashboard-right">
                <img id="product-image">
            </div>
        </div>
        <?php
        //Controleer als er een POST request naar de server wordt gestuurd.
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $title = $_POST['title'];
            $description = $_POST['description'];
            $price = $_POST['price'];
            $image = uniqid() . "-" . $_FILES['image']['name'];
            $imagefile = $_FILES['image'];
            $categoryID = $_POST['category'];
            $subcategoryID = $_POST['subcategory'];
            if ($subcategoryID === 'none') {
                $subcategoryID = NULL;
            }

            //Voor createProduct function uit zodat product wordt toegevoegd aan de database en de afbeeldiing wordt geupload naar de server.
            $product->createProduct($title, $description, $price, $image, $categoryID, $subcategoryID, $imagefile);
        }
    } else {
        $user->redirect('/dashboard');
    }


    include($_SERVER['DOCUMENT_ROOT'] . '/dashboard/footer.php');