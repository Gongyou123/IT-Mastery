<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'user_registration');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['action']) && isset($_POST['application_id'])) {
    $application_id = $_POST['application_id'];
    $action = $_POST['action'];

    $status = ($action == 'accept') ? 'accepted' : 'rejected';

    $sql = "UPDATE teacher_applications SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status, $application_id);

    if ($stmt->execute()) {
        echo "Application $action successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$sql = "SELECT ta.id, t.fullname, ta.resume, ta.status 
        FROM teacher_applications ta 
        JOIN teachers t ON ta.teacher_id = t.id 
        WHERE ta.status = 'pending'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 0;
        }
        .container {
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 5px;
        }
        h2, h3 {
            text-align: center;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
        img.resume-img {
            width: 100px;
            height: auto;
        }
        form {
            display: inline;
        }
        input[type="submit"] {
            margin-left: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Admin Panel</h2>
        <h3>Pending Teacher Applications</h3>
        <table>
            <thead>
                <tr>
                    <th>Full Name</th>
                    <th>Resume</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['fullname']); ?></td>
                        <td>
                            <?php if ($row['resume']): ?>
                                <img src="uploads/<?php echo htmlspecialchars($row['resume']); ?>" alt="Resume" class="resume-img">
                            <?php else: ?>
                                No resume available
                            <?php endif; ?>
                        </td>
                        <td>
                            <form action="admin.php" method="post">
                                <input type="hidden" name="application_id" value="<?php echo $row['id']; ?>">
                                <input type="submit" name="action" value="accept">
                                <input type="submit" name="action" value="reject">
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
$conn->close();
?>
