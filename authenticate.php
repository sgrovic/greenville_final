<?php
require_once 'dbcon.php';

header("Content-Type: application/json");
ini_set('display_errors', 1);
error_reporting(E_ALL);

$input = json_decode(file_get_contents("php://input"), true);
if (!isset($input['username']) || !isset($input['password'])) {
    echo json_encode([
        "success" => false,
        "message" => "Missing username or password"
    ]);
    exit;
}

$username = $input['username'];
$password = $input['password'];

// Call stored procedure
$stmt = $connection->prepare("CALL CheckUserCredentialsFlag(?, ?)");
$stmt->bind_param("ss", $username, $password);
$stmt->execute();
$result = $stmt->get_result();
$userData = $result->fetch_assoc();

if ($userData) {
    // Login successful
    echo json_encode([
        "success" => true,
        "message" => "Login successful",
        "firstname" => $userData['firstname'],
        "lastname" => $userData['lastname'],
        "status" => $userData['status']
    ]);
} else {
    // Login failed
    echo json_encode([
        "success" => false,
        "message" => "Invalid username or password"
    ]);
}

$stmt->close();

$connection->close();
