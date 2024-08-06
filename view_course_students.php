<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'teacher') {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['course_id'])) {
    header("Location: teacher_profile.php");
    exit();
}

$teacher_id = $_SESSION['user_id'];
$course_id = $_GET['course_id'];
$conn = new mysqli('localhost', 'root', '', 'user_registration');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT s.fullname, s.contact_number, s.email
        FROM student_courses sc
        JOIN students s ON sc.student_id = s.id
        WHERE sc.course_id = ? AND sc.teacher_id = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("Error preparing statement: " . $conn->error);
}

$stmt->bind_param("ii", $course_id, $teacher_id);
$stmt->execute();
$result = $stmt->get_result();

$students = [];
while ($row = $result->fetch_assoc()) {
    $students[] = $row;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Students Enrolled in Course</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #ece9e6, #ffffff);
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #2c3e50;
        }

        .container {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 600px;
            width: 100%;
        }

        h2 {
            font-size: 28px;
            color: #34495e;
            margin-bottom: 20px;
        }

        ul {
            list-style: none;
            padding: 0;
        }

        li {
            margin-bottom: 20px;
            font-size: 16px;
            text-align: left;
        }

        strong {
            display: block;
            margin-top: 10px;
            font-size: 18px;
        }

        a {
            display: inline-block;
            text-decoration: none;
            color: white;
            background-color: #3498db;
            padding: 10px 20px;
            border-radius: 8px;
            transition: background-color 0.3s;
            font-size: 16px;
            cursor: pointer;
        }

        a:hover {
            background-color:#2980b9;
        }

        @media (max-width: 600px) {
            .container {
                padding: 20px;
                max-width: 90%;
            }

            h2 {
                font-size: 24px;
            }

            li {
                font-size: 14px;
            }

            a {
                font-size: 14px;
                padding: 10px 20px;
            }

            strong {
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Students Enrolled in Course</h2>
        <ul>
            <?php if (empty($students)): ?>
                <li>No students enrolled in this course.</li>
            <?php else: ?>
                <?php foreach ($students as $student): ?>
                    <li>
                        <strong>Name:</strong> <?php echo htmlspecialchars($student['fullname']); ?><br>
                        <strong>Contact Number:</strong> <?php echo htmlspecialchars($student['contact_number']); ?><br>
                        <strong>Email:</strong> <?php echo htmlspecialchars($student['email']); ?><br>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
        <br>
        <a href="teacher_profile.php">Back to Profile</a>
    </div>
</body>
</html>

