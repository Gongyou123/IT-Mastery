<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'student') {
    header("Location: login.php");
    exit();
}

if (!isset($_POST['request_id']) || !isset($_POST['response'])) {
    header("Location: view_teacher_requests.php");
    exit();
}

$student_id = $_SESSION['user_id'];
$request_id = $_POST['request_id'];
$response = $_POST['response'];

$conn = new mysqli('localhost', 'root', '', 'user_registration');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($response == 'Accept') {
    // Update request status to accepted
    $sql = "UPDATE teacher_student_requests SET status = 'accepted' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $request_id);

    if ($stmt->execute()) {
        // Add student to course
        $sql = "INSERT INTO student_courses (student_id, course_id, teacher_id) 
                SELECT student_id, course_id, teacher_id FROM teacher_student_requests WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $request_id);
        $stmt->execute();
    }
} else {
    // Update request status to declined
    $sql = "UPDATE teacher_student_requests SET status = 'declined' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $request_id);
    $stmt->execute();
}

$stmt->close();
$conn->close();
header("Location: view_teacher_requests.php");
exit();
?>
