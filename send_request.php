<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'student') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = $_SESSION['user_id'];
    $teacher_id = $_POST['teacher_id'];
    $course_id = $_POST['course_id'];

    $conn = new mysqli('localhost', 'root', '', 'user_registration');

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "INSERT INTO student_requests (student_id, teacher_id, course_id) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $student_id, $teacher_id, $course_id);

    if ($stmt->execute()) {
        echo "Request sent successfully! Please wait for the teacher to accept your request.";
    header("refresh:2;url=view_courses.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: view_courses.php");
    exit();
}
?>
