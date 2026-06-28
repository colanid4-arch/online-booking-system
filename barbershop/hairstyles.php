<?php

require_once "config/db_conn.php";
require_once "config/session.php";

$category = $_GET['category'] ?? '';
$duration = $_GET['duration'] ?? '';
$price = $_GET['price'] ?? '';
$search = $_GET['search'] ?? '';

$where = [];

if (!empty($category)) {
    if ($category == "fade") {
        $where[] = "(hairstyle_name LIKE '%fade%' OR description LIKE '%fade%')";
    } elseif ($category == "premium") {
        $where[] = "(price >= 150 OR duration_minutes >= 90)";
    } elseif ($category == "quick") {
        $where[] = "duration_minutes <= 30";
    } elseif ($category == "dreadlocks") {
        $where[] = "(hairstyle_name LIKE '%dread%' OR description LIKE '%dread%')";
    }
}

if (!empty($duration)) {
    if ($duration == "under30") {
        $where[] = "duration_minutes < 30";
    } elseif ($duration == "30to60") {
        $where[] = "duration_minutes BETWEEN 30 AND 60";
    } elseif ($duration == "over60") {
        $where[] = "duration_minutes > 60";
    }
}

if (!empty($price)) {
    if ($price == "under100") {
        $where[] = "price < 100";
    } elseif ($price == "100to200") {
        $where[] = "price BETWEEN 100 AND 200";
    } elseif ($price == "above200") {
        $where[] = "price > 200";
    }
}

if (!empty($search)) {
    $search_safe = mysqli_real_escape_string($conn, $search);
    $where[] = "(hairstyle_name LIKE '%$search_safe%' OR description LIKE '%$search_safe%')";
}

$sql = "SELECT * FROM hairstyles";

if (count($where) > 0) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

$sql .= " ORDER BY hairstyle_name ASC";

$result = mysqli_query($conn, $sql);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Hairstyle Gallery</title>
    <?php include 'header.php'; ?>
</head>

<body>

    <section class="section">

        <div class="container">

            <div class="text-center mb-5">
                <h1 class="gold">Hairstyle Collection</h1>
                <p class="mt-3">
                    Explore modern and classic hairstyles tailored for different grooming preferences, hair textures and lifestyles.
                </p>
            </div>

            <form method="GET" class="row mb-5">

                <div class="col-lg-3 mb-3">
                    <select class="form-select" name="category" onchange="this.form.submit()">
                        <option value="">All Categories</option>
                        <option value="fade" <?php if ($category == "fade") echo "selected"; ?>>Fade Styles</option>
                        <option value="premium" <?php if ($category == "premium") echo "selected"; ?>>Premium Styles</option>
                        <option value="quick" <?php if ($category == "quick") echo "selected"; ?>>Quick Cuts</option>
                        <option value="dreadlocks" <?php if ($category == "dreadlocks") echo "selected"; ?>>Dreadlocks</option>
                    </select>
                </div>

                <div class="col-lg-3 mb-3">
                    <select class="form-select" name="duration" onchange="this.form.submit()">
                        <option value="">Duration</option>
                        <option value="under30" <?php if ($duration == "under30") echo "selected"; ?>>Under 30 mins</option>
                        <option value="30to60" <?php if ($duration == "30to60") echo "selected"; ?>>30 - 60 mins</option>
                        <option value="over60" <?php if ($duration == "over60") echo "selected"; ?>>Over 60 mins</option>
                    </select>
                </div>

                <div class="col-lg-3 mb-3">
                    <select class="form-select" name="price" onchange="this.form.submit()">
                        <option value="">Price Range</option>
                        <option value="under100" <?php if ($price == "under100") echo "selected"; ?>>Under E100</option>
                        <option value="100to200" <?php if ($price == "100to200") echo "selected"; ?>>E100 - E200</option>
                        <option value="above200" <?php if ($price == "above200") echo "selected"; ?>>Above E200</option>
                    </select>
                </div>

                <div class="col-lg-3 mb-3">
                    <div class="input-group">
                        <span class="input-group-text bg-dark border-secondary text-light">
                            <i class="bi bi-search"></i>
                        </span>

                        <input
                            type="text"
                            name="search"
                            class="form-control"
                            placeholder="Search hairstyle"
                            value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                </div>

                <div class="col-12 text-end">
                    <button type="submit" class="btn btn-gold">Apply Filters</button>
                    <a href="hairstyles.php" class="btn btn-outline-gold">Clear Filters</a>
                </div>

            </form>

            <div class="row g-4">

                <?php if (mysqli_num_rows($result) > 0) { ?>

                    <?php while ($row = mysqli_fetch_assoc($result)) { ?>

                        <div class="col-lg-4 col-md-6">

                            <div class="card style-gallery-card h-100">

                                <div class="img-placeholder hairstyle-image">
                                    <img src="<?php echo $row["image_path"]; ?>" class="img-fluid" alt="<?php echo $row["hairstyle_name"]; ?>">
                                </div>

                                <div class="card-body">

                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h5 class="mb-0">
                                            <?php echo $row["hairstyle_name"]; ?>
                                        </h5>
                                    </div>

                                    <p>
                                        <?php echo $row["description"]; ?>
                                    </p>

                                    <div class="style-meta">

                                        <div class="d-flex justify-content-between mb-2">

                                            <span>
                                                <i class="bi bi-clock"></i>
                                                <?php echo $row["duration_minutes"]; ?> mins
                                            </span>

                                            <span>
                                                <i class="bi bi-cash-stack"></i>
                                                E<?php echo number_format($row["price"], 2); ?>
                                            </span>

                                        </div>

                                    </div>



                                </div>

                            </div>

                        </div>

                    <?php } ?>

                <?php } else { ?>

                    <div class="col-12">
                        <div class="alert alert-dark border-secondary text-center">
                            No hairstyles found matching your filters.
                        </div>
                    </div>

                <?php } ?>

            </div>

        </div>

    </section>

</body>

</html>