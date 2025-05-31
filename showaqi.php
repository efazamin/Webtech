<?php
session_start();
if (!isset($_SESSION['selected_countries']) || count($_SESSION['selected_countries']) != 10) {
    echo "Invalid access or not enough countries selected.";
    exit();
}

// Database connection
$con = mysqli_connect("localhost", "root", "", "AQI");
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

// Prepare country list for SQL IN clause
$selected = array_map('mysqli_real_escape_string', array_fill(0, count($_SESSION['selected_countries']), $con), $_SESSION['selected_countries']);
$in = "'" . implode("','", $selected) . "'";

// Fetch info for selected countries
$sql = "SELECT city, country, aqi FROM INFO WHERE country IN ($in) ORDER BY country, city";
$result = mysqli_query($con, $sql);

$bgcolor = isset($_COOKIE['aqi_bgcolor']) ? $_COOKIE['aqi_bgcolor'] : '#ffffff';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>AQI Table</title>
    <style>
        body {
            margin: 0;
            height: 100vh;
            background-color: <?php echo htmlspecialchars($bgcolor); ?>;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: Arial, sans-serif;
        }

        .aqi-table-container {
            background-color: white;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-height: 90vh;
            overflow-y: auto;
        }

        h2 {
            margin-top: 10px;
            margin-bottom: 20px;
        }

        h3 {
            margin-bottom: 10px;
        }

        a.logout {
            font-size: 14px;
            color: red;
            text-decoration: none;
            margin-left: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: center;
        }

        th {
            background-color: #f0f0f0;
        }

        @media (max-width: 600px) {
            .aqi-table-container {
                width: 95%;
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="aqi-table-container">
        <?php if (isset($_SESSION['user'])): ?>
            <h3>
                Welcome, <?php echo htmlspecialchars($_SESSION['user']['Name']); ?>
                <a href='logout.php' class="logout">LOGOUT</a>
            </h3>
        <?php else:
            header("Location: nindex.html#login");
            exit();
        endif; ?>

        <h2>Selected Countries AQI Information</h2>
        <table>
            <thead>
                <tr>
                    <th>City</th>
                    <th>Country</th>
                    <th>AQI</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['city']); ?></td>
                        <td><?php echo htmlspecialchars($row['country']); ?></td>
                        <td><?php echo htmlspecialchars($row['aqi']); ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
mysqli_close($con);
?>
