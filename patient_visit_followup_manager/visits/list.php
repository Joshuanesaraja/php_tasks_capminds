<?php

require_once "../config/db.php";

$page_title = "Visit List";

// Search & Filter Inputs
$search = $_GET["search"] ?? "";
$visit_status = $_GET["visit_status"] ?? "";
$visit_date = $_GET["visit_date"] ?? "";


// Pagination
$limit = 10;

$page = isset($_GET["page"])
    ? (int) $_GET["page"]
    : 1;

if ($page < 1) {
    $page = 1;
}

/*
|--------------------------------------------------------------------------
| Fetch Visits
|--------------------------------------------------------------------------
|
| All date calculations are performed inside SQL.
|
*/

$sql = "
    SELECT

        v.visit_id,
        v.patient_id,

        p.name AS patient_name,

        v.visit_date,
        v.consultation_fee,
        v.lab_fee,
        v.follow_up_due,

        /*
        |--------------------------------------------------------------------------
        | Total visit cost
        |--------------------------------------------------------------------------
        */

        (
            v.consultation_fee + v.lab_fee
        ) AS total_fee,

        /*
        |--------------------------------------------------------------------------
        | Days since visit
        |--------------------------------------------------------------------------
        */

        DATEDIFF(
            CURDATE(),
            v.visit_date
        ) AS days_since_visit,

        /*
        |--------------------------------------------------------------------------
        | Follow-up status
        |--------------------------------------------------------------------------
        */

        CASE

            WHEN v.follow_up_due < CURDATE()
                THEN 'OVERDUE'

            WHEN v.follow_up_due = CURDATE()
                THEN 'DUE TODAY'

            ELSE 'UPCOMING'

        END AS follow_up_status

    FROM visits v

INNER JOIN patients p
    ON v.patient_id = p.patient_id
";


// ============================================================
// SEARCH & FILTER CONDITIONS
// ============================================================

$conditions = [];


// Search by patient name or phone

if ($search !== "") {

    $safe_search = $conn->real_escape_string($search);

    $conditions[] = "
        (
            p.name LIKE '%$safe_search%'
            OR p.phone LIKE '%$safe_search%'
        )
    ";
}


// Filter by visit date

if ($visit_date !== "") {

    $safe_visit_date = $conn->real_escape_string($visit_date);

    $conditions[] = "
        v.visit_date = '$safe_visit_date'
    ";
}


// Filter by follow-up status

if ($visit_status === "overdue") {

    $conditions[] = "
        v.follow_up_due < CURDATE()
    ";
} elseif ($visit_status === "today") {

    $conditions[] = "
        v.follow_up_due = CURDATE()
    ";
} elseif ($visit_status === "upcoming") {

    $conditions[] = "
        v.follow_up_due > CURDATE()
    ";
}


// Add WHERE only when a filter was selected

if (count($conditions) > 0) {

    $sql .= "
        WHERE
        " . implode(" AND ", $conditions);
}

// ============================================================
// COUNT FILTERED VISITS
// ============================================================

$count_sql = "

    SELECT COUNT(*) AS total_records

    FROM visits v

    INNER JOIN patients p
        ON v.patient_id = p.patient_id

";

if (count($conditions) > 0) {

    $count_sql .= "
        WHERE
        " . implode(" AND ", $conditions);
}

$count_result = $conn->query($count_sql);

if (!$count_result) {

    die("Count query failed: "
        . $conn->error);
}

$count_row = $count_result->fetch_assoc();

$total_records = (int) $count_row["total_records"];

$total_pages = (int) ceil(
    $total_records / $limit
);

$offset = ($page - 1) * $limit;

// Existing ordering remains unchanged

$sql .= "

    ORDER BY
        v.visit_date DESC,
        v.visit_id DESC

    LIMIT $limit
    OFFSET $offset
";
$result = $conn->query($sql);


if (!$result) {

    die("Query failed: " . $conn->error);
}


require_once "../includes/header.php";

?>

