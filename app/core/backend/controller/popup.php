<?php

namespace anet\core\backend\controller;

/**
 * Gets a popup's content.
 */

require '../../../conf/load.php';

$session = new Session();
if ($session->error == '1') {

    $error = get_language('E008');
    echo json(array('error' => '1', 'error_details' => $error));
    exit;

} else {

    // Check if there is content to return.
    // So if we are loading a popup and want
    // to pre-populate a form, this will
    // automatically get the data from the
    // model and return it with the request.
    $js_data = '';
    if (! empty($_POST['id']) && $_POST['id'] != 'undefined') {
        $get = new \anet\core\backend\controller\get($_POST['scope'], $_POST['id']);
        if (! empty($get->data)) {
            $data = $get->data;
        } else {
            $data = '';
        }
    }

    // Get popup content
    ob_start();
    include(\anet\conf\BACKEND . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . 'popup' . DIRECTORY_SEPARATOR . $_POST['p'] . '.php');
    $content = ob_get_contents();
    ob_end_clean();

    // Return content
    echo json(array('content' => $content, js_data => $data));
    exit;

}