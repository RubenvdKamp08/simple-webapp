<?php include('header.php');
    //Controleer als product wordt toegevoegd aan winkelmandje.
    if(isset($_POST["add_to_cart"])) {
        if (isset($_SESSION["shopping_cart"])) {
            //Check if Item is not in the cart and create new item after that
            $item_array_id = array_column($_SESSION["shopping_cart"], "item_id");
            if (!in_array($_GET["id"], $item_array_id)) {
                $count = count($_SESSION["shopping_cart"]);
                $item_array = array(
                    'item_id' => $_GET["id"],
                    'item_name' => $_POST["hidden_name"],
                    'item_price' => $_POST["hidden_price"],
                    'item_image' => $_POST["hidden_image"],
                    'item_amount' => $_POST["hidden_amount"]
                );
                $_SESSION["shopping_cart"][$count + 1] = $item_array;
                $user->redirect($_SERVER['HTTP_REFERER']);
            } else {
                //Als het product al in de winkelwagen zit pas de waarde aan met 1 extra.
                $item_array_id = array_column($_SESSION["shopping_cart"], "item_id");
                foreach ($_SESSION["shopping_cart"] as $item => $key) {
                    if ($key['item_id'] == $_GET["id"]) {
                        $amount = $key['item_amount'];
                        $item_array = array(
                            'item_id' => $_GET["id"],
                            'item_name' => $_POST["hidden_name"],
                            'item_price' => $_POST["hidden_price"],
                            'item_image' => $_POST["hidden_image"],
                            'item_amount' => $_POST["hidden_amount"] + $amount
                        );
                        //Unset het product uit de winkelwagen sessie en voeg hem opnieuw toe met de nieuwe waardes.
                        unset($_SESSION["shopping_cart"][$item]);
                        $_SESSION["shopping_cart"][$item] = $item_array;
                        $user->redirect($_SERVER['HTTP_REFERER']);
                    }
                }
            }
        } else {
            //Als er nog geen product in de winkelwagen zit, maak een nieuwe sessie aan en voer daar de producten aan toe.
            $item_array = array(
                'item_id' => $_GET["id"],
                'item_name' => $_POST["hidden_name"],
                'item_price' => $_POST["hidden_price"],
                'item_image' => $_POST["hidden_image"],
                'item_amount' => $_POST["hidden_amount"]
            );

            $_SESSION["shopping_cart"][0] = $item_array;
        }
    }
?>
<section class="content main-content">
    <?php
        //Controleer als er een GET request wordt meegegeven in de URL.
        if(isset($_GET['id'])) {
            $id = $_GET['id'];

            //Haal het bijhorende product op uit de database.
            $products = $conn->prepare('SELECT p.*, pc.name as category FROM products AS p INNER JOIN productcategory AS pc ON p.categoryID = pc.ID WHERE p.id = ' . $id);
            $products->execute();

            //Loop door alle productdata heen.
            foreach ($products as $item) { ?>
                <div class="itembreadcrumb">
                    <a href="/shop">Producten /</a>
                    <a href="/shop?categorie=<?php echo $item["category"] ?>"><?php echo $item["category"] ?> /</a>
                    <h5><?php echo $item["title"] ?> </h5>
                </div>
                <div class="product-content">
                  <div class="mediacontent">
                      <div class="left-product">
                        <img src="/assets/images/products/<?php echo $item["image"]; ?>">
                      </div>
                    <div class="middle-product">
                        <h1 class="producttitle"> <?php echo $item["title"]; ?> </h1>
                        <div class="productundertitle"> <h4>Categorie:</h4><?php echo " " . $item["category"]; ?> </div>
                        <div class="itemdescription"><h4>Beschikbaarheid: <?php echo ($item["available"] == 1) ? 'beschikbaar' : 'niet beschikbaar'; ?></h4></div>
                        <div class="itemdescription"> <h4>Beschrijving:</h4><?php echo " " . "<br>" . $item["description"]; ?> </div>
                    </div>
                  </div>
                    <div class="right-product">
                        <div class="itemprice">
                            <?php echo "€ " . $item["price"]; ?>
                        </div>
                        <div class="itemsubmit">
                            <form method="post" action="/product?action=add&id=<?php echo $item["ID"]; ?>">
                                <input type="hidden" name="hidden_amount" value="1" />
                                <input type="hidden" name="hidden_name" value="<?php echo $item["title"]; ?>" />
                                <input type="hidden" name="hidden_price" value="<?php echo $item["price"]; ?>" />
                                <input type="hidden" name="hidden_image" value="<?php echo $item["image"]; ?>" />
                                <button name="add_to_cart" type="submit" value="verzend">in winkelwagen</button>
                            </form>
                        </div>
                    </div>
                <?php
            }
        }
    ?>
</section>
<?php include('footer.php'); ?>
