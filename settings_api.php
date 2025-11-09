<?php 
require_once 'dbcon.php'; // include your database connection

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$method = $_GET['action'] ?? ''; // e.g. students_api.php?action=create
$data = json_decode(file_get_contents("php://input"), true);

// --- FUNCTIONS FOR EACH ACTION --- //
function createStudent($connection, $data) {
    $stmt = $connection->prepare("CALL AddUser(?,?,?,?,?,?)");
    $stmt->bind_param("ssssss",
        $data['username'],
        $data['firstname'],
        $data['middlename'],
        $data['lastname'],
        $data['password'],
        $data['status']
    );
    //username,
    //firstname,
    //middlename,
    //lastname,
    //password,
    //status
    //.UserName = TextBoxInputUserName.Text,
    //.FirstName = TextBoxInputFirstName.Text,
    //.MiddleName = TextBoxInputMiddleName.Text,
    //.LastName = TextBoxInputLastName.Text,
    //.Status = ComboBoxAccessLevel.Text,
    //.Password = TextBoxInputPassword.Text
    //$stmt->execute();

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Student inserted successfully."]);
    } else {
        echo json_encode(["success" => false, "message" => "Insert failed: " . $stmt->error]);
    }
    //echo json_encode(["status" => "success", "message" => "Student added (if unique)"]);
}

//.miscellaneous_fee = TextBoxInputMiscellaneousFee.Text,
//.penalty_fee = TextBoxInputPenaltyFee.Text,
//.installment_interest = TextBoxInputInstallmentInterest.Text,
//.school_year = ComboBoxSchoolYear.Text
/*
function updateSettings($connection, $data) {
    $stmt = $connection->prepare("CALL UpdateSettings(?,?,?,?,?,?,?,?,?)");
    $stmt->bind_param("sssssssss",
    $data['miscellaneous_fee'],   
    $data['penalty_fee'],
    $data['installment_interest'],
    $data['doctoratefee_per_unit'],
    $data['masteralfee_per_unit'],
    $data['newregistration_fee'],
    $data['oldregistration_fee'],
    $data['research_fee'],
    $data['other_fee']
    );
    //$stmt->execute();
    //echo json_encode(["status" => "success", "message" => "Student updated"]);
    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Student inserted successfully."]);
    } else {
        echo json_encode(["success" => false, "message" => "Insert failed: " . $stmt->error]);
    }    
}
*/

function updateSettings($connection, $data) {
    $stmt = $connection->prepare("
        CALL UpdateSettings(
            ?, ?, ?, ?, ?, ?, ?, ?, ?, 
            ?, ?, ?, ?, ?, 
            ?, ?, ?, ?, ?, 
            ?, ?, ?, ?, ?, 
            ?, ?, ?, ?, ?, 
            ?, ?, ?, ?
        )
    ");

    $stmt->bind_param(
        "sssssssssssssssssssssssssssssssss",
        $data['miscellaneous_fee'],
        $data['installment_interest'],
        $data['penalty_fee'],
        $data['doctoratefee_per_unit'],
        $data['masteralfee_per_unit'],
        $data['newregistration_fee'],
        $data['oldregistration_fee'],
        $data['research_fee'],
        $data['other_fee'],

        // Newly added fields
        $data['ToRwSO'],
        $data['OToR'],
        $data['CoEU'],
        $data['CPEC'],
        $data['VF'],

        $data['CoGM'],
        $data['CoCAR'],
        $data['CoE'],
        $data['CTCoD'],
        $data['AC'],

        $data['CoG'],
        $data['CoGr'],
        $data['DCoD'],
        $data['CER'],
        $data['Term1Date'],

        $data['Term2Date'],
        $data['Term3Date'],
        $data['TFDP'],
        $data['TODF'],
        $data['DODF'],

        $data['OtherFee'],
        $data['InstallmentDP'],
        $data['CfMoI'],
        $data['OtherFee4']
    );

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Settings updated successfully."]);
    } else {
        echo json_encode(["success" => false, "message" => "Update failed: " . $stmt->error]);
    }
}

/*
function deleteStudent($connection, $data) {
    $stmt = $connection->prepare("CALL DeleteUser(?)");
    $stmt->bind_param("s", $data['UserName']);
    //$stmt->execute();
    //echo json_encode(["success" => "true", "message" => "Student deleted"]);
    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "User deleted successfully."]);
    } else {
        echo json_encode(["success" => false, "message" => "Delete failed: " . $stmt->error]);
    }        
}
*/

function deleteUser($connection, $data) {
    if (!isset($data['Username'])) {
        echo json_encode(["success" => false, "message" => "Missing Username"]);
        return;
    }

    $username = $data['Username'];

    // Call stored procedure
    $stmt = $connection->prepare("CALL DeleteUser(?)");
    if (!$stmt) {
        echo json_encode(["success" => false, "message" => "Prepare failed: " . $connection->error]);
        return;
    }

    $stmt->bind_param("s", $username);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "User deleted"]);
    } else {
        echo json_encode(["success" => false, "message" => "Delete failed: " . $stmt->error]);
    }

    $stmt->close();
}

function getStudent($connection, $data) {
    $stmt = $connection->prepare("CALL GetStudentByNumber(?)");
    $stmt->bind_param("s", $data['StudentNumber']);
    $stmt->execute();
    $result = $stmt->get_result();
    $student = $result->fetch_assoc();
    echo json_encode($student ?: ["status" => "error", "message" => "Student not found"]);
}

function listSettings($connection) {
    $result = $connection->query("CALL GetAllSettings()");
    $students = [];

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $students[] = $row;
        }
        echo json_encode(["success" => true, "data" => $students]);
    } else {
        echo json_encode(["success" => false, "message" => "Query failed"]);
    }
    
}

function searchStudents($connection, $data) {
    //$stmt = $connection->prepare("CALL SearchStudentByLastName(?)");
    $stmt = $connection->prepare("CALL SearchUserByLastName(?)");
    $stmt->bind_param("s", $data['Lastname']);
    $stmt->execute();
    $result = $stmt->get_result();

    $students = [];
    while ($row = $result->fetch_assoc()) {
       $students[] = $row;
    }

    $response = [
        "success" => count($students) > 0,
        "data" => $students
    ];

    echo json_encode($response);

    $stmt->close();
}

function addUser($connection, $data) {
    // normalize missing fields to empty strings
    $fields = ['username','password','firstname','middlename','lastname','status'];
    foreach ($fields as $f) {
        if (!isset($data[$f])) $data[$f] = '';
    }

    // Required fields
    if ($data['username'] === '' || $data['password'] === '' || 
        $data['firstname'] === '' || $data['lastname'] === '' || 
        $data['status'] === '') 
    {
        echo json_encode(["success" => false, "message" => "Missing required fields (username, password, firstname, lastname, status)"]);
        return;
    }

    // Prepare call to stored procedure
    $stmt = $connection->prepare("CALL AddUser(?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        echo json_encode(["success" => false, "message" => "Prepare failed: " . $connection->error]);
        return;
    }

    // bind params: username, password, firstname, middlename, lastname, status
    $stmt->bind_param(
        "ssssss",
        $data['username'],
        $data['lastname'],        
        $data['firstname'],
        $data['middlename'],
        $data['password'],
        $data['status']
    );

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "User added successfully"]);
    } else {
        echo json_encode(["success" => false, "message" => "Insert failed: " . $stmt->error]);
    }

    $stmt->close();
}

// --- MAIN ROUTER --- //
switch ($method) {
    case "create":
        createStudent($connection, $data);
        break;

    case "update":
        updateSettings($connection, $data);
        break;

    case "delete":
        deleteUser($connection, $data);
        break;

    case "get":
        getStudent($connection, $data);
        break;

    case "list":
        listSettings($connection);
        break;
      
    case "search":
        searchStudents($connection, $data);
        break;        

    case "add":
        addUser($connection, $data);
        break;

    default:
        echo json_encode(["status" => "error", "message" => "Invalid action"]);
}
?>