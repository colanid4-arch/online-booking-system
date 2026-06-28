<?php

require_once "config/db_conn.php";

if (!isset($_POST['branch_id'])) {
    exit;
}

$branch_id = intval($_POST['branch_id']);

$sql = "SELECT * FROM barbers WHERE branch_id = $branch_id AND is_active = 1";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) == 0) {
    echo '<p class="text-secondary">No barbers available for this branch.</p>';
    exit;
}

while ($barber = mysqli_fetch_assoc($result)) {
?>

    <div class="col-md-4">

        <div class="selection-card barber-card-option"
            data-barber-id="<?php echo $barber['barber_id']; ?>"
            data-barber-name="<?php echo $barber['first_name'] . ' ' . $barber['last_name']; ?>">

            <div class="img-placeholder mb-3">

                <img src="<?php echo $barber['profile_image']; ?>"
                    class="barber-image img-fluid"
                    alt="<?php echo $barber['first_name'] . ' ' . $barber['last_name']; ?>">

            </div>

            <h5>
                <?php echo $barber['first_name'] . ' ' . $barber['last_name']; ?>
            </h5>

            <p>
                <?php echo $barber['bio']; ?>
            </p>

            <span class="badge bg-success">
                Available Today
            </span>

        </div>

    </div>

<?php
}
?>