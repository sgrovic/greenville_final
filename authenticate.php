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
if (!$stmt = $connection->prepare("CALL CheckUserCredentialsFlag(?, ?, @p_isValid)")) {
    echo json_encode([
        "success" => false,
        "message" => "Prepare failed: " . $connection->error
    ]);
    exit;
}

$stmt->bind_param("ss", $username, $password);
if (!$stmt->execute()) {
    echo json_encode([
        "success" => false,
        "message" => "Execution failed: " . $stmt->error
    ]);
    $stmt->close();
    $connection->close();
    exit;
}
$stmt->close();

// Fetch OUT parameter
$result = $connection->query("SELECT @p_isValid AS isValid");
$row = $result->fetch_assoc();
$isValid = (int)$row['isValid'];

if ($isValid) {
    // Fetch user details
    $query = $connection->prepare("SELECT firstname, lastname, status FROM users WHERE username = ?");
    $query->bind_param("s", $username);
    $query->execute();
    $userResult = $query->get_result();
    $userData = $userResult->fetch_assoc();

    echo json_encode([
        "success" => true,
        "message" => "Login successful",
        "firstname" => $userData['firstname'],
        "lastname" => $userData['lastname'],
        "status" => $userData['status'] // e.g. Admin / User
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Invalid username or password"
    ]);
}

$connection->close();
