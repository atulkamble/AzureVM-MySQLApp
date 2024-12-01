# azureVM-Mysql-Website

Here’s a basic guide to deploy a simple web application with a MySQL database on an Azure Virtual Machine (VM):  

---

### **Setup Plan**
1. **Create Azure VM**
2. **Install Web Server (Apache or Nginx)**
3. **Install MySQL Server**
4. **Deploy the Web Application**
5. **Test Application**

---

### **Step-by-Step Guide**

#### **1. Create an Azure VM**
1. Log in to the [Azure Portal](https://portal.azure.com/).
2. Navigate to **Virtual Machines** > **Create**.
3. Configure the VM:
   - **OS**: Ubuntu Server 20.04 (or another OS of your choice).
   - **Size**: Select a size suitable for a web app, e.g., `B1ms`.
   - **Authentication**: Use SSH or password for login.
4. Open the necessary ports:
   - HTTP: 80
   - MySQL: 3306 (if needed externally)
5. Create and start the VM.

---

#### **2. Install Web Server**
1. SSH into the VM:
   ```bash
   ssh <username>@<VM_Public_IP>
   ```
2. Update the package list and install Apache (or Nginx):
   ```bash
   sudo apt update
   sudo apt install apache2 -y  # For Apache
   sudo apt install nginx -y    # For Nginx
   ```
3. Verify the server:
   - Open the public IP in a browser. You should see the default web page.

---

#### **3. Install MySQL**
1. Install MySQL Server:
   ```bash
   sudo apt install mysql-server -y
   ```
2. Secure the installation:
   ```bash
   sudo mysql_secure_installation
   ```
3. Log in to MySQL and create a database:
   ```bash
   sudo mysql -u root -p
   CREATE DATABASE appdb;
   CREATE USER 'appuser'@'localhost' IDENTIFIED BY 'password';
   GRANT ALL PRIVILEGES ON appdb.* TO 'appuser'@'localhost';
   FLUSH PRIVILEGES;
   EXIT;
   ```

---

#### **4. Deploy the Web Application**
1. Clone or upload your app:
   - Use `scp` or tools like Filezilla.
   ```bash
   scp -r /path/to/app <username>@<VM_Public_IP>:/var/www/html/
   ```
2. Configure the application:
   - Update database connection settings in your app’s config file to use the MySQL database:
     ```php
     $servername = "localhost";
     $username = "appuser";
     $password = "password";
     $dbname = "appdb";
     ```
3. Set proper permissions for the web folder:
   ```bash
   sudo chown -R www-data:www-data /var/www/html/
   sudo chmod -R 755 /var/www/html/
   ```

---

#### **5. Test the Application**
1. Restart the web server:
   ```bash
   sudo systemctl restart apache2  # For Apache
   sudo systemctl restart nginx    # For Nginx
   ```
2. Access the application in your browser:
   ```bash
   http://<VM_Public_IP>
   ```

---

### **Optional Enhancements**
- **SSL:** Secure your app with Let's Encrypt.
  ```bash
  sudo apt install certbot python3-certbot-apache
  sudo certbot --apache
  ```
- **Firewall Configuration:**
  Ensure your VM firewall allows traffic to ports `80` and `3306`.

This setup creates a basic environment. For scalability, you can later explore **Azure App Services**, **Managed MySQL**, or **Containerized Deployment** using Docker.


Here’s a basic example of a PHP web application that connects to a MySQL database, displays data from a table, and allows the insertion of new records.  

---

### **Database Schema**
First, create a table in the MySQL database:

```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

---

### **Application Code**

Save the following files in your web server directory (e.g., `/var/www/html/`).

---

#### **1. `config.php`**
Defines the database connection.

```php
<?php
// Database configuration
$servername = "localhost";
$username = "appuser";
$password = "password";
$dbname = "appdb";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
```

---

#### **2. `index.php`**
Displays data from the database.

```php
<?php
include 'config.php';

// Fetch users from the database
$sql = "SELECT * FROM users";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>User List</title>
</head>
<body>
    <h1>User List</h1>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Created At</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['id']}</td>
                        <td>{$row['name']}</td>
                        <td>{$row['email']}</td>
                        <td>{$row['created_at']}</td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='4'>No users found</td></tr>";
        }
        ?>
    </table>
    <br>
    <a href="add_user.php">Add New User</a>
</body>
</html>
```

---

#### **3. `add_user.php`**
Form to add a new user to the database.

```php
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
```

---

#### **4. `save_user.php`**
Handles user form submission and inserts data into the database.

```php
<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];

    // Insert user into the database
    $sql = "INSERT INTO users (name, email) VALUES ('$name', '$email')";

    if ($conn->query($sql) === TRUE) {
        echo "New user added successfully.";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    echo '<br><a href="index.php">Back to User List</a>';
}

$conn->close();
?>
```

---

### **Testing**
1. Navigate to `http://<VM_Public_IP>/index.php` to see the user list.
2. Click **Add New User** to add a user and verify it appears in the list.

---

### **Security Considerations**
- Use prepared statements to prevent SQL injection.
- Use environment variables for storing sensitive data like database credentials.
- Configure proper file and folder permissions.
