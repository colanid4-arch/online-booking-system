<?php

require_once "../config/db_conn.php";
require_once "../config/session.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: appointments.php");
    exit();
}

$appointment_id = intval($_POST['appointment_id'] ?? 0);
$status = $_POST['status'] ?? '';

$allowed_statuses = ['Pending', 'Confirmed', 'Completed', 'Cancelled', 'No Show'];

if ($appointment_id <= 0 || !in_array($status, $allowed_statuses)) {
    $_SESSION['error_message'] = "Invalid appointment update request.";
    header("Location: appointments.php");
    exit();
}

$status_safe = mysqli_real_escape_string($conn, $status);

$sql = "
    UPDATE appointments
    SET status = '$status_safe'
    WHERE appointment_id = $appointment_id
";

if (mysqli_query($conn, $sql)) {
    $_SESSION['success_message'] = "Appointment status updated successfully.";
} else {
    $_SESSION['error_message'] = "Failed to update appointment status.";
}

header("Location: appointments.php");
exit();
