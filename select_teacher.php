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
    $course_id = $_POST['course_id'];

    $sql = "INSERT INTO student_requests (student_id, teacher_id, course_id, message) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiis", $student_id, $teacher_id, $course_id, $_POST['message']);
    $stmt->execute();
    $stmt->close();

    $sql = "UPDATE student_progress SET status='teacher_selected' WHERE student_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $stmt->close();

    header("Location: send_message.php");
    exit();
}

$sql = "SELECT c.id AS course_id, c.course_name, t.id AS teacher_id, t.fullname 
        FROM courses c
        JOIN teacher_courses tc ON c.id = tc.course_id
        JOIN teachers t ON tc.teacher_id = t.id
        WHERE c.id IN (SELECT course_id FROM student_courses WHERE student_id=?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$teachers_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Teacher</title>
</head>
<body>
    <h2>Select Teacher</h2>
    <form action="select_teacher.php" method="post">
        <?php while ($row = $teachers_result->fetch_assoc()): ?>
            <input type="radio" id="teacher_<?php echo $row['teacher_id']; ?>" name="teacher_id" value="<?php echo $row['teacher_id']; ?>" required>
            <label for="teacher_<?php echo $row['teacher_id']; ?>"><?php echo $row['fullname']; ?> (<?php echo $row['course_name']; ?>)</label><br>
            <input type="hidden" name="course_id" value="<?php echo $row['course_id']; ?>">
        <?php endwhile; ?>
        <br>
        <label for="message">Message to Teacher:</label><br>
        <textarea id="message" name="message" required></textarea><br><br>
        <input type="submit" value="Select Teacher">
    </form>
</body>
</html>

<?php
$conn->close();
?>
