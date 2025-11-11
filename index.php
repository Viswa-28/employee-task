<?php
include 'config/config.php';
include 'include/header.php';
session_start();
$email_err = $password_err = "";
if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email='$email' AND password='$password'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) == 1) {
        $_SESSION['email'] = $email;
        $_SESSION['role'] = mysqli_fetch_assoc($result)['role'];
        if ($_SESSION['role'] == 'admin') {
            header("Location: admin-dashboard.php");
        } else {
            header("Location: user-dashboard.php");
        }
    } else {
        echo "<script>alert('Invalid email or password');</script>";
    }
}
?>


<div class="container rounded p-5 mt-5">
    <h1 class="text-white mt-5 text-center">Login</h1>
    <div class="form-container mx-auto w-50 mt-4 p-4 rounded">
        <form action="index.php" method="POST">
            <div class="mb-3 w-50 mx-auto">
                <label class="form-label text-white">Email</label>
                <input type="text" class="form-control" name="email">
                <!-- <span class="text-danger"><?php echo $email_err; ?></span> -->
            </div>
            <div class="mb-3 w-50 mx-auto">
                <label class="form-label text-white">Password</label>
                <input type="password" class="form-control" name="password">
                <!-- <span class="text-danger"><?php echo $password_err; ?></span> -->
            </div>
            <button type="submit" name="login" class="btn-accent mx-auto d-block">Login</button>
        </form>
        <p class="text-center text-white mt-3">
            Don't have an account?
            <a href="register.php" class="text-primary">Register here</a>
        </p>
    </div>
</div>
