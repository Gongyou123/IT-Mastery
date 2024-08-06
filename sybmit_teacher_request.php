<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'teacher') {
    header("Location: login.php");
    exit();
}

if (isset($_POST['submit_request'])) {
    $teacher_id = $_SESSION['user_id'];
    $student_id = $_POST['student_id'];
    $course_id = $_POST['course_id'];

    // Insert the request into the database
    $conn = new mysqli('localhost', 'root', '', 'user_registration');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "INSERT INTO teacher_student_requests (teacher_id, student_id, course_id) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $teacher_id, $student_id, $course_id);

    if ($stmt->execute()) {
        echo "Request submitted successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
