<?php 
$data = array();

if(isset($_GET['action']))
{
    include('includes/functions.php');
    $action = $_GET['action'];
}

header("Content-Type: application/json");
echo json_encode($data, true);
exit();