<?php 
session_start();
include 'config/config.php';
include 'include/header.php';


if (!isset($_SESSION['email']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}


if (isset($_GET['id'])) {
    $user_id = intval($_GET['id']);
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


$from_date = isset($_GET['from']) ? $_GET['from'] : date('Y-m-01'); 
$to_date   = isset($_GET['to']) ? $_GET['to'] : date('Y-m-d');      


$attendance_sql = "SELECT * FROM attendance 
                   WHERE user_id='$user_id' 
                   AND date BETWEEN '$from_date' AND '$to_date'
                   ORDER BY date DESC";
$attendance_result = mysqli_query($conn, $attendance_sql);


$work_sql = "SELECT SEC_TO_TIME(SUM(TIME_TO_SEC(total_hours))) AS total_hours 
             FROM attendance 
             WHERE user_id='$user_id' 
             AND date BETWEEN '$from_date' AND '$to_date'";
$work_result = mysqli_query($conn, $work_sql);

$display_hours = "00:00:00";

if ($work_result && mysqli_num_rows($work_result) == 1) {
    $work = mysqli_fetch_assoc($work_result);
    $user['total_hours'] = $work['total_hours'] ?: "00:00:00";

    // Convert HH:MM:SS → seconds
    if (strpos($user['total_hours'], ':') !== false) {
        list($h, $m, $s) = explode(':', $user['total_hours']);
        $total_seconds = ($h * 3600) + ($m * 60) + $s;
    } else {
        $total_seconds = intval($user['total_hours']);
    }

    // Determine display unit
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


<div class="position-absolute top-0 start-0 m-3">
    <a href="admin-dashboard.php" class="btn btn-secondary btn-sm mt-3 ms-3">Back to Dashboard</a>
</div>


<div class="container mt-5">
    <h2 class="text-center mb-4 text-white">View User Details</h2>

    <div class="card shadow-lg">
        <div class="card-body">
            <h5 class="card-title mb-3">User Information</h5>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>Role:</strong> <?php echo htmlspecialchars($user['role']); ?></p>

            <div class="mt-4">
                <h5><strong>Total Working Hours (<?php echo $from_date . " to " . $to_date; ?>):</strong></h5>
                <p class="fw-bold"><?php echo htmlspecialchars($display_hours); ?></p>
            </div>
        </div>
    </div>

   
    <div class="mt-5">
        <form method="GET" class="row g-3 align-items-center text-white">
            <input type="hidden" name="id" value="<?php echo $user_id; ?>">
            <div class="col-auto">
                <label for="from" class="col-form-label">From:</label>
            </div>
            <div class="col-auto">
                <input type="date" class="form-control" name="from" value="<?php echo $from_date; ?>">
            </div>
            <div class="col-auto">
                <label for="to" class="col-form-label">To:</label>
            </div>
            <div class="col-auto">
                <input type="date" class="form-control" name="to" value="<?php echo $to_date; ?>">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="view-user.php?id=<?php echo $user_id; ?>" class="btn btn-secondary">Reset</a>
            </div>
        </form>
    </div>

   
    <div class="mt-4">
        <h3 class="text-white mb-3">Attendance Records</h3>
        <table class="table table-dark table-striped text-center">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Check-in</th>
                    <th>Check-out</th>
                    <th>Total Hours</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($attendance_result && mysqli_num_rows($attendance_result) > 0) {
                    while ($row = mysqli_fetch_assoc($attendance_result)) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['date']) . "</td>";
                        echo "<td>" . ($row['checkin_time'] ?: '-') . "</td>";
                        echo "<td>" . ($row['checkout_time'] ?: '-') . "</td>";
                        echo "<td>" . ($row['total_hours'] ?: '-') . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>No attendance records found for this date range.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
