<?php
    include_once($_SERVER['DOCUMENT_ROOT'] . '/dashboard/header.php');
    include_once($_SERVER['DOCUMENT_ROOT'] . '/functions/products.php');
    $user = new User($conn);

    //Controleer als de gebruiker de rol eigenaar of administrator heeft anders terugsturen naar dashboard pagina.
    if($user->has_role('Eigenaar') || $user->has_role('Administrator')) {
        //Controleer als er een ID in de url voorkomt.
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            //Vraag het contactformulier op met het aangegeven ID.
            $contact = $conn->prepare('SELECT * FROM contact WHERE id = ' . $id);
            $contact->execute();
            //Loop door de data heen van het opgevraagde contact formulier.
            foreach ($contact as $formulier) {
                ?>
                <div class="formulierinfo">
                    <div class="formulierheader">
                        <div class="formulierback">
                            <a href="/dashboard/contact" class="back-btn"><i class="fa fa-arrow-left"
                                                                             aria-hidden="true"></i>&nbsp; Terug</a></div>
                        <div class="formuliertitle">
                            <h1>Contactformulier</h1>
                        </div>
                        <div class="formulierdelete">
                            <a href="/dashboard/contact/delete?id=<?php echo $formulier['ID']; ?>" name="contactdelete"
                               class="back-btn"
                               onclick="return confirm('Weet u zeker dat u het contactformulier wil verwijderen?');"><i
                                        class="fa fa-trash-o" aria-hidden="true"></i>&nbsp; Verwijder</a>
                        </div>
                    </div>
                    <br>
                    <div class="fullform">
                        <div class="leftform">
                            <div class="formblock formbl">
                                <h4>Naam</h4>
                                <a> <?php echo $formulier['firstname'] . " " . $formulier['middlename'] . " " . $formulier['lastname']; ?> </a>
                            </div>
                            <div class="formblock formbl">
                                <h4>Email</h4>
                                <a> <?php echo $formulier['email']; ?> </a>
                            </div>

                            <div class="formblock formbl">
                                <h4>Telefoonnummer</h4>
                                <a> <?php ;
                                    if ($formulier['phonenumber'] == '') {
                                        echo "Niet beschikbaar";
                                    } else {
                                        echo $formulier['phonenumber'];
                                    }
                                    ?> </a>

                            </div>
                        </div>
                        <div class="rightform">
                            <div class="formblock formbl">
                                <h4>Onderwerp</h4>
                                <a> <?php echo $formulier['subject']; ?> </a>
                            </div>

                            <div class="formblock messageblock">
                                <h4>Bericht</h4>
                                <a> <?php echo $formulier['message']; ?> </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
        }
    } else {
        $user->redirect('/dashboard');
    }

    include_once($_SERVER['DOCUMENT_ROOT'] . '/dashboard/footer.php');
?>