<?php
    include_once($_SERVER['DOCUMENT_ROOT'] . '/dashboard/header.php');
    $user = new User($conn);

    //Controleer als de gebruiker de rol eigenaar, administrator of medewerker heeft en anders terugsturen naar dashboard pagina.
    if($user->has_role('Eigenaar') || $user->has_role('Administrator') || $user->has_role('Medewerker')) {
        //Controleer als er een ID in de url voorkomt.
        if (isset($_GET['id'])) {
            $id = $_GET["id"];

            //Vraag data op van desbetreffende ID.
            $monthlyproduct = $conn->prepare("SELECT * FROM monthly_product WHERE ID= :id");
            $monthlyproduct->execute(array(
                ':id' => $id));

            //Loop door alle maandelijkse producten.
            while ($row = $monthlyproduct->fetch()) {
                $type = $row["type"];
                $title = $row["title"];
                $description = $row["description"];
                $image = $row["image"];
                ?>

                <a href="/dashboard/montlyproduct" class="back-btn"><i class="fa fa-arrow-left" aria-hidden="true"></i>&nbsp;
                    Terug</a>
                <div class="home-products">
                    <div class="single-product">
                        <div class="product-image"
                             style="background-image: url('/assets/images/monthlyproducts/<?php echo $image ?>')">
                            <div class="overlay">
                                <h2><?php echo $type; ?></h2>
                            </div>
                        </div>
                        <div class="product-info">
                            <h2><?php echo $title; ?></h2>
                            <p><?php echo $description; ?></p>
                        </div>
                    </div>
                </div>
            <?php }
        } else {
            echo 'Er kon geen maandelijkse product worden gevonden.';
        }
    } else {
        $user->redirect('/dashboard');
    }
?>
