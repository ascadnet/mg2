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

require "../../system/app.php";
$app = new App();

$session = new session();
if ($session->session_error == '1') {

    $error = new language('E001');
    echo "0+++" . $error;
    exit;

} else {

    // Permissions
    $perms = new Permissions('read',$_POST['scope'],$_POST['id'],$session->permissions);
    if ($perms->permission != '1') {
        $error = new language('L007');
        echo "0+++" . $error;
        exit;
    }

    // Check if there is content to return.
    $js_data = '';
    if (! empty($_POST['id']) && $_POST['id'] != 'undefined') {
        $get = new get($_POST['scope'],$_POST['id'],$_POST);
        if (! empty($get->data)) {
            $js_data = json_encode($get->data);
            $data = $get->data;
        } else {
            $js_data = '';
            $data = '';
        }
    }

    // Get popup
    ob_start();
    include(PF_PATH . '/' . PF_ADMIN . '/pages/popup/' . $_POST['p'] . '.php');
    $content = ob_get_contents();
    ob_end_clean();

    $content = '<script type="text/javascript" src="/app/admin/assets/js/popup_forms.js"></script>' . $content;

    // Return content
    echo "1+++" . $content . "+++" . $js_data;
    exit;

}