<?php  

$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "api_tutorial";


$mysqli = new mysqli($db_host,$db_user,$db_pass,$db_name);

if ($mysqli->connect_errno) {
$response = array(
    "error"=>true,
    "message"=>"Invalid Database Connection Detail!"
);
echo json_encode($response);
die();
}
?>