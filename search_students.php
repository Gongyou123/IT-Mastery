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

$students_result = null;
if (isset($_POST['search'])) {
    $search_term = $_POST['search_term'];
    $sql = "SELECT id, fullname FROM students WHERE fullname LIKE ?";
    $stmt = $conn->prepare($sql);
    $like_search_term = '%' . $search_term . '%';
    $stmt->bind_param("s", $like_search_term);
    $stmt->execute();
    $students_result = $stmt->get_result();
}

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
    <title>Search Students</title>
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
            max-width: 600px;
            width: 100%;
        }

        h2, h3 {
            font-size: 28px;
            color: #34495e;
            margin-bottom: 20px;
        }

        form {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-size: 16px;
            margin-bottom: 8px;
            text-align: left;
        }

        input[type="text"], select {
            width: calc(100% - 20px);
            padding: 10px;
            font-size: 16px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            display: inline-block;
            text-decoration: none;
            color: white;
            background-color: #3498db;
            padding: 12px 24px;
            border-radius: 8px;
            transition: background-color 0.3s;
            font-size: 16px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #2980b9;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
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

        @media (max-width: 600px) {
            .container {
                padding: 20px;
                max-width: 90%;
            }

            h2, h3 {
                font-size: 24px;
            }

            input[type="text"], select {
                width: calc(100% - 20px);
                font-size: 14px;
                padding: 10px;
            }

            input[type="submit"] {
                font-size: 14px;
                padding: 10px 20px;
            }

            label {
                font-size: 14px;
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
        <a href="search_students.php">Search Students</a>
        <a href="view_student_requests.php">View Student Requests</a>
        <form action="logout.php" method="post" style="display: inline;">
            <input type="submit" value="Logout" class="logout-button">
        </form>
    </div>

    <div class="container">
        <h2>Search Students</h2>
        <form action="search_students.php" method="post">
            <label for="search_term">Search for students:</label>
            <input type="text" id="search_term" name="search_term" required>
            <input type="submit" name="search" value="Search">
        </form>

        <?php if ($students_result): ?>
            <h3>Search Results:</h3>
            <table>
                <tr>
                    <th>Full Name</th>
                    <th>Action</th>
                </tr>
                <?php while ($row = $students_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['fullname']); ?></td>
                        <td>
                            <form action="teacher_view_student_profile.php" method="get" style="display:inline-block;">
                                <input type="hidden" name="student_id" value="<?php echo $row['id']; ?>">
                                <input type="submit" value="View Profile">
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php endif; ?>
        <br>
        <a href="teacher_home.php" class="button">Back to Home</a>
    </div>
</body>
</html>

<?php
$conn->close();
?>
