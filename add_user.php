<!DOCTYPE html>
<html>
<head>
    <title>Add User</title>
</head>
<body>
    <h1>Add New User</h1>
    <form method="POST" action="save_user.php">
        <label>Name:</label>
        <input type="text" name="name" required><br><br>
        <label>Email:</label>
        <input type="email" name="email" required><br><br>
        <button type="submit">Add User</button>
    </form>
    <br>
    <a href="index.php">Back to User List</a>
</body>
</html>
