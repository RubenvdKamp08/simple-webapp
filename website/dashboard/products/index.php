<?php
    include_once($_SERVER['DOCUMENT_ROOT'] . '/dashboard/header.php');
    include_once($_SERVER['DOCUMENT_ROOT'] . '/functions/products.php');
    $user = new User($conn);
    $product = new Product($conn);

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
                $user->redirect('/dashboard/products');
            }
        }

        //Controleer als er wordt gezocht naar een product.
        if (isset($_GET['search'])) {
            $search = $_GET['search'];
            $searchquery = ' WHERE p.title LIKE "%' . $search . '%"';
        } else {
            $search = '';
            $searchquery = '';
        }

        //Haal alle producten op.
        $products = $product->getProducts($searchquery, $currentRow, $limit);

        //Bereken het aantal pagina's door het aantal producten op te halen.
        $rowCounts = $conn->prepare('SELECT COUNT(*) as amount FROM products AS p' . $searchquery);
        $rowCounts->execute();
        $rowCount = $rowCounts->fetch(PDO::FETCH_ASSOC);
        $total_pages = ceil($rowCount['amount'] / $limit);

        ?>
        <div class="content-header">
            <a href="/dashboard/product_category" class="back-btn"><i class="fa fa-arrow-right" aria-hidden="true"></i>&nbsp;
                Categorieën</a>
            <form method="get" class="search-product">
                <input type="text" name="search" class="search" value="<?php echo $search; ?>" placeholder="Zoeken">
            </form>
        </div>
        <div class="content">
            <?php
            //Controleer als er minimaal 1 product aanwezig is.
            if ($products->rowCount() > 0) {
                ?>
                <table class="dash-table tableresp">
                    <thead>
                    <tr>
                        <th class="productname">Productnaam</th>
                        <th class="productcategory">Categorie</th>
                        <th class="productsubcategory">Subcategorie</th>
                        <th class="productprice">Prijs</th>
                        <th class="productavailable">Beschikbaar</th>
                        <th class="productedit">Bewerken</th>
                        <th class="productdelete">Verwijderen</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    //Loop door alle producten heen.
                    foreach ($products as $item) {
                        ?>
                        <tr>
                            <td class="productname"><?php echo $item['title']; ?></td>
                            <td class="productcategory"><?php echo $item['category']; ?></td>
                            <td class="productsubcategory"><?php echo $item['subcategory']; ?></td>
                            <td class="productprice"><?php echo $item['price']; ?></td>
                            <td class="productavailable"><?php echo ($item['available'] == 1) ? 'Ja' : 'Nee'; ?></td>
                            <td class="productedit"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> <a
                                        href="/dashboard/products/edit?id=<?php echo $item['ID']; ?>">Bewerk</a></td>
                            <td class="productdelete"><i class="fa fa-trash-o" aria-hidden="true"></i> <a
                                        onclick="return confirm('Weet u zeker dat u het product wil verwijderen?');"
                                        href="/dashboard/products/delete?id=<?php echo $item['ID']; ?>">Verwijder</a>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                    </tbody>
                </table>
                <?php
            } else {
                echo 'Geen producten gevonden<br>';
            }
            ?>
        </div>
        <?php
        //Controleer als er minimaal 1 product aanwezig is.
        if ($products->rowCount() > 0) {
            ?>
            <div class="flex-pagination">
                <?php
                //Reset category Get because of products, only check for get in URL now.
                if (isset($_GET['categorie'])) {
                    $category = $_GET['categorie'];
                } else {
                    $category = '';
                }

                //Controleer als er gezocht is naar een product.
                if (isset($_GET['search'])) {
                    $search = '&search=' . $_GET['search'];
                } else {
                    $search = '';
                }

                //Kijk als er een GET parameter voor de pagina is meegegeven aan de URL.
                if ($_GET['pagina']) {
                    $current = $_GET['pagina'];
                    //Kijk als de huidige pagina niet pagina nummer 1 is.
                    if ($current != 1) {
                        echo '<a href="/dashboard/products/?pagina=' . $search . '"> << </a>';
                        echo '<a href="?pagina=' . ($current - 1) . $search . '"> < </a>';
                    }
                } else {
                    $current = 1;
                }

                //Loop door alle pagina's heen
                for ($i = $current; $i <= $current + 2; $i++) {
                    //Vraag de huidige pagina op en controleer als het pagina 1 is of niet.
                    if ($_GET['pagina'] == $i) {
                        echo '<a href="?pagina=' . $i . $search . '" class="current">' . $i . '</a>';
                    } elseif (empty($_GET['pagina']) && $i === 1) {
                        echo '<a href="?pagina=' . $i . $search . '" class="current">' . $i . '</a>';
                    } else {
                        //controleer als de huidige pagina niet de laatste pagina is.
                        if ($current != $total_pages) {
                            if ($current != $total_pages - 1) {
                                echo '<a href="?pagina=' . $i . $search . '">' . $i . '</a>';
                            }
                        }
                    }
                }

                //Controleer als de huidige pagina niet de laatste pagina is.
                if ($_GET['pagina'] != $total_pages) {
                    //Controleer als de huidige pagina meer dan 3 pagina's verschil heeft met de laatste pagina.
                    if ($current <= $total_pages - 3) {
                        echo '<a href="#">...</a>';
                        echo '<a href="?pagina=' . $total_pages . $search . '">' . $total_pages . '</a>';
                    }
                    //Controleer als de huidige pagina de op een na laatste pagina is.
                    if ($current == $total_pages - 1) {
                        echo '<a href="?pagina=' . $total_pages . $search . '">' . $total_pages . '</a>';
                    }
                    //Controleer als de huidige pagina niet de laatste pagina is.
                    if ($current != $total_pages) {
                        echo '<a href="?pagina=' . ($current + 1) . $search . '"> > </a>';
                        echo '<a href="?pagina=' . $total_pages . $search . '"> >> </a>';
                    }
                }
                ?>
            </div>
            <?php
        }
        ?>
        <a href="/dashboard/products/create" class="create-btn">Product toevoegen</a>

        <?php
    } else {
        $user->redirect('/dashboard');
    }
    include($_SERVER['DOCUMENT_ROOT'] . '/dashboard/footer.php');

