<?php

require_once "../account/auth.php";
require_once "../config/db.php";

$page_title = "Patients";

// Search & Filter Inputs
$search = $_GET["search"] ?? "";
$join_year = $_GET["join_year"] ?? "";
$visit_status = $_GET["visit_status"] ?? "";

// Pagination
$limit = 10;

$page = isset($_GET["page"])
    ? (int) $_GET["page"]
    : 1;

if ($page < 1) {
    $page = 1;
}

require_once "../includes/header.php";


$sql = "
    SELECT
        p.patient_id,
        p.name,
        p.dob,
        p.join_date,
        p.phone,
        p.address,

        TIMESTAMPDIFF(
            YEAR,
            p.dob,
            CURDATE()
        ) AS age_years,

        MOD(
            TIMESTAMPDIFF(
                MONTH,
                p.dob,
                CURDATE()
            ),
            12
        ) AS remaining_months,

        YEAR(p.join_date) AS join_year,

        MONTHNAME(p.join_date) AS join_month_name,

        DAY(p.join_date) AS join_day,

        COUNT(v.visit_id) AS total_visits

    FROM patients p

    LEFT JOIN visits v
        ON p.patient_id = v.patient_id
";

// Build SQL filters
$conditions = [];

if ($search !== "") {

    $safe_search = $conn->real_escape_string($search);

    $conditions[] = "
        (
            p.name LIKE '%$safe_search%'
            OR p.phone LIKE '%$safe_search%'
        )
    ";
}

if ($join_year !== "") {

    $safe_join_year = $conn->real_escape_string($join_year);

    $conditions[] = "
        YEAR(p.join_date) = '$safe_join_year'
    ";
}

if ($visit_status === "has_visits") {

    $conditions[] = "
        v.visit_id IS NOT NULL
    ";
}

if ($visit_status === "no_visits") {

    $conditions[] = "
        v.visit_id IS NULL
    ";
}

if (count($conditions) > 0) {

    $sql .= "
        WHERE
        " . implode(" AND ", $conditions);
}

// ============================================================
// COUNT FILTERED PATIENTS
// ============================================================

$count_sql = "
    SELECT COUNT(DISTINCT p.patient_id) AS total_records

    FROM patients p

    LEFT JOIN visits v
        ON p.patient_id = v.patient_id
";


$count_result = $conn->query($count_sql);

if (!$count_result) {

    die("Count query failed: " . $conn->error);
}

$count_row = $count_result->fetch_assoc();

$total_records = (int) $count_row["total_records"];

$total_pages = (int) ceil(
    $total_records / $limit
);

$offset = ($page - 1) * $limit;

$sql .= "

    GROUP BY
        p.patient_id,
        p.name,
        p.dob,
        p.join_date,
        p.phone,
        p.address

    ORDER BY p.patient_id DESC

    LIMIT $limit
    OFFSET $offset
";


$result = $conn->query($sql);

if (!$result) {
    die("Query failed: " . $conn->error);
}

?>

