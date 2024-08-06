<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
</head>
<body>
    <h2>Forgot Password</h2>
    <form action="send_verification.php" method="post">
        <label for="email">Enter your email:</label>
        <input type="email" id="email" name="email" required>
        <label for="user_type">User Type:</label>
        <input type="radio" id="teacher" name="user_type" value="teacher" required>
        <label for="teacher">Teacher</label>
        <input type="radio" id="student" name="user_type" value="student" required>
        <label for="student">Student</label>
        <button type="submit">Submit</button>
    </form>
</body>
</html>
