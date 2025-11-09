<?php 
require_once 'dbcon.php'; // include your database connection

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$method = $_GET['action'] ?? ''; // e.g. students_api.php?action=create
$data = json_decode(file_get_contents("php://input"), true);

// --- FUNCTIONS FOR EACH ACTION --- //
function createAssessment($connection, $data) {
    // ✅ Prepare with 54 placeholders
    $stmt = $connection->prepare("CALL AddAssessment(
        ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
        ?, ?, ?, 
        ?, ?, ?, 
        ?, ?, ?, 
        ?, ?, ?, 
        ?, ?, ?, 
        ?, ?, ?, 
        ?, ?, ?, 
        ?, ?, ?, 
        ?, ?, ?, 
        ?, ?, ?
    )");

    if (!$stmt) {
        echo json_encode([
            "success" => false,
            "message" => "Prepare failed: " . $connection->error
        ]);
        return;
    }

    // ✅ 53 parameters, all VARCHAR → 54 "s"
    $stmt->bind_param(
        str_repeat("s", 58),
        $data["assessmentnumber"], $data["studentnumber"], $data["lastname"], $data["firstname"], $data["middlename"],
        $data["program"], $data["schoolyear"], $data["semester"],
        $data["tuitionfee"], $data["miscellaneous"], $data["total"], $data["paymentmode"], $data["terms"],
        $data["installmentamount"], $data["penalty"], $data["amountpaid"], $data["balance"], $data["enrolled"],
        $data["date1"], $data["time1"], $data["registrationfee"], $data["researchfee"], $data["tuitionperunit"], $data["totalunits"], $data["InstallmentDP"],
        $data["Term1Date"], $data["Term2Date"], $data["Term3Date"], 
 

        $data["coursecode1"], $data["subject1"], $data["unit1"],
        $data["coursecode2"], $data["subject2"], $data["unit2"],
        $data["coursecode3"], $data["subject3"], $data["unit3"],
        $data["coursecode4"], $data["subject4"], $data["unit4"],
        $data["coursecode5"], $data["subject5"], $data["unit5"],
        $data["coursecode6"], $data["subject6"], $data["unit6"],
        $data["coursecode7"], $data["subject7"], $data["unit7"],
        $data["coursecode8"], $data["subject8"], $data["unit8"],
        $data["coursecode9"], $data["subject9"], $data["unit9"],
        $data["coursecode10"], $data["subject10"], $data["unit10"]
    );

    if ($stmt->execute()) {
        echo json_encode([
            "success" => true,
            "message" => "Assessment inserted successfully."
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Insert failed: " . $stmt->error
        ]);
    }

    $stmt->close();
}

function createAssessmentWithChecking($connection, $data) {
    // ✅ Prepare CALL statement with 70 placeholders (matching AddAssessment procedure)
    $stmt = $connection->prepare("CALL AddAssessmentWithChecking(
        ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
        ?, ?, ?, 
        ?, ?, ?, 
        ?, ?, ?, 
        ?, ?, ?, 
        ?, ?, ?, 
        ?, ?, ?, 
        ?, ?, ?, 
        ?, ?, ?, 
        ?, ?, ?, 
        ?, ?, ?
    )");

    if (!$stmt) {
        echo json_encode([
            "success" => false,
            "message" => "Prepare failed: " . $connection->error
        ]);
        return;
    }

    // ✅ Bind 70 parameters
    $stmt->bind_param(
        str_repeat('s', 58), // all are VARCHAR, so 's' for string
        $data['assessmentnumber'],
        $data['studentnumber'],
        $data['lastname'],
        $data['firstname'],
        $data['middlename'],

        $data['program'],
        $data['schoolyear'],
        $data['semester'],

        $data['tuitionfee'],
        $data['miscellaneous'],
        $data['total'],
        $data['paymentmode'],
        $data['terms'],

        $data['installmentamount'],
        $data['penalty'],
        $data['amountpaid'],
        $data['balance'],
        $data['enrolled'],

        $data['date1'],
        $data['time1'],
        $data['registrationfee'],
        $data['researchfee'],
        $data['tuitionperunit'],
        $data['totalunits'],
        $data['InstallmentDP'],

        $data['Term1Date'],
        $data['Term2Date'],
        $data['Term3Date'],

        $data['coursecode1'], $data['subject1'], $data['unit1'],
        $data['coursecode2'], $data['subject2'], $data['unit2'],
        $data['coursecode3'], $data['subject3'], $data['unit3'],
        $data['coursecode4'], $data['subject4'], $data['unit4'],
        $data['coursecode5'], $data['subject5'], $data['unit5'],
        $data['coursecode6'], $data['subject6'], $data['unit6'],
        $data['coursecode7'], $data['subject7'], $data['unit7'],
        $data['coursecode8'], $data['subject8'], $data['unit8'],
        $data['coursecode9'], $data['subject9'], $data['unit9'],
        $data['coursecode10'], $data['subject10'], $data['unit10']
    );

    // ✅ Execute the stored procedure
    if (!$stmt->execute()) {
        echo json_encode([
            "success" => false,
            "message" => "Execute failed: " . $stmt->error
        ]);
        $stmt->close();
        return;
    }

    // ✅ Retrieve the result set (the AddAssessment procedure SELECTs records)
    $result = $stmt->get_result();

    if ($result) {
        $records = $result->fetch_all(MYSQLI_ASSOC);
        echo json_encode([
            "success" => true,
            "data" => $records
        ]);
    } else {
        echo json_encode([
            "success" => true,
            "message" => "No records returned."
        ]);
    }
    //$stmt->close();
}


function updateSubject($connection, $data) {
    $stmt = $connection->prepare("CALL UpdateSubject(?,?,?,?)");
    $stmt->bind_param("ssss",
        $data['course_code'],
        $data['subject'],
        $data['units'],
        $data['program']
    );
    //$stmt->execute();
    //echo json_encode(["status" => "success", "message" => "Student updated"]);
    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Student inserted successfully."]);
    } else {
        echo json_encode(["success" => false, "message" => "Insert failed: " . $stmt->error]);
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

function deleteSubject($connection, $data) {
    if (!isset($data['course_code'])) {
        echo json_encode(["success" => false, "message" => "Missing Username"]);
        return;
    }

    $username = $data['course_code'];

    // Call stored procedure
    $stmt = $connection->prepare("CALL DeleteSubject(?)");
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

function listSubjects($connection) {
    $result = $connection->query("CALL GetAllSUbjects()");
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

function searchSubjects($connection, $data) {
    //$stmt = $connection->prepare("CALL SearchStudentByLastName(?)");
    $stmt = $connection->prepare("CALL SearchSubjectsByCourseCode(?)");
    $stmt->bind_param("s", $data['course_code']);
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

function searchAssessment($connection, $data) {
    //$stmt = $connection->prepare("CALL SearchStudentByLastName(?)");
    $stmt = $connection->prepare("CALL GetAssessmentByStudentNumber(?)");
    $stmt->bind_param("s", $data['studentnumber']);
    $stmt->execute();
    $result = $stmt->get_result();

    $students = [];
    while ($row = $result->fetch_assoc()) {
       $students[] = $row;
    }

    $response = [
        "success" => count($students) > 0,
        "data" => $data['studentnumber']//$students
    ];

    echo json_encode($response);

    //$stmt->close();
}

function verifyUsernamePassword($connection, $data) {
    // Prepare stored procedure call
    $stmt = $connection->prepare("CALL CheckUserCredentialsFlag(?, ?)");
    $stmt->bind_param("ss", $data['username'], $data['password']);
    $stmt->execute();

    $result = $stmt->get_result();

    $users = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
    }

    $response = [
        "success" => count($users) > 0,
        "data"    => $users
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

function searchAssessment2($connection, $data) {
    //$stmt = $connection->prepare("CALL SearchStudentByLastName(?)");
    //$stmt = $connection->prepare("CALL GetAssessmentByStudentNumber(?)"); /*can use lastname and student number */
    $stmt = $connection->prepare("CALL GetAssessmentByStudentNumberSYSem(?,?,?)"); 
        $stmt->bind_param("sss", $data['assessmentnumber'],
        $data['schoolyear'],
        $data['semester'],
    );
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

function updateAssessment($connection, $data) {
    $stmt = $connection->prepare("
        CALL UpdateAssessment(
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
            ?, ?, ?,
            ?, ?, ?, 
            ?, ?, ?, 
            ?, ?, ?, 
            ?, ?, ?, 
            ?, ?, ?, 
            ?, ?, ?, 
            ?, ?, ?, 
            ?, ?, ?, 
            ?, ?, ?
        )
    ");

    // 58 parameters — all VARCHAR
    $stmt->bind_param(
        str_repeat("s", 58),
        $data['assessmentnumber'],
        $data['studentnumber'],
        $data['lastname'],
        $data['firstname'],
        $data['middlename'],

        $data['program'],
        $data['schoolyear'],
        $data['semester'],

        $data['tuitionfee'],
        $data['miscellaneous'],
        $data['total'],
        $data['paymentmode'],
        $data['terms'],

        $data['installmentamount'],
        $data['penalty'],
        $data['amountpaid'],
        $data['balance'],
        $data['enrolled'],

        $data['date1'],
        $data['time1'],
        $data['registrationfee'],
        $data['researchfee'],
        $data['tuitionperunit'],
        $data['totalunits'],
        $data['InstallmentDP'],

        $data['Term1Date'],
        $data['Term2Date'],
        $data['Term3Date'],

        $data['coursecode1'], $data['subject1'], $data['unit1'],
        $data['coursecode2'], $data['subject2'], $data['unit2'],
        $data['coursecode3'], $data['subject3'], $data['unit3'],
        $data['coursecode4'], $data['subject4'], $data['unit4'],
        $data['coursecode5'], $data['subject5'], $data['unit5'],
        $data['coursecode6'], $data['subject6'], $data['unit6'],
        $data['coursecode7'], $data['subject7'], $data['unit7'],
        $data['coursecode8'], $data['subject8'], $data['unit8'],
        $data['coursecode9'], $data['subject9'], $data['unit9'],
        $data['coursecode10'], $data['subject10'], $data['unit10']
    );

    if ($stmt->execute()) {
        echo json_encode([
            "success" => true,
            "message" => "Assessment updated successfully."
        ]);
    }
    
    //$stmt->close();
}

// --- MAIN ROUTER --- //
switch ($method) {
    case "create":
        createAssessmentWithChecking($connection, $data);
        break;

    case "update":
        updateAssessment($connection, $data);
        break;

    case "delete":
        deleteSubject($connection, $data);
        break;

    case "get":
        getStudent($connection, $data);
        break;

    case "list":
        listSubjects($connection);
        break;
      
    case "search":
        searchSubjects($connection, $data);
        break;        

    case "search2":
        searchAssessment2($connection, $data);
        break;        

    case "add":
        addUser($connection, $data);
        break;
    
    case "verify":        
        verifyUsernamePassword($connection, $data);
    break;

    case "searchassessment":
        searchAssessment($connection, $data);
    break;

    default:
        echo json_encode(["status" => "error", "message" => "Invalid action"]);
    break;
}
?>