<?php
    include_once($_SERVER['DOCUMENT_ROOT'] . '/dashboard/header.php');
    $user = new User($conn);

    //Kijk als gebruiker Eigenaar of Administrator is anders doorsturen naar dashboard.
    if($user->has_role('Eigenaar') || $user->has_role('Administrator')) {
        $limit = 20;
        if (empty($_GET['pagina'])) {
            $currentRow = 0;
            $_GET['pagina'] = 0;
        } else {
            if (is_numeric($_GET['pagina'])) {
                $pagina = $_GET['pagina'];
                $currentRow = ($pagina - 1) * $limit;
            } else {
                $user->redirect('/dashboard/news');
            }
        }

        //Vraag alle contactinzendingen op.
        $contact = $conn->prepare('SELECT * FROM contact ORDER BY date DESC LIMIT ' . $currentRow . ', ' . $limit);
        $contact->execute();

        //Vraag aantal pagina's op
        $rowCounts = $conn->prepare('SELECT COUNT(*) AS amount FROM contact');
        $rowCounts->execute();
        $rowCount = $rowCounts->fetch(PDO::FETCH_ASSOC);
        $total_pages = ceil($rowCount['amount'] / $limit);

        //Controleer als er minimaal 1 contact formulier aanwezig is.
        if ($contact->rowCount() > 0) {
            ?>
            <div class="content">
                <table class="dash-table tableresp">
                    <thead>
                    <tr>
                        <th>Onderwerp</th>
                        <th class="contactname">Naam</th>
                        <th class="contactdate">Datum</th>
                        <th>Bekijken</th>
                        <th>Verwijderen</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    //Loop door alle contactformulieren heen.
                    foreach ($contact as $formulier) {
                        ?>
                        <tr>
                            <td><?php echo $formulier['subject']; ?></td>
                            <td class="contactname"><?php echo $formulier['firstname'] . " " . $formulier['middlename'] . " " .
                                    $formulier['lastname']; ?></td>
                            <td class="contactdate"><?php echo $formulier['date'] ?></td>
                            <td><i class="fa fa-eye" aria-hidden="true"></i></i> <a
                                        href="/dashboard/contact/view/<?php echo $formulier["ID"]; ?>">Bekijk</a></td>
                            <td><i class="fa fa-trash-o" aria-hidden="true"></i> <a
                                        href="/dashboard/contact/delete?id=<?php echo $formulier['ID']; ?>"
                                        onclick="return confirm('Weet u zeker dat u het contactformulier wil verwijderen?');">Verwijder</a>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                    </tbody>
                </table>
            </div>
            <div class="flex-pagination">
                <?php
                //Kijk als er een GET parameter voor de pagina is meegegeven aan de URL.
                if ($_GET['pagina']) {
                    $current = $_GET['pagina'];
                    //Kijk als de huidige pagina niet pagina nummer 1 is.
                    if ($current != 1) {
                        echo '<a href="/dashboard/contact"> << </a>';
                        echo '<a href="?pagina=' . ($current - 1) . '"> < </a>';
                    }
                } else {
                    $current = 1;
                }

                //Loop door alle pagina's.
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
            <?php
        } else {
            echo 'Er zijn nog geen inzendingen verstuurd.';
        }
    } else {
        $user->redirect('/dashboard');
    }
include($_SERVER['DOCUMENT_ROOT'] . '/dashboard/footer.php');

