<?php
    include_once($_SERVER['DOCUMENT_ROOT'] . '/dashboard/header.php');
    include_once($_SERVER['DOCUMENT_ROOT'] . '/functions/slide.php');
    $user = new User($conn);
    $slide = new Slide($conn);

    //Controleer als de gebruiker de rol Eigenaar of administrator heeft.
    if($user->has_role('Eigenaar') || $user->has_role('Administrator')) {
        //Haal alle slides op.
        $slides = $slide->getSlides();
        ?>
        <div class="content">
            <?php
            //Controleer als er minimaal 1 slide aanwezig is.
            if ($slides->rowCount() > 0) {
                ?>
                <table class="dash-table tableresp">
                    <thead>
                    <tr>
                        <th class="slideid">Slide</th>
                        <th>Slidertekst</th>
                        <th>Bewerk</th>
                        <th>Verwijder</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $i = 0;
                    //Loop door alle slides en toen slide nummer
                    foreach ($slides as $item) {
                        $i++;
                        ?>
                        <tr>
                            <td class="slideid"><?php echo $i; ?></td>
                            <td><?php echo $item['title']; ?></td>
                            <td><i class="fa fa-pencil-square-o" aria-hidden="true"></i> <a
                                        href="/dashboard/slider/edit?id=<?php echo $item['ID']; ?>">Bewerk</a></td>
                            <td><i class="fa fa-trash-o" aria-hidden="true"></i> <a
                                        onclick="return confirm('Weet u zeker dat u het product wil verwijderen?')"
                                        href="/dashboard/slider/delete?id=<?php echo $item['ID']; ?>">Verwijder</a></td>
                        </tr>
                        <?php
                    }
                    ?>
                    </tbody>
                </table>
                <?php
            } else {
                echo 'Geen slides gevonden<br>';
            }
            ?>
        </div>
        <?php
        //Controleer als er meer dan 5 slides zijn, geef anders een fout melding.
        if ($slides->rowCount() > 4) {
            echo '<p>Er kunnen niet meer dan 5 slides worden toegevoegd, verwijder een slide voordat je een nieuwe kan toevoegen.';
        } else {
            echo '<a href="/dashboard/slider/create" class="create-btn">Slide toevoegen</a>';
        }
    } else {
        $user->redirect('/dashboard');
    }

    include($_SERVER['DOCUMENT_ROOT'] . '/dashboard/footer.php');

