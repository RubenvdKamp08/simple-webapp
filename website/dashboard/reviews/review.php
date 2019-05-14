<?php
    include_once($_SERVER['DOCUMENT_ROOT'] . '/dashboard/header.php');
    $user = new User($conn);

    //Controleer als de gebruiker de rol Eigenaar, administrator of medewerker heeft.
    if($user->has_role('Eigenaar') || $user->has_role('Administrator') || $user->has_role('Medewerker')) {
        //Controleer als er een ID als GET parameter wordt meegegeven aan de URL.
        if (isset($_GET['id'])) {
            $id = $_GET["id"];
            //Haal de data van de review op.
            $review = $conn->prepare("SELECT * FROM reviews WHERE id=:id");
            $review->execute(array(
                ':id' => $id
            ));
            ?>
            <a href="/dashboard/reviews" class="back-btn"><i class="fa fa-arrow-left" aria-hidden="true"></i>&nbsp;
                Terug</a>
            <?php
            //Loop door alle data van de review heen.
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
                echo "<p class='reviewInfo'>Toelichting:</p>
                    <p class='review-text'>$message<p> ";
                echo "<p class='reviewInfo'> Reactie: </p>";
                if (empty($reaction)) {
                    echo "<p class='review-text'> Geen reactie </p>";
                } else {
                    echo "<p class='review-text'> $reaction </p>";
                }
            }
        } else {
            echo 'Geen review gevonden';
        }
    } else {
        $user->redirect('/dashboard');
    }