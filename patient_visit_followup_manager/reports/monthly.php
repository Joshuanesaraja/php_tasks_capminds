<?php
require_once "../account/auth.php";
require_once "../config/db.php";

$page_title = "Monthly Report";


// ============================================================
// 1. VISITS PER MONTH - LAST 6 MONTHS
// ============================================================
//
// We use SQL to:
//
// - Find the first day of the month 5 months ago
// - Include the current month
// - Group visits by year and month
//
// PHP does NOT calculate any dates.
//
// ============================================================

$visits_monthly_sql = "

    SELECT

        YEAR(visit_date) AS visit_year,

        MONTH(visit_date) AS visit_month,

        DATE_FORMAT(
            visit_date,
            '%M %Y'
        ) AS month_name,

        COUNT(*) AS total_visits

    FROM visits

    WHERE visit_date >=
        DATE_FORMAT(
            DATE_SUB(
                CURDATE(),
                INTERVAL 5 MONTH
            ),
            '%Y-%m-01'
        )

    GROUP BY

        YEAR(visit_date),
        MONTH(visit_date)

    ORDER BY

        visit_year ASC,
        visit_month ASC

";

$visits_monthly_result = $conn->query($visits_monthly_sql);


if (!$visits_monthly_result) {

    die("Monthly visits query failed: "
        . $conn->error);
}


// ============================================================
// 2. PATIENTS JOINED PER MONTH
// ============================================================
//
// Groups patients according to their join_date.
//
// ============================================================

$patients_joined_sql = "

    SELECT

        YEAR(join_date) AS join_year,

        MONTH(join_date) AS join_month,

        DATE_FORMAT(
            join_date,
            '%M %Y'
        ) AS month_name,

        COUNT(*) AS patients_joined

    FROM patients

    GROUP BY

        YEAR(join_date),
        MONTH(join_date)

    ORDER BY

        join_year ASC,
        join_month ASC

";

$patients_joined_result = $conn->query(
    $patients_joined_sql
);


if (!$patients_joined_result) {

    die("Patients joined query failed: "
        . $conn->error);
}


// ============================================================
// 3. VISITS LINKED TO JOIN-MONTH GROUPS
// ============================================================
//
// Example:
//
// Patients who joined in February 2026
//       ↓
// How many visits did those patients make?
//
// We JOIN patients and visits using patient_id.
//
// Then we group according to the patient's join month.
//
// ============================================================

$join_month_visits_sql = "

    SELECT

        YEAR(p.join_date) AS join_year,

        MONTH(p.join_date) AS join_month,

        DATE_FORMAT(
            p.join_date,
            '%M %Y'
        ) AS join_month_name,

        COUNT(v.visit_id) AS total_visits

    FROM patients p

    LEFT JOIN visits v
        ON p.patient_id = v.patient_id

    GROUP BY

        YEAR(p.join_date),
        MONTH(p.join_date)

    ORDER BY

        join_year ASC,
        join_month ASC

";

$join_month_visits_result = $conn->query(
    $join_month_visits_sql
);


if (!$join_month_visits_result) {

    die("Join-month visits query failed: "
        . $conn->error);
}


?>

<?php require_once "../includes/header.php"; ?>


<div class="container-fluid py-4">


    <!-- ================================================= -->
    <!-- PAGE HEADER -->
    <!-- ================================================= -->

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h2 class="fw-bold mb-1">
                Monthly Healthcare Report
            </h2>

            <p class="text-muted mb-0">
                Monthly visits, patient registrations and join-month analysis
            </p>

        </div>

    </div>



    <!-- ================================================= -->
    <!-- 1. VISITS PER MONTH -->
    <!-- ================================================= -->

    <div class="card border-0 shadow-sm mb-4">


        <div class="card-header bg-primary text-white">

            <div class="d-flex justify-content-between align-items-center">

                <h5 class="mb-0">
                    Visits Per Month
                </h5>

                <span class="badge bg-light text-primary">
                    Last 6 Months
                </span>

            </div>

        </div>


        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">


                    <thead class="table-light">

                        <tr>

                            <th>
                                Month
                            </th>

                            <th>
                                Year
                            </th>

                            <th>
                                Total Visits
                            </th>

                        </tr>

                    </thead>


                    <tbody>


                        <?php if ($visits_monthly_result->num_rows > 0): ?>


                            <?php while ($row = $visits_monthly_result->fetch_assoc()): ?>


                                <tr>

                                    <td>

                                        <strong>
                                            <?= htmlspecialchars($row["month_name"]) ?>
                                        </strong>

                                    </td>


                                    <td>
                                        <?= htmlspecialchars($row["visit_year"]) ?>
                                    </td>


                                    <td>

                                        <span class="badge text-bg-primary">

                                            <?= $row["total_visits"] ?>

                                        </span>

                                    </td>

                                </tr>


                            <?php endwhile; ?>


                        <?php else: ?>


                            <tr>

                                <td
                                    colspan="3"
                                    class="text-center py-4 text-muted">

                                    No visits found for the last 6 months.

                                </td>

                            </tr>


                        <?php endif; ?>


                    </tbody>

                </table>

            </div>

        </div>

    </div>



    <!-- ================================================= -->
    <!-- 2. PATIENTS JOINED PER MONTH -->
    <!-- ================================================= -->

    <div class="card border-0 shadow-sm mb-4">


        <div class="card-header bg-success text-white">

            <h5 class="mb-0">
                Patients Joined Per Month
            </h5>

        </div>


        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">


                    <thead class="table-light">

                        <tr>

                            <th>
                                Month
                            </th>

                            <th>
                                Year
                            </th>

                            <th>
                                Patients Joined
                            </th>

                        </tr>

                    </thead>


                    <tbody>


                        <?php if ($patients_joined_result->num_rows > 0): ?>


                            <?php while ($row = $patients_joined_result->fetch_assoc()): ?>


                                <tr>

                                    <td>

                                        <strong>
                                            <?= htmlspecialchars($row["month_name"]) ?>
                                        </strong>

                                    </td>


                                    <td>
                                        <?= htmlspecialchars($row["join_year"]) ?>
                                    </td>


                                    <td>

                                        <span class="badge text-bg-success">

                                            <?= $row["patients_joined"] ?>

                                        </span>

                                    </td>

                                </tr>


                            <?php endwhile; ?>


                        <?php else: ?>


                            <tr>

                                <td
                                    colspan="3"
                                    class="text-center py-4 text-muted">

                                    No patient registrations found.

                                </td>

                            </tr>


                        <?php endif; ?>


                    </tbody>

                </table>

            </div>

        </div>

    </div>



    <!-- ================================================= -->
    <!-- 3. VISITS LINKED TO JOIN-MONTH GROUPS -->
    <!-- ================================================= -->

    <div class="card border-0 shadow-sm mb-4">


        <div class="card-header bg-warning">

            <h5 class="mb-0">
                Visits Linked to Join-Month Groups
            </h5>

        </div>


        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">


                    <thead class="table-light">

                        <tr>

                            <th>
                                Patient Join Month
                            </th>

                            <th>
                                Year
                            </th>

                            <th>
                                Total Visits
                            </th>

                        </tr>

                    </thead>


                    <tbody>


                        <?php if ($join_month_visits_result->num_rows > 0): ?>


                            <?php while ($row = $join_month_visits_result->fetch_assoc()): ?>


                                <tr>

                                    <td>

                                        <strong>
                                            <?= htmlspecialchars($row["join_month_name"]) ?>
                                        </strong>

                                    </td>


                                    <td>
                                        <?= htmlspecialchars($row["join_year"]) ?>
                                    </td>


                                    <td>

                                        <span class="badge text-bg-warning">

                                            <?= $row["total_visits"] ?>

                                        </span>

                                    </td>

                                </tr>


                            <?php endwhile; ?>


                        <?php else: ?>


                            <tr>

                                <td
                                    colspan="3"
                                    class="text-center py-4 text-muted">

                                    No join-month data found.

                                </td>

                            </tr>


                        <?php endif; ?>


                    </tbody>

                </table>

            </div>

        </div>

    </div>


</div>


<?php require_once "../includes/footer.php"; ?>