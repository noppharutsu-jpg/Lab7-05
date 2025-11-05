<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$link = mysqli_connect('localhost', 'root', '', 'noppharut1');
mysqli_set_charset($link, 'utf8');

if (!$link) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed']);
    exit;
}

$requestMethod = $_SERVER["REQUEST_METHOD"];

// ---------------------- GET ----------------------
if ($requestMethod == 'GET') {
    if (isset($_GET['course_code']) && !empty($_GET['course_code'])) {
        $course_code = mysqli_real_escape_string($link, $_GET['course_code']);
        $sql = "SELECT * FROM courses WHERE course_code = '$course_code'";
    } else {
        $sql = "SELECT * FROM courses";
    }

    $result = mysqli_query($link, $sql);
    $arr = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $arr[] = $row;
    }
    echo json_encode($arr);
    exit;
}

// อ่านข้อมูล JSON ที่ส่งมา
$data = file_get_contents('php://input');
$result = json_decode($data, true);

// ---------------------- POST ----------------------
if ($requestMethod == 'POST') {
    if (!empty($result)) {
        $course_code = mysqli_real_escape_string($link, $result['course_code']);
        $course_name = mysqli_real_escape_string($link, $result['course_name']);
        $credit = (int)$result['credit'];

        $sql = "INSERT INTO courses (course_code, course_name, credit)
                VALUES ('$course_code', '$course_name', $credit)";
        $query = mysqli_query($link, $sql);

        if ($query) {
            http_response_code(201);
            echo json_encode(['status' => 'ok', 'message' => 'Insert Data Complete']);
        } else {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => mysqli_error($link)]);
        }
    }
    exit;
}

// ---------------------- PUT ----------------------
if ($requestMethod == 'PUT') {
    if (!empty($result['course_code'])) {
        $course_code = mysqli_real_escape_string($link, $result['course_code']);
        $course_name = mysqli_real_escape_string($link, $result['course_name']);
        $credit = (int)$result['credit'];

        $sql = "UPDATE courses 
                SET course_name = '$course_name', credit = $credit 
                WHERE course_code = '$course_code'";

        $query = mysqli_query($link, $sql);

        if ($query) {
            http_response_code(200);
            echo json_encode(['status' => 'ok', 'message' => 'Update Data Complete']);
        } else {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => mysqli_error($link)]);
        }
    } else {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Missing course_code']);
    }
    exit;
}

// ---------------------- DELETE ----------------------
if ($requestMethod == 'DELETE') {
    if (isset($_GET['course_code']) && !empty($_GET['course_code'])) {
        $course_code = mysqli_real_escape_string($link, $_GET['course_code']);
        $sql = "DELETE FROM courses WHERE course_code = '$course_code'";
        $query = mysqli_query($link, $sql);

        if ($query) {
            http_response_code(200);
            echo json_encode(['status' => 'ok', 'message' => 'Delete Data Complete']);
        } else {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => mysqli_error($link)]);
        }
    } else {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Missing course_code']);
    }
    exit;
}
?>
