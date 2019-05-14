<?php
    include_once($_SERVER['DOCUMENT_ROOT'] . '/dashboard/header.php');
    $user = new User($conn);

//Controleer als de gebruiker de rol Eigenaar, administrator of medewerker heeft.
    if($user->has_role('Eigenaar') || $user->has_role('Administrator') || $user->has_role('Medewerker')) {
        //Check for page request
        $limit = 20;
        if (empty($_GET['pagina'])) {
            $currentRow = 0;
            $_GET['pagina'] = 0;
        } else {
            if (is_numeric($_GET['pagina'])) {
                $pagina = $_GET['pagina'];
                $currentRow = ($pagina - 1) * $limit;
            } else {
                $user->redirect('/dashboard/reviews');
            }
        }

        //Haal alle reviews op
        $reviews = $conn->prepare("SELECT * FROM reviews ORDER BY date DESC  LIMIT " . $currentRow . ", " . $limit);
        //Vraag gemiddelde van alle reviews op.
        $gemiddelde = $conn->prepare("SELECT AVG(rating) FROM reviews");
        $gemiddelde->execute();
        //Vraag aantal reviews op en bereken aantal pagina's.
        $aantalReviews = $conn->prepare("SELECT COUNT(*) AS amount FROM reviews");
        $aantalReviews->execute();
        $rowCount = $aantalReviews->fetch(PDO::FETCH_ASSOC);
        $total_pages = ceil($rowCount['amount'] / $limit);

        $reviews->execute();
        //Controleer als er minimaal 1 review aanwezig is.
        if ($reviews->rowCount() > 0) {
            ?>
            <table class="dash-table tableresp tablereviews">
                <thead>
                <tr>
                    <th> Naam</th>
                    <th class="reviewdate"> Datum</th>
                    <th class="reviewstars"> Aantal sterren</th>
                    <th> Bekijken</th>
                    <?php
                    //Controleer als de gebruiker de rol Eigenaar of administrator heeft en toon de Reageer knop.
                    if($user->has_role('Eigenaar') || $user->has_role('Administrator')) {
                    ?>
                        <th> Wijzigen</th>
                    <?php
                    }
                    ?>
                    <th> verwijderen</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $reviews->execute();
                //Loop door alle data heen.
                while ($row = $reviews->fetch()) {
                    $id = $row["ID"];
                    $firstname = $row["firstname"];
                    $middlename = $row["middlename"];
                    $lastname = $row["lastname"];
                    $date = $row["date"];
                    $anonymous = $row["anonymous"];
                    $rating = $row["rating"];
                    $message = $row["message"];
                    echo "<tr>";
                    if ($anonymous == 1) {
                        echo "<td> Anoniem</td>";
                    } else {
                        echo "<td> $firstname $middlename $lastname";
                    }
                    echo "<td class='reviewdate'> $date </td><td class='reviewstars'>";
                    for ($i = 0; $i < $rating; $i++) {
                        echo "<i class=\"starsrating fa fa-star\" aria-hidden=\"true\"></i>";
                    }
                    echo "</td>";
                    echo "<td> <i class=\"fa fa-eye\" aria-hidden=\"true\"></i> <a href=\"/dashboard/reviews/review?id=$id\">Bekijken</a></td>";
                    //Controleer als de gebruiker de rol Eigenaar of administrator heeft en toon de Reageer knop.
                    if($user->has_role('Eigenaar') || $user->has_role('Administrator')) {
                        echo "<td><i class=\"fa fa-pencil-square-o\" aria-hidden=\"true\"></i> <a href=\"/dashboard/reviews/react?id=$id\">Reageren</a></td>";
                    }
                    echo "<td><i class=\"fa fa-trash-o\" aria-hidden=\"true\"></i> <a href=\"/dashboard/reviews/delete?id=$id\" onclick=\"return confirm('Weet je zeker dat je het wilt verwijderen?');\">Verwijder</a></td>";
                    echo "</tr>";
                }
                ?>
                </tbody>
            </table>
            <?php
            $aantal = $rowCount['amount'];
            ?>
            <div class="flex-pagination">
                <?php
                //Kijk als er een GET parameter voor de pagina is meegegeven aan de URL.
                if ($_GET['pagina']) {
                    $current = $_GET['pagina'];
                    //Kijk als de huidige pagina niet pagina nummer 1 is.
                    if ($current != 1) {
                        echo '<a href="/dashboard/reviews"> << </a>';
                        echo '<a href="?pagina=' . ($current - 1) . '"> < </a>';
                    }
                } else {
                    $current = 1;
                }

                //Loop door alle pagina's heen
                for ($i = $current; $i <= $current + 2; $i++) {
                    //Vraag de huidige pagina op en controleer als het pagina 1 is of niet.
                    if ($_GET['pagina'] == $i) {
                        echo '<a href="?pagina=' . $i . '" class="current">' . $i . '</a>';
                    } elseif (empty($_GET['pagina']) && $i === 1) {
                        echo '<a href="?pagina=' . $i . '" class="current">' . $i . '</a>';
                    } else {
                        //controleer als de huidige pagina niet de laatste pagina is.
                        if ($current != $total_pages) {
                            if ($current != $total_pages - 1) {
                                echo '<a href="?pagina=' . $i . '">' . $i . '</a>';
                            }
                        }
                    }
                }

                //Controleer als de huidige pagina niet de laatste pagina is.
                if ($_GET['pagina'] != $total_pages) {
                    //Controleer als de huidige pagina meer dan 3 pagina's verschil heeft met de laatste pagina.
                    if ($current <= $total_pages - 3) {
                        echo '<a href="#">...</a>';
                        echo '<a href="?pagina=' . $total_pages . '">' . $total_pages . '</a>';
                    }
                    //Controleer als de huidige pagina de op een na laatste pagina is.
                    if ($current == $total_pages - 1) {
                        echo '<a href="?pagina=' . $total_pages . '">' . $total_pages . '</a>';
                    }
                    //Controleer als de huidige pagina niet de laatste pagina is.
                    if ($current != $total_pages) {
                        echo '<a href="?pagina=' . ($current + 1) . '"> > </a>';
                        echo '<a href="?pagina=' . $total_pages . '"> >> </a>';
                    }
                }
                ?>
            </div>
            <div class='gem'>
                <?php
                //Controleer als er meer dan 1 review geplaatst is en toon dan het gemiddelde van de reviews.
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
                }
                ?>
            </div>
            <?php
        } else {
            echo "geen reviews aanwezig";
        }
    } else {
        $user->redirect('/dashboard');
    }

    include_once($_SERVER['DOCUMENT_ROOT'] . '/dashboard/footer.php');
?>



