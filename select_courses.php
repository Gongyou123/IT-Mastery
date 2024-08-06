<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'teacher') {
    header("Location: login.php");
    exit();
}

$teacher_id = $_SESSION['user_id'];
$conn = new mysqli('localhost', 'root', '', 'user_registration');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $course_id = $_POST['course_id'];

    // Check if the teacher has already applied for this course
    $sql = "SELECT * FROM teacher_courses WHERE teacher_id = ? AND course_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $teacher_id, $course_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        // If not, insert the course into teacher_courses
        $sql = "INSERT INTO teacher_courses (teacher_id, course_id) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $teacher_id, $course_id);
        
        if ($stmt->execute()) {
            $message = "Course selected successfully!";
        } else {
            $message = "Error: " . $stmt->error;
        }
    } else {
        $message = "You have already applied for this course.";
    }

    $stmt->close();
}

$sql = "SELECT * FROM courses";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Courses</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: #f5f5f5;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
            color: #333;
        }

        .header {
            width: 100%;
            background-color: #3498db;
            padding: 10px 0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            text-align: center;
            margin-bottom: 20px;
            animation: slideDown 1s ease-in-out;
        }

        @keyframes slideDown {
            from { transform: translateY(-100%); }
            to { transform: translateY(0); }
        }

        .header a {
            color: white;
            text-decoration: none;
            padding: 12px 24px;
            transition: background-color 0.3s;
            font-size: 16px;
            margin: 0 10px;
        }

        .header a:hover {
            background-color: #2980b9;
        }

        .container {
            background-color: #fff;
            padding: 40px;
            border-radius: 200px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 3000px;
            width: 100%;
            animation: fadeIn 1s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        h2 {
            font-size: 28px;
            color: #333;
            margin-bottom: 20px;
        }

        ul {
            list-style: none;
            padding: 0;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
        }

        li {
            margin: 20px;
            text-align: center;
            transition: transform 0.3s;
        }

        li:hover {
            transform: scale(1.05);
        }

        img {
            width: 150px;
            height: 150px;
            margin-bottom: 10px;
            border-radius: 12px;
            border: 2px solid #3498db;
        }

        button {
            display: inline-block;
            text-decoration: none;
            color: white;
            background-color: #3498db;
            padding: 10px 20px;
            border-radius: 8px;
            transition: background-color 0.3s;
            font-size: 16px;
            cursor: pointer;
            border: none;
        }

        button:hover {
            background-color: #2980b9;
        }

        .message {
            margin-bottom: 20px;
            font-size: 18px;
            color: #2ecc71;
        }

        .error {
            color: #e74c3c;
        }

        a {
            color: #3498db;
            text-decoration: none;
            margin-top: 20px;
            display: inline-block;
            font-size: 18px;
        }

        a:hover {
            text-decoration: underline;
        }

        @media (max-width: 600px) {
            .container {
                padding: 20px;
            }

            h2 {
                font-size: 24px;
            }

            li {
                margin: 10px;
            }

            img {
                width: 120px;
                height: 120px;
            }

            button {
                font-size: 14px;
                padding: 8px 16px;
            }

            a {
                font-size: 16px;
            }

            .header a {
                font-size: 14px;
                padding: 10px;
                margin: 0 5px;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <a href="select_courses.php">Select Courses</a>
        <a href="teacher_profile.php">View Profile</a>
        <a href="search_students.php">Search and Add Students</a>
        <a href="view_student_requests.php">View Student Requests</a>
        <form action="logout.php" method="post" style="display: inline;">
            <input type="submit" value="Logout" class="logout-button">
        </form>
    </div>

    <?php if ($message): ?>
        <div class="message"><?php echo $message; ?></div>
    <?php endif; ?>

    <div class="container">
        <h2>Select Courses</h2>
        <form action="select_courses.php" method="post">
            <ul>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <li>
                            <img src="logo/<?php echo $row['logo']; ?>" alt="Course Logo">
                            <p><?php echo $row['course_name']; ?></p>
                            <button type="submit" name="course_id" value="<?php echo $row['id']; ?>">Select Course</button>
                        </li>
                    <?php endwhile; ?>
                <?php else: ?>
                    <li>No courses available</li>
                <?php endif; ?>
            </ul>
        </form>
        <a href="teacher_home.php">Back to Home</a>
    </div>
</body>
</html>

<?php
$conn->close();
?>
