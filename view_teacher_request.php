<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'student') {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['user_id'];
$conn = new mysqli('localhost', 'root', '', 'user_registration');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT tr.*, t.fullname 
        FROM teacher_student_requests tr
        JOIN teachers t ON tr.teacher_id = t.id
        WHERE tr.student_id = ? AND tr.status = 'pending'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$requests = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Requests</title>
    <style>
        /* Add your CSS styling here */
    </style>
</head>
<body>
    <h2>Teacher Requests</h2>
    <ul>
        <?php foreach ($requests as $request): ?>
            <li>
                <strong>Teacher:</strong> <?php echo htmlspecialchars($request['fullname']); ?><br>
                <a href="student_view_teacher_profile.php?teacher_id=<?php echo $request['teacher_id']; ?>">View Profile</a>
            </li>
        <?php endforeach; ?>
    </ul>
    <button onclick="location.href='student_home.php'">Back to Home</button>
</body>
</html>
