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

    // Scope is always required.
    if (! empty($_POST['scope'])) {

        // Permissions
        $perms = new Permissions('write',$_POST['scope'],$_POST['id'],$session->permissions);
        if ($perms->permission != '1') {
            $error = new language('L007');
            echo "0+++" . $error;
            exit;
        }

        define('PF_SCOPE',$_POST['scope']);

        // Updating
        if (! empty($_POST['id']) && $_POST['id'] != 'undefined') {
            $action = 'edit';
            $id = $_POST['id'];
        }
        // Inserting
        else {
            $action = 'add';
            $option = $app->option('programs_id_format');
            if ($option) {
                $id = generate_id($option);
            } else {
                $id = generate_id('random','20');
            }
        }

        if ($object = new $_POST['scope']($id,$action,$_POST)) {
            if ($object->error == '1') {
                if (! empty($object->error_details)) {
                    $err = $object->error_details;
                } else {
                    $language = new language($object->error_details);
                }
                echo "0+++" . $err;
                exit;
            } else {
                echo "1+++" . $object->ajax_reply;
                exit;
            }
        } else {
            $language = new language('S001');
            echo "0+++" . $language;
            exit;
        }

    } else {
        $language = new language('S002');
        echo "0+++" . $language;
        exit;
    }

}