<?php
session_start();
// if (!isset($_SESSION['email']) || $_SESSION['is_admin']) {
//     header("Location: index.php");
//     exit();
// }
include('./config/config.php');
include('./include/header.php');

$email = $_SESSION['email'];
$sql = "SELECT * FROM users WHERE email='$email'";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);
$name = $user['name'];
$id = $user['id'];
$date = date("Y-m-d");
$checkin_time = date("H:i:s");
$checckout_time = date("H:i:s");
$total_hours = "00:00:00";

if (isset($_POST['checkin'])) {
    date_default_timezone_set('Asia/Kolkata');
    $email = $_SESSION['email'];
    $date = date("Y-m-d");
    $checkin_time = date("H:i:s");


    $user_result = mysqli_query($conn, "SELECT id FROM users WHERE email='$email'");
    if (!$user_result) {
        die("User query failed: " . mysqli_error($conn));
    }
    $user = mysqli_fetch_assoc($user_result);
    $id = $user['id'];


    $check = mysqli_query($conn, "SELECT * FROM attendance WHERE user_id='$id' AND date='$date'");
    if (mysqli_num_rows($check) > 0) {
        echo "<script>alert('You have already checked in today.');</script>";
        header("Location: user-dashboard.php");
        exit();
    } else {

        $sql_checkin = "INSERT INTO attendance (user_id, date, checkin_time, checkout_time, total_hours)
                        VALUES ('$id', '$date', '$checkin_time', NULL, NULL)";

        if (!mysqli_query($conn, $sql_checkin)) {
            die("Check-in failed: " . mysqli_error($conn));
        }

        header("Location: user-dashboard.php");
        exit();
    }
}

if (isset($_POST['checkout'])) {
    date_default_timezone_set('Asia/Kolkata');
    $email = $_SESSION['email'];
    $date = date("Y-m-d");
    $checkout_time = date("H:i:s");
    $user_result = mysqli_query($conn, "SELECT id FROM users WHERE email='$email'");
    if (!$user_result) {
        die("User query failed: " . mysqli_error($conn));
    }
    $user = mysqli_fetch_assoc($user_result);
    $id = $user['id'];
    $attendance_result = mysqli_query($conn, "SELECT * FROM attendance WHERE user_id='$id' AND date='$date'");
    if (mysqli_num_rows($attendance_result) == 0) {
        echo "<script>alert('You have not checked in today.');</script>";
        header("Location: user-dashboard.php");
        exit();
    } else {
        $attendance = mysqli_fetch_assoc($attendance_result);
        $checkin_time = $attendance['checkin_time'];
        $diff_seconds = strtotime($checkout_time) - strtotime($checkin_time);
        $total_hours = gmdate('H:i:s', $diff_seconds);

        $sql_checkout = "UPDATE attendance SET checkout_time = '$checkout_time', total_hours = '$total_hours' WHERE user_id = '$id' AND date = '$date'";
        if (!mysqli_query($conn, $sql_checkout)) {
            die("Check-out failed: " . mysqli_error($conn));
        }
        header("Location: user-dashboard.php");
        exit();
    }
}


?>
<div class="main">
    <div class="container mt-5 text-white d-flex flex-column align-items-center">
        <h1>Welcome, <?php echo htmlspecialchars($user['name']); ?>!</h1>
        <p>Your email: <?php echo htmlspecialchars($user['email']); ?></p>

        <form action="user-dashboard.php" method="POST">
            <button type="submit" name="checkin" class="success m-2 ">Check In</button>
            <button type="submit" name="checkout" class="danger m-2 ">Check Out</button>
        </form>
        <?php
        if($total_hours>"00:00:00") {
            echo "<p>Total Hours Worked Today: " . htmlspecialchars($total_hours) . "</p>";
        }

?>
        <a href="logout.php" onclick="checkLogout()" class="danger">Logout</a>

    </div>


    <script>
        function checkLogout() {
            if (confirm("Are you sure you want to log out?")) {
                window.location.href = "logout.php";
            }
        }

      
    </script>