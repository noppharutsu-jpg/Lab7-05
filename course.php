<?php
//กาํหนดค่า Access-Control-Allow-Origin ให้เครืÉองอืÉน ๆ สามารถเรียกใชง้านหนา้นÊีได้
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

//ตัÊงค่าการเชืÉอมต่อฐานขอ้ มูล
$link = mysqli_connect('localhost', 'root', '', 'noppharut1');
mysqli_set_charset($link, 'utf8');
$requestMethod = $_SERVER["REQUEST_METHOD"];
//ตรวจสอบหากใช้Method GET
if($requestMethod == 'GET'){
//ตรวจสอบการส่งค่า code
if(isset($_GET['course_code']) && !empty($_GET['course_code'])){
$student_code = $_GET['course_code'];
//คาํสัÉง SQL กรณี มีการส่งค่า id มาให้แสดงเฉพาะขอ้ มูลของ code นัÊน
$sql = "SELECT * FROM courses WHERE course_code = '$student_code'";

}else{
    //คาํสัÉง SQL แสดงขอ้ มูลทÊงหมด ั
$sql = "SELECT * FROM courses";
}
$result = mysqli_query($link, $sql);
//สร้างตวัแปร array สาํ หรบั เก็บขอ้ มูลทีÉได้
$arr = array();
while ($row = mysqli_fetch_assoc($result)) {
$arr[] = $row;
}
echo json_encode($arr);
}
$data = file_get_contents('php://input');
//แปลงขอ้ มูลทÉอ่านได้ ี เป็ น array แลว้เก็บไวท้ีÉตวัแปร result
$result = json_decode($data,true);
//ตรวจสอบการเรียกใชง้านวา่ เป็น Method POST หรือไม่
if($requestMethod == 'POST'){
if(!empty($result)){
$student_code = $result['course_code'];
$student_name = $result['course_name'];
$gender = $result['credit'];
//คาํสัÉง SQL สาํ หรบั เพิÉมขอ้ มูลใน Database
$sql = "INSERT INTO courses (course_code, course_name, credit) VALUES
('$student_code', '$student_name','$gender')";
$result = mysqli_query($link, $sql);
if ($result) {
echo json_encode(['status' => 'ok','message' => 'Insert Data Complete']);
} else {
echo json_encode(['status' => 'error','message' => 'Error']);
}
}
}
//ตรวจสอบการเรียกใชง้านวา่ เป็น Method PUT หรือไม่
if ($requestMethod == 'PUT') {
    $key_student = $_GET["course_code"];
    $student_code = $result['course_code'];
    $student_name = $result['course_name'];
    $gender = $result['credit'];
    //คาํสัÉง SQL สาํ หรบัแกไ้ขขอ้ มูลใน Database โดยจะแกไ้ขเฉพาะขอ้ มูลตามค่าstudent_code ทีÉส่งมา
$sql ="UPDATE courses SET course_code='$course_code', course_name='$course_name',
credit=$credit WHERE course_code='$course_code_edit'";
    try {
        $result = mysqli_query($link, $sql);
        echo json_encode(['status' => 'ok', 'message' => 'Update Data Complete']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
//ตรวจสอบการเรียกใชง้านวา่ เป็น Method DELETE หรือไม่
if ($requestMethod == 'DELETE') {
    //ตรวจสอบวา่ มีการส่งค่า student_code มาหรือไม่
    if (isset($_GET['student_code']) && !empty($_GET['student_code'])) {
        $student_code = $_GET['student_code'];
        //คาํสัÉง SQL สาํ หรบัลบขอ้ มูลใน Database ตามค่า id ทีÉส่งมา
        $sql = "DELETE FROM courses WHERE course_code='$course_code'";
        try {
            $result = mysqli_query($link, $sql);
            echo json_encode(['status' => 'ok', 'message' => 'Delete Data Complete']);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}