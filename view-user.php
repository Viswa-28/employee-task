<?php 

include 'config/config.php';
include 'include/header.php';
session_start();

if (!isset($_SESSION['email']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}
if(isset($_GET['id'])){
    $user_id = $_GET['id'];
    $sql = "SELECT * FROM users WHERE id='$user_id' AND role='employee'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
    } else {
        echo "<script>alert('User not found or not an employee.'); window.location.href='admin-dashboard.php';</script>";
        exit();
    }
} else {
    echo "<script>alert('No user ID provided.'); window.location.href='admin-dashboard.php';</script>";
    exit();
}
$work_sql = "SELECT SUM(total_hours) AS total_hours FROM attendance WHERE user_id='$user_id'";
$work_result = mysqli_query($conn, $work_sql);
if (mysqli_num_rows($work_result) == 1) {
    $work = mysqli_fetch_assoc($work_result);
   $user['total_hours'] = $work['total_hours'];


if ($user['total_hours'] == NULL) {
    $user['total_hours'] = "00:00:00";
}
if (strpos($user['total_hours'], ':') !== false) {
    list($h, $m, $s) = explode(':', $user['total_hours']);
    $total_seconds = ($h * 3600) + ($m * 60) + $s;
} else {
    $total_seconds = intval($user['total_hours']);
}

if ($total_seconds < 60) {
    $display_hours = $total_seconds . " seconds";
} elseif ($total_seconds < 3600) {
    $minutes = round($total_seconds / 60, 2);
    $display_hours = $minutes . " minutes";
} else {
    $hours = round($total_seconds / 3600, 2);
    $display_hours = $hours . " hours";
}


}

?>

<div class="back position-absolute top-0 start-0 m-3">
    <a href="admin-dashboard.php" class="btn btn-secondary btn-sm mt-3 ms-3">Back to Dashboard</a>
</div>

<div class="container mt-5">
    <h2 class="text-center mb-4 text-white">View User Details</h2>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">User Information</h5>
            <p class="card-text"><strong>Name:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
            <p class="card-text"><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p class="card-text"><strong>Role:</strong> <?php echo htmlspecialchars($user['role']); ?></p>
          <div class="row">
            <div class="col-md-6">
                <h3>Working Hours</h3>
                <p><?php echo htmlspecialchars($display_hours); ?></p>
          </div>
        </div>
    </div>
</div>
