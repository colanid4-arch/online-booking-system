<?php

require_once "../config/db_conn.php";
require_once "../config/session.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$message = "";

if (isset($_POST['add_barber'])) {
    $branch_id = intval($_POST['branch_id']);
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $bio = mysqli_real_escape_string($conn, $_POST['bio']);
    $profile_image = mysqli_real_escape_string($conn, $_POST['profile_image']);

    $sql = "
        INSERT INTO barbers
        (branch_id, first_name, last_name, phone, bio, profile_image, is_active)
        VALUES
        ($branch_id, '$first_name', '$last_name', '$phone', '$bio', '$profile_image', 1)
    ";

    mysqli_query($conn, $sql);
    $message = "Barber added successfully.";
}

if (isset($_POST['update_barber'])) {
    $barber_id = intval($_POST['barber_id']);
    $branch_id = intval($_POST['branch_id']);
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $bio = mysqli_real_escape_string($conn, $_POST['bio']);
    $profile_image = mysqli_real_escape_string($conn, $_POST['profile_image']);
    $is_active = intval($_POST['is_active']);

    $sql = "
        UPDATE barbers
        SET branch_id = $branch_id,
            first_name = '$first_name',
            last_name = '$last_name',
            phone = '$phone',
            bio = '$bio',
            profile_image = '$profile_image',
            is_active = $is_active
        WHERE barber_id = $barber_id
    ";

    mysqli_query($conn, $sql);
    $message = "Barber updated successfully.";
}

if (isset($_GET['delete'])) {
    $barber_id = intval($_GET['delete']);

    $sql = "UPDATE barbers SET is_active = 0 WHERE barber_id = $barber_id";
    mysqli_query($conn, $sql);

    $message = "Barber deactivated successfully.";
}

$branches = mysqli_query($conn, "SELECT * FROM branches ORDER BY branch_name ASC");

$barbers = mysqli_query($conn, "
    SELECT barbers.*, branches.branch_name
    FROM barbers
    INNER JOIN branches ON barbers.branch_id = branches.branch_id
    ORDER BY barbers.created_at DESC
");

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Manage Barbers</title>

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
                    <h1 class="gold">Manage Barbers</h1>
                    <p class="text-secondary">Add, edit, deactivate and assign barbers to branches.</p>
                </div>

                <button class="btn btn-gold" data-bs-toggle="modal" data-bs-target="#addBarberModal">
                    <i class="bi bi-plus-circle"></i> Add Barber
                </button>
            </div>

            <?php if (!empty($message)) { ?>
                <div class="alert alert-success"><?php echo $message; ?></div>
            <?php } ?>

            <div class="card p-4">

                <div class="table-responsive">

                    <table class="table table-dark table-hover align-middle">

                        <thead>
                            <tr>
                                <th>Photo</th>
                                <th>Name</th>
                                <th>Branch</th>
                                <th>Phone</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php while ($barber = mysqli_fetch_assoc($barbers)) { ?>

                                <tr>
                                    <td>
                                        <img src="../<?php echo $barber['profile_image']; ?>" style="width:60px;height:60px;object-fit:cover;border-radius:8px;">
                                    </td>

                                    <td>
                                        <?php echo $barber['first_name'] . " " . $barber['last_name']; ?>
                                    </td>

                                    <td><?php echo $barber['branch_name']; ?></td>

                                    <td><?php echo $barber['phone']; ?></td>

                                    <td>
                                        <?php if ($barber['is_active']) { ?>
                                            <span class="badge bg-success">Active</span>
                                        <?php } else { ?>
                                            <span class="badge bg-danger">Inactive</span>
                                        <?php } ?>
                                    </td>

                                    <td>
                                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editBarber<?php echo $barber['barber_id']; ?>">
                                            Edit
                                        </button>

                                        <a href="barbers.php?delete=<?php echo $barber['barber_id']; ?>"
                                            class="btn btn-sm btn-danger"
                                            onclick="return confirm('Deactivate this barber?');">
                                            Deactivate
                                        </a>
                                    </td>
                                </tr>

                                <div class="modal fade" id="editBarber<?php echo $barber['barber_id']; ?>">
                                    <div class="modal-dialog modal-lg modal-dialog-centered">
                                        <div class="modal-content bg-dark text-light">

                                            <form method="POST">

                                                <div class="modal-header border-secondary">
                                                    <h5 class="modal-title">Edit Barber</h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>

                                                <div class="modal-body">

                                                    <input type="hidden" name="barber_id" value="<?php echo $barber['barber_id']; ?>">

                                                    <div class="row g-3">

                                                        <div class="col-md-6">
                                                            <label class="form-label">First Name</label>
                                                            <input type="text" name="first_name" class="form-control" value="<?php echo $barber['first_name']; ?>" required>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <label class="form-label">Last Name</label>
                                                            <input type="text" name="last_name" class="form-control" value="<?php echo $barber['last_name']; ?>" required>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <label class="form-label">Branch</label>
                                                            <select name="branch_id" class="form-select" required>
                                                                <?php
                                                                $branch_options = mysqli_query($conn, "SELECT * FROM branches ORDER BY branch_name ASC");
                                                                while ($branch = mysqli_fetch_assoc($branch_options)) {
                                                                ?>
                                                                    <option value="<?php echo $branch['branch_id']; ?>" <?php if ($branch['branch_id'] == $barber['branch_id']) echo "selected"; ?>>
                                                                        <?php echo $branch['branch_name']; ?>
                                                                    </option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <label class="form-label">Phone</label>
                                                            <input type="text" name="phone" class="form-control" value="<?php echo $barber['phone']; ?>">
                                                        </div>

                                                        <div class="col-md-12">
                                                            <label class="form-label">Bio</label>
                                                            <textarea name="bio" class="form-control" rows="3"><?php echo $barber['bio']; ?></textarea>
                                                        </div>

                                                        <div class="col-md-12">
                                                            <label class="form-label">Profile Image Path</label>
                                                            <input type="text" name="profile_image" class="form-control" value="<?php echo $barber['profile_image']; ?>">
                                                        </div>

                                                        <div class="col-md-6">
                                                            <label class="form-label">Status</label>
                                                            <select name="is_active" class="form-select">
                                                                <option value="1" <?php if ($barber['is_active'] == 1) echo "selected"; ?>>Active</option>
                                                                <option value="0" <?php if ($barber['is_active'] == 0) echo "selected"; ?>>Inactive</option>
                                                            </select>
                                                        </div>

                                                    </div>

                                                </div>

                                                <div class="modal-footer border-secondary">
                                                    <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" name="update_barber" class="btn btn-gold">Save Changes</button>
                                                </div>

                                            </form>

                                        </div>
                                    </div>
                                </div>

                            <?php } ?>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </section>

    <div class="modal fade" id="addBarberModal">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content bg-dark text-light">

                <form method="POST">

                    <div class="modal-header border-secondary">
                        <h5 class="modal-title">Add Barber</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <div class="row g-3">

                            <div class="col-md-6">
                                <label class="form-label">First Name</label>
                                <input type="text" name="first_name" class="form-control" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Last Name</label>
                                <input type="text" name="last_name" class="form-control" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Branch</label>
                                <select name="branch_id" class="form-select" required>
                                    <?php while ($branch = mysqli_fetch_assoc($branches)) { ?>
                                        <option value="<?php echo $branch['branch_id']; ?>">
                                            <?php echo $branch['branch_name']; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Phone</label>
                                <input type="text" name="phone" class="form-control">
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">Bio</label>
                                <textarea name="bio" class="form-control" rows="3"></textarea>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">Profile Image Path</label>
                                <input type="text" name="profile_image" class="form-control" placeholder="assets/images/barbers/barber-1.jpg">
                            </div>

                        </div>

                    </div>

                    <div class="modal-footer border-secondary">
                        <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="add_barber" class="btn btn-gold">Add Barber</button>
                    </div>

                </form>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>