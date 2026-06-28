<?php
require_once "config/db_conn.php";
require_once "config/session.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Our Branches</title>
        <?php 
    include 'header.php';
    ?>
    <section class="section">
        <div class="container">
            <div class="text-center mb-5">
                <h1 class="gold">Our Branches</h1>
                <p>Visit any of our branches to get the perfect haircut. Each branch offers professional barbers and
                    comfortable ambiance.</p>
            </div>

            <div class="row g-4">

                <!-- Branch Card -->
                <div class="col-md-4">
                    <div class="card branch-card">
                        <h5 class="mb-3">Mbabane Branch</h5>
                        <p><i class="bi bi-geo-alt"></i> Main Street, Mbabane</p>
                        <p><i class="bi bi-telephone"></i> +268 7700 1234</p>
                        <p><i class="bi bi-person"></i> 5 Barbers Available</p>
                        <a class="btn btn-gold w-100 mt-3">Book Appointment</a>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card branch-card">
                        <h5 class="mb-3">Manzini Branch</h5>
                        <p><i class="bi bi-geo-alt"></i> Market Street, Manzini</p>
                        <p><i class="bi bi-telephone"></i> +268 7700 5678</p>
                        <p><i class="bi bi-person"></i> 4 Barbers Available</p>
                        <a class="btn btn-gold w-100 mt-3">Book Appointment</a>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card branch-card">
                        <h5 class="mb-3">Siteki Branch</h5>
                        <p><i class="bi bi-geo-alt"></i> Central Ave, Siteki</p>
                        <p><i class="bi bi-telephone"></i> +268 7700 9012</p>
                        <p><i class="bi bi-person"></i> 3 Barbers Available</p>
                        <a class="btn btn-gold w-100 mt-3">Book Appointment</a>
                    </div>
                </div>

            </div>
        </div>
    </section>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>