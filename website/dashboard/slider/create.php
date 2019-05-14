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
        <a href="/dashboard/slider" class="back-btn"><i class="fa fa-arrow-left" aria-hidden="true"></i>&nbsp; Terug</a>
        <div class="content">
            <div class="dashboard-left">
                <?php
                if ($slides->rowCount() < 5) {
                    ?>
                    <form method="post" enctype="multipart/form-data">
                        <label>Slide tekst</label>
                        <input type="text" name="title" placeholder="Slider tekst" required>
                        <label>Slide afbeelding</label>
                        <input type="file" name="image" id="image" onchange="readURL(this);" required>
                        <input type="submit" value="Toevoegen">
                    </form>
                    <?php
                } else {
                    $user->redirect('dashboard/slider/create');
                    echo '<p>Er kunnen niet meer dan 5 slides worden toegevoegd</p>';
                }
                ?>
            </div>
            <div class="dashboard-right">
                <img id="product-image">
            </div>
        </div>
        <?php
        //Controleer als er meer dan 5 slides zijn.
        if ($slides->rowCount() < 5) {
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $title = $_POST['title'];
                $image = uniqid() . "-" . $_FILES['image']['name'];
                $imagefile = $_FILES['image'];

                $slide->createSlide($title, $image, $imagefile);
            }
        }
    } else {
        $user->redirect('/dashboard');
    }

    include($_SERVER['DOCUMENT_ROOT'] . '/dashboard/footer.php');