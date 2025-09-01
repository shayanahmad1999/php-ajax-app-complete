<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once '../src/User.php';

$user = new User();
$request_method = $_SERVER["REQUEST_METHOD"];

// Get the action from URL parameter
$action = isset($_GET['action']) ? $_GET['action'] : '';

switch ($request_method) {
    case 'GET':
        if ($action == 'search') {
            searchUsers();
        } elseif ($action == 'get' && isset($_GET['id'])) {
            getUser($_GET['id']);
        } else {
            getAllUsers();
        }
        break;

    case 'POST':
        if ($action == 'create') {
            createUser();
        }
        break;

    case 'PUT':
        if ($action == 'update') {
            updateUser();
        }
        break;

    case 'DELETE':
        if ($action == 'delete' && isset($_GET['id'])) {
            deleteUser($_GET['id']);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(array("message" => "Method not allowed"));
        break;
}

function getAllUsers()
{
    global $user;

    $stmt = $user->readAll();
    $num = $stmt->rowCount();

    if ($num > 0) {
        $users_arr = array();
        $users_arr["records"] = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);

            $user_item = array(
                "id" => $id,
                "name" => $name,
                "email" => $email,
                "phone" => $phone,
                "created_at" => $created_at
            );

            array_push($users_arr["records"], $user_item);
        }

        http_response_code(200);
        echo json_encode($users_arr);
    } else {
        http_response_code(404);
        echo json_encode(array("message" => "No users found."));
    }
}

function searchUsers()
{
    global $user;

    $search_term = isset($_GET['q']) ? $_GET['q'] : '';

    if (empty($search_term)) {
        getAllUsers();
        return;
    }

    $stmt = $user->search($search_term);
    $num = $stmt->rowCount();

    if ($num > 0) {
        $users_arr = array();
        $users_arr["records"] = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);

            $user_item = array(
                "id" => $id,
                "name" => $name,
                "email" => $email,
                "phone" => $phone,
                "created_at" => $created_at
            );

            array_push($users_arr["records"], $user_item);
        }

        http_response_code(200);
        echo json_encode($users_arr);
    } else {
        http_response_code(404);
        echo json_encode(array("message" => "No users found matching your search."));
    }
}

function getUser($id)
{
    global $user;

    $user->id = $id;

    if ($user->readOne()) {
        $user_arr = array(
            "id" => $user->id,
            "name" => $user->name,
            "email" => $user->email,
            "phone" => $user->phone,
            "created_at" => $user->created_at
        );

        http_response_code(200);
        echo json_encode($user_arr);
    } else {
        http_response_code(404);
        echo json_encode(array("message" => "User not found."));
    }
}

function createUser()
{
    global $user;

    $data = json_decode(file_get_contents("php://input"));

    if (!empty($data->name) && !empty($data->email)) {
        $user->name = $data->name;
        $user->email = $data->email;
        $user->phone = isset($data->phone) ? $data->phone : '';

        // Check if email already exists
        if ($user->emailExists()) {
            http_response_code(400);
            echo json_encode(array("message" => "Email already exists."));
            return;
        }

        if ($user->create()) {
            http_response_code(201);
            echo json_encode(array("message" => "User was created successfully."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Unable to create user."));
        }
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "Unable to create user. Data is incomplete."));
    }
}

function updateUser()
{
    global $user;

    $data = json_decode(file_get_contents("php://input"));

    if (!empty($data->id) && !empty($data->name) && !empty($data->email)) {
        $user->id = $data->id;
        $user->name = $data->name;
        $user->email = $data->email;
        $user->phone = isset($data->phone) ? $data->phone : '';

        if ($user->update()) {
            http_response_code(200);
            echo json_encode(array("message" => "User was updated successfully."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Unable to update user."));
        }
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "Unable to update user. Data is incomplete."));
    }
}

function deleteUser($id)
{
    global $user;

    $user->id = $id;

    if ($user->delete()) {
        http_response_code(200);
        echo json_encode(array("message" => "User was deleted successfully."));
    } else {
        http_response_code(503);
        echo json_encode(array("message" => "Unable to delete user."));
    }
}
