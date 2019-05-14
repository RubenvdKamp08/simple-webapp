<?php
    require_once('lib/connection.php');
    require_once('functions/user.php');

    $user = new User($conn);

    if($user->is_loggedin()) {
        $user->logout();
        $user->redirect('/');
    } else {
        $user->redirect('/');
    }