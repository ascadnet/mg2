<?php
$admin = new admin;
$page = $admin->build_page('programs');
$pagination = $admin->paginate($page);
?>

<ul id="anet_suboptions">
    <li class="first">Options:</li>
    <li><a href="returnnull.php" onclick="return popup('programs-manage','<?php echo PF_SCOPE; ?>');">Add Program</a></li>
</ul>

<?php echo $pagination['list']; ?>
<h1>Viewing Programs</h1>
<table cellspacing="0" cellpadding="0" width="100%" id="listings" class="anet tablesorter">
    <thead class="anet_box">
    <th class="no_sort">ID</th>
    <th>Name</th>
    <th>Degree Type</th>
    <th>Minimum Education</th>
    <th width="80" class="no_sort">Options</th>
    </thead>
    <tbody>
    <?php

    $data = $admin->get_programs($page);
    foreach ($data as $row) {
        echo "<tr id=\"row-" . $row['program_id'] . "\">";
        echo "<td id=\"cell-" . $row['program_id'] . '-program_id' . "\">" . $row['program_id'] . "</td>";
        echo "<td id=\"cell-" . $row['program_id'] . '-program_name' . "\">" . $row['program_name'] . "</td>";
        echo "<td id=\"cell-" . $row['program_id'] . '-degree_type' . "\">" . $row['degree_type'] . "</td>";
        echo "<td id=\"cell-" . $row['program_id'] . '-min_education' . "\">" . $row['min_education'] . "</td>";
        echo "<td><a href=\"return_null.php\" onclick=\"return popup('programs-exclusions','programs','" . $row['program_id'] . "');\"><img src=\"http://www.penn-foster.com/forms/templates/imgs/icons/icon-exclusions.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"State Exclusions\" class=\"anet_icon\" /></a>" . $admin->element->edit($row['program_id'],'popup') . $admin->element->delete($row['program_id']) . "</td>";
        echo "</tr>";
    }

    ?>
    </tbody>
</table>