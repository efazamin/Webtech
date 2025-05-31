<?php
session_start();
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "aqi";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['loginPassword']);

    // Prepare statement to check for matching Name or Email and password
    $stmt = $conn->prepare("SELECT * FROM user WHERE (Name = ? OR Email = ?) AND Password = ?");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("sss", $username, $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        // Login successful
        $_SESSION['user'] = $result->fetch_assoc();
        header("Location: request.php"); 
        exit();
    } else {
        // Login failed
        echo "<p style='color:red;'>Invalid username/email or password.</p>";
        echo "<a href='nindex.html#login'>Back to Login</a>";
    }
    $stmt->close();
}
$conn->close();
?>