<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'student') {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['teacher_id'])) {
    header("Location: view_teacher_request.php");
    exit();
}

$teacher_id = $_GET['teacher_id'];
$conn = new mysqli('localhost', 'root', '', 'user_registration');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT tp.*, t.fullname FROM teacher_profiles tp 
        JOIN teachers t ON tp.teacher_id = t.id 
        WHERE tp.teacher_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$result = $stmt->get_result();
$profile = $result->fetch_assoc();
$stmt->close();
$conn->close();

if (!$profile) {
    die("Teacher profile not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Profile</title>
    <style>
        /* Add your CSS styling here */
    </style>
</head>
<body>
    <h2>Teacher Profile</h2>
    <p>Full Name: <?php echo htmlspecialchars($profile['fullname']); ?></p>
    <p>Academic Achievements: <?php echo htmlspecialchars($profile['academic_achievements']); ?></p>
    <p>Skills: <?php echo htmlspecialchars($profile['skills']); ?></p>
    <p>Contact Details: <?php echo htmlspecialchars($profile['contact_details']); ?></p>
    <p>Available Schedule: <?php echo htmlspecialchars($profile['available_schedule']); ?></p>
    
    <form action="handle_teacher_request.php" method="post">
        <input type="hidden" name="teacher_id" value="<?php echo $teacher_id; ?>">
        <button type="submit" name="action" value="accept">Accept</button>
        <button type="submit" name="action" value="reject">Reject</button>
    </form>
    <button onclick="location.href='view_teacher_request.php'">Back to Requests</button>
</body>
</html>
