<?php 
require_once 'dbcon.php'; // include your database connection

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$method = $_GET['action'] ?? ''; // e.g. students_api.php?action=create
$data = json_decode(file_get_contents("php://input"), true);

/*
installmentpaid3
*/
// --- FUNCTIONS FOR EACH ACTION --- //
function createPayment($connection, $data) {
    $stmt = $connection->prepare("CALL AddPayment(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");

    if (!$stmt) {
        echo json_encode(["success" => false, "message" => "Prepare failed: " . $connection->error]);
        return;
    }

    $stmt->bind_param(
        "sssssssssssisississsssssssssssssssssssssssssssssssssssssssss",
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
        $data['terms'],             // i
        $data['installmentamount'],
        $data['penalty'],           // i
        $data['amountpaid'],
        $data['balance'],
        $data['enrolled'],          // i
        $data['date1'],
        $data['time1'],
        $data['assessmentnumber'],
        $data['paymenttype'],
        $data['downpayment'],
        $data['installmentpaid1'],
        $data['installmentpaid2'],
        $data['installmentpaid3'],
        $data['registrationfee'],
        $data['researchfee'],
        $data['term1date'],
        $data['term2date'],
        $data['term3date'],

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
        echo json_encode(["success" => true, "message" => "Payment inserted successfully."]);
    } else {
        echo json_encode(["success" => false, "message" => "Insert failed: " . $stmt->error]);
    }

    $stmt->close();
}


function updateStudent($connection, $data) {
    $stmt = $connection->prepare("CALL UpdateStudent(?,?,?,?,?,?,?,?,?,?)");
    $stmt->bind_param("ssssssssss",
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
    $stmt = $connection->prepare("CALL GetPaymentByStudentNumber (?)");
    $stmt->bind_param("s", $data['StudentNumber']);
    $stmt->execute();
    $result = $stmt->get_result();
    $student = $result->fetch_assoc();
    echo json_encode($student ?: ["status" => "error", "message" => "Student not found"]);
    $stmt->close();
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

function listPrograms($connection) {
    $result = $connection->query("CALL GetPrograms()");
    $programs = [];

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $programs[] = $row;
        }
        echo json_encode(["success" => true, "data" => $programs]);
    } else {
        echo json_encode(["success" => false, "message" => "Query failed"]);
    }
}

function searchStudents($connection, $data) {
    //$stmt = $connection->prepare("CALL SearchStudentByLastName(?)");
    $stmt = $connection->prepare("CALL SearchStudentByLastName(?)");
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

function listPayments($connection, $data) {
    $stmt = $connection->prepare("CALL GetPaymentByStudentNumber(?)");
    $stmt->bind_param("s", $data['StudentNumber']);
    
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $payments = [];
        while ($row = $result->fetch_assoc()) {
            $payments[] = $row;
        }
        echo json_encode(["success" => true, "data" => $payments]);
    } else {
        echo json_encode(["success" => false, "message" => "Query failed: " . $stmt->error]);
    }
}

function listPaymentsbyDate($connection, $data) {
    $stmt = $connection->prepare("CALL GetPaymentByDate(?,?)");
    $stmt->bind_param(
        "ss",                     // 3 strings (or "sii" etc. depending on types)
        $data['startdate'],
        $data['enddate']
    );
    
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $payments = [];
        while ($row = $result->fetch_assoc()) {
            $payments[] = $row;
        }
        echo json_encode(["success" => true, "data" => $payments]);
    } else {
        echo json_encode(["success" => false, "message" => "Query failed: " . $stmt->error]);
    }
}

function listPaymentsbyProgramSYSem($connection, $data) {
    $stmt = $connection->prepare("CALL GetPendingStudentsByProgramSYSem(?,?,?)");
    $stmt->bind_param(
        "sss",                     // 3 strings (or "sii" etc. depending on types)
        $data['program'],
        $data['schoolyear'],
        $data['semester']
    );
    
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $payments = [];
        while ($row = $result->fetch_assoc()) {
            $payments[] = $row;
        }
        echo json_encode(["success" => true, "data" => $payments]);
    } else {
        echo json_encode(["success" => false, "message" => "Query failed: " . $stmt->error]);
    }
}

function listPaymentsbyProgramSYSemFullyPaid($connection, $data) {
    $stmt = $connection->prepare("CALL GetFullyPaidStudentsByProgramSYSemFullyPaid(?,?,?)");
    $stmt->bind_param(
        "sss",                     // 3 strings (or "sii" etc. depending on types)
        $data['program'],
        $data['schoolyear'],
        $data['semester']
    );
    
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $payments = [];
        while ($row = $result->fetch_assoc()) {
            $payments[] = $row;
        }
        echo json_encode(["success" => true, "data" => $payments]);
    } else {
        echo json_encode(["success" => false, "message" => "Query failed: " . $stmt->error]);
    }
}

// --- MAIN ROUTER --- //
switch ($method) {
    case "create":
        createPayment($connection, $data);
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
    
    case "add":
        addStudent($connection, $data);       
        break;

    case "listprograms":
        listPrograms($connection);
        break;        
    
    case "listpayments":
        listPayments($connection, $data);
        break;   

    case "listpaymentsbydate":
        listPaymentsbyDate($connection, $data);
        break;  

    case "listpaymentsbyprogramsyrsem":
        listPaymentsbyProgramSYSem($connection, $data);
        break;
 
    case "listpaymentsbyprogramsyrsemfullypaid":
        listPaymentsbyProgramSYSemFullyPaid($connection, $data);
    break;

    default:
        echo json_encode(["status" => "error", "message" => "Invalid action"]);
        break;
}
?>