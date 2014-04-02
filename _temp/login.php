<?php

/**
 *
 *
 * @project     SREDengine
 * @link        http://www.sredengine.com/
 *
 * This file is built on the "Ascad Framework". All
 * elements of the Ascad Framework remain copyrighted
 * to their respective owner.
 *
 * @author      Ascad Networks
 * @link        http://www.ascadnetworks.com/
 * @license     Commercial
 * @link        http://www.ascadnetworks.com/Framework/License
 * @date        4/22/13
 * @version     v1.0
 * @project     Ascad Framework
 */

require "../../system/loader.php";

$session = new session;
$user = $session->get_user('',$_POST['username']);

if ($user) {

    // Check password
    $password = new password($_POST['password'],$user->result['salt']);
    // Valid password
    if ($user->result['password'] == $password->return) {
        $session = new session();
        $session->start_session($user->result['id']);
        header('Location: ' . PF_URL . '/' . PF_ADMIN . '/');
        exit;
    }

    // Incorrect password.
    else {
        header('Location: ' . PF_URL . '/' . PF_ADMIN . '/?error=E005');
        exit;
    }

} else {
    header('Location: ' . PF_URL . '/' . PF_ADMIN . '/?error=E004');
    exit;
}