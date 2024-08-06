<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'student') {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['teacher_id'])) {
    header("Location: view_teacher_requests.php");
    exit();
}

$teacher_id = $_GET['teacher_id'];
$conn = new mysqli('localhost', 'root', '', 'user_registration');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM teacher_profiles WHERE teacher_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$result = $stmt->get_result();
$profile = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Teacher Profile</title>
    <style>
        /* Your CSS styles here */
    </style>
</head>
<body>
    <div class="container">
        <h2>Teacher Profile</h2>
        <?php if ($profile): ?>
            <div>
                <strong>Profile Picture:</strong><br>
                <img src="<?php echo $profile['profile_picture']; ?>" alt="Profile Picture">
            </div>
            <div>
                <strong>Academic Achievements:</strong><br>
                <?php echo $profile['academic_achievements']; ?>
            </div>
            <div>
                <strong>Skills:</strong><br>
                <?php echo $profile['skills']; ?>
            </div>
            <div>
                <strong>Contact Details:</strong><br>
                <?php echo $profile['contact_details']; ?>
            </div>
            <div>
                <strong>Available Schedule:</strong><br>
                <?php echo $profile['available_schedule']; ?>
            </div>
        <?php else: ?>
            <p>No profile found for this teacher.</p>
        <?php endif; ?>
        <br>
        <a href="view_teacher_requests.php">Back to Teacher Requests</a>
    </div>
</body>
</html>
