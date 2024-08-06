<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'student') {
    header("Location: login.php");
    exit();
}

if (!isset($_POST['teacher_id']) || !isset($_POST['action'])) {
    header("Location: view_teacher_request.php");
    exit();
}

$student_id = $_SESSION['user_id'];
$teacher_id = $_POST['teacher_id'];
$action = $_POST['action'];

$conn = new mysqli('localhost', 'root', '', 'user_registration');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Find the course associated with this request
$sql = "SELECT course_id FROM teacher_student_requests WHERE teacher_id = ? AND student_id = ? AND status = 'pending'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $teacher_id, $student_id);
$stmt->execute();
$result = $stmt->get_result();
$request = $result->fetch_assoc();
$stmt->close();

if (!$request) {
    die("Request not found or already processed.");
}

$course_id = $request['course_id'];

// Check if the student is already enrolled in the course
$sql = "SELECT * FROM student_courses WHERE student_id = ? AND course_id = ? AND teacher_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $student_id, $course_id, $teacher_id);
$stmt->execute();
$result = $stmt->get_result();
$already_enrolled = $result->num_rows > 0;
$stmt->close();

if ($already_enrolled) {
    $_SESSION['message'] = "You are already enrolled in this course.";
    header("Location: student_home.php");
    exit();
}

if ($action == 'accept') {
    // Update the request status to 'accepted' and enroll the student in the course
    $sql = "UPDATE teacher_student_requests SET status = 'accepted' WHERE teacher_id = ? AND student_id = ? AND course_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $teacher_id, $student_id, $course_id);
    $stmt->execute();
    $stmt->close();

    // Enroll the student in the course
    $sql = "INSERT INTO student_courses (student_id, course_id, teacher_id) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $student_id, $course_id, $teacher_id);
    $stmt->execute();
    $stmt->close();

    // Set a success message in the session and redirect to student home
    $_SESSION['message'] = "You have successfully accepted the teacher's request.";
    header("Location: student_home.php");
    exit();
} else {
    // Update the request status to 'rejected'
    $sql = "UPDATE teacher_student_requests SET status = 'rejected' WHERE teacher_id = ? AND student_id = ? AND course_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $teacher_id, $student_id, $course_id);
    $stmt->execute();
    $stmt->close();

    // Set a rejection message in the session and redirect to student home
    $_SESSION['message'] = "You have rejected the teacher's request.";
    header("Location: student_home.php");
    exit();
}

$conn->close();
?>
