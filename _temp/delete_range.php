<?php

require "../../system/loader.php";

$session = new session();
if ($session->session_error == '1') {
    header('Location: ' . PF_URL . '/' . PF_ADMIN . '/login?error=' . $session->session_error_code);
    exit;
}

if (! empty($_POST['live']) && ! empty($_POST['confirm'])) {

    $query = array(
        'query' => "
            SELECT `id`
            FROM `pf_submission`
            WHERE
                `date`>='" . $_POST['date_low'] . " 00:00:00' AND
                `date`<='" . $_POST['date_high'] . " 23:55:55'
        ",
    );
    $entries = new db('pf_submission','get_rows',$query);
    $deleted = 0;
    foreach ($entries->result as $item) {
        $deleted++;
        $q1 = array(
            'where' => array(
                'submission_id' => $item['id'],
            ),
        );
        $del1 = new db('submission_data','delete',$q1);
        $q2 = array(
            'where' => array(
                'id' => $item['id'],
            ),
        );
        $del2 = new db('submission','delete',$q2);
    }

    echo <<<EOF
<div style="border:1px solid #ccc;margin: 48px auto;font-family:arial;width:500px;font-size: 0.8em;padding: 48px;">
<h1>Deletion Complete</h1>
<p>Your deletion is complete. {$deleted} submissions were deleted.</p>
<div style="text-align:right;">
    <a href="/forms/admin/system/">Return to administrative control panel</a>
</div>
</form>
EOF;
    exit;


} else {

    if (empty($_POST['date_low']) || empty($_POST['date_high'])) {
        echo "Please go back and input a low and high date.";
        exit;
    }
    else if ($_POST['date_low'] > $_POST['date_high']) {
        echo "The 'low' date cannot be greater than the 'high' date.";
        exit;
    }

    $query = array(
        'query' => "
        SELECT
            COUNT(*)
        FROM
            `pf_submission`
        WHERE
            `date`>='" . $_POST['date_low'] . " 00:00:00' AND
            `date`<='" . $_POST['date_high'] . " 23:55:55'
    ",
    );
    $count = new db('pf_submission','count',$query);
    
    echo <<<EOF

<div style="border:1px solid #ccc;margin: 48px auto;font-family:arial;width:500px;font-size: 0.8em;padding: 48px;">
<h1>Previewing Mass Delete</h1>
<p>Date Range: On or after {$_POST['date_low']} and on or before {$_POST['date_high']}</p>
<p>This entry will  permanently delete <b>{$count->result}</b> entries.</p>

<h1>Ready to Delete?</h1>
<form action="delete_range.php" method="post">
<input type="hidden" name="date_low" value="{$_POST['date_low']}" />
<input type="hidden" name="date_high" value="{$_POST['date_high']}" />
<input type="hidden" name="live" value="1" />

<div style="text-align:right;">
    <input type="checkbox" name="confirm" value="1" /> I understand that this is irreversible. Proceed with the deletion.<br /><input type="submit" value="DELETE RECORDS - THIS CANNOT BE REVERSED!" />
</div>

</form>

EOF;
    exit;

}