<?php
    include_once($_SERVER['DOCUMENT_ROOT'] . '/dashboard/header.php');
    $user = new User($conn);

    //Controleer als de gebruiker de rol eigenaar, administrator of medewerker heeft en anders terugsturen naar dashboard pagina.
    if($user->has_role('Eigenaar') || $user->has_role('Administrator') || $user->has_role('Medewerker')) {
        //Controleer als er een ID in de url voorkomt.
        if (isset($_GET["id"])) {
            $id = $_GET["id"];

            //Vraag de maandelijkse producten op uit de database.
            $monthlyproduct = $conn->prepare("SELECT * FROM monthly_product WHERE ID= :id");
            $monthlyproduct->execute(array(
                ':id' => $id));

            //Controleer als er een POST request binnen komt.
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                //Genereer een nieuwe unieke afbeelding naam.
                $image = uniqid() . "-" . $_FILES['image']['name'];
                $imagefile = $_FILES['image'];

                //Controleer als de afbeelding is aangepast of niet.
                if ($imagefile['name'] == '') {
                    $stmt = $conn->prepare("UPDATE monthly_product SET title=:title, description=:description WHERE id=:id");
                } else {
                    $stmt = $conn->prepare("UPDATE monthly_product SET title = :title, description = :description, image = :image WHERE id= :id ");
                    $stmt->bindparam(":image", $image);

                    //Vraag afbeelding op uit de database.
                    $product = $conn->prepare("SELECT image FROM monthly_product WHERE ID = " . $id);
                    $product->execute();
                    $productimage = $product->fetch(PDO::FETCH_ASSOC);

                    //Navigeer naar de map waar de afbeelding moet komen te staan.
                    $target_dir = $_SERVER['DOCUMENT_ROOT'] . "/assets/images/monthlyproducts/";
                    $target_file = $target_dir . basename($image);
                    $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);

                    //Controleer als de afbeelding een JPG, PNG of JPEG is.
                    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
                        return false;
                    } else {
                        //Verwijder de oude afbeelding en upload de nieuwe afbeelding naar de goede map.
                        unlink($_SERVER['DOCUMENT_ROOT'] . '/assets/images/monthlyproducts/' . $productimage['image']);
                        move_uploaded_file($imagefile["tmp_name"], $target_file);
                    }
                }


                $stmt->bindparam(":title", $_POST["title"]);
                $stmt->bindparam(":description", $_POST["description"]);
                $stmt->bindparam(":id", $id);
                $stmt->execute();

                //Vraag de nieuwe updated data op.
                $monthlyproduct = $conn->prepare("SELECT * FROM monthly_product WHERE ID= :id");
                $monthlyproduct->execute(array(
                    ':id' => $id));
            }

            //Loop door de data.
            while ($row = $monthlyproduct->fetch()) {
                $title = $row["title"];
                $description = $row["description"];
                $currentimage = $row["image"];
            }
            ?>

            <a href="/dashboard/montlyproduct" class="back-btn"><i class="fa fa-arrow-left" aria-hidden="true"></i>&nbsp;
                Terug</a>
            <div class="content">
                <div class="dashboard-left">
                    <form class="update-monthlyproduct" method="post" enctype="multipart/form-data">
                        <label>Titel</label>
                        <input type="text" name="title" value="<?php echo "$title"; ?>">
                        <label>Beschrijving</label>
                        <textarea name="description"><?php echo "$description"; ?></textarea>
                        <label>Product afbeelding</label>
                        <input type="file" name="image" id="image" value="<?php echo $currentimage; ?>"
                               onchange="readURL(this);">
                        <input type="submit" name="submit" value="Wijzigen">
                    </form>
                </div>
                <div class="dashboard-right">
                    <img id="product-image" src="/assets/images/monthlyproducts/<?php echo $currentimage; ?>">
                </div>
            </div>

            <?php
        } else {
            echo 'Er is geen product gevonden';
        }
    } else {
        $user->redirect('/dashboard');
    }
    include_once($_SERVER['DOCUMENT_ROOT'] . '/dashboard/footer.php');
?>

