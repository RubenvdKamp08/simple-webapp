<?php
    include_once($_SERVER['DOCUMENT_ROOT'] . '/dashboard/header.php');
    $user = new User($conn);

    //Controleer als de gebruiker de rol eigenaar, administrator of medewerker heeft en anders terugsturen naar dashboard pagina.
    if($user->has_role('Eigenaar') || $user->has_role('Administrator') || $user->has_role('Medewerker')) {
        //Vraag de maandelijkse producten op uit de database.
        $montlyproduct = $conn->prepare("SELECT * FROM monthly_product");
        ?>

        <table class="dash-table tableresp">
            <thead>
            <tr>
                <th> Product van de maand</th>
                <th> Naam</th>
                <th> Bekijken</th>
                <th> Wijzigen</th>
            </tr>
            </thead>


            <?php
            $montlyproduct->execute();
            echo "<tbody>";
            //Loop door alle maandelijkse producten heen en vraag de bijhorende data op.
            while ($row = $montlyproduct->fetch()) {
                $id = $row["ID"];
                $type = $row["type"];
                $title = $row["title"];
                echo "<tr> 
                <td> $type </td> 
                <td> $title </td> 
                <td><i class=\"fa fa-eye\" aria-hidden=\"true\"></i> <a href=\"product?id=$id\">Bekijken</a></td>
                <td><i class=\"fa fa-pencil-square-o\" aria-hidden=\"true\"></i> <a href=\"update?id=$id\">Bewerk</a></td>                
              </tr>";
            }
            echo "<tbody>";
            ?>

        </table>

        <?php
    } else {
        $user->redirect('/dashboard');
    }

    include($_SERVER['DOCUMENT_ROOT'] . '/dashboard/footer.php');
?>