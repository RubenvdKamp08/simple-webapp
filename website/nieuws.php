<?php
//includes:
include('header.php');
include('lib/connection.php');

//Check for page request
$limit = 4;
if(empty($_GET['pagina'])) {
    $currentRow = 0;
    $_GET['pagina'] = 0;
} else {
    if(is_numeric($_GET['pagina'])) {
        $pagina = $_GET['pagina'];
        $currentRow = ($pagina - 1) * $limit;
    } else {
        $user->redirect('/nieuws');
    }
}

//check for submit
if (isset($_GET['categorie'])) {
    $category = $_GET['categorie'];
    $newscategoryquery = ' WHERE nc.name = "' . $category . '" AND active = 1';
    $categorysearch = '&categorie=' . $category;
} else {
    $newscategoryquery = ' WHERE active = 1';
    $class = '';
    $categorysearch = '';
}

//vars:
$descmax = 170;
//Haal nieuwscategorieen op.
$newscategory = $conn->prepare("SELECT nc.name, nc.ID, COUNT(n.ID) AS amount FROM newscategory AS nc INNER JOIN news AS n ON nc.ID=n.categoryID GROUP BY nc.ID");
$newscategory->execute();

//Haal nieuwsberichten op.
$newsitem = $conn->prepare("SELECT n.ID, image, title, description FROM news AS n INNER JOIN newscategory AS nc ON n.categoryID = nc.ID" . $newscategoryquery . " ORDER BY n.ID DESC LIMIT " . $currentRow . ', ' . $limit . '');
$newsitem->execute();

//Haal aantal regels op en bereken het aantal pagina's.
$rowCounts = $conn->prepare('SELECT COUNT(*) as amount FROM news AS n INNER JOIN newscategory AS nc ON n.categoryID = nc.ID ' . $newscategoryquery);
$rowCounts->execute();
$rowCount = $rowCounts->fetch(PDO::FETCH_ASSOC);
$total_pages = ceil($rowCount['amount'] / $limit);

//functions:
function shorten($text, $max) {
    echo '<br>';
    if (strlen($text) <= $max) {
        echo $text;
    } else {
        echo substr($text, 0, $max - 3) . '...';
    }
}
?>
<section class="content content-news">
    <div class="main-news">
        <div class="news-category">
            <span class="title">Categorieën</span>
            <ul>
                <?php
                //Loop door alle nieuwscategorieen heen.
                foreach ($newscategory as $category) {
                    if (isset($_GET['categorie'])) {
                        if ($category["name"] === $_GET['categorie']) {
                            $class = 'class="selected"';
                        } else {
                            $class = '';
                        }
                    }
                    ?>
                    <li><a href="?categorie=<?php echo $category["name"]; ?>" <?php echo $class; ?>> <?php echo $category["name"] . ' ('. $category["amount"] .')'; ?></a></li>
                <?php
                }
                if (isset($_GET["categorie"])) {
                    echo '<li><a href="/nieuws">Toon alles</a></li>';
                }
                ?>
            </ul>
        </div>
        <div class="news-item">
            <span class="title">Nieuws artikelen:</span>
            <?php
            //Controleer als er minimaal 1 nieuwsbericht aanwezig is.
            if($newsitem->rowCount() > 0) {
                //Loop door alle nieuwsberichten heen.
                foreach ($newsitem as $news) {
                    ?>
                    <div class="newsarticle">
                        <div class="newspicture">
                            <img src="/assets/images/news/<?php echo $news["image"]; ?>"
                                 alt='<?php echo $news["title"]; ?> '>
                        </div>
                        <div class="newstext">
                            <div class="newstitle"><?php echo $news["title"]; ?></div>
                            <?php shorten($news["description"], $descmax); ?>
                            <a href="artikel/<?php echo $news["ID"]; ?>" alt="'<?php echo $news["title"]; ?>'">Lees meer</a>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo '<h1>Er konden geen nieuwsberichten worden gevonden!';
            }
            ?>
        </div>
    </div>
    <div class="flex-pagination">
        <?php
        //Controleer als er minimaal 1 nieuwsbericht aanwezig is.
        if($newsitem->rowCount() > 0) {
            //Kijk als er een GET parameter voor de pagina is meegegeven aan de URL.
            if ($_GET['pagina']) {
                $current = $_GET['pagina'];
                //Kijk als de huidige pagina niet pagina nummer 1 is.
                if ($current != 1) {
                    if (isset($_GET['categorie'])) {
                        $categorysearch = '?categorie=' . $category;
                    }
                    echo '<a href="/nieuws' . $categorysearch . '"> << </a>';
                    echo '<a href="?pagina=' . ($current - 1) . $categorysearch . '"> < </a>';
                }
            } else {
                $current = 1;
            }

            //Loop door alle pagina's heen
            for ($i = $current; $i <= $current + 2; $i++) {
                //Vraag de huidige pagina op en controleer als het pagina 1 is of niet.
                if ($_GET['pagina'] == $i) {
                    echo '<a href="?pagina=' . $i . $categorysearch . '" class="current">' . $i . '</a>';
                } elseif (empty($_GET['pagina']) && $i === 1) {
                    echo '<a href="?pagina=' . $i . $categorysearch . '" class="current">' . $i . '</a>';
                } else {
                    //controleer als de huidige pagina niet de laatste pagina is.
                    if ($current != $total_pages) {
                        if ($current != $total_pages - 1) {
                            echo '<a href="?pagina=' . $i . $categorysearch . '">' . $i . '</a>';
                        }
                    }
                }
            }
            //Controleer als de huidige pagina niet de laatste pagina is.
            if ($_GET['pagina'] != $total_pages) {
                //Controleer als de huidige pagina meer dan 3 pagina's verschil heeft met de laatste pagina.
                if ($current <= $total_pages - 3) {
                    echo '<a href="#">...</a>';
                    echo '<a href="?pagina=' . $total_pages . $categorysearch . '">' . $total_pages . '</a>';
                }
                //Controleer als de huidige pagina de op een na laatste pagina is.
                if ($current == $total_pages - 1) {
                    echo '<a href="?pagina=' . $total_pages . $categorysearch . '">' . $total_pages . '</a>';
                }
                //Controleer als de huidige pagina niet de laatste pagina is.
                if ($current != $total_pages) {
                    echo '<a href="?pagina=' . ($current + 1) . $categorysearch . '"> > </a>';
                    echo '<a href="?pagina=' . $total_pages . '"> >> </a>';
                }
            }
        }
        ?>
    </div>
</section>
<?php include('footer.php'); ?>
