<?php
session_start();

// Database connection
$con = mysqli_connect("localhost", "root", "", "aqi");
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch unique countries from INFO table
$sql = "SELECT DISTINCT country FROM INFO ORDER BY country ASC";
$result = mysqli_query($con, $sql);

$countries = [];
while ($row = mysqli_fetch_assoc($result)) {
    $countries[] = $row['country'];
}

if (isset($_SESSION['user'])) {
    $welcomeText = "<h3>Welcome, " . htmlspecialchars($_SESSION['user']['Name']) .
         " <a href='logout.php' style='font-size:14px; color:red; text-decoration:none;'>LOGOUT</a></h3>";
} else {
    header("Location: nindex.html#login");
    exit();
}

// Handle form submission
$error = "";
$selected = [];
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $selected = isset($_POST['countries']) ? $_POST['countries'] : [];
    if (count($selected) != 10) {
        $error = "Please select exactly 10 countries.";
    } else {
        $_SESSION['selected_countries'] = $selected;
        header("Location: showaqi.php");
        exit();
    }
}

mysqli_close($con);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Select 10 Countries</title>
    <style>
        body {
            margin: 0;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
        }

        .container {
            background-color: white;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            text-align: center;
            max-height: 90vh;
            overflow-y: auto;
        }

        .checkbox-column {
            display: flex;
            flex-direction: column;
            gap: 8px;
            max-height: 300px;
            overflow-y: auto;
            align-items: flex-start;
            margin: 0 auto;
        }

        input[type="submit"] {
            margin-top: 20px;
            padding: 8px 16px;
            font-size: 16px;
            cursor: pointer;
        }

        p {
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php echo $welcomeText; ?>
        <h2>Select Exactly 10 Countries</h2>
        <form method="post">
            <div class="checkbox-column" id="country-checkboxes">
                <?php foreach ($countries as $country): ?>
                    <label>
                        <input type="checkbox" name="countries[]" value="<?php echo htmlspecialchars($country); ?>"
                            <?php if (in_array($country, $selected)) echo 'checked'; ?>>
                        <?php echo htmlspecialchars($country); ?>
                    </label>
                <?php endforeach; ?>
            </div>
            <input type="submit" value="Submit">
        </form>
        <?php if ($error): ?>
            <p style="color:red;"><?php echo $error; ?></p>
        <?php endif; ?>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkboxes = document.querySelectorAll('#country-checkboxes input[type="checkbox"]');
        checkboxes.forEach(cb => {
            cb.addEventListener('change', function() {
                const checked = document.querySelectorAll('#country-checkboxes input[type="checkbox"]:checked');
                if (checked.length >= 10) {
                    checkboxes.forEach(box => {
                        if (!box.checked) box.disabled = true;
                    });
                } else {
                    checkboxes.forEach(box => box.disabled = false);
                }
            });
        });

        // On page load, enforce the rule if already checked
        const checked = document.querySelectorAll('#country-checkboxes input[type="checkbox"]:checked');
        if (checked.length >= 10) {
            checkboxes.forEach(box => {
                if (!box.checked) box.disabled = true;
            });
        }
    });
    </script>
</body>
</html>
