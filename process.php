<?php
session_start();

// Database connection (update with your DB credentials)
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "aqi";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Review Information</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .center-box {
            background-color: #fff;
            padding: 30px 40px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            width: 400px;
            text-align: left;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 22px;
            font-weight: bold;
        }

        ul {
            list-style: none;
            padding: 0;
            margin-bottom: 20px;
        }

        li {
            margin-bottom: 10px;
            font-size: 14px;
        }

        strong {
            display: inline-block;
            width: 120px;
        }

        form {
            display: flex;
            justify-content: space-between;
        }

        button {
            width: 48%;
            padding: 10px;
            background-color: #4285f4;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
        }

        button:hover {
            background-color: #357ae8;
        }

        .error-message {
            color: red;
            text-align: center;
            margin-top: 10px;
        }

        .info-message {
            text-align: center;
            color: green;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="center-box">
        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['final_submit'])) {
            $_SESSION['form_data'] = $_POST;
            ?>
            <h2>Review Your Information</h2>
            <ul>
                <li><strong>Name:</strong> <?php echo htmlspecialchars($_POST['fname']); ?></li>
                <li><strong>Email:</strong> <?php echo htmlspecialchars($_POST['email']); ?></li>
                <li><strong>Gender:</strong> <?php echo htmlspecialchars($_POST['gender']); ?></li>
                <li><strong>Date of Birth:</strong> <?php echo htmlspecialchars($_POST['dob']); ?></li>
                <li><strong>Country:</strong> <?php echo htmlspecialchars($_POST['Country']); ?></li>
                <li><strong>Terms:</strong> <?php echo isset($_POST['terms']) ? "Agreed" : "Not Agreed"; ?></li>
                <li><strong>Opinion:</strong> <?php echo htmlspecialchars($_POST['opinion']); ?></li>
            </ul>
            <form method="post">
                <button type="submit" name="final_submit" value="confirm">Confirm</button>
                <button type="submit" name="final_submit" value="cancel">Cancel</button>
            </form>
            <?php
        } elseif (isset($_POST['final_submit'])) {
            if ($_POST['final_submit'] === 'confirm') {
                $data = $_SESSION['form_data'];

                if (isset($data['bgcolor'])) {
                    setcookie('aqi_bgcolor', $data['bgcolor'], time() + (86400 * 30), "/");
                }

                $stmt = $conn->prepare("INSERT INTO user (Name, Email, Gender, Dob, Country, Opinion, Password) VALUES (?, ?, ?, ?, ?, ?, ?)");
                if (!$stmt) {
                    die("<p class='error-message'>Prepare failed: " . $conn->error . "</p>");
                }
                $stmt->bind_param(
                    "sssssss",
                    $data['fname'],
                    $data['email'],
                    $data['gender'],
                    $data['dob'],
                    $data['Country'],
                    $data['opinion'],
                    $data['cpassword']
                );

                if ($stmt->execute()) {
                    unset($_SESSION['form_data']);
                    session_write_close();
                    header("Location: request.php");
                    exit();
                } else {
                    echo "<p class='error-message'>Error saving registration: " . htmlspecialchars($stmt->error) . "</p>";
                }
                $stmt->close();
                unset($_SESSION['form_data']);
            } else {
                unset($_SESSION['form_data']);
                header("Location: nindex.html");
                exit();
            }
        } else {
            echo "<p class='error-message'>Invalid Request</p>";
        }

        $conn->close();
        ?>
    </div>
</body>
</html>
