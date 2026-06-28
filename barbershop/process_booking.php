<?php

session_start();
require_once "config/db_conn.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: booking.php");
    exit();
}

$user_id = intval($_SESSION['user_id']);

$branch_id = intval($_POST['branch_id'] ?? 0);
$hairstyle_id = intval($_POST['hairstyle_id'] ?? 0);
$barber_id = intval($_POST['barber_id'] ?? 0);
$appointment_date = $_POST['appointment_date'] ?? '';
$start_time = $_POST['start_time'] ?? '';

if (
    $branch_id <= 0 ||
    $hairstyle_id <= 0 ||
    $barber_id <= 0 ||
    empty($appointment_date) ||
    empty($start_time)
) {
    die("Please complete all booking details before confirming.");
}

$appointment_date = mysqli_real_escape_string($conn, $appointment_date);
$start_time = date("H:i:s", strtotime($start_time));

$hairstyle_sql = "
    SELECT duration_minutes 
    FROM hairstyles 
    WHERE hairstyle_id = $hairstyle_id
";

$hairstyle_result = mysqli_query($conn, $hairstyle_sql);

if (!$hairstyle_result || mysqli_num_rows($hairstyle_result) === 0) {
    die("Selected hairstyle does not exist.");
}

$hairstyle = mysqli_fetch_assoc($hairstyle_result);
$duration_minutes = intval($hairstyle['duration_minutes']);

$end_time = date("H:i:s", strtotime($start_time . " +$duration_minutes minutes"));

$opening_time = "08:00:00";
$closing_time = "17:00:00";

if ($start_time < $opening_time || $end_time > $closing_time) {
    die("Selected time is outside business hours.");
}

$barber_check_sql = "
    SELECT barber_id 
    FROM barbers 
    WHERE barber_id = $barber_id 
    AND branch_id = $branch_id 
    AND is_active = 1
";

$barber_check_result = mysqli_query($conn, $barber_check_sql);

if (!$barber_check_result || mysqli_num_rows($barber_check_result) === 0) {
    die("Selected barber does not belong to the selected branch.");
}

$appointment_conflict_sql = "
    SELECT appointment_id
    FROM appointments
    WHERE barber_id = $barber_id
    AND appointment_date = '$appointment_date'
    AND status IN ('Pending', 'Confirmed')
    AND (
        start_time < '$end_time'
        AND end_time > '$start_time'
    )
";

$appointment_conflict_result = mysqli_query($conn, $appointment_conflict_sql);

if ($appointment_conflict_result && mysqli_num_rows($appointment_conflict_result) > 0) {
    die("This barber is already booked during the selected time.");
}

$unavailability_sql = "
    SELECT unavailability_id
    FROM barber_unavailability
    WHERE barber_id = $barber_id
    AND unavailable_date = '$appointment_date'
    AND (
        is_full_day = 1
        OR (
            start_time < '$end_time'
            AND end_time > '$start_time'
        )
    )
";

$unavailability_result = mysqli_query($conn, $unavailability_sql);

if ($unavailability_result && mysqli_num_rows($unavailability_result) > 0) {
    die("This barber is unavailable during the selected time.");
}

$insert_sql = "
    INSERT INTO appointments (
        user_id,
        barber_id,
        branch_id,
        hairstyle_id,
        appointment_date,
        start_time,
        end_time,
        status
    )
    VALUES (
        $user_id,
        $barber_id,
        $branch_id,
        $hairstyle_id,
        '$appointment_date',
        '$start_time',
        '$end_time',
        'Pending'
    )
";

if (mysqli_query($conn, $insert_sql)) {
    $_SESSION['success_message'] = "Your appointment has been booked successfully.";
    header("Location: my_appointments.php");
    exit();
} else {
    die("Booking failed: " . mysqli_error($conn));
}
