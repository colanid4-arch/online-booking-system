<?php

require_once "../config/db_conn.php";
require_once "../config/session.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$total_appointments = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM appointments"))[0];
$total_pending = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM appointments WHERE status = 'Pending'"))[0];
$total_confirmed = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM appointments WHERE status = 'Confirmed'"))[0];
$total_completed = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM appointments WHERE status = 'Completed'"))[0];
$total_customers = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM users WHERE role = 'customer'"))[0];
$total_barbers = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM barbers WHERE is_active = 1"))[0];
$total_branches = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM branches"))[0];
$total_hairstyles = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM hairstyles"))[0];

$today = date("Y-m-d");

$today_sql = "
    SELECT 
        appointments.*,
        users.first_name AS customer_first_name,
        users.last_name AS customer_last_name,
        hairstyles.hairstyle_name,
        branches.branch_name,
        barbers.first_name AS barber_first_name,
        barbers.last_name AS barber_last_name
    FROM appointments
    INNER JOIN users ON appointments.user_id = users.user_id
    INNER JOIN hairstyles ON appointments.hairstyle_id = hairstyles.hairstyle_id
    INNER JOIN branches ON appointments.branch_id = branches.branch_id
    INNER JOIN barbers ON appointments.barber_id = barbers.barber_id
    WHERE appointments.appointment_date = '$today'
    ORDER BY appointments.start_time ASC
";

$today_result = mysqli_query($conn, $today_sql);

$recent_sql = "
    SELECT 
        appointments.*,
        users.first_name AS customer_first_name,
        users.last_name AS customer_last_name,
        hairstyles.hairstyle_name,
        branches.branch_name,
        barbers.first_name AS barber_first_name,
        barbers.last_name AS barber_last_name
    FROM appointments
    INNER JOIN users ON appointments.user_id = users.user_id
    INNER JOIN hairstyles ON appointments.hairstyle_id = hairstyles.hairstyle_id
    INNER JOIN branches ON appointments.branch_id = branches.branch_id
    INNER JOIN barbers ON appointments.barber_id = barbers.barber_id
    ORDER BY appointments.created_at DESC
    LIMIT 6
";

$recent_result = mysqli_query($conn, $recent_sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Admin Dashboard</title>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <link rel="stylesheet" href="../css/style.css">
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid px-4">

            <a class="navbar-brand" href="dashboard.php">BARBER ADMIN</a>

            <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#adminNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="adminNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="appointments.php">Appointments</a></li>
                    <li class="nav-item"><a class="nav-link" href="barbers.php">Barbers</a></li>
                    <li class="nav-item"><a class="nav-link active" href="hairstyles.php">Hairstyles</a></li>
                    <li class="nav-item"><a class="nav-link text-danger" href="../logout.php">Logout</a></li>
                </ul>
            </div>

        </div>
    </nav>

    <section class="section">

        <div class="container-fluid px-4">

            <div class="mb-5">
                <h1 class="gold">Admin Dashboard</h1>
                <p class="text-secondary">
                    Manage barbershop operations, appointments, branches, barbers and hairstyles.
                </p>
            </div>

            <div class="row g-4 mb-5">

                <div class="col-lg-3 col-md-6">
                    <div class="card p-4 h-100">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-secondary">Total Appointments</small>
                                <h2 class="gold mb-0"><?php echo $total_appointments; ?></h2>
                            </div>
                            <i class="bi bi-calendar-check gold fs-1"></i>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="card p-4 h-100">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-secondary">Pending Bookings</small>
                                <h2 class="gold mb-0"><?php echo $total_pending; ?></h2>
                            </div>
                            <i class="bi bi-hourglass-split gold fs-1"></i>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="card p-4 h-100">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-secondary">Active Barbers</small>
                                <h2 class="gold mb-0"><?php echo $total_barbers; ?></h2>
                            </div>
                            <i class="bi bi-person-badge gold fs-1"></i>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="card p-4 h-100">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-secondary">Customers</small>
                                <h2 class="gold mb-0"><?php echo $total_customers; ?></h2>
                            </div>
                            <i class="bi bi-people gold fs-1"></i>
                        </div>
                    </div>
                </div>

            </div>

            <div class="row g-4 mb-5">

                <div class="col-lg-3 col-md-6">
                    <div class="card p-4 h-100">
                        <small class="text-secondary">Confirmed</small>
                        <h3 class="text-success mb-0"><?php echo $total_confirmed; ?></h3>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="card p-4 h-100">
                        <small class="text-secondary">Completed</small>
                        <h3 class="text-primary mb-0"><?php echo $total_completed; ?></h3>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="card p-4 h-100">
                        <small class="text-secondary">Branches</small>
                        <h3 class="gold mb-0"><?php echo $total_branches; ?></h3>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="card p-4 h-100">
                        <small class="text-secondary">Hairstyles</small>
                        <h3 class="gold mb-0"><?php echo $total_hairstyles; ?></h3>
                    </div>
                </div>

            </div>

            <div class="row g-4">

                <div class="col-lg-7">

                    <div class="card p-4 h-100">

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="gold mb-0">Today's Schedule</h4>
                            <span class="badge bg-dark border border-secondary">
                                <?php echo date("d M Y"); ?>
                            </span>
                        </div>

                        <?php if (mysqli_num_rows($today_result) > 0) { ?>

                            <div class="table-responsive">

                                <table class="table table-dark table-hover align-middle">

                                    <thead>
                                        <tr>
                                            <th>Time</th>
                                            <th>Client</th>
                                            <th>Barber</th>
                                            <th>Style</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>

                                    <tbody>

                                        <?php while ($row = mysqli_fetch_assoc($today_result)) { ?>

                                            <tr>
                                                <td>
                                                    <?php echo date("H:i", strtotime($row['start_time'])); ?>
                                                    -
                                                    <?php echo date("H:i", strtotime($row['end_time'])); ?>
                                                </td>

                                                <td>
                                                    <?php echo $row['customer_first_name'] . " " . $row['customer_last_name']; ?>
                                                </td>

                                                <td>
                                                    <?php echo $row['barber_first_name'] . " " . $row['barber_last_name']; ?>
                                                </td>

                                                <td>
                                                    <?php echo $row['hairstyle_name']; ?>
                                                </td>

                                                <td>
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
                                                </td>
                                            </tr>

                                        <?php } ?>

                                    </tbody>

                                </table>

                            </div>

                        <?php } else { ?>

                            <div class="alert alert-dark border-secondary text-center">
                                No appointments scheduled for today.
                            </div>

                        <?php } ?>

                    </div>

                </div>

                <div class="col-lg-5">

                    <div class="card p-4 h-100">

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="gold mb-0">Recent Bookings</h4>
                            <a href="appointments.php" class="btn btn-outline-gold btn-sm">
                                View All
                            </a>
                        </div>

                        <?php if (mysqli_num_rows($recent_result) > 0) { ?>

                            <?php while ($row = mysqli_fetch_assoc($recent_result)) { ?>

                                <div class="border-bottom border-secondary pb-3 mb-3">

                                    <div class="d-flex justify-content-between align-items-center">

                                        <div>
                                            <h6 class="mb-1">
                                                <?php echo $row['customer_first_name'] . " " . $row['customer_last_name']; ?>
                                            </h6>

                                            <small class="text-secondary">
                                                <?php echo $row['hairstyle_name']; ?>
                                                with
                                                <?php echo $row['barber_first_name'] . " " . $row['barber_last_name']; ?>
                                            </small>

                                            <br>

                                            <small class="text-secondary">
                                                <?php echo date("d M Y", strtotime($row['appointment_date'])); ?>
                                                at
                                                <?php echo date("H:i", strtotime($row['start_time'])); ?>
                                            </small>
                                        </div>

                                        <div>
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

                                    </div>

                                </div>

                            <?php } ?>

                        <?php } else { ?>

                            <div class="alert alert-dark border-secondary text-center">
                                No recent bookings yet.
                            </div>

                        <?php } ?>

                    </div>

                </div>

            </div>

        </div>

    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>