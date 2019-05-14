<?php
    include_once($_SERVER['DOCUMENT_ROOT'] . '/dashboard/header.php');
    $user = new User($conn);

    //Controleer als de gebruiker de rol Eigenaar of administrator heeft.
    if($user->has_role('Eigenaar') || $user->has_role('Administrator')) {
        //Controleer als er een GET parameter van ID wordt meegegeven in de URL.
        if (isset($_GET['id'])) {
            $id = $_GET["id"];
            //Vraag review op.
            $review = $conn->prepare("SELECT * FROM reviews WHERE id=:id");
            //Voeg reactie toe aan review.
            $reaction = $conn->prepare("UPDATE reviews SET reaction=:reaction WHERE id=:id");

            //Controler als er gepost is en voer daarna de reactie query uit.
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $reaction->execute(array(
                    ':reaction' => $_POST["reaction"],
                    ':id' => $id
                ));
            }

            $review->execute(array(
                ':id' => $id
            ));
            ?>
            <a href="/dashboard/reviews" class="back-btn"><i class="fa fa-arrow-left" aria-hidden="true"></i>&nbsp;
                Terug</a>
            <?php
            //Loop door alle data heen.
            while ($row = $review->fetch()) {
                $firstname = $row["firstname"];
                $middlename = $row["middlename"];
                $lastname = $row["lastname"];
                $date = $row["date"];
                $anonymous = $row["anonymous"];
                $rating = $row["rating"];
                $message = $row["message"];
                $reaction = $row["reaction"];
                echo "<p class='reviewInfo'>Naam:</p>";
                if ($anonymous == 1) {
                    echo "<p class='reviewText'> Anoniem </p>";
                } else {
                    echo "<p class='reviewText'> $firstname $middlename $lastname </p> ";
                }
                echo "<p class='reviewInfo'> Datum: </p>
                  <p class='reviewText'> $date </p>";
                echo "<p class='reviewInfo'> Aantal sterren:<p> <p>";
                for ($i = 0; $i < $rating; $i++) {
                    echo "<i class=\"starsrating fa fa-star fa-2x\" aria-hidden=\"true\"></i>";
                }
                echo "<p> ";
                echo "<p class='reviewInfo'>Toelichting:</p>
                        <p class='review-text'>$message<p> ";
            }
            ?>

            <p class="reviewInfo"> Reageren </p>
            <form class='review-form' method="post">
                <textarea name="reaction" placeholder="Reactie" maxlength="200"><?php echo "$reaction"; ?></textarea>
                <br>
                <input type="submit" name="submit" value="Plaatsen"
                       onclick="return confirm('Weet je zeker dat je de reactie wil plaatsen?');">
            </form>

            <?php
        }
    } else {
        $user->redirect('/dashboard/reviews');
    }
?>
