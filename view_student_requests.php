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

if (isset($_POST['action']) && isset($_POST['request_id'])) {
    $request_id = $_POST['request_id'];
    $action = $_POST['action'];

    $status = ($action == 'accept') ? 'accepted' : 'rejected';

    $sql = "UPDATE student_requests SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status, $request_id);

    if ($stmt->execute()) {
        if ($status == 'accepted') {
            // Move the student to student_courses
            $sql = "INSERT INTO student_courses (student_id, course_id, teacher_id)
                    SELECT student_id, course_id, teacher_id
                    FROM student_requests
                    WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $request_id);
            $stmt->execute();
        }
        echo "<script type='text/javascript'>alert('Request processed successfully');</script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$sql = "SELECT sr.id, s.fullname, c.course_name, sr.status 
        FROM student_requests sr
        JOIN students s ON sr.student_id = s.id
        JOIN courses c ON sr.course_id = c.id
        WHERE sr.teacher_id = ? AND sr.status = 'pending'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$requests_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Student Requests</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #ece9e6, #ffffff);
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
            color: #2c3e50;
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
            background-color: #ffffff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 800px;
            width: 100%;
        }

        h2 {
            font-size: 28px;
            color: #34495e;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #ddd;
        }

        form {
            display: inline-block;
            margin: 0;
        }

        input[type="submit"] {
            display: inline-block;
            text-decoration: none;
            color: white;
            background-color: #3498db;
            padding: 10px 20px;
            border-radius: 8px;
            transition: background-color 0.3s;
            font-size: 14px;
            cursor: pointer;
            margin: 0 5px;
        }

        input[type="submit"]:hover {
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

            th, td {
                padding: 8px;
                font-size: 14px;
            }

            input[type="submit"] {
                font-size: 12px;
                padding: 8px 16px;
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

    <div class="container">
        <h2>Student Requests</h2>
        <?php if ($requests_result->num_rows > 0): ?>
            <table>
                <tr>
                    <th>Student Name</th>
                    <th>Course</th>
                    <th>Action</th>
                </tr>
                <?php while ($row = $requests_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['fullname']); ?></td>
                        <td><?php echo htmlspecialchars($row['course_name']); ?></td>
                        <td>
                            <form action="view_student_requests.php" method="post">
                                <input type="hidden" name="request_id" value="<?php echo $row['id']; ?>">
                                <input type="submit" name="action" value="accept">
                                <input type="submit" name="action" value="reject">
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>No pending requests.</p>
        <?php endif; ?>
        <br>
        <a href="teacher_home.php" class="button">Back to Home</a>
    </div>
</body>
</html>

<?php
$conn->close();
?>
