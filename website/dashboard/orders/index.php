<?php
    include_once($_SERVER['DOCUMENT_ROOT'] . '/dashboard/header.php');
    include_once($_SERVER['DOCUMENT_ROOT'] . '/functions/products.php');
    $user = new User($conn);

    //Controleer als de gebruiker de rol eigenaar, administrator of medewerker heeft en anders terugsturen naar dashboard pagina.
    if($user->has_role('Eigenaar') || $user->has_role('Administrator') || $user->has_role('Medewerker')) {

        //Maak variabelen leeg.
        $orderquery = "";
        $wherequery = "";
        $searchquery = "";
        $search = "";

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
                $user->redirect('/dashboard/orders');
            }
        }

        //Controleer als er een sortering is opgegegeven en baseer daar de query op.
        if (isset($_GET['select-order'])) {
            $filter = $_GET['select-order'];
            $orderparam = "&select-order=" . $filter;
            if ($filter == 'date-ASC') {
                $orderquery = " ORDER BY date ASC";
            } elseif ($filter == 'date-DESC') {
                $orderquery = " ORDER BY date DESC";
            } elseif ($filter == 'name-ASC') {
                $orderquery = " ORDER BY firstname ASC";
            } elseif ($filter == 'name-DESC') {
                $orderquery = " ORDER BY firstname DESC";
            } elseif ($filter == 'amount-DESC') {
                $orderquery = " ORDER BY aantal DESC";
            } elseif ($filter == 'amount-ASC') {
                $orderquery = " ORDER BY aantal ASC";
            }
        } else {
            $orderparam = '';
            $orderquery = " ORDER BY date DESC";
        }

        //Controleer als er een status is aangegeven en baseer daar de query op.
        if (isset($_GET["select-status"])) {
            $status = $_GET['select-status'];
            $statusparam = "&select-status=" . $status;
            if ($status == 'status-verwerken') {
                $wherequery = " WHERE status = 'verwerken'";
            } elseif ($status == 'status-afgerond') {
                $wherequery = " WHERE status = 'afgerond'";
            } elseif ($status == 'status-afgerond-verwerken') {
                $wherequery = "";
            }
        } else {
            $statusparam = '';
        }

        //Controleer als er een zoekwoord is opgegeven en baseer daar de query op.
        if (!empty($_GET["search-orders"])) {
            $search = $_GET["search-orders"];
            $searchquery = ' WHERE firstname LIKE "%' . $search . '%" OR lastname LIKE "%' . $search . '%"';
            $paginationsearch = '&search-orders=' . $search;
        } else {
            $paginationsearch = '';
        }

        //Vraag alle orders op.
        $orders = $conn->prepare("SELECT O.ID id, firstname, middlename, lastname, sum(amount) aantal, O.date datum, status FROM orders O JOIN customer C ON O.customerID = C.ID JOIN orderlines OL ON O.ID=OL.orderID" . $wherequery . $searchquery . " GROUP BY id " . $orderquery . " LIMIT " . $currentRow . ", " . $limit);
        $orders->execute();

        //Vraag totaal aantal pagina's op.
        $rowCounts = $conn->prepare("SELECT COUNT(*) AS amount FROM orders O JOIN customer C ON O.customerID = C.ID " . $wherequery . $searchquery);
        $rowCounts->execute();
        $rowCount = $rowCounts->fetch(PDO::FETCH_ASSOC);
        $total_pages = ceil($rowCount['amount'] / $limit);
        ?>

        <div class="ordersOverview-top">
            <div class="ordersSort">
                <form method="get" class="sort-orders">
                    <select name="select-order">
                        <option name="default"> Sorteren op</option>
                        <option name="date-DESC" value="date-ASC"> Datum oud-nieuw</option>
                        <option name="date-ASC" value="date-DESC"> Datum nieuw-oud</option>
                        <option name="name-ASC" value="name-ASC"> Naam A - Z</option>
                        <option name="name-DESC" value="name-DESC"> Naam Z - A</option>
                        <option name="amount-DESC" value="amount-DESC"> Aantal veel - weinig</option>
                        <option name="amount-ASC" value="amount-ASC"> Aantal weinig - veel</option>
                    </select>
                    <select name="select-status">
                        <option name="default"> Sorteren op status</option>
                        <option name="status-verwerken" value="status-verwerken"> Verwerken</option>
                        <option name="status-afgerond" value="status-afgerond"> Afgerond</option>
                        <option name="status-afgerond-verwerken" value="status-afgerond-verwerken"> Beiden</option>
                    </select>
                    <div class="buttonorder leftorderbutton">
                        <button type="submit"> Verzenden</button>
                    </div>
                    <input class="search-order search-orders ait" type="text" name="search-orders"
                           value="<?php echo $search; ?>" placeholder="zoeken">
                    <div class="buttonorder ait">
                        <button type="submit"> Zoeken</button>
                    </div>
                </form>
            </div>
            <div class="ordersSearch">

            </div>
        </div>

        <div class="ordersOverview-bottom">
        <?php
        //Controleer als er minimaal 1 order aanwezig is.
        if ($orders->rowCount() > 0) {
            ?>
            <table class="dash-table tableresp">
                <thead>
                <tr>
                    <th class="orderid"> ID</th>
                    <th class="ordername"> Naam</th>
                    <th class="orderproducts"> Aantal producten</th>
                    <th class="orderdate"> Datum</th>
                    <th class="orderstatus"> Status</th>
                    <th class="orderview"> Bekijken</th>
                    <th class="orderend"> Afronden</th>
                    <th class="orderid"> Verwijderen</th>
                </tr>
                </thead>
                <tbody>
                <?php
                //Loop door alle orders heen en ontvang de data.
                while ($row = $orders->fetch()) {
                    $id = $row["id"];
                    $firstname = $row["firstname"];
                    $middlename = $row["middlename"];
                    $lastname = $row["lastname"];
                    $aantal = $row["aantal"];
                    $datum = $row["datum"];
                    $status = $row["status"];
                    echo "<tr>
                    <td class='orderid'> $id </td>
                    <td class='ordername'> $firstname $middlename $lastname </td>
                    <td class='orderproducts'> $aantal </td>
                    <td class='orderdate'> $datum </td>
                    <td class='orderstatus'> $status </td>
                    <td class='orderview'> <i class=\"fa fa-eye\" aria-hidden=\"true\"></i> <a href=\"/dashboard/orders/order?id=$id\">Bekijken</a></td>";
                    if ($status == "afgerond") {
                        echo "<td class='orderend'> Afgerond </td>";
                    } else {
                        echo "<td class='orderdelete'> <i class=\"fa fa-check-square-o\" aria-hidden=\"true\"></i> <a href=\"/dashboard/orders/status?id=$id\" onclick=\"return confirm('Weet je zeker dat je de status wilt veranderen?');\">Afronden</a></td>";
                    }
                    echo "<td> <i class=\"fa fa-trash-o\" aria-hidden=\"true\"></i><a href=\"/dashboard/orders/delete?id=$id\" onclick=\"return confirm('Weet je zeker dat je de bestelling wilt verwijderen?');\"> Verwijderen</a></td>
                  </tr>";
                }
                ?>
                </tbody>
            </table>
            </div>
            <?php
            //Controleer als er minimaal 1 order is.
            if ($orders->rowCount() > 0) {
                ?>
                <div class="flex-pagination">
                    <?php
                    //Kijk als er een GET parameter voor de pagina is meegegeven aan de URL.
                    if ($_GET['pagina']) {
                        $current = $_GET['pagina'];
                        //Kijk als de huidige pagina niet pagina nummer 1 is.
                        if ($current != 1) {
                            $paginationsearch = '&search-orders=' . $search;
                            echo '<a href="/dashboard/orders/?pagina=1' . $paginationsearch . $orderparam . $statusparam . '"> << </a>';
                            echo '<a href="?pagina=' . ($current - 1) . $paginationsearch . $orderparam . $statusparam . '"> < </a>';
                        }
                    } else {
                        $current = 1;
                    }

                    //Loop door alle pagina's.
                    for ($i = $current; $i <= $current + 2; $i++) {
                        //Vraag de huidige pagina op en controleer als het pagina 1 is of niet.
                        if ($_GET['pagina'] == $i) {
                            echo '<a href="?pagina=' . $i . $paginationsearch . $orderparam . $statusparam . '" class="current">' . $i . '</a>';
                        } elseif (empty($_GET['pagina']) && $i === 1) {
                            echo '<a href="?pagina=' . $i . $paginationsearch . $orderparam . $statusparam . '" class="current">' . $i . '</a>';
                        } else {
                            //controleer als de huidige pagina niet de laatste pagina is.
                            if ($current != $total_pages) {
                                if ($current != $total_pages - 1) {
                                    echo '<a href="?pagina=' . $i . $paginationsearch . $orderparam . $statusparam . '">' . $i . '</a>';
                                }
                            }
                        }
                    }

                    //Controleer als de huidige pagina niet de laatste pagina is.
                    if ($_GET['pagina'] != $total_pages) {
                        //Controleer als de huidige pagina meer dan 3 pagina's verschil heeft met de laatste pagina.
                        if ($current <= $total_pages - 3) {
                            echo '<a href="#">...</a>';
                            echo '<a href="?pagina=' . $total_pages . $paginationsearch . $orderparam . $statusparam . '">' . $total_pages . '</a>';
                        }
                        //Controleer als de huidige pagina de op een na laatste pagina is.
                        if ($current == $total_pages - 1) {
                            echo '<a href="?pagina=' . $total_pages . $paginationsearch . $orderparam . $statusparam . '">' . $total_pages . '</a>';
                        }
                        //Controleer als de huidige pagina niet de laatste pagina is.
                        if ($current != $total_pages) {
                            echo '<a href="?pagina=' . ($current + 1) . $paginationsearch . $orderparam . $statusparam . '"> > </a>';
                            echo '<a href="?pagina=' . $total_pages . $paginationsearch . $orderparam . $statusparam . '"> >> </a>';
                        }
                    }
                    ?>
                </div>
                <?php
            }
        } else {
            ?>
            <p>Er konden geen orders worden gevonden, er zijn nog geen orders geplaatst of er kon niks worden gevonden
                bij uw zoekopdracht. Klik <a href="/dashboard/orders">hier</a> om terug te gaan.</p>
            <?php
        }
    } else {
        $user->redirect('/dashboard');
    }

    include($_SERVER['DOCUMENT_ROOT'] . '/dashboard/footer.php');
