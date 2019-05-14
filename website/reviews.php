<?php
    include('header.php');

    //Check for page request
    $limit = 12;
    if(empty($_GET['pagina'])) {
        $currentRow = 0;
        $_GET['pagina'] = 0;
    } else {
        $pagina = $_GET['pagina'];
        $currentRow = ($pagina - 1) * $limit;
    }

    $reviews = $conn->prepare("SELECT * FROM reviews ORDER BY date DESC LIMIT " . $currentRow . ", " . $limit);
    $gemiddelde = $conn->prepare("SELECT AVG(rating) FROM reviews");
    $aantalReviews = $conn->prepare("SELECT COUNT(*) AS amount FROM reviews");
    $aantalReviews->execute();
    $rowCount = $aantalReviews->fetch(PDO::FETCH_ASSOC);
    $total_pages = ceil($rowCount['amount'] / $limit);

    $captcha = false;
    $firstnameTrue=true;
    $lastnameTrue=true;
    $firstname = "";
    $middlename = "";
    $lastname="";
    $message="";
    $anonymous="3";
    $star="";
    $starsTrue = true;
    $send = "";
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])) {
            //Recaptcha key
            $secret = '6LeESTkUAAAAAJ7wfXVne6e9rBBdquHvF2alnBkU';
            //Response data
            $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . $secret .
                '&response=' . $_POST['g-recaptcha-response']);
            $responseData = json_decode($verifyResponse);
            if ($responseData->success) {
                $_POST = array_map('strip_tags', $_POST);
                $firstnameTrue = false;
                $lastnameTrue = false;
                $starsTrue = false;
                $firstname = ucfirst($_POST["firstname"]);
                $middlename = $_POST["middlename"];
                $lastname = ucfirst($_POST["lastname"]);
                $anonymous = $_POST["anonymous"];
                if (isset($_POST["star"])) {
                    $star = $_POST["star"];
                } else {
                    $star = '';
                }

                $message = ucfirst($_POST["message"]);
                if ($_POST["anonymous"] == 0) {
                    if (empty($_POST["firstname"])) {
                        $firstnameTrue = false;
                    } else {
                        $firstnameTrue = true;
                    }
                    if (empty($_POST["lastname"])) {
                        $lastnameTrue = false;
                    } else {
                        $lastnameTrue = true;
                    }
                } else {
                    $firstnameTrue = true;
                    $lastnameTrue = true;
                }
                if (empty($star)) {
                    $starsTrue = false;
                } else {
                    $starsTrue = true;
                }
                if ($firstnameTrue == true && $lastnameTrue == true && $starsTrue) {
                    $stmt = $conn->prepare("INSERT INTO reviews(firstname, middlename, lastname, anonymous, rating, message) VALUES(:firstname, :middlename, :lastname, :anonymous, :star, :message)");
                    $stmt->execute(array(
                        ':firstname' => $firstname,
                        ':middlename' => $middlename,
                        ':lastname' => $lastname,
                        ':anonymous' => $anonymous,
                        ':star' => $star,
                        ':message' => $message
                    ));
                    $send = true;
                }
                $captcha = false;
            } else {
                $captcha = true;
            }
        } else {
            $captcha = true;
        }
    }
?>