<div class="container-fluid py-4">

    <?php if (isset($_GET["action"]) && $_GET["action"] === "add_visit"): ?>

        <div class="alert alert-info">

            <strong>Select a patient</strong>

            <br>

            Choose the patient for whom you want to record a new visit.

        </div>

    <?php endif; ?>

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">


        <div>

            <h2 class="fw-bold mb-1">
                Patient List
            </h2>

            <p class="text-muted mb-0">
                Patient details and SQL-calculated information
            </p>

        </div>

        <?php if ($_SESSION["role"] === "admin"): ?>

            <a
                href="add.php"
                class="btn btn-primary">
                + Register Patient
            </a>

        <?php endif; ?>


    </div>

    <!-- ================================================= -->
    <!-- SEARCH & FILTERS -->
    <!-- ================================================= -->

    <div class="card border-0 shadow-sm mb-4">

        <div class="card-body">


            <form method="GET">

                <div class="row g-3 align-items-end">

                    <!-- SEARCH -->

                    <div class="col-12 col-md-5">

                        <label class="form-label fw-semibold">
                            Search Patient
                        </label>

                        <input
                            type="text"
                            name="search"
                            class="form-control"
                            placeholder="Search by name or phone"
                            value="<?= htmlspecialchars($search) ?>">

                    </div>


                    <!-- JOIN YEAR -->

                    <div class="col-12 col-md-3">

                        <label class="form-label fw-semibold">
                            Join Year
                        </label>

                        <select
                            name="join_year"
                            class="form-select">

                            <option value="">
                                All Years
                            </option>

                            <?php

                            $year_sql = "
                            SELECT DISTINCT YEAR(join_date) AS join_year
                            FROM patients
                            ORDER BY join_year DESC
                        ";

                            $year_result = $conn->query($year_sql);

                            ?>

                            <?php while ($year_row = $year_result->fetch_assoc()): ?>

                                <option
                                    value="<?= $year_row["join_year"] ?>"
                                    <?= ($join_year == $year_row["join_year"])
                                        ? "selected"
                                        : "" ?>>

                                    <?= $year_row["join_year"] ?>

                                </option>

                            <?php endwhile; ?>

                        </select>

                    </div>


                    <!-- VISIT STATUS -->

                    <div class="col-12 col-md-2">

                        <label class="form-label fw-semibold">
                            Visit Status
                        </label>

                        <select
                            name="visit_status"
                            class="form-select">

                            <option
                                value=""
                                <?= $visit_status === ""
                                    ? "selected"
                                    : "" ?>>
                                All
                            </option>

                            <option
                                value="has_visits"
                                <?= $visit_status === "has_visits"
                                    ? "selected"
                                    : "" ?>>
                                Has Visits
                            </option>

                            <option
                                value="no_visits"
                                <?= $visit_status === "no_visits"
                                    ? "selected"
                                    : "" ?>>
                                No Visits
                            </option>

                        </select>

                    </div>


                    <!-- BUTTONS -->

                    <div class="col-12 col-md-2">

                        <div class="d-flex gap-2">

                            <button
                                type="submit"
                                class="btn btn-primary flex-grow-1">

                                Search

                            </button>

                            <a
                                href="list.php"
                                class="btn btn-outline-secondary">

                                Reset

                            </a>

                        </div>

                    </div>

                </div>

            </form>

        </div>

    </div>

    <div class="card border-0 shadow-sm">

        <div class="card-body p-0">

            <div class="card-header bg-primary text-white">

                <div class="d-flex justify-content-between align-items-center">

                    <h5 class="mb-0">
                        Patient Visits
                    </h5>

                    <span class="badge bg-light text-primary">

                        <?= $total_records ?> Patients

                    </span>

                </div>

            </div>

            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <thead class="table-primary">

                        <tr>

                            <th>ID</th>

                            <th>Patient </th>

                            <th>DOB</th>

                            <th>Age</th>

                            <th>Join Date</th>

                            <th>Join Year</th>

                            <th>Join Month</th>

                            <th>Join Day</th>

                            <th>Total Visits</th>

                            <th>Actions</th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php if ($result->num_rows > 0): ?>

                            <?php while ($row = $result->fetch_assoc()): ?>

                                <tr>

                                    <td>
                                        <span class="badge text-bg-secondary">
                                            <?= $row["patient_id"] ?>
                                        </span>

                                    </td>

                                    <td>

                                        <div class="fw-semibold">
                                            <?= htmlspecialchars($row["name"]) ?>
                                        </div>

                                        <small class="text-muted">
                                            <?= htmlspecialchars($row["phone"]) ?>
                                        </small>

                                    </td>

                                    <td>
                                        <?= htmlspecialchars($row["dob"]) ?>
                                    </td>

                                    <td>

                                        <span class="badge text-bg-info">

                                            <?= $row["age_years"] ?>
                                            years
                                            <?= $row["remaining_months"] ?>
                                            months

                                        </span>

                                    </td>

                                    <td>
                                        <?= htmlspecialchars($row["join_date"]) ?>
                                    </td>

                                    <td>
                                        <?= $row["join_year"] ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($row["join_month_name"]) ?>
                                    </td>

                                    <td>
                                        <?= $row["join_day"] ?>
                                    </td>

                                    <td>

                                        <?php if ($row["total_visits"] > 0): ?>

                                            <span class="badge text-bg-success">
                                                <?= $row["total_visits"] ?>
                                            </span>

                                        <?php else: ?>

                                            <span class="badge text-bg-warning">
                                                No visits
                                            </span>

                                        <?php endif; ?>

                                    </td>

                                    <td>

                                        <div class="d-flex gap-1">

                                            <a
                                                href="view.php?id=<?= $row["patient_id"] ?>"
                                                class="btn btn-sm btn-outline-primary">
                                                View
                                            </a>

                                            <?php if ($_SESSION["role"] === "admin"): ?>

                                                <a
                                                    href="edit.php?id=<?= $row["patient_id"] ?>"
                                                    class="btn btn-sm btn-outline-warning">
                                                    Edit
                                                </a>

                                            <?php endif; ?>

                                        </div>

                                    </td>

                                </tr>

                            <?php endwhile; ?>

                        <?php else: ?>

                            <tr>

                                <td
                                    colspan="10"
                                    class="text-center py-5">

                                    <h5 class="text-muted">
                                        No patients found
                                    </h5>

                                    <a
                                        href="add.php"
                                        class="btn btn-primary">
                                        Add First Patient
                                    </a>

                                </td>

                            </tr>

                        <?php endif; ?>

                    </tbody>

                </table>

                <!-- ================================================= -->
                <!-- PAGINATION -->
                <!-- ================================================= -->

                <?php if ($total_pages > 1): ?>

                    <div class="d-flex justify-content-center py-4">

                        <nav aria-label="Patient pagination">

                            <ul class="pagination mb-0">


                                <!-- PREVIOUS -->

                                <li
                                    class="page-item
                    <?= ($page <= 1) ? 'disabled' : '' ?>">

                                    <?php if ($page > 1): ?>

                                        <a
                                            class="page-link"
                                            href="?search=<?= urlencode($search) ?>&join_year=<?= urlencode($join_year) ?>&visit_status=<?= urlencode($visit_status) ?>&page=<?= $page - 1 ?>">
                                            Previous
                                        </a>

                                    <?php else: ?>

                                        <span class="page-link">
                                            Previous
                                        </span>

                                    <?php endif; ?>

                                </li>


                                <!-- PAGE NUMBERS -->

                                <?php for (
                                    $i = 1;
                                    $i <= $total_pages;
                                    $i++
                                ): ?>

                                    <li
                                        class="page-item
                        <?= ($i == $page) ? 'active' : '' ?>">

                                        <a
                                            class="page-link"
                                            href="?search=<?= urlencode($search) ?>&join_year=<?= urlencode($join_year) ?>&visit_status=<?= urlencode($visit_status) ?>&page=<?= $i ?>">

                                            <?= $i ?>

                                        </a>

                                    </li>

                                <?php endfor; ?>


                                <!-- NEXT -->

                                <li
                                    class="page-item
                    <?= ($page >= $total_pages) ? 'disabled' : '' ?>">

                                    <?php if ($page < $total_pages): ?>

                                        <a
                                            class="page-link"
                                            href="?search=<?= urlencode($search) ?>&join_year=<?= urlencode($join_year) ?>&visit_status=<?= urlencode($visit_status) ?>&page=<?= $page + 1 ?>">
                                            Next
                                        </a>

                                    <?php else: ?>

                                        <span class="page-link">
                                            Next
                                        </span>

                                    <?php endif; ?>

                                </li>


                            </ul>

                        </nav>

                    </div>

                <?php endif; ?>

            </div>

        </div>

    </div>

</div>

<?php require_once "../includes/footer.php"; ?>