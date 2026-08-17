<?php
require_once "../account/auth.php";
require_once "../config/db.php";

$page_title = "Patient Visit History";

$patient_id = isset($_GET["id"])
    ? (int) $_GET["id"]
    : 0;


/*
|--------------------------------------------------------------------------
| Validate Patient ID
|--------------------------------------------------------------------------
*/

if ($patient_id <= 0) {

    die("Invalid patient ID.");
}


/*
|--------------------------------------------------------------------------
| Fetch Patient Information
|--------------------------------------------------------------------------
*/

$patient_sql = "
    SELECT
        patient_id,
        name,
        dob,
        join_date,
        phone
    FROM patients
    WHERE patient_id = ?
";

$stmt = $conn->prepare($patient_sql);

$stmt->bind_param(
    "i",
    $patient_id
);

$stmt->execute();

$result = $stmt->get_result();

$patient = $result->fetch_assoc();

$stmt->close();


if (!$patient) {

    die("Patient not found.");
}


/*
|--------------------------------------------------------------------------
| Fetch Visit Summary
|--------------------------------------------------------------------------
|
| All calculations are performed by SQL.
|
*/

$summary_sql = "
    SELECT

        COUNT(visit_id) AS total_visits,

        MIN(visit_date) AS first_visit_date,

        MAX(visit_date) AS last_visit_date,

        CASE

            WHEN COUNT(visit_id) = 0
                THEN NULL

            ELSE DATEDIFF(
                MAX(visit_date),
                MIN(visit_date)
            )

        END AS days_between_visits,

        COALESCE(
            SUM(consultation_fee + lab_fee),
            0
        ) AS total_amount

        /*
        |--------------------------------------------------------------------------
        | COALESCE() returns the first non-NULL value from the values you give it., if COALESCE(NULL,0) it returns 0
        |--------------------------------------------------------------------------
        */


    FROM visits

    WHERE patient_id = ?
";

$stmt = $conn->prepare($summary_sql);

$stmt->bind_param(
    "i",
    $patient_id
);

$stmt->execute();

$result = $stmt->get_result();

$summary = $result->fetch_assoc();

$stmt->close();


/*
|--------------------------------------------------------------------------
| Fetch Individual Visit History
|--------------------------------------------------------------------------
*/

$visits_sql = "
    SELECT

        visit_id,
        visit_date,
        consultation_fee,
        lab_fee,

        (
            consultation_fee + lab_fee
        ) AS total_fee,

        follow_up_due,

        /*
        |--------------------------------------------------------------------------
        | Days since visit
        |--------------------------------------------------------------------------
        */

        DATEDIFF(
            CURDATE(),
            visit_date
        ) AS days_since_visit,

        /*
        |--------------------------------------------------------------------------
        | Follow-up status
        |--------------------------------------------------------------------------
        */

        CASE

            WHEN follow_up_due < CURDATE()
                THEN 'OVERDUE'

            WHEN follow_up_due = CURDATE()
                THEN 'DUE TODAY'

            ELSE 'UPCOMING'

        END AS follow_up_status

    FROM visits

    WHERE patient_id = ?

    ORDER BY
        visit_date DESC,
        visit_id DESC
";

$stmt = $conn->prepare($visits_sql);

$stmt->bind_param(
    "i",
    $patient_id
);

$stmt->execute();

$visits_result = $stmt->get_result();

$stmt->close();


/*
|--------------------------------------------------------------------------
| Include Header
|--------------------------------------------------------------------------
*/

require_once "../includes/header.php";

?>

<div class="container py-4">


    <!-- Page Header -->

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">

        <div>

            <h2 class="fw-bold mb-1">
                Visit History
            </h2>

            <p class="text-muted mb-0">

                Complete visit history for
                <strong>
                    <?= htmlspecialchars($patient["name"]) ?>
                </strong>

            </p>

        </div>


        <div class="d-flex flex-wrap gap-2">

            <a
                href="../patients/view.php?id=<?= $patient["patient_id"] ?>"
                class="btn btn-outline-secondary">

                Back to Patient

            </a>

            <?php if ($_SESSION["role"] === "admin"): ?>
            <a
                href="add.php?patient_id=<?= $patient["patient_id"] ?>"
                class="btn btn-primary">

                + Add Visit

            </a>
            <?php endif; ?>

        </div>

    </div>


    <!-- Patient Information -->

    <div class="card border-0 shadow-sm mb-4">

        <div class="card-body">

            <div class="row g-3">

                <div class="col-md-3">

                    <small class="text-muted">
                        Patient ID
                    </small>

                    <div class="fw-semibold">

                        #<?= $patient["patient_id"] ?>

                    </div>

                </div>


                <div class="col-md-3">

                    <small class="text-muted">
                        Patient Name
                    </small>

                    <div class="fw-semibold">

                        <?= htmlspecialchars($patient["name"]) ?>

                    </div>

                </div>


                <div class="col-md-3">

                    <small class="text-muted">
                        Date of Birth
                    </small>

                    <div class="fw-semibold">

                        <?= htmlspecialchars($patient["dob"]) ?>

                    </div>

                </div>


                <div class="col-md-3">

                    <small class="text-muted">
                        Join Date
                    </small>

                    <div class="fw-semibold">

                        <?= htmlspecialchars($patient["join_date"]) ?>

                    </div>

                </div>

            </div>

        </div>

    </div>


    <!-- Visit Summary -->

    <div class="row g-4 mb-4">


        <!-- Total Visits -->

        <div class="col-md-6 col-lg-3">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body">

                    <p class="text-muted mb-2">
                        Total Visits
                    </p>

                    <h3 class="fw-bold mb-0">

                        <?= $summary["total_visits"] ?>

                    </h3>

                </div>

            </div>

        </div>


        <!-- First Visit -->

        <div class="col-md-6 col-lg-3">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body">

                    <p class="text-muted mb-2">
                        First Visit
                    </p>

                    <h5 class="fw-bold mb-0">

                        <?= $summary["first_visit_date"] ?? "No visits" ?>

                    </h5>

                </div>

            </div>

        </div>


        <!-- Last Visit -->

        <div class="col-md-6 col-lg-3">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body">

                    <p class="text-muted mb-2">
                        Last Visit
                    </p>

                    <h5 class="fw-bold mb-0">

                        <?= $summary["last_visit_date"] ?? "No visits" ?>

                    </h5>

                </div>

            </div>

        </div>


        <!-- Days Between -->

        <div class="col-md-6 col-lg-3">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body">

                    <p class="text-muted mb-2">
                        Days Between First & Last
                    </p>

                    <h5 class="fw-bold mb-0">

                        <?php if ($summary["days_between_visits"] !== null): ?>

                            <?= $summary["days_between_visits"] ?>
                            days

                        <?php else: ?>

                            -

                        <?php endif; ?>

                    </h5>

                </div>

            </div>

        </div>

    </div>


    <!-- Total Amount -->

    <div class="card border-0 shadow-sm mb-4">

        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center">

                <div>

                    <p class="text-muted mb-1">
                        Total Amount Spent
                    </p>

                    <h3 class="fw-bold mb-0">

                        ₹<?= number_format(
                                (float) $summary["total_amount"],
                                2
                            ) ?>

                    </h3>

                </div>

            </div>

        </div>

    </div>


    <!-- Visit History Table -->

    <div class="card border-0 shadow-sm">

        <div class="card-header bg-primary text-white">

            <h5 class="mb-0">
                Visit Records
            </h5>

        </div>


        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <thead class="table-primary">

                        <tr>

                            <th>
                                Visit ID
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

                        </tr>

                    </thead>


                    <tbody>


                        <?php if ($visits_result->num_rows > 0): ?>


                            <?php while ($visit = $visits_result->fetch_assoc()): ?>


                                <tr>


                                    <!-- Visit ID -->

                                    <td>

                                        <span class="badge text-bg-secondary">

                                            #<?= $visit["visit_id"] ?>

                                        </span>

                                    </td>


                                    <!-- Visit Date -->

                                    <td>

                                        <?= htmlspecialchars(
                                            $visit["visit_date"]
                                        ) ?>

                                    </td>


                                    <!-- Days Since Visit -->

                                    <td>


                                        <?php if ($visit["days_since_visit"] == 0): ?>


                                            <span class="badge text-bg-success">

                                                Today

                                            </span>


                                        <?php else: ?>


                                            <?= $visit["days_since_visit"] ?>
                                            days

                                        <?php endif; ?>


                                    </td>


                                    <!-- Consultation Fee -->

                                    <td>

                                        ₹<?= number_format(
                                                (float) $visit["consultation_fee"],
                                                2
                                            ) ?>

                                    </td>


                                    <!-- Lab Fee -->

                                    <td>

                                        ₹<?= number_format(
                                                (float) $visit["lab_fee"],
                                                2
                                            ) ?>

                                    </td>


                                    <!-- Total Fee -->

                                    <td>

                                        <strong>

                                            ₹<?= number_format(
                                                    (float) $visit["total_fee"],
                                                    2
                                                ) ?>

                                        </strong>

                                    </td>


                                    <!-- Follow-Up -->

                                    <td>

                                        <?= htmlspecialchars(
                                            $visit["follow_up_due"]
                                        ) ?>

                                    </td>


                                    <!-- Status -->

                                    <td>


                                        <?php if (
                                            $visit["follow_up_status"]
                                            === "OVERDUE"
                                        ): ?>


                                            <span class="badge text-bg-danger">

                                                Overdue

                                            </span>


                                        <?php elseif (
                                            $visit["follow_up_status"]
                                            === "DUE TODAY"
                                        ): ?>


                                            <span class="badge text-bg-warning">

                                                Due Today

                                            </span>


                                        <?php else: ?>


                                            <span class="badge text-bg-success">

                                                Upcoming

                                            </span>


                                        <?php endif; ?>


                                    </td>


                                </tr>


                            <?php endwhile; ?>


                        <?php else: ?>


                            <tr>

                                <td
                                    colspan="8"
                                    class="text-center py-5">

                                    <h5 class="text-muted">

                                        No visits found

                                    </h5>

                                    <p class="text-muted mb-3">

                                        This patient has no recorded visits.

                                    </p>

                                    <?php if ($_SESSION["role"] === "admin"): ?>
                                    <a
                                        href="add.php?patient_id=<?= $patient["patient_id"] ?>"
                                        class="btn btn-primary">

                                        Add First Visit

                                    </a>
                                    <?php endif; ?>        
                                    

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