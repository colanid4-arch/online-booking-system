<?php
session_start();
require_once "config/db_conn.php";

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $first_name = trim($_POST["first_name"]);
    $last_name = trim($_POST["last_name"]);
    $email = trim($_POST["email"]);
    $phone = trim($_POST["phone"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    if (empty($first_name) || empty($last_name) || empty($email) || empty($password)) {
        $error = "Please fill in all required fields.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        $email_safe = mysqli_real_escape_string($conn, $email);

        $check_sql = "SELECT user_id FROM users WHERE email = '$email_safe'";
        $check_result = mysqli_query($conn, $check_sql);

        if (mysqli_num_rows($check_result) > 0) {
            $error = "An account with this email already exists.";
        } else {
            $first_name = mysqli_real_escape_string($conn, $first_name);
            $last_name = mysqli_real_escape_string($conn, $last_name);
            $phone = mysqli_real_escape_string($conn, $phone);
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            $sql = "
                INSERT INTO users (first_name, last_name, email, phone, password_hash, role)
                VALUES ('$first_name', '$last_name', '$email_safe', '$phone', '$password_hash', 'customer')
            ";

            if (mysqli_query($conn, $sql)) {
                $success = "Account created successfully. You can now login.";
            } else {
                $error = "Registration failed: " . mysqli_error($conn);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Create Account</title>
    <?php include "header.php"; ?>
</head>

<body>

    <section class="section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-5">

                    <div class="card p-4">

                        <h3 class="gold text-center mb-3">Create Your Account</h3>

                        <p class="text-center text-secondary">
                            Join our platform to easily book and manage your barbershop appointments.
                        </p>

                        <?php if (!empty($error)) { ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php } ?>

                        <?php if (!empty($success)) { ?>
                            <div class="alert alert-success"><?php echo $success; ?></div>
                        <?php } ?>

                        <form method="POST">

                            <div class="mb-3">
                                <label class="form-label">First Name</label>
                                <input type="text" name="first_name" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Last Name</label>
                                <input type="text" name="last_name" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Email Address</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Phone Number</label>
                                <input type="text" name="phone" class="form-control">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Confirm Password</label>
                                <input type="password" name="confirm_password" class="form-control" required>
                            </div>

                            <button type="submit" class="btn btn-gold w-100">
                                Create Account
                            </button>

                        </form>

                        <p class="text-center mt-3">
                            Already have an account?
                            <a href="login.php" class="gold">Sign In</a>
                        </p>

                    </div>

                </div>
            </div>
        </div>
    </section>

</body>

</html>