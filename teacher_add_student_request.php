<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'teacher') {
    header("Location: login.php");
    exit();
}

if (!isset($_POST['student_id']) || !isset($_POST['course_id'])) {
    header("Location: teacher_view_student_profile.php");
    exit();
}

$teacher_id = $_SESSION['user_id'];
$student_id = $_POST['student_id'];
$course_id = $_POST['course_id'];

$conn = new mysqli('localhost', 'root', '', 'user_registration');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the student is already enrolled in the selected course
$sql = "SELECT * FROM student_courses WHERE student_id = ? AND course_id = ? AND teacher_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $student_id, $course_id, $teacher_id);
$stmt->execute();
$result = $stmt->get_result();
$already_enrolled = $result->num_rows > 0;
$stmt->close();

if ($already_enrolled) {
    $_SESSION['message'] = "Student is already enrolled in this course.";
    header("Location: teacher_view_student_profile.php?student_id=" . $student_id);
    exit();
}

// Insert the student-course request
$sql = "INSERT INTO teacher_student_requests (teacher_id, student_id, course_id, status) VALUES (?, ?, ?, 'pending')";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $teacher_id, $student_id, $course_id);
$stmt->execute();
$stmt->close();

$_SESSION['message'] = "Request to add student to course has been sent.";
header("Location: teacher_view_student_profile.php?student_id=" . $student_id);
exit();

$conn->close();
?>
