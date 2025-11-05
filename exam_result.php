<?php
// กำหนดค่า CORS และ Header ต่าง ๆ
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// เชื่อมต่อฐานข้อมูล
$link = mysqli_connect('localhost', 'root', '', 'noppharut1');
mysqli_set_charset($link, 'utf8');

$requestMethod = $_SERVER["REQUEST_METHOD"];

// -------------------- READ (GET) --------------------
if ($requestMethod == 'GET') {
    if (isset($_GET['id']) && !empty($_GET['id'])) {
        $id = $_GET['id'];
        // ✅ ดึงข้อมูลเฉพาะ id พร้อมชื่อ course และ student
        $sql = "SELECT e.id, e.course_code, c.course_name, e.student_code, s.student_name, e.point
                FROM exam_results e
                LEFT JOIN courses c ON e.course_code = c.course_code
                LEFT JOIN students s ON e.student_code = s.student_code
                WHERE e.id = '$id'";
    } else {
        // ✅ ดึงข้อมูลทั้งหมดพร้อมชื่อ course และ student
        $sql = "SELECT e.id, e.course_code, c.course_name, e.student_code, s.student_name, e.point
                FROM exam_results e
                LEFT JOIN courses c ON e.course_code = c.course_code
                LEFT JOIN students s ON e.student_code = s.student_code";
    }

    $result = mysqli_query($link, $sql);
    $arr = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $arr[] = $row;
    }
    echo json_encode($arr);
    exit;
}

// อ่านข้อมูลจาก body (ใช้ใน POST / PUT)
$data = file_get_contents('php://input');
$result = json_decode($data, true);

// -------------------- CREATE (POST) --------------------
if ($requestMethod == 'POST') {
    if (!empty($result)) {
        $course_code = $result['course_code'];
        $student_code = $result['student_code'];
        $point = $result['point'];

        // ✅ ถ้า id เป็น AUTO_INCREMENT ไม่ต้องกำหนดใน INSERT
        $sql = "INSERT INTO exam_results (course_code, student_code, point)
                VALUES ('$course_code', '$student_code', '$point')";

        $res = mysqli_query($link, $sql);

        if ($res) {
            echo json_encode(['status' => 'ok', 'message' => 'Insert Data Complete']);
        } else {
            echo json_encode(['status' => 'error', 'message' => mysqli_error($link)]);
        }
    }
    exit;
}

// -------------------- UPDATE (PUT) --------------------
if ($requestMethod == 'PUT') {
    if (!empty($result['id'])) {
        $id = $result['id'];
        $point = $result['point'];

        $sql = "UPDATE exam_results SET point = '$point' WHERE id = '$id'";
        $res = mysqli_query($link, $sql);

        if ($res) {
            http_response_code(201); // สำหรับ POST
            echo json_encode(['status' => 'ok', 'message' => 'Insert Data Complete']);
        } else {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => mysqli_error($link)]);
}

    } else {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Missing ID']);
    }
    exit;
}


// -------------------- DELETE (DELETE) --------------------
if ($requestMethod == 'DELETE') {
    if (isset($_GET['id']) && !empty($_GET['id'])) {
        $id = $_GET['id'];
        $sql = "DELETE FROM exam_results WHERE id = '$id'";
        $res = mysqli_query($link, $sql);

        if ($res) {
            echo json_encode(['status' => 'ok', 'message' => 'Delete Data Complete']);
        } else {
            echo json_encode(['status' => 'error', 'message' => mysqli_error($link)]);
        }
    }
    exit;
}
?>
