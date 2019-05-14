<?php
    include_once($_SERVER['DOCUMENT_ROOT'] . '/dashboard/header.php');
    $user = new User($conn);

    //Controleer als de gebruiker de rol Eigenaar of administrator heeft.
    if($user->has_role('Eigenaar') || $user->has_role('Administrator')) {
        $insert = false;
        //Controleer als er een POST request naar de server is gestuurd.
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $insert = true;
            //Voeg query uit om nieuwscategorie toe te voegen aan de database.
            $insert_newscategory = $conn->prepare("INSERT INTO newscategory(name, checked) VALUES(:category_name, :checked)");
            $insert_newscategory->execute(array(
                ':category_name' => ucfirst($_POST["category_name"]),
                ':checked' => $_POST["active"]));
        }
        ?>

        <a href="/dashboard/news_category" class="back-btn"><i class="fa fa-arrow-left" aria-hidden="true"></i>&nbsp; Terug</a>
        <div class="content">
            <div class="dashboard-left">
                <form method="post">
                    <label>Categorie</label>
                    <input type="text" name="category_name" placeholder="Categorienaam" required>
                    <span> Actief of non-actief: </span> <br>
                    <input type="radio" name="active" class="radio-btn" value="true" checked="checked" required> Actief
                    <input type="radio" name="active" class="radio-btn" value="false"> Non-actief <br>
                    <input type="submit" name="submit" value="toevoegen">
                </form>
            </div>
        </div>

        <?php
        if ($insert) {
            echo "Nieuwscategorie is toegevoegd!";
        }
    } else {
        $user->redirect('/dashboard');
    }

    include_once($_SERVER['DOCUMENT_ROOT'] . '/dashboard/footer.php');
?>
