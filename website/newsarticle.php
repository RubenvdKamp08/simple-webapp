<?php
include('header.php');
?>
<section class="content content-newsarticle" >
    <div class="main-newsarticle" >
        <?php
            //Controleer als er een ID parameter in de URL wordt meegegeven.
            if(isset($_GET['ID'])) {
                $ID = $_GET["ID"];
                //Haal het nieuwsbericht op.
                $newsitems = $conn->prepare("SELECT * FROM news WHERE ID ='" . $ID . "'");
                $newsitems->execute();
                $newsitem = $newsitems->fetchAll();
                $newsitems = NULL;
                foreach ($newsitem as $item) {
                    echo '<div class="newsitem-text"></div><div class="title"><h2>' . $item["title"] . '</h2></div>';
                    echo '<div class="newsitem-buttom">';
                    echo '<div class="newsitem-image"><img src="/assets/images/news/' . $item["image"] . '" alt=' . $item["title"] . '></div>';
                    echo '<div class="newsitem-description">' . $item["description"] . '</div></div>';
                    echo '</div>';
                }
            } else {
                echo 'Er kon geen nieuwsartikel worden gevonden';
            }
?>


    </div>
</section>
<?php include('footer.php'); ?>
