<?php
require_once "config/db_conn.php";
require_once "config/session.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    $email_safe = mysqli_real_escape_string($conn, $email);

    $sql = "SELECT * FROM users WHERE email = '$email_safe' LIMIT 1";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);

        if (password_verify($password, $user["password_hash"])) {
            $_SESSION["user_id"] = $user["user_id"];
            $_SESSION["first_name"] = $user["first_name"];
            $_SESSION["role"] = $user["role"];

            if ($user["role"] === "admin") {
                header("Location: admin/dashboard.php");
                exit();
            }
            else{
                header("Location: index.php");
            }
        } else {
            $error = "Invalid email or password.";
        }
    } else {
        $error = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Login</title>
    <?php include "header.php"; ?>
</head>

<body>

    <section class="section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-5">

                    <div class="card p-4">

                        <h3 class="gold text-center mb-3">Welcome Back</h3>

                        <p class="text-center text-secondary">
                            Sign in to your account to manage your bookings and schedule your next appointment.
                        </p>

                        <?php if (!empty($error)) { ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php } ?>

                        <form method="POST">

                            <div class="mb-3">
                                <label class="form-label">Email Address</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>

                            <button type="submit" class="btn btn-gold w-100">
                                Login
                            </button>

                        </form>

                        <p class="text-center mt-3">
                            Don't have an account?
                            <a href="register.php" class="gold">Create an Account</a>
                        </p>

                    </div>

                </div>
            </div>
        </div>
    </section>

</body>

</html>