<?php

require_once "config/db_conn.php";
require_once "config/session.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = intval($_SESSION['user_id']);

$sql = "
    SELECT 
        appointments.*,
        hairstyles.hairstyle_name,
        hairstyles.price,
        branches.branch_name,
        barbers.first_name AS barber_first_name,
        barbers.last_name AS barber_last_name
    FROM appointments
    INNER JOIN hairstyles 
        ON appointments.hairstyle_id = hairstyles.hairstyle_id
    INNER JOIN branches 
        ON appointments.branch_id = branches.branch_id
    INNER JOIN barbers 
        ON appointments.barber_id = barbers.barber_id
    WHERE appointments.user_id = $user_id
    ORDER BY appointments.appointment_date DESC, appointments.start_time DESC
";

$result = mysqli_query($conn, $sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>My Appointments</title>
    <?php include "header.php"; ?>

<section class="section">

    <div class="container">

        <div class="text-center mb-5">
            <h1 class="gold">My Appointments</h1>
            <p>
                View your upcoming and past barbershop appointments.
            </p>
        </div>

        <?php if (isset($_SESSION['success_message'])) { ?>

            <div class="alert alert-success">
                <?php
                echo $_SESSION['success_message'];
                unset($_SESSION['success_message']);
                ?>
            </div>

        <?php } ?>

        <?php if (mysqli_num_rows($result) > 0) { ?>

            <div class="row g-4">

                <?php while ($row = mysqli_fetch_assoc($result)) { ?>

                    <div class="col-lg-6">

                        <div class="card p-4 h-100">

                            <div class="d-flex justify-content-between align-items-center mb-3">

                                <h5 class="gold mb-0">
                                    <?php echo $row['hairstyle_name']; ?>
                                </h5>

                                <?php if ($row['status'] == "Pending") { ?>
                                    <span class="badge bg-warning text-dark">Pending</span>
                                <?php } elseif ($row['status'] == "Confirmed") { ?>
                                    <span class="badge bg-success">Confirmed</span>
                                <?php } elseif ($row['status'] == "Completed") { ?>
                                    <span class="badge bg-primary">Completed</span>
                                <?php } elseif ($row['status'] == "Cancelled") { ?>
                                    <span class="badge bg-danger">Cancelled</span>
                                <?php } else { ?>
                                    <span class="badge bg-secondary">No Show</span>
                                <?php } ?>

                            </div>

                            <p class="mb-2">
                                <i class="bi bi-geo-alt gold"></i>
                                <?php echo $row['branch_name']; ?>
                            </p>

                            <p class="mb-2">
                                <i class="bi bi-person gold"></i>
                                <?php echo $row['barber_first_name'] . " " . $row['barber_last_name']; ?>
                            </p>

                            <p class="mb-2">
                                <i class="bi bi-calendar-event gold"></i>
                                <?php echo date("d M Y", strtotime($row['appointment_date'])); ?>
                            </p>

                            <p class="mb-2">
                                <i class="bi bi-clock gold"></i>
                                <?php echo date("H:i", strtotime($row['start_time'])); ?>
                                -
                                <?php echo date("H:i", strtotime($row['end_time'])); ?>
                            </p>

                            <p class="mb-3">
                                <i class="bi bi-cash-stack gold"></i>
                                E<?php echo number_format($row['price'], 2); ?>
                            </p>

                            <?php if ($row['status'] == "Pending" || $row['status'] == "Confirmed") { ?>

                                <a
                                    href="cancel_appointment.php?id=<?php echo $row['appointment_id']; ?>"
                                    class="btn btn-outline-danger w-100"
                                    onclick="return confirm('Are you sure you want to cancel this appointment?');">
                                    Cancel Appointment
                                </a>

                            <?php } ?>

                        </div>

                    </div>

                <?php } ?>

            </div>

        <?php } else { ?>

            <div class="alert alert-dark border-secondary text-center">
                You have not booked any appointments yet.
                <br><br>
                <a href="booking.php" class="btn btn-gold">
                    Book Appointment
                </a>
            </div>

        <?php } ?>

    </div>

</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>