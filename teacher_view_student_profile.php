<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'teacher') {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['student_id'])) {
    header("Location: search_students.php");
    exit();
}

$student_id = $_GET['student_id'];
$conn = new mysqli('localhost', 'root', '', 'user_registration');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT tsp.*, s.fullname 
        FROM teacher_student_profiles tsp 
        JOIN students s ON tsp.student_id = s.id
        WHERE tsp.student_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$student_profile = $result->fetch_assoc();

if (!$student_profile) {
    echo "Student profile not found.";
    exit();
}

// Fetch teacher courses
$teacher_id = $_SESSION['user_id'];
$sql = "SELECT tc.course_id, c.course_name 
        FROM teacher_courses tc
        JOIN courses c ON tc.course_id = c.id
        WHERE tc.teacher_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$courses_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Student Profile</title>
    <style>
        /* Your CSS styles here */
    </style>
</head>
<body>
    <div class="container">
        <h2><?php echo htmlspecialchars($student_profile['fullname']); ?>'s Profile</h2>
        <img src="uploads/<?php echo $student_profile['profile_picture']; ?>" alt="Profile Picture" width="150">
        <div class="profile-info">
            <p><strong>Age:</strong> <?php echo htmlspecialchars($student_profile['age']); ?></p>
            <p><strong>Date of Birth:</strong> <?php echo htmlspecialchars($student_profile['date_of_birth']); ?></p>
            <p><strong>Gender:</strong> <?php echo htmlspecialchars($student_profile['gender']); ?></p>
            <p><strong>Address:</strong> <?php echo htmlspecialchars($student_profile['address']); ?></p>
            <p><strong>Contact Number:</strong> <?php echo htmlspecialchars($student_profile['contact_number']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($student_profile['email']); ?></p>
            <p><strong>Skills:</strong> <?php echo htmlspecialchars($student_profile['skills']); ?></p>
            <p><strong>Parent/Guardian Information:</strong> <?php echo htmlspecialchars($student_profile['parent_guardian_info']); ?></p>
            <p><strong>Parent/Guardian Contact:</strong> <?php echo htmlspecialchars($student_profile['parent_guardian_contact']); ?></p>
            <p><strong>Reason for using this system:</strong> <?php echo htmlspecialchars($student_profile['reason']); ?></p>
        </div>
        <form action="teacher_add_student_request.php" method="post">
            <input type="hidden" name="student_id" value="<?php echo htmlspecialchars($student_id); ?>">
            <label for="course_id">Select Course:</label>
            <select name="course_id" required>
                <?php while ($course_row = $courses_result->fetch_assoc()): ?>
                    <option value="<?php echo htmlspecialchars($course_row['course_id']); ?>"><?php echo htmlspecialchars($course_row['course_name']); ?></option>
                <?php endwhile; ?>
            </select>
            <input type="submit" value="Add Student to Course">
        </form>
        <a href="search_students.php">Back to Search</a>
    </div>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
