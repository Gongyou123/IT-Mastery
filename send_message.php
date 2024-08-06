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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $teacher_id = $_POST['teacher_id'];
    $message = $_POST['message'];

    $sql = "INSERT INTO messages (sender_id, sender_type, receiver_id, receiver_type, message) VALUES (?, 'student', ?, 'teacher', ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iis", $student_id, $teacher_id, $message);
    $stmt->execute();
    $stmt->close();

    echo "Message sent!";
}

$sql = "SELECT teacher_id FROM student_requests WHERE student_id = ? AND status = 'accepted'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$teacher_result = $stmt->get_result();
$teacher = $teacher_result->fetch_assoc();

if (!$teacher) {
    echo "No teacher assigned yet.";
    exit();
}
$teacher_id = $teacher['teacher_id'];

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Message to Teacher</title>
</head>
<body>
    <h2>Send Message to Teacher</h2>
    <form action="send_message.php" method="post">
        <input type="hidden" name="teacher_id" value="<?php echo $teacher_id; ?>">
        <label for="message">Message:</label><br>
        <textarea id="message" name="message" required></textarea><br><br>
        <input type="submit" value="Send Message">
    </form>
    <br>
    <a href="student_home.php">Back to Home</a>
</body>
</html>