<div class="container-fluid py-4">


    <!-- Page Header -->

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">

        <div>

            <h2 class="fw-bold mb-1">
                Visit List
            </h2>

            <p class="text-muted mb-0">
                All patient visits and SQL-calculated follow-up information.
            </p>

        </div>

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
                            placeholder="Search by patient name or phone"
                            value="<?= htmlspecialchars($search) ?>">

                    </div>


                    <!-- VISIT DATE -->

                    <div class="col-12 col-md-3">

                        <label class="form-label fw-semibold">
                            Visit Date
                        </label>

                        <input
                            type="date"
                            name="visit_date"
                            class="form-control"
                            value="<?= htmlspecialchars($visit_date) ?>">

                    </div>


                    <!-- FOLLOW-UP STATUS -->

                    <div class="col-12 col-md-2">

                        <label class="form-label fw-semibold">
                            Follow-Up Status
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
                                value="overdue"
                                <?= $visit_status === "overdue"
                                    ? "selected"
                                    : "" ?>>
                                Overdue
                            </option>

                            <option
                                value="today"
                                <?= $visit_status === "today"
                                    ? "selected"
                                    : "" ?>>
                                Due Today
                            </option>

                            <option
                                value="upcoming"
                                <?= $visit_status === "upcoming"
                                    ? "selected"
                                    : "" ?>>
                                Upcoming
                            </option>

                        </select>

                    </div>


                    <!-- BUTTONS -->

                    <div class="col-12 col-md-2">

                        <div class="d-flex gap-2">

                            <button
                                type="submit"
                                class="btn btn-primary flex-grow-1">
                                Filter
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

    <!-- Visit Table -->

    <div class="card border-0 shadow-sm">

        <div class="card-header bg-primary text-white">

            <div class="d-flex justify-content-between align-items-center">

                <h5 class="mb-0">
                    Patient Visits
                </h5>

                <span class="badge bg-light text-primary">

                    <?= $total_records ?> visits

                </span>

            </div>

        </div>


        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <thead class="table-primary">

                        <tr>

                            <th>
                                ID
                            </th>

                            <th>
                                Patient
                            </th>

                            <th>
                                Visit Date
                            </th>

                            <th>
                                Days Since Visit
                            </th>

                            <th>
                                Consultation
                            </th>

                            <th>
                                Lab
                            </th>

                            <th>
                                Total
                            </th>

                            <th>
                                Follow-Up
                            </th>

                            <th>
                                Status
                            </th>

                            <th>
                                Action
                            </th>

                        </tr>

                    </thead>


                    <tbody>


                        <?php if ($result->num_rows > 0): ?>


                            <?php while ($row = $result->fetch_assoc()): ?>


                                <tr>


                                    <!-- Visit ID -->

                                    <td>

                                        <span class="badge text-bg-secondary">

                                            <?= $row["visit_id"] ?>

                                        </span>

                                    </td>


                                    <!-- Patient -->

                                    <td>

                                        <div class="fw-semibold">

                                            <?= htmlspecialchars($row["patient_name"]) ?>

                                        </div>

                                        <small class="text-muted">

                                            Patient #<?= $row["patient_id"] ?>

                                        </small>

                                    </td>


                                    <!-- Visit Date -->

                                    <td>

                                        <?= htmlspecialchars($row["visit_date"]) ?>

                                    </td>


                                    <!-- Days Since Visit -->

                                    <td>


                                        <?php if ($row["days_since_visit"] == 0): ?>


                                            <span class="badge text-bg-success">

                                                Today

                                            </span>


                                        <?php else: ?>


                                            <span class="badge text-bg-info">

                                                <?= $row["days_since_visit"] ?>

                                                days

                                            </span>


                                        <?php endif; ?>


                                    </td>


                                    <!-- Consultation Fee -->

                                    <td>

                                        ₹<?= number_format(
                                                (float) $row["consultation_fee"],
                                                2
                                            ) ?>

                                    </td>


                                    <!-- Lab Fee -->

                                    <td>

                                        ₹<?= number_format(
                                                (float) $row["lab_fee"],
                                                2
                                            ) ?>

                                    </td>


                                    <!-- Total Fee -->

                                    <td>

                                        <strong>

                                            ₹<?= number_format(
                                                    (float) $row["total_fee"],
                                                    2
                                                ) ?>

                                        </strong>

                                    </td>


                                    <!-- Follow-Up Date -->

                                    <td>

                                        <?= htmlspecialchars($row["follow_up_due"]) ?>

                                    </td>


                                    <!-- Follow-Up Status -->

                                    <td>


                                        <?php if ($row["follow_up_status"] === "OVERDUE"): ?>


                                            <span class="badge text-bg-danger">

                                                Overdue

                                            </span>


                                        <?php elseif ($row["follow_up_status"] === "DUE TODAY"): ?>


                                            <span class="badge text-bg-warning">

                                                Due Today

                                            </span>


                                        <?php else: ?>


                                            <span class="badge text-bg-success">

                                                Upcoming

                                            </span>


                                        <?php endif; ?>


                                    </td>


                                    <!-- Action -->

                                    <td>

                                        <a
                                            href="patient_visits.php?id=<?= $row["patient_id"] ?>"
                                            class="btn btn-sm btn-outline-primary">

                                            History

                                        </a>

                                    </td>


                                </tr>


                            <?php endwhile; ?>


                        <?php else: ?>


                            <tr>

                                <td
                                    colspan="10"
                                    class="text-center py-5">

                                    <h5 class="text-muted">
                                        No visits found
                                    </h5>

                                    <p class="text-muted mb-3">
                                        Add a visit from a patient's profile.
                                    </p>

                                    <a
                                        href="../patients/list.php"
                                        class="btn btn-primary">

                                        View Patients

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

                        <nav aria-label="Visit pagination">

                            <ul class="pagination mb-0">


                                <!-- PREVIOUS -->

                                <li
                                    class="page-item
                    <?= ($page <= 1) ? 'disabled' : '' ?>">

                                    <?php if ($page > 1): ?>

                                        <a
                                            class="page-link"
                                            href="?search=<?= urlencode($search) ?>&visit_date=<?= urlencode($visit_date) ?>&visit_status=<?= urlencode($visit_status) ?>&page=<?= $page - 1 ?>">
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
                                            href="?search=<?= urlencode($search) ?>&visit_date=<?= urlencode($visit_date) ?>&visit_status=<?= urlencode($visit_status) ?>&page=<?= $i ?>">

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
                                            href="?search=<?= urlencode($search) ?>&visit_date=<?= urlencode($visit_date) ?>&visit_status=<?= urlencode($visit_status) ?>&page=<?= $page + 1 ?>">
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