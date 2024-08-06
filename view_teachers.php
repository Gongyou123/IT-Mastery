<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'student') {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['course_id'])) {
    header("Location: view_courses.php");
    exit();
}

$course_id = $_GET['course_id'];
$student_id = $_SESSION['user_id'];
$conn = new mysqli('localhost', 'root', '', 'user_registration');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT t.id, t.fullname
        FROM teacher_courses tc
        JOIN teachers t ON tc.teacher_id = t.id
        WHERE tc.course_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $course_id);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Teachers</title>
    <style type="text/css">
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
            margin: 0;
        }

        li {
            font-size: 18px;
            color: #2c3e50;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        form {
            display: inline;
        }

        input[type="submit"] {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        input[type="submit"]:hover {
            background-color: #2980b9;
        }

        a {
            display: inline-block;
            text-decoration: none;
            color: white;
            background-color: #3498db;
            padding: 12px 24px;
            border-radius: 8px;
            transition: background-color 0.3s;
            font-size: 16px;
            margin-top: 20px;
        }

        a:hover {
            background-color: #2980b9;
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
                font-size: 16px;
            }

            input[type="submit"] {
                padding: 8px 16px;
                font-size: 14px;
            }

            a {
                font-size: 14px;
                padding: 10px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Select a Teacher</h2>
        <ul>
            <?php while ($row = $result->fetch_assoc()): ?>
                <li>
                    <?php echo $row['fullname']; ?>
                    <form action="send_request.php" method="post">
                        <input type="hidden" name="teacher_id" value="<?php echo $row['id']; ?>">
                        <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
                        <input type="submit" value="Request Tutoring">
                    </form>
                </li>
            <?php endwhile; ?>
        </ul>
        <br>
        <a href="view_courses.php">Back to Courses</a>
    </div>
</body>
</html>
