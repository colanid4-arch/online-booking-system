<?php
require_once "config/db_conn.php";

$barber_id = intval($_POST['barber_id'] ?? 0);
$hairstyle_id = intval($_POST['hairstyle_id'] ?? 0);
$appointment_date = $_POST['appointment_date'] ?? '';

if ($barber_id <= 0 || $hairstyle_id <= 0 || empty($appointment_date)) {
    echo "<p class='text-secondary'>Select hairstyle, barber and date to view available slots.</p>";
    exit();
}

$style_sql = "SELECT duration_minutes FROM hairstyles WHERE hairstyle_id = $hairstyle_id";
$style_result = mysqli_query($conn, $style_sql);

if (!$style_result || mysqli_num_rows($style_result) == 0) {
    echo "<p class='text-danger'>Invalid hairstyle selected.</p>";
    exit();
}

$style = mysqli_fetch_assoc($style_result);
$duration = intval($style['duration_minutes']);

$opening_time = "08:00";
$closing_time = "17:00";
$interval = 15;

$current = strtotime($opening_time);
$closing = strtotime($closing_time);

while ($current < $closing) {
    $slot_start = date("H:i:s", $current);
    $slot_label = date("H:i", $current);
    $slot_end = date("H:i:s", strtotime("+$duration minutes", $current));

    if (strtotime($slot_end) > $closing) {
        break;
    }

    $appointment_sql = "
        SELECT appointment_id
        FROM appointments
        WHERE barber_id = $barber_id
        AND appointment_date = '$appointment_date'
        AND status IN ('Pending', 'Confirmed')
        AND (
            start_time < '$slot_end'
            AND end_time > '$slot_start'
        )
    ";

    $appointment_result = mysqli_query($conn, $appointment_sql);

    $unavailable_sql = "
        SELECT unavailability_id
        FROM barber_unavailability
        WHERE barber_id = $barber_id
        AND unavailable_date = '$appointment_date'
        AND (
            is_full_day = 1
            OR (
                start_time < '$slot_end'
                AND end_time > '$slot_start'
            )
        )
    ";

    $unavailable_result = mysqli_query($conn, $unavailable_sql);

    $available = mysqli_num_rows($appointment_result) == 0 && mysqli_num_rows($unavailable_result) == 0;

    echo "<div class='col-4 col-md-3'>";

    if ($available) {
        echo "
            <button type='button' class='slot-btn time-slot' data-time='$slot_label'>
                $slot_label
            </button>
        ";
    } else {
        echo "
            <button type='button' class='slot-btn slot-unavailable' disabled>
                $slot_label
            </button>
        ";
    }

    echo "</div>";

    $current = strtotime("+$interval minutes", $current);
}
