<?php
    include('header.php');

    //Haal sliders, maandelijkse producten en nieuwsberichten op uit de database.
    $slideritems = $conn->query('SELECT * FROM homepageslider');
    $monthlyitems = $conn->query('SELECT * FROM monthly_product');
    $newsitems = $conn->query('SELECT * FROM news ORDER BY ID DESC LIMIT 3');
?>
<section class="home-slider">
    <div class="slider">
        <?php
            //Controleer als er minimaal 1 slide aanwezig is.
            if ($slideritems->rowCount() > 0) {
                //Loop door alle sliders heen.
                foreach ($slideritems as $slideritem) {
                    ?>
                    <div class="slide" style="background: url('assets/images/slider/<?php echo $slideritem["image"]; ?>') no-repeat center center">
                        <div class="slide-overlay"></div>
                        <div class="slide-text">
                            <h1><?php echo $slideritem["title"]; ?></h1>
                            <div class="slidearrow">
                                <i class="fa fa-arrow-down" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            } else {
                ?>
                <div class="slide" style="background: url('assets/images/slider/slide2.jpg') no-repeat center center">

                </div>
                <?php
            }
        ?>
    </div>
</section>
<section class="content" id="content">
    <div class="home-products">
        <?php
        //Controleer als er minimaal 1 maandelijks product aanwezig is.
        if ($monthlyitems->rowCount() > 0) {
            //Loop door alle maandelijkse producten heen.
            foreach ($monthlyitems as $monthlyitem) {
                ?>
                <div class="single-product">
                    <div class="product-image" style="background-image: url('/assets/images/monthlyproducts/<?php echo $monthlyitem["image"] ?>');">
                        <div class="overlay">
                            <h2><?php echo $monthlyitem["type"]; ?></h2>
                        </div>
                    </div>
                    <div class="product-info">
                        <h2><?php echo $monthlyitem["title"]; ?></h2>
                        <p><?php echo $monthlyitem["description"]; ?></p>
                    </div>
                </div>
                <?php
            }
        }
        ?>
    </div>
    <div class="home-news">
        <?php
            //Controleer als er minimaal 1 nieuwsbericht aanwezig is.
            if ($newsitems->rowCount() > 0) {
                //Loop door alle nieuwsberichten heen.
                foreach ($newsitems as $newsitem) {
                    $description = (strlen($newsitem["description"]) > 125) ? substr($newsitem["description"], 0, 125) . '...' : $newsitem["description"];
                    ?>
                    <div class="single-news">
                        <div class="news-image" style="background-image: url('/assets/images/news/<?php echo $newsitem["image"]; ?>')"></div>
                        <div class="news-info">
                            <h2><?php echo $newsitem["title"]; ?></h2>
                            <p><?php echo $description; ?></p>
                            <a href="/newsarticle?ID=<?php echo $newsitem["ID"]; ?>" class="btn">Lees meer</a>
                        </div>
                    </div>
                    <?php
                }
            }
        ?>
    </div>
</section>
<?php include('footer.php'); ?>

