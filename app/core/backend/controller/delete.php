<?php

namespace anet\core\backend\controller;

/**
 * Delete Items
 *
 * @author      Ascad Networks
 * @link        http://www.ascadnetworks.com/
 * @version     v1.0
 * @project     Penn Foster Forms
 */

require '../../../conf/load.php';

$session = new Session();
if ($session->error == '1') {
    $error = new Language('E008');
    echo json(array('error' => '1', 'error_details' => $session->error_code));
    exit;
}

if (empty($_POST['scope'])) {
    $error = new Language('E010');
    echo json(array('error' => '1', 'error_details' => $error->data->));
    exit;
} else {
    $scope = new $_POST['scope']('','');
    if (! method_exists($scope,'delete')) {
        $error = get_language('E011');
        echo json(array('error' => '1', 'error_details' => $error));
        exit;
    } else {
        $failed = '';
        $worked = '';
        foreach ($_POST['id'] as $id => $yes_no) {
            $del = $scope->delete($id);
            if ($del == 1) {
                $worked[] = $id;
            } else {
                $failed[] = $id;
            }
        }
        echo json(array('error' => '0', 'worked' => $worked, 'failed' => $failed));
        exit;
    }
}