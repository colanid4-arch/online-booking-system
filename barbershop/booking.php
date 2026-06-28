<?php
require_once "config/db_conn.php";
require_once "config/session.php";
$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    header("Location: login.php");
    exit();
}


$sql = "SELECT COUNT(*) FROM barbers WHERE branch_id = 1";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_row($result);
$mbabane_count = $row[0];

$sql = "SELECT COUNT(*) FROM barbers WHERE branch_id = 2";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_row($result);
$manzini_count = $row[0];

$sql = "SELECT COUNT(*) FROM barbers WHERE branch_id = 3";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_row($result);
$siteki_count = $row[0];

$sql = "SELECT * FROM hairstyles ORDER BY hairstyle_name";
$hairstyles_result = mysqli_query($conn, $sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Book Appointment</title>
    <?php include 'header.php'; ?>
</head>

<body>

    <section class="section">

        <div class="container">

            <div class="text-center mb-5">
                <h1 class="gold">Book Appointment</h1>
                <p>
                    Customize your grooming experience by selecting your branch, hairstyle, barber and preferred schedule.
                </p>
            </div>

            <form action="process_booking.php" method="POST">

                <input type="hidden" name="branch_id" id="branch_id" value="1">
                <input type="hidden" name="hairstyle_id" id="hairstyle_id">
                <input type="hidden" name="barber_id" id="barber_id" value="1">
                <input type="hidden" name="start_time" id="start_time">

                <div class="row">

                    <div class="col-lg-8">

                        <div class="booking-step">

                            <h3 class="gold mb-4">1. Choose Branch</h3>

                            <div class="row g-4">

                                <div class="col-md-4">
                                    <div class="selection-card active branch-card-option" data-branch-id="1" data-branch-name="Mbabane Branch">
                                        <h5>Mbabane Branch</h5>
                                        <p><i class="bi bi-geo-alt"></i> Main Street</p>
                                        <p><i class="bi bi-person"></i> <?php echo $mbabane_count; ?> Barbers Available</p>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="selection-card branch-card-option" data-branch-id="2" data-branch-name="Manzini Branch">
                                        <h5>Manzini Branch</h5>
                                        <p><i class="bi bi-geo-alt"></i> Market Road</p>
                                        <p><i class="bi bi-person"></i> <?php echo $manzini_count; ?> Barbers Available</p>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="selection-card branch-card-option" data-branch-id="3" data-branch-name="Siteki Branch">
                                        <h5>Siteki Branch</h5>
                                        <p><i class="bi bi-geo-alt"></i> Central Avenue</p>
                                        <p><i class="bi bi-person"></i> <?php echo $siteki_count; ?> Barbers Available</p>
                                    </div>
                                </div>

                            </div>

                        </div>

                        <div class="booking-step">

                            <h3 class="gold mb-4">2. Choose Hairstyle</h3>

                            <div id="hairstyleCarousel" class="carousel slide">

                                <div class="carousel-inner">

                                    <?php
                                    $active = true;

                                    while ($hairstyle = mysqli_fetch_assoc($hairstyles_result)) {
                                    ?>

                                        <div class="carousel-item <?php echo $active ? 'active' : ''; ?>">

                                            <div class="card style-card">

                                                <div class="img-placeholder">
                                                    <img
                                                        src="<?php echo $hairstyle['image_path']; ?>"
                                                        class="hairstyle-image img-fluid"
                                                        alt="<?php echo htmlspecialchars($hairstyle['hairstyle_name']); ?>">
                                                </div>

                                                <div class="card-body">

                                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                                        <h5 class="mb-0">
                                                            <?php echo htmlspecialchars($hairstyle['hairstyle_name']); ?>
                                                        </h5>
                                                    </div>

                                                    <p>
                                                        <?php echo htmlspecialchars($hairstyle['description']); ?>
                                                    </p>

                                                    <div class="d-flex justify-content-between">
                                                        <span><?php echo $hairstyle['duration_minutes']; ?> mins</span>
                                                        <span>E<?php echo number_format($hairstyle['price'], 2); ?></span>
                                                    </div>

                                                    <button
                                                        type="button"
                                                        class="btn btn-gold w-100 mt-4 select-hairstyle-btn"
                                                        data-id="<?php echo $hairstyle['hairstyle_id']; ?>"
                                                        data-name="<?php echo htmlspecialchars($hairstyle['hairstyle_name']); ?>"
                                                        data-price="<?php echo $hairstyle['price']; ?>"
                                                        data-duration="<?php echo $hairstyle['duration_minutes']; ?>">
                                                        Select Style
                                                    </button>

                                                </div>

                                            </div>

                                        </div>

                                    <?php
                                        $active = false;
                                    }
                                    ?>

                                </div>

                                <button class="carousel-control-prev" type="button" data-bs-target="#hairstyleCarousel" data-bs-slide="prev">
                                    <span class="carousel-control-prev-icon"></span>
                                </button>

                                <button class="carousel-control-next" type="button" data-bs-target="#hairstyleCarousel" data-bs-slide="next">
                                    <span class="carousel-control-next-icon"></span>
                                </button>

                            </div>

                        </div>

                        <div class="booking-step">

                            <h3 class="gold mb-4">3. Select Barber</h3>

                            <div class="row g-4" id="barber-container">

                                <?php

                                $barber_sql = "SELECT * FROM barbers WHERE branch_id = 1 AND is_active = 1";
                                $barber_result = mysqli_query($conn, $barber_sql);

                                while ($barber = mysqli_fetch_assoc($barber_result)) {
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

                            </div>

                        </div>
                        <div class="booking-step">

                            <h3 class="gold mb-4">4. Choose Date & Time</h3>

                            <div class="mb-4">
                                <input
                                    type="date"
                                    class="form-control"
                                    name="appointment_date"
                                    id="appointment_date"
                                    required>
                            </div>

                            <div class="row g-3" id="slots-container">
                                <p class="text-secondary">Select hairstyle, barber and date to view available slots.</p>
                            </div>

                        </div>

                    </div>

                    <div class="col-lg-4">

                        <div class="booking-summary">

                            <h4 class="gold mb-4">Booking Summary</h4>

                            <div class="mb-3">
                                <small class="text-secondary">Branch</small>
                                <h6 id="summary-branch">Mbabane Branch</h6>
                            </div>

                            <div class="mb-3">
                                <small class="text-secondary">Hairstyle</small>
                                <h6 id="summary-hairstyle">None selected</h6>
                            </div>

                            <div class="mb-3">
                                <small class="text-secondary">Barber</small>
                                <h6 id="summary-barber">Sipho Dlamini</h6>
                            </div>

                            <div class="mb-3">
                                <small class="text-secondary">Time</small>
                                <h6 id="summary-time">None selected</h6>
                            </div>

                            <hr>

                            <div class="d-flex justify-content-between mb-2">
                                <span>Total Duration</span>
                                <strong id="summary-duration">0 mins</strong>
                            </div>

                            <div class="d-flex justify-content-between mb-4">
                                <span>Total Price</span>
                                <strong class="gold" id="summary-price">E0</strong>
                            </div>

                            <button type="submit" class="btn btn-gold w-100">
                                <i class="bi bi-calendar-check"></i>
                                Confirm Booking
                            </button>

                        </div>

                    </div>

                </div>

            </form>

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
                        <li>Home</li>
                        <li>Hairstyles</li>
                        <li>Branches</li>
                        <li>Book Appointment</li>
                        <li>Login</li>
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
    <script>
        function attachBarberClickEvents() {
            document.querySelectorAll('.barber-card-option').forEach(card => {
                card.addEventListener('click', function() {
                    document.getElementById('barber_id').value = this.dataset.barberId;
                    document.getElementById('summary-barber').innerText = this.dataset.barberName;

                    document.querySelectorAll('.barber-card-option').forEach(item => {
                        item.classList.remove('active');
                    });

                    this.classList.add('active');

                    loadAvailableSlots();
                });
            });
        }

        function attachSlotEvents() {
            document.querySelectorAll('.time-slot').forEach(slot => {
                slot.addEventListener('click', function() {
                    document.getElementById('start_time').value = this.dataset.time;
                    document.getElementById('summary-time').innerText = this.dataset.time;

                    document.querySelectorAll('.time-slot').forEach(btn => {
                        btn.classList.remove('active');
                    });

                    this.classList.add('active');
                });
            });
        }

        function loadBarbers(branchId) {
            let xhr = new XMLHttpRequest();

            xhr.open("POST", "load_barbers.php", true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

            xhr.onload = function() {
                if (this.status === 200) {
                    document.getElementById("barber-container").innerHTML = this.responseText;
                    attachBarberClickEvents();

                    document.getElementById('start_time').value = '';
                    document.getElementById('summary-time').innerText = 'None selected';

                    document.getElementById('slots-container').innerHTML =
                        "<p class='text-secondary'>Select barber and date to view available slots.</p>";
                }
            };

            xhr.send("branch_id=" + encodeURIComponent(branchId));
        }

        function loadAvailableSlots() {
            let barberId = document.getElementById('barber_id').value;
            let hairstyleId = document.getElementById('hairstyle_id').value;
            let appointmentDate = document.getElementById('appointment_date').value;

            document.getElementById('start_time').value = '';
            document.getElementById('summary-time').innerText = 'None selected';

            if (barberId === '' || hairstyleId === '' || appointmentDate === '') {
                document.getElementById('slots-container').innerHTML =
                    "<p class='text-secondary'>Select hairstyle, barber and date to view available slots.</p>";
                return;
            }

            let xhr = new XMLHttpRequest();

            xhr.open("POST", "load_slots.php", true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

            xhr.onload = function() {
                if (this.status === 200) {
                    document.getElementById("slots-container").innerHTML = this.responseText;
                    attachSlotEvents();
                }
            };

            xhr.send(
                "barber_id=" + encodeURIComponent(barberId) +
                "&hairstyle_id=" + encodeURIComponent(hairstyleId) +
                "&appointment_date=" + encodeURIComponent(appointmentDate)
            );
        }

        document.querySelectorAll('.select-hairstyle-btn').forEach(button => {
            button.addEventListener('click', function() {
                document.getElementById('hairstyle_id').value = this.dataset.id;
                document.getElementById('summary-hairstyle').innerText = this.dataset.name;
                document.getElementById('summary-duration').innerText = this.dataset.duration + ' mins';
                document.getElementById('summary-price').innerText = 'E' + parseFloat(this.dataset.price).toFixed(2);

                loadAvailableSlots();
            });
        });

        document.querySelectorAll('.branch-card-option').forEach(card => {
            card.addEventListener('click', function() {
                let branchId = this.dataset.branchId;
                let branchName = this.dataset.branchName;

                document.getElementById('branch_id').value = branchId;
                document.getElementById('summary-branch').innerText = branchName;

                document.getElementById('barber_id').value = '';
                document.getElementById('summary-barber').innerText = 'None selected';

                document.querySelectorAll('.branch-card-option').forEach(item => {
                    item.classList.remove('active');
                });

                this.classList.add('active');

                loadBarbers(branchId);
            });
        });

        document.getElementById('appointment_date').addEventListener('change', function() {
            loadAvailableSlots();
        });

        attachBarberClickEvents();
    </script>

</body>

</html>