<?php
    require_once 'dbcon.php';

    header("Content-Type: application/json");
    ini_set('display_errors', 1);  // Turn on for debugging, off in production
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
    if (!$result) {
        echo json_encode([
            "success" => false,
            "message" => "Failed to fetch output: " . $conn->error
        ]);
        $connection->close();
        exit;
    }

    $row = $result->fetch_assoc();
    $isValid = (int)$row['isValid'];

    // Detect port automatically
    //$port = $_SERVER['SERVER_PORT'];
    //$serverUrl = "http://localhost" . ($port != "80" ? ":$port" : "");

    if ($isValid) {
        echo json_encode([
            "success" => true,
            "message" => "Login successful",
            //"serverUrl" => $serverUrl
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Invalid username or password",
            //"serverUrl" => $serverUrl
        ]);
    }

    $connection->close();
?>
