<?php

require_once "../config/db_conn.php";
require_once "../config/session.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$message = "";

if (isset($_POST['add_hairstyle'])) {
    $hairstyle_name = mysqli_real_escape_string($conn, $_POST['hairstyle_name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $duration_minutes = intval($_POST['duration_minutes']);
    $price = floatval($_POST['price']);
    $image_path = mysqli_real_escape_string($conn, $_POST['image_path']);

    $sql = "
        INSERT INTO hairstyles
        (hairstyle_name, description, duration_minutes, price, image_path)
        VALUES
        ('$hairstyle_name', '$description', $duration_minutes, $price, '$image_path')
    ";

    mysqli_query($conn, $sql);
    $message = "Hairstyle added successfully.";
}

if (isset($_POST['update_hairstyle'])) {
    $hairstyle_id = intval($_POST['hairstyle_id']);
    $hairstyle_name = mysqli_real_escape_string($conn, $_POST['hairstyle_name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $duration_minutes = intval($_POST['duration_minutes']);
    $price = floatval($_POST['price']);
    $image_path = mysqli_real_escape_string($conn, $_POST['image_path']);

    $sql = "
        UPDATE hairstyles
        SET hairstyle_name = '$hairstyle_name',
            description = '$description',
            duration_minutes = $duration_minutes,
            price = $price,
            image_path = '$image_path'
        WHERE hairstyle_id = $hairstyle_id
    ";

    mysqli_query($conn, $sql);
    $message = "Hairstyle updated successfully.";
}

if (isset($_GET['delete'])) {
    $hairstyle_id = intval($_GET['delete']);

    $check_sql = "SELECT appointment_id FROM appointments WHERE hairstyle_id = $hairstyle_id LIMIT 1";
    $check_result = mysqli_query($conn, $check_sql);

    if (mysqli_num_rows($check_result) > 0) {
        $message = "This hairstyle cannot be deleted because it has existing appointments.";
    } else {
        mysqli_query($conn, "DELETE FROM hairstyles WHERE hairstyle_id = $hairstyle_id");
        $message = "Hairstyle deleted successfully.";
    }
}

$hairstyles = mysqli_query($conn, "SELECT * FROM hairstyles ORDER BY hairstyle_name ASC");

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Manage Hairstyles</title>

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
                    <h1 class="gold">Manage Hairstyles</h1>
                    <p class="text-secondary">Add, edit and manage hairstyle prices and durations.</p>
                </div>

                <button class="btn btn-gold" data-bs-toggle="modal" data-bs-target="#addHairstyleModal">
                    <i class="bi bi-plus-circle"></i> Add Hairstyle
                </button>
            </div>

            <?php if (!empty($message)) { ?>
                <div class="alert alert-info"><?php echo $message; ?></div>
            <?php } ?>

            <div class="card p-4">

                <div class="table-responsive">

                    <table class="table table-dark table-hover align-middle">

                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Hairstyle</th>
                                <th>Duration</th>
                                <th>Price</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php while ($style = mysqli_fetch_assoc($hairstyles)) { ?>

                                <tr>
                                    <td>
                                        <img src="../<?php echo $style['image_path']; ?>" style="width:70px;height:70px;object-fit:cover;border-radius:8px;">
                                    </td>

                                    <td>
                                        <strong><?php echo $style['hairstyle_name']; ?></strong>
                                        <br>
                                        <small class="text-secondary">
                                            <?php echo substr($style['description'], 0, 80); ?>...
                                        </small>
                                    </td>

                                    <td><?php echo $style['duration_minutes']; ?> mins</td>

                                    <td>E<?php echo number_format($style['price'], 2); ?></td>

                                    <td>
                                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editStyle<?php echo $style['hairstyle_id']; ?>">
                                            Edit
                                        </button>

                                        <a href="hairstyles.php?delete=<?php echo $style['hairstyle_id']; ?>"
                                            class="btn btn-sm btn-danger"
                                            onclick="return confirm('Delete this hairstyle?');">
                                            Delete
                                        </a>
                                    </td>
                                </tr>

                                <div class="modal fade" id="editStyle<?php echo $style['hairstyle_id']; ?>">
                                    <div class="modal-dialog modal-lg modal-dialog-centered">
                                        <div class="modal-content bg-dark text-light">

                                            <form method="POST">

                                                <div class="modal-header border-secondary">
                                                    <h5 class="modal-title">Edit Hairstyle</h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>

                                                <div class="modal-body">

                                                    <input type="hidden" name="hairstyle_id" value="<?php echo $style['hairstyle_id']; ?>">

                                                    <div class="row g-3">

                                                        <div class="col-md-6">
                                                            <label class="form-label">Hairstyle Name</label>
                                                            <input type="text" name="hairstyle_name" class="form-control" value="<?php echo $style['hairstyle_name']; ?>" required>
                                                        </div>

                                                        <div class="col-md-3">
                                                            <label class="form-label">Duration</label>
                                                            <input type="number" name="duration_minutes" class="form-control" value="<?php echo $style['duration_minutes']; ?>" step="15" min="15" required>
                                                        </div>

                                                        <div class="col-md-3">
                                                            <label class="form-label">Price</label>
                                                            <input type="number" name="price" class="form-control" value="<?php echo $style['price']; ?>" step="0.01" min="0" required>
                                                        </div>

                                                        <div class="col-md-12">
                                                            <label class="form-label">Description</label>
                                                            <textarea name="description" class="form-control" rows="3"><?php echo $style['description']; ?></textarea>
                                                        </div>

                                                        <div class="col-md-12">
                                                            <label class="form-label">Image Path</label>
                                                            <input type="text" name="image_path" class="form-control" value="<?php echo $style['image_path']; ?>">
                                                        </div>

                                                    </div>

                                                </div>

                                                <div class="modal-footer border-secondary">
                                                    <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" name="update_hairstyle" class="btn btn-gold">Save Changes</button>
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

    <div class="modal fade" id="addHairstyleModal">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content bg-dark text-light">

                <form method="POST">

                    <div class="modal-header border-secondary">
                        <h5 class="modal-title">Add Hairstyle</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <div class="row g-3">

                            <div class="col-md-6">
                                <label class="form-label">Hairstyle Name</label>
                                <input type="text" name="hairstyle_name" class="form-control" required>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Duration</label>
                                <input type="number" name="duration_minutes" class="form-control" step="15" min="15" required>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Price</label>
                                <input type="number" name="price" class="form-control" step="0.01" min="0" required>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="3"></textarea>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">Image Path</label>
                                <input type="text" name="image_path" class="form-control" placeholder="assets/images/hairstyles/hairstyle-1.jpg">
                            </div>

                        </div>

                    </div>

                    <div class="modal-footer border-secondary">
                        <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="add_hairstyle" class="btn btn-gold">Add Hairstyle</button>
                    </div>

                </form>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>