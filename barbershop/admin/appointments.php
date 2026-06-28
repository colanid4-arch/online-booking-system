<?php

require_once "../config/db_conn.php";
require_once "../config/session.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$status_filter = $_GET['status'] ?? '';
$date_filter = $_GET['date'] ?? '';
$branch_filter = $_GET['branch'] ?? '';

$where = [];

if (!empty($status_filter)) {
    $status_safe = mysqli_real_escape_string($conn, $status_filter);
    $where[] = "appointments.status = '$status_safe'";
}

if (!empty($date_filter)) {
    $date_safe = mysqli_real_escape_string($conn, $date_filter);
    $where[] = "appointments.appointment_date = '$date_safe'";
}

if (!empty($branch_filter)) {
    $branch_safe = intval($branch_filter);
    $where[] = "appointments.branch_id = $branch_safe";
}

$sql = "
    SELECT 
        appointments.*,
        users.first_name AS customer_first_name,
        users.last_name AS customer_last_name,
        users.phone AS customer_phone,
        hairstyles.hairstyle_name,
        hairstyles.price,
        branches.branch_name,
        barbers.first_name AS barber_first_name,
        barbers.last_name AS barber_last_name
    FROM appointments
    INNER JOIN users 
        ON appointments.user_id = users.user_id
    INNER JOIN hairstyles 
        ON appointments.hairstyle_id = hairstyles.hairstyle_id
    INNER JOIN branches 
        ON appointments.branch_id = branches.branch_id
    INNER JOIN barbers 
        ON appointments.barber_id = barbers.barber_id
";

if (count($where) > 0) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

$sql .= " ORDER BY appointments.appointment_date DESC, appointments.start_time DESC";

$result = mysqli_query($conn, $sql);

$branches_result = mysqli_query($conn, "SELECT * FROM branches ORDER BY branch_name ASC");

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Admin Appointments</title>

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

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="gold">Appointments</h1>
                    <p class="text-secondary mb-0">
                        View, filter, and manage all customer bookings.
                    </p>
                </div>
            </div>

            <?php if (isset($_SESSION['success_message'])) { ?>
                <div class="alert alert-success">
                    <?php
                    echo $_SESSION['success_message'];
                    unset($_SESSION['success_message']);
                    ?>
                </div>
            <?php } ?>

            <?php if (isset($_SESSION['error_message'])) { ?>
                <div class="alert alert-danger">
                    <?php
                    echo $_SESSION['error_message'];
                    unset($_SESSION['error_message']);
                    ?>
                </div>
            <?php } ?>

            <div class="card p-4 mb-4">

                <form method="GET" class="row g-3">

                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All Statuses</option>
                            <option value="Pending" <?php if ($status_filter == "Pending") echo "selected"; ?>>Pending</option>
                            <option value="Confirmed" <?php if ($status_filter == "Confirmed") echo "selected"; ?>>Confirmed</option>
                            <option value="Completed" <?php if ($status_filter == "Completed") echo "selected"; ?>>Completed</option>
                            <option value="Cancelled" <?php if ($status_filter == "Cancelled") echo "selected"; ?>>Cancelled</option>
                            <option value="No Show" <?php if ($status_filter == "No Show") echo "selected"; ?>>No Show</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Appointment Date</label>
                        <input
                            type="date"
                            name="date"
                            class="form-control"
                            value="<?php echo htmlspecialchars($date_filter); ?>">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Branch</label>
                        <select name="branch" class="form-select">
                            <option value="">All Branches</option>

                            <?php while ($branch = mysqli_fetch_assoc($branches_result)) { ?>
                                <option
                                    value="<?php echo $branch['branch_id']; ?>"
                                    <?php if ($branch_filter == $branch['branch_id']) echo "selected"; ?>>
                                    <?php echo $branch['branch_name']; ?>
                                </option>
                            <?php } ?>

                        </select>
                    </div>

                    <div class="col-md-3 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-gold w-100">
                            Filter
                        </button>

                        <a href="appointments.php" class="btn btn-outline-gold w-100">
                            Clear
                        </a>
                    </div>

                </form>

            </div>

            <div class="card p-4">

                <div class="table-responsive">

                    <table class="table table-dark table-hover align-middle">

                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Client</th>
                                <th>Branch</th>
                                <th>Barber</th>
                                <th>Hairstyle</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Price</th>
                                <th>Status</th>
                                <th>Update</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php if (mysqli_num_rows($result) > 0) { ?>

                                <?php while ($row = mysqli_fetch_assoc($result)) { ?>

                                    <tr>
                                        <td><?php echo $row['appointment_id']; ?></td>

                                        <td>
                                            <strong>
                                                <?php echo $row['customer_first_name'] . " " . $row['customer_last_name']; ?>
                                            </strong>
                                            <br>
                                            <small class="text-secondary">
                                                <?php echo $row['customer_phone']; ?>
                                            </small>
                                        </td>

                                        <td><?php echo $row['branch_name']; ?></td>

                                        <td>
                                            <?php echo $row['barber_first_name'] . " " . $row['barber_last_name']; ?>
                                        </td>

                                        <td><?php echo $row['hairstyle_name']; ?></td>

                                        <td>
                                            <?php echo date("d M Y", strtotime($row['appointment_date'])); ?>
                                        </td>

                                        <td>
                                            <?php echo date("H:i", strtotime($row['start_time'])); ?>
                                            -
                                            <?php echo date("H:i", strtotime($row['end_time'])); ?>
                                        </td>

                                        <td>
                                            E<?php echo number_format($row['price'], 2); ?>
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

                                        <td>
                                            <form action="update_appointment_status.php" method="POST" class="d-flex gap-2">

                                                <input
                                                    type="hidden"
                                                    name="appointment_id"
                                                    value="<?php echo $row['appointment_id']; ?>">

                                                <select name="status" class="form-select form-select-sm">
                                                    <option value="Pending" <?php if ($row['status'] == "Pending") echo "selected"; ?>>Pending</option>
                                                    <option value="Confirmed" <?php if ($row['status'] == "Confirmed") echo "selected"; ?>>Confirmed</option>
                                                    <option value="Completed" <?php if ($row['status'] == "Completed") echo "selected"; ?>>Completed</option>
                                                    <option value="Cancelled" <?php if ($row['status'] == "Cancelled") echo "selected"; ?>>Cancelled</option>
                                                    <option value="No Show" <?php if ($row['status'] == "No Show") echo "selected"; ?>>No Show</option>
                                                </select>

                                                <button type="submit" class="btn btn-sm btn-gold">
                                                    Save
                                                </button>

                                            </form>
                                        </td>
                                    </tr>

                                <?php } ?>

                            <?php } else { ?>

                                <tr>
                                    <td colspan="10" class="text-center text-secondary py-4">
                                        No appointments found.
                                    </td>
                                </tr>

                            <?php } ?>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>