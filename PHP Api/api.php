<?php
header('Content-type:application/json');

$response = array(
    'error' => true,
    "message" => "Error Occure"
);

$api_key = $_SERVER["HTTP_API_KEY"] ?? '';
$valid_api_key = 'asdfghjkl1234567890';

if ($api_key !== $valid_api_key) {
    header('HTTP/1.1 401 Unauthorized');
    $response['message'] = 'Invalid Api';
    echo json_encode($response);
    die();
}


require_once('dp.php');
$action = $_GET['action'];

// function sanitize($data){
//     global $mysqi;
//     return $mysqi->real_escape_string($data);
// }

if ($action == "create-user") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $query =
        "Insert into users (name, email, password) values ('$name','$email','$password')";


    $result = $mysqli->query($query);

    if ($result) {

        $response['error'] = false;
        $response['message'] = 'User Added Sussessfullty';
    } else {
        $response['error'] = true;
        $response['message'] = 'User Can not Added Sussessfullty';
    }

    echo json_encode($response);
    die();
} else if ($action == "login-user") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $query = "Select * from users where email = '$email' and password = '$password'";
    $result = $mysqli->query($query);
    if ($result->num_rows > 0) {
        $userRow = $result->fetch_assoc();
        header('HTTP/1.1 400 Not Found');
        $response['error']  = false;
        $response['message']  = 'User Logged in Sussessfully';
        $response['data'] = $userRow;
    } else {
        $response['error']  = true;
        $response['message']  = 'Incorrect email or Password';
    }

    echo json_encode($response);
    die();
} elseif ($action == 'get-user-details') {
    $id = $_GET['id'];

    $query = "Select * from users where id = $id";
    $result = $mysqli->query($query);
    if ($result->num_rows > 0) {
        $userRow = $result->fetch_assoc();
        header('HTTP/1.1 400 Not Found');
        $response['error']  = false;
        $response['message']  = 'User found';
        $response['data'] = $userRow;
    } else {
        $response['error']  = true;
        $response['message']  = 'User not found';
    }
    echo json_encode($response);
} elseif ($action == 'get-products') {
    $query = 'Select * from products';
    $result = $mysqli->query($query);
    if ($result->num_rows > 0) {
        $response['error']  = false;
        $response['message']  = $result->num_rows . ' product found';

        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        $response['data'] = $products;
    } else {
        header('HTTP/1.1 400 Not Found');
        $response['error']  = true;
        $response['message']  = 'product not found';
    }
    echo json_encode($response);
}

exit();
