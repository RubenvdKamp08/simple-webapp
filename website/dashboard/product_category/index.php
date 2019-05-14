<?php
    include_once($_SERVER['DOCUMENT_ROOT'] . '/dashboard/header.php');
    $user = new User($conn);

    //Controleer als de gebruiker de rol Eigenaar, administrator of medewerker heeft.
    if($user->has_role('Eigenaar') || $user->has_role('Administrator') || $user->has_role('Medewerker')) {
        $productcategory = $conn->prepare("SELECT * FROM productcategory");
        ?>
        <a href="/dashboard/products" class="back-btn"><i class="fa fa-arrow-left" aria-hidden="true"></i>&nbsp;
            Terug</a>
        <div class="content">
            <table class="dash-table">
                <thead>
                <tr>
                    <th> Categorienaam</th>
                    <th class="productactive"> Actief</th>
                    <th> Bewerken</th>
                    <th> Verwijderen</th>
                </tr>
                </thead>
                <?php
                $productcategory->execute();
                echo "<tbody>";
                //Loop door alle productcategorieeen heen.
                while ($row = $productcategory->fetch()) {
                    $id = $row["ID"];
                    $name = $row["name"];
                    $active = $row["checked"];

                    if ($active == "true") {
                        echo "<tr> 
                    <td> $name </td> 
                    <td class=\"productactive\"> Actief </td> 
                    <td><i class=\"fa fa-pencil-square-o\" aria-hidden=\"true\"></i> <a href=\"update?id=$id\">Bewerk</a></td>
                    <td><i class=\"fa fa-trash-o\" aria-hidden=\"true\"></i> <a href=\"delete/?id=$id\" onclick=\"return confirm('Weet je zeker dat je het wilt verwijderen?');\">Verwijder</a></td>                
                  </tr>";
                    } else {
                        echo "<tr>
                    <td> $name </td>
                    <td> Non-actief </td>
                    <td><i class=\"fa fa-pencil-square-o\" aria-hidden=\"true\"></i> <a href=\"update?id=$id\">Bewerk</a></td>
                    <td><i class=\"fa fa-trash-o\" aria-hidden=\"true\"></i> <a href=\"delete/?id=$id\" onclick=\"return confirm('Weet je zeker dat je het wilt verwijderen?');\">Verwijder</a></td>
                  </tr>";
                    }
                }
                echo "</tbody>";
                ?>
            </table>
        </div>
        <a href="/dashboard/product_category/create" class="create-btn">Categorie toevoegen</a>
        <a href="/dashboard/product_subcategory" class="create-btn">Subcategorieën</a>

        <?php
    } else {
        $user->redirect('/dashboard');
    }

    include($_SERVER['DOCUMENT_ROOT'] . '/dashboard/footer.php');
?>