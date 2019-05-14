<?php
    include_once($_SERVER['DOCUMENT_ROOT'] . '/dashboard/header.php');
    include_once($_SERVER['DOCUMENT_ROOT'] . '/functions/slide.php');
    $user = new User($conn);
    $slides = new Slide($conn);

    //Controleer als de gebruiker de rol Eigenaar of administrator heeft.
    if($user->has_role('Eigenaar') || $user->has_role('Administrator')) {
        $message = '';
        //Controleer als er een GET parameter aan de URL wordt toegevoegd van ID
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            //Haal de sldie op.
            $slide = $slides->getSlide($id);
            //Controleer als er een POST request naar de server wordt gestuurd.
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $title = $_POST['title'];
                $image = uniqid() . "-" . $_FILES['image']['name'];
                $imagefile = $_FILES['image'];

                //Controleer als de slider succesvol gewijzigd is of niet.
                if ($slides->editSlide($title, $image, $imagefile, $id) === true) {
                    $message = 'Slider is succesvol gewijzigd!';
                } else {
                    $message = 'Slider kon niet worden gewijzigd, controleer als de geuploade afbeelding wel een jpg, png of jpeg bestand is!';
                }
                //Get product again to update values
                $slide = $slides->getSlide($id);
            }

            //Controleer als er minimaal 1 slide aanwezig is.
            if ($slide->rowCount() > 0) {
                foreach ($slide as $item) {
                    ?>
                    <a href="/dashboard/slider" class="back-btn"><i class="fa fa-arrow-left" aria-hidden="true"></i>&nbsp;
                        Terug</a>
                    <div class="content">
                        <div class="dashboard-left">
                            <form method="post" enctype="multipart/form-data">
                                <label>Slide tekst</label>
                                <input type="text" name="title" value="<?php echo $item["title"]; ?>"
                                       placeholder="Slider tekst" required>
                                <label>Product afbeelding</label>
                                <input type="file" name="image" id="image" value="<?php echo $item["image"]; ?>"
                                       onchange="readURL(this);">
                                <input type="submit" value="Wijzigen">
                            </form>
                            <?php echo $message; ?>
                        </div>
                        <div class="dashboard-right">
                            <img id="product-image" src="/assets/images/slider/<?php echo $item['image']; ?>">
                        </div>
                    </div>
                    <?php
                }
            } else {
                $user->redirect('/dashboard/slider');
            }
        }
    } else {
        $user->redirect('/dashboard');
    }

    include($_SERVER['DOCUMENT_ROOT'] . '/dashboard/footer.php');