<?php
require_once "config/db_conn.php";
require_once "config/session.php";

$hairstyles_sql = "SELECT * FROM hairstyles ORDER BY price ASC LIMIT 3";
$hairstyles_result = mysqli_query($conn, $hairstyles_sql);

$barbers_sql = "
    SELECT barbers.*, branches.branch_name
    FROM barbers
    INNER JOIN branches ON barbers.branch_id = branches.branch_id
    WHERE barbers.is_active = 1
    ORDER BY barbers.created_at DESC
    LIMIT 4
";
$barbers_result = mysqli_query($conn, $barbers_sql);

$branches_sql = "SELECT * FROM branches ORDER BY branch_name ASC LIMIT 3";
$branches_result = mysqli_query($conn, $branches_sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Modern African Barbershop</title>
    <?php include 'header.php'; ?>

<section class="hero text-center">
    <div class="container">
        <h1 class="display-4 fw-bold">
            Look Sharp. <span class="gold">Book Your Cut</span> in Minutes.
        </h1>

        <p class="lead mt-4">
            Browse styles, choose your barber, and schedule your next haircut at any of our branches — all in just a few clicks.
        </p>

        <div class="mt-4">
            <a class="btn btn-gold btn-lg me-3" href="booking.php">
                <i class="bi bi-calendar-check"></i> Book an Appointment
            </a>

            <a class="btn btn-outline-gold btn-lg" href="hairstyles.php">
                <i class="bi bi-scissors"></i> Browse Hairstyles
            </a>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">

        <div class="text-center mb-5">
            <h2 class="gold">Popular Hairstyles</h2>
            <p>
                Explore some of our most requested styles. Choose the look that suits you and book your next appointment today.
            </p>
        </div>

        <div class="row g-4">

            <?php while ($style = mysqli_fetch_assoc($hairstyles_result)) { ?>

                <div class="col-md-4">
                    <div class="card h-100">

                        <div class="img-placeholder">
                            <img src="<?php echo $style['image_path']; ?>"
                                 class="hairstyle-image img-fluid"
                                 alt="<?php echo htmlspecialchars($style['hairstyle_name']); ?>">
                        </div>

                        <div class="card-body">
                            <h5><?php echo htmlspecialchars($style['hairstyle_name']); ?></h5>

                            <p><?php echo htmlspecialchars($style['description']); ?></p>

                            <div class="d-flex justify-content-between mb-3">
                                <span><i class="bi bi-clock"></i> <?php echo $style['duration_minutes']; ?> mins</span>
                                <span class="gold">E<?php echo number_format($style['price'], 2); ?></span>
                            </div>
                        </div>

                    </div>
                </div>

            <?php } ?>

        </div>

        <div class="text-center mt-5">
            <a class="btn btn-gold" href="hairstyles.php">
                View Full Gallery
            </a>
        </div>

    </div>
</section>

<section class="section bg-dark">
    <div class="container">

        <div class="text-center mb-5">
            <h2 class="gold">Meet Our Professional Barbers</h2>
            <p>
                Our experienced barbers are dedicated to delivering precision cuts, modern styles, and exceptional service.
            </p>
        </div>

        <div class="row g-4">

            <?php while ($barber = mysqli_fetch_assoc($barbers_result)) { ?>

                <div class="col-md-3">
                    <div class="card text-center h-100">

                        <div class="img-placeholder">
                            <img src="<?php echo $barber['profile_image']; ?>"
                                 class="barber-image img-fluid"
                                 alt="<?php echo htmlspecialchars($barber['first_name'] . ' ' . $barber['last_name']); ?>">
                        </div>

                        <div class="card-body">
                            <h6>
                                <?php echo htmlspecialchars($barber['first_name'] . ' ' . $barber['last_name']); ?>
                            </h6>

                            <span class="badge bg-secondary">
                                <?php echo htmlspecialchars($barber['branch_name']); ?>
                            </span>
                        </div>

                    </div>
                </div>

            <?php } ?>

        </div>

    </div>
</section>

<section class="section">
    <div class="container">

        <div class="text-center mb-5">
            <h2 class="gold">Visit One of Our Branches</h2>
            <p>
                We operate multiple barbershop branches to serve you better. Find the location closest to you and book your appointment with ease.
            </p>
        </div>

        <div class="row g-4">

            <?php while ($branch = mysqli_fetch_assoc($branches_result)) { ?>

                <div class="col-md-4">
                    <div class="card branch-card h-100">

                        <div class="card-body">

                            <h5><?php echo htmlspecialchars($branch['branch_name']); ?></h5>

                            <p>
                                <i class="bi bi-geo-alt"></i>
                                <?php echo htmlspecialchars($branch['address']); ?>
                            </p>

                            <p>
                                <i class="bi bi-telephone"></i>
                                <?php echo htmlspecialchars($branch['phone']); ?>
                            </p>

                            <p>
                                <i class="bi bi-clock"></i>
                                Mon – Sat: 8:00 AM – 5:00 PM
                            </p>

                            <a href="booking.php" class="btn btn-outline-gold w-100">
                                Book at This Branch
                            </a>

                        </div>

                    </div>
                </div>

            <?php } ?>

        </div>

    </div>
</section>

<section class="section bg-dark text-center">
    <div class="container">

        <h2 class="gold">Ready for Your Next Cut?</h2>

        <p class="mt-3">
            Skip the waiting lines. Schedule your appointment online and get the style you want at a time that works for you.
        </p>

        <a href="booking.php" class="btn btn-gold btn-lg mt-3">
            Book Now
        </a>

    </div>
</section>

<footer>
    <div class="container">

        <div class="row">

            <div class="col-md-4">
                <h5 class="gold">BARBER</h5>
                <p>
                    Modern African barbershop experience with professional service and precision styling.
                </p>
            </div>

            <div class="col-md-4">
                <h6>Quick Links</h6>
                <ul class="list-unstyled">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="hairstyles.php">Hairstyles</a></li>
                    <li><a href="branches.php">Branches</a></li>
                    <li><a href="booking.php">Book Appointment</a></li>
                    <li><a href="login.php">Login</a></li>
                </ul>
            </div>

            <div class="col-md-4">
                <h6>Contact</h6>
                <p><i class="bi bi-telephone"></i> +268 XXX XXXX</p>
                <p><i class="bi bi-envelope"></i> info@barbershop.com</p>
                <p><i class="bi bi-geo-alt"></i> Eswatini</p>
            </div>

        </div>

    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>