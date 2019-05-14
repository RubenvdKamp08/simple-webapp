<?php
    include_once($_SERVER['DOCUMENT_ROOT'] . '/dashboard/header.php');
    $user = new User($conn);

    //Controleer als de gebruiker de rol Eigenaar, administrator of medewerker heeft.
    if($user->has_role('Eigenaar') || $user->has_role('Administrator') || $user->has_role('Medewerker')) {
        //Haal alle subcategorieeen op.
        $productsubcategory = $conn->prepare("SELECT P.ID id, P.name name, P.checked checked, C.name category FROM productsubcategory P JOIN productcategory C ON P.categoryID = C.ID");
        ?>
        <a href="/dashboard/product_category" class="back-btn"><i class="fa fa-arrow-left" aria-hidden="true"></i>&nbsp;
            Terug</a>
        <div class="content">
            <table class="dash-table tableresp">
                <thead>
                <tr>
                    <th> Subcategorienaam</th>
                    <th class="subcategory"> Categorie</th>
                    <th> Bewerken</th>
                    <th> Verwijderen</th>
                </tr>
                </thead>
                <?php
                $productsubcategory->execute();
                echo "<tbody>";
                //Loop door alle subcategorieen heen.
                while ($row = $productsubcategory->fetch()) {
                    $id = $row["id"];
                    $name = $row["name"];
                    $active = $row["checked"];
                    $category = $row["category"];
                    if ($active == 1) {
                        echo "<tr> 
                    <td> $name </td> 
                    <td class='subcategory'> $category </td>
                    <td><i class=\"fa fa-pencil-square-o\" aria-hidden=\"true\"></i> <a href=\"update?id=$id\">Bewerk</a></td>
                    <td><i class=\"fa fa-trash-o\" aria-hidden=\"true\"></i> <a href=\"delete.php/?id=$id\" onclick=\"return confirm('Weet je zeker dat je het wilt verwijderen?');\">Verwijder</a></td>                
                  </tr>";
                    } else {
                        echo "<tr>
                    <td> $name </td>
                    <td> $category </td>
                    <td> non-actief </td>
                    <td><i class=\"fa fa-pencil-square-o\" aria-hidden=\"true\"></i> <a href=\"update?id=$id\">Bewerk</a></td>
                    <td><i class=\"fa fa-trash-o\" aria-hidden=\"true\"></i> <a href=\"delete.php/?id=$id\" onclick=\"return confirm('Weet je zeker dat je het wilt verwijderen?');\">Verwijder</a></td>
                  </tr>";
                    }
                }
                echo "</tbody>";
                ?>
            </table>
        </div>
        <a href="/dashboard/product_subcategory/create" class="create-btn">Subcategorie toevoegen</a>

        <?php
    } else {
        $user->redirect('/dashboard');
    }

    include($_SERVER['DOCUMENT_ROOT'] . '/dashboard/footer.php');
?>