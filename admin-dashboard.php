<?php
session_start();
if (!isset($_SESSION['email']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}
include('./config/config.php');
include('./include/header.php');

$email = $_SESSION['email'];
$sql = "SELECT * FROM users WHERE email='$email'";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);
$name = $user['name'];
$id = $user['id'];
$date = date("Y-m-d");
?>

<div class="container-fluid bg-dashboard vh-100 d-flex flex-column">
    <div class="container rounded p-5 mt-5 text-white">
        <h1 class="text-center mb-4">Admin Dashboard</h1>
        <h3 class="text-center">Welcome, <?php echo htmlspecialchars($name); ?>!</h3>
        <p class="text-center">Your email: <?php echo htmlspecialchars($user['email']); ?></p>

        <hr class="bg-light">

        <h4 class="mt-5 mb-3 text-center">All Registered Users</h4>
        <table class="table table-striped table-dark text-center">
            <thead>
                <tr>
                    <th>id</th>
                    <th>UserID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $allUsers = mysqli_query($conn, "SELECT * FROM users ORDER BY role DESC, name ASC");
                if (mysqli_num_rows($allUsers) > 0) {
                    $counter = 1;
                    while ($row = mysqli_fetch_assoc($allUsers)) {
                        echo "<tr>";
                        echo "<td>" . $counter++ . "</td>";
                        echo "<td>" . $row['id'] . "</td>";
                        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                        echo "<td><span class='badge bg-" . 
                            ($row['role'] == 'admin' ? "danger" : "success") . "'>" . 
                            htmlspecialchars($row['role']) . "</span></td>";
                        if($row['role']=='employee'){
                            echo "<td><a href='view-user.php?id=" . $row['id'] . "' class='btn btn-sm btn-outline-success' ;\">view</a></td>";
                        } else {
                            echo "<td></td>";
                        }
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No users found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