<section class="content content-review">
    <div class="heading">
        <h2> Reviews </h2>
    </div>
    <div class="main-review">
        <div class="review-form">
            <form method="post">
                <div class="making-review">
                    <div class="review-info">
                        <input type="text" name="firstname" placeholder="Voornaam" value="<?php if($lastnameTrue == false || $starsTrue == false) { echo "$firstname"; }?>"> <?php if ($firstnameTrue == false) { echo "<span class='naamTrue'>verplicht!</span>";} ?><br> <br>
                        <input type="text" name="middlename" placeholder="Tussenvoegsel" value="<?php if($firstnameTrue == false || $lastnameTrue == false || $starsTrue == false) {echo "$middlename";}?>"> <br> <br>
                        <input type="text" name="lastname" placeholder="Achternaam" value="<?php if($firstnameTrue == false || $lastnameTrue == false || $starsTrue == false) { echo "$lastname";}?>"> <?php if ($lastnameTrue == false) { echo "<span class='naamTrue'>verplicht!</span>";}?><br> <br>
                        <p> Anoniem </p>
                        <input type="radio" name="anonymous" value="1" required <?php if($anonymous == 1){echo"checked=\"true\"";}?>><span> Ja </span>
                        <input type="radio" name="anonymous" value="0" <?php if ($anonymous==0){echo"checked=\"true\"";}?>><span> Nee </span> <br>
                    </div>
                    <div class="review">
                        Aantal sterren <br>
                        <!-- <input type="number" name="rating" placeholder="aantal sterren" required><br> -->
                        <div class="stars">
                                <input class="star star-5" id="star-5" type="radio" name="star" value="5" <?php if($star==5 && ($lastnameTrue == false || $starsTrue == false)){echo"checked=\"true\"";}?>>
                                <label class="star star-5" for="star-5"></label>
                                <input class="star star-4" id="star-4" type="radio" name="star" value="4" <?php if($star==4 && ($lastnameTrue == false || $starsTrue == false)){echo"checked=\"true\"";}?>>
                                <label class="star star-4" for="star-4"></label>
                                <input class="star star-3" id="star-3" type="radio" name="star" value="3" <?php if($star==3 && ($lastnameTrue == false || $starsTrue == false)){echo"checked=\"true\"";}?>>
                                <label class="star star-3" for="star-3"></label>
                                <input class="star star-2" id="star-2" type="radio" name="star" value="2" <?php if($star==2 && ($lastnameTrue == false || $starsTrue == false)){echo"checked=\"true\"";}?>>
                                <label class="star star-2" for="star-2"></label>
                                <input class="star star-1" id="star-1" type="radio" name="star" value="1" <?php if($star==1 && ($lastnameTrue == false || $starsTrue == false)){echo"checked=\"true\"";}?>>
                                <label class="star star-1" for="star-1"></label>
                        </div> <?php if ($starsTrue == false) { echo "<span class='starTrue'>Verplicht!</span>";}?>
                        <p> Toelichting <span class="max-300"> (Maximaal 300 tekens)</span> </p>
                        <textarea name="message" placeholder="Toelichting" required maxlength="300"><?php if($firstnameTrue == false || $lastnameTrue == false || $starsTrue == false) { echo "$message";};?></textarea> <br> <br>
                        <div class="g-recaptcha" data-sitekey="6LeESTkUAAAAAIpBfp_ocb0-21UbKJzthvPaIX3r"></div><br>
                        <button type="submit" name="submit" value="plaatsen"> Plaatsen </button>
                        <?php
                            if($captcha === true) {
                                echo 'Vul de Captcha in!';
                            }
                        ?>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <?php
        if ($send == true) { echo "bedankt voor uw review!";}
    ?>
    <div class="review-gem">
        <?php
            $gemiddelde->execute();
            $aantalReviews->execute();
            $aantal = 0;
            while ($row = $aantalReviews->fetch()) {
                $aantal = $row["amount"];
            }

            if ($aantal != 0) {
                while ($row = $gemiddelde->fetch()) {
                    $gem = $row["AVG(rating)"];
                    $gem = round($gem);
                    for ($i = 0; $i < $gem; $i++) {
                        echo "<i class=\"fa fa-star fa-2x\" aria-hidden=\"true\"></i>";
                    }
                }
                echo "<br>";
                echo "$gem sterren uit $aantal reviews";
            } else {
                echo "Geen reviews aanwezig";
            }
        ?>
    </div>
    <div class="show-review">
        <?php
            $reviews->execute();
            while ($row = $reviews->fetch()) { 
                $firstname = $row["firstname"];
                $middlename = $row["middlename"];
                $lastname = $row["lastname"];
                $date = $row["date"];
                $anonymous = $row["anonymous"];
                $rating = $row["rating"];
                $message = $row["message"];
                $reaction = $row["reaction"];
                if ($anonymous == 0) {
                    echo "<div class='review-left'> 
                            <span class='review-name'> $firstname $middlename $lastname</span> <br>
                            <span class='review-date'> $date </span> <br>";
                } else {
                    echo "<div class=\"review-left\">
                            <span class='review-name'> Anoniem </span> <br>
                            <span class='review-date'> $date </span> <br>";
                }
                echo "<div class='review-star'>";
                for ($i=0; $i<$rating; $i++) {
                    echo "<i class=\"fa fa-star fa-2x\" aria-hidden=\"true\"></i>";
                }
                echo "</div>";
                echo "<p> $message </p>";
                if (!empty($reaction)) {
                    echo "<p class='review-reaction'> $reaction </p>";
                }
                echo "</div>";
            }
        ?>
    </div>
    <?php
    if($reviews->rowCount() > 0) {
        ?>
        <div class="flex-pagination">
            <?php
                //Reset category Get because of products, only check for get in URL now.
                if (isset($_GET['categorie'])) {
                    $category = $_GET['categorie'];
                } else {
                    $category = '';
                }
                if ($_GET['pagina']) {
                    $current = $_GET['pagina'];
                    if ($current != 1) {
                        echo '<a href="/reviews' . $category . '"> << </a>';
                        echo '<a href="?pagina=' . ($current - 1) . $category . '"> < </a>';
                    }
                } else {
                    $current = 1;
                }

                for ($i = $current; $i <= $current + 2; $i ++) {
                    if ($_GET['pagina'] == $i) {
                        echo '<a href="?pagina=' . $i . $category . '" class="current">' . $i . '</a>';
                    } elseif (empty($_GET['pagina']) && $i === 1) {
                        echo '<a href="?pagina=' . $i . $category . '" class="current">' . $i . '</a>';
                    } else {
                        if ($current != $total_pages) {
                            if ($current != $total_pages - 1) {
                                echo '<a href="?pagina=' . $i . $category . '">' . $i . '</a>';
                            }
                        }
                    }
                }
                if ($_GET['pagina'] != $total_pages) {
                    if ($current <= $total_pages - 3) {
                        echo '<a href="#">...</a>';
                        echo '<a href="?pagina=' . $total_pages . $category . '">' . $total_pages . '</a>';
                    }
                    if ($current == $total_pages - 1) {
                        echo '<a href="?pagina=' . $total_pages . $category . '">' . $total_pages . '</a>';
                    }
                    if ($current != $total_pages) {
                        echo '<a href="?pagina=' . ($current + 1) . $category . '"> > </a>';
                        echo '<a href="?pagina=' . $total_pages . '"> >> </a>';
                    }
                }
            ?>
        </div>
        <?php
    }
    ?>
</section>

<?php

include('footer.php');

?>


