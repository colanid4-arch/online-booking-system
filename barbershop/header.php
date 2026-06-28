<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

<link rel="stylesheet" href="css/style.css">
</head>

<body>


    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">

            <a class="navbar-brand" href="index.php">BARBER</a>

            <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#nav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="nav">

                <ul class="navbar-nav ms-auto">

                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="hairstyles.php">Hairstyles</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="branches.php">Branches</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="booking.php">Book Appointment</a>
                    </li>

                    <?php if (isset($_SESSION['user_id'])) { ?>

                        <li class="nav-item">
                            <a class="nav-link" href="my_appointments.php">
                                My Appointments
                            </a>
                        </li>

                        <?php if ($_SESSION['role'] === 'admin') { ?>

                            <li class="nav-item">
                                <a class="nav-link" href="admin/dashboard.php">
                                    Admin Dashboard
                                </a>
                            </li>

                        <?php } ?>

                        <li class="nav-item dropdown">

                            <a class="nav-link dropdown-toggle"
                                href="#"
                                role="button"
                                data-bs-toggle="dropdown">

                                <i class="bi bi-person-circle"></i>
                                <?php echo htmlspecialchars($_SESSION['first_name']); ?>

                            </a>

                            <ul class="dropdown-menu dropdown-menu-end">

                                <li>
                                    <a class="dropdown-item" href="my_appointments.php">
                                        My Appointments
                                    </a>
                                </li>

                                <li>
                                    <hr class="dropdown-divider">
                                </li>

                                <li>
                                    <a class="dropdown-item text-danger" href="logout.php">
                                        Logout
                                    </a>
                                </li>

                            </ul>

                        </li>

                    <?php } else { ?>

                        <li class="nav-item">
                            <a class="nav-link" href="login.php">
                                Login
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="btn btn-gold ms-lg-2" href="register.php">
                                Sign Up
                            </a>
                        </li>

                    <?php } ?>

                </ul>

            </div>
        </div>
    </nav>