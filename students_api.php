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
    $stmt = $connection->prepare("CALL AddStudent(?,?,?,?,?,?,?,?,?,?,?,?)");
    $stmt->bind_param("ssssssssssss",
    $data['studentnumber'],
    $data['firstname'],
    $data['middlename'],
    $data['lastname'],
    $data['gender'],
    $data['dob'],
    $data['address'],
    $data['guardian'],
    $data['contactnumber'],
    $data['relationship'],
    $data['email'],
    $data['age']
    );
    //$stmt->execute();

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Student inserted successfully."]);
    } else {
        echo json_encode(["success" => false, "message" => "Insert failed: " . $stmt->error]);
    }
    //echo json_encode(["status" => "success", "message" => "Student added (if unique)"]);
    $stmt->close();
}

function updateStudent($connection, $data) {
    $stmt = $connection->prepare("CALL UpdateStudent(?,?,?,?,?,?,?,?,?,?,?,?)");
    $stmt->bind_param("ssssssssssss",
    $data['studentnumber'],
    $data['firstname'],
    $data['middlename'],
    $data['lastname'],
    $data['gender'],
    $data['dob'],
    $data['address'],
    $data['guardian'],
    $data['contactnumber'],
    $data['relationship'],
    $data['email'],
    $data['age']
    );
    //$stmt->execute();
    //echo json_encode(["status" => "success", "message" => "Student updated"]);
    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Student inserted successfully."]);
    } else {
        echo json_encode(["success" => false, "message" => "Insert failed: " . $stmt->error]);
    }    
    $stmt->close();
}

/*
.StudentNumber = TextBoxInputStudentNumber.Text,
.FirstName = TextBoxInputFirstName.Text,
.MiddleName = TextBoxInputMiddleName.Text,
.LastName = TextBoxInputLastName.Text,
.Gender = If(ComboBoxGender.SelectedItem?.ToString(), ""),
.DOB = DateTimePickerDOB.Value,
.Address = TextBoxInputAddress.Text,
.Guardian = TextBoxInputGuardian.Text,
.ContactNumber = TextBoxInputTel.Text,
.Relationship = TextBoxInputRelationship.Text
*/

function editStudent($connection, $data) {
    $stmt = $connection->prepare("CALL UpdateStudent(?,?,?,?,?,?,?,?,?,?)");
    $stmt->bind_param("ssssssssss",
    $data['StudentNumber'],
    $data['FirstName'],
    $data['MiddleName'],
    $data['LastName'],
    $data['Gender'],
    $data['DOB'],
    $data['Address'],
    $data['Guardian'],
    $data['ContactNumber'],
    $data['Relationship']
    );
    //$stmt->execute();
    //echo json_encode(["status" => "success", "message" => "Student updated"]);
    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Student inserted successfully."]);
    } else {
        echo json_encode(["success" => false, "message" => "Insert failed: " . $stmt->error]);
    }    
    $stmt->close();
}

/*
function deleteStudent($connection, $data) {
    $stmt = $connection->prepare("CALL DeleteStudent(?)");
    $stmt->bind_param("s", $data['StudentNumber']);
    $stmt->execute();
    echo json_encode(["success" => "true", "message" => "Student deleted"]);
}
*/

function deleteStudent($connection, $data) {
    if (!isset($data['StudentNumber'])) {
        echo json_encode(["success" => false, "message" => "Missing StudentNumber"]);
        return;
    }

    $studentNumber = $data['StudentNumber'];

    // Call stored procedure
    $stmt = $connection->prepare("CALL DeleteStudent(?)");
    if (!$stmt) {
        echo json_encode(["success" => false, "message" => "Prepare failed: " . $connection->error]);
        return;
    }

    $stmt->bind_param("s", $studentNumber);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Student deleted"]);
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

function listStudents($connection) {
    $result = $connection->query("CALL GetAllStudents()");
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

function searchStudents2($connection, $data) {
    //$stmt = $connection->prepare("CALL SearchStudentByLastName(?)");
    $stmt = $connection->prepare("CALL GetAssessmentByStudentNumber(?)"); /*can use lastname and student number */
    $stmt->bind_param("s", $data['assessmentnumber']);
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
}

function addStudent($connection, $data) {
    // normalize missing fields to empty strings so bind_param has values
    $fields = [
        'studentnumber','firstname','middlename','lastname','gender',
        'dob','address','guardian','contactnumber','relationship'
    ];
    foreach ($fields as $f) {
        if (!isset($data[$f])) $data[$f] = '';
    }

    if ($data['studentnumber'] === '' || $data['firstname'] === '' || $data['lastname'] === '') {
        echo json_encode(["success" => false, "message" => "Missing required fields (studentnumber, firstname, lastname)"]);
        return;
    }

    $stmt = $connection->prepare("CALL AddStudent(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        echo json_encode(["success" => false, "message" => "Prepare failed: " . $connection->error]);
        return;
    }

    $stmt->bind_param(
        "ssssssssss",
        $data['studentnumber'],
        $data['firstname'],
        $data['middlename'],
        $data['lastname'],
        $data['gender'],
        $data['dob'],
        $data['address'],
        $data['guardian'],
        $data['contactnumber'],
        $data['relationship']
    );

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Student added successfully"]);
    } else {
        echo json_encode(["success" => false, "message" => "Insert failed: " . $stmt->error]);
    }
    $stmt->close();
}

function searchStudents($connection, $data) {
    //$stmt = $connection->prepare("CALL SearchStudentByLastName(?)");
    $stmt = $connection->prepare("CALL SearchStudentByLastName(?)"); /*can use lastname and student number */
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
}

// --- MAIN ROUTER --- //
switch ($method) {
    case "create":
        createStudent($connection, $data);
        break;

    case "update":
        updateStudent($connection, $data);
        break;

    case "delete":
        deleteStudent($connection, $data);
        break;

    case "get":
        getStudent($connection, $data);
        break;

    case "list":
        listStudents($connection);
        break;
      
    case "search":
        searchStudents($connection, $data);
        break;        

    case "search2":
        searchStudents2($connection, $data);
    break;    

    case "add":
        addStudent($connection, $data);       
    break;

    case "edit": /*vb.net call*/
        editStudent($connection, $data);      
    break;

    default:
        echo json_encode(["status" => "error", "message" => "Invalid action"]);
    break;        
}
?>