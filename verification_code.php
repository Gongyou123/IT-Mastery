<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enter Verification Code</title>
</head>
<body>
    <h2>Enter Verification Code</h2>
    <form action="verify_code.php" method="post">
        <label for="verification_code">Verification Code:</label>
        <input type="text" id="verification_code" name="verification_code" required>
        <button type="submit">Submit</button>
    </form>
</body>
</html>
