<?php

require_once "../config/db.php";

$page_title = "Patient Details";

$patient_id = isset($_GET["id"]) ? (int) $_GET["id"] : 0;

if ($patient_id <= 0) {
    die("Invalid patient ID.");
}


/*
 * All calculations are performed inside SQL.
 */
$sql = "
    SELECT

        p.patient_id,
        p.name,
        p.dob,
        p.join_date,
        p.phone,
        p.address,

        /* Age in completed years */
        TIMESTAMPDIFF(
            YEAR,
            p.dob,
            CURDATE()
        ) AS age_years,

        /* Age in months */
        MOD(
            TIMESTAMPDIFF(
                MONTH,
                p.dob,
                CURDATE()
            ),
            12
        ) AS age_months,

        /* Total visits */
        COUNT(v.visit_id) AS total_visits,

        /* Most recent visit */
        MAX(v.visit_date) AS last_visit_date,

        /* Days since last visit */
        CASE

            WHEN MAX(v.visit_date) IS NULL
                THEN NULL

            ELSE DATEDIFF(
                CURDATE(),
                MAX(v.visit_date)
            )

        END AS days_since_last_visit,

        /* Next follow-up */
        MIN(
            CASE
                WHEN v.follow_up_due >= CURDATE()
                    THEN v.follow_up_due
                ELSE NULL
            END
        ) AS next_follow_up,

        /* Check whether the latest follow-up is overdue */
        CASE

            WHEN MAX(v.follow_up_due) IS NULL
                THEN 'NO FOLLOW-UP'

            WHEN MAX(v.follow_up_due) < CURDATE()
                THEN 'OVERDUE'

            ELSE 'UPCOMING'

        END AS follow_up_status

    FROM patients p

    LEFT JOIN visits v
        ON p.patient_id = v.patient_id

    WHERE p.patient_id = ?

    GROUP BY

        p.patient_id,
        p.name,
        p.dob,
        p.join_date,
        p.phone,
        p.address
";


$stmt = $conn->prepare($sql);

$stmt->bind_param("i", $patient_id);

$stmt->execute();

$result = $stmt->get_result();

$patient = $result->fetch_assoc();

$stmt->close();


if (!$patient) {
    die("Patient not found.");
}

require_once "../includes/header.php";

?>

<div class="container py-4">

    <!-- Page Header -->

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">

        <div>

            <h2 class="fw-bold mb-1">
                Patient Details
            </h2>

            <p class="text-muted mb-0">
                SQL-generated patient information
            </p>

        </div>

        <div class="d-flex gap-2">

            <a
                href="list.php"
                class="btn btn-outline-secondary">
                Back to Patients
            </a>

            <a
                href="../visits/add.php?patient_id=<?= $patient["patient_id"] ?>"
                class="btn btn-primary">
                + Add Visit
            </a>

        </div>

    </div>


    <!-- Patient Information -->

    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-primary text-white">

            <h5 class="mb-0">
                <?= htmlspecialchars($patient["name"]) ?>
            </h5>

        </div>


        <div class="card-body">

            <div class="row g-4">

                <div class="col-md-6">

                    <small class="text-muted">
                        Patient ID
                    </small>

                    <div class="fw-semibold">
                        <?= $patient["patient_id"] ?>
                    </div>

                </div>


                <div class="col-md-6">

                    <small class="text-muted">
                        Phone
                    </small>

                    <div class="fw-semibold">
                        <?= htmlspecialchars($patient["phone"]) ?>
                    </div>

                </div>


                <div class="col-md-6">

                    <small class="text-muted">
                        Date of Birth
                    </small>

                    <div class="fw-semibold">
                        <?= $patient["dob"] ?>
                    </div>

                </div>


                <div class="col-md-6">

                    <small class="text-muted">
                        Age
                    </small>

                    <div>

                        <span class="badge text-bg-info fs-6">

                            <?= $patient["age_years"] ?>
                            years
                            <?= $patient["age_months"] ?>
                            months

                        </span>

                    </div>

                </div>


                <div class="col-md-6">

                    <small class="text-muted">
                        Join Date
                    </small>

                    <div class="fw-semibold">
                        <?= $patient["join_date"] ?>
                    </div>

                </div>


                <div class="col-md-6">

                    <small class="text-muted">
                        Address
                    </small>

                    <div class="fw-semibold">

                        <?= htmlspecialchars($patient["address"]) ?>

                    </div>

                </div>

            </div>

        </div>

    </div>


    <!-- Statistics -->

    <div class="row g-4">

        <!-- Total Visits -->

        <div class="col-md-6 col-lg-3">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body">

                    <p class="text-muted mb-2">
                        Total Visits
                    </p>

                    <h3 class="fw-bold">

                        <?= $patient["total_visits"] ?>

                    </h3>

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

                    <h5 class="fw-bold">

                        <?= $patient["last_visit_date"] ?? "No visits" ?>

                    </h5>

                </div>

            </div>

        </div>


        <!-- Days Since Last Visit -->

        <div class="col-md-6 col-lg-3">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body">

                    <p class="text-muted mb-2">
                        Days Since Last Visit
                    </p>

                    <h3 class="fw-bold">

                        <?php if ($patient["days_since_last_visit"] !== null): ?>

                            <?= $patient["days_since_last_visit"] ?>

                        <?php else: ?>

                            -

                        <?php endif; ?>

                    </h3>

                </div>

            </div>

        </div>


        <!-- Next Follow-Up -->

        <div class="col-md-6 col-lg-3">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body">

                    <p class="text-muted mb-2">
                        Next Follow-Up
                    </p>

                    <h5 class="fw-bold">

                        <?= $patient["next_follow_up"] ?? "None" ?>

                    </h5>


                    <?php if ($patient["follow_up_status"] === "OVERDUE"): ?>

                        <span class="badge text-bg-danger">
                            Overdue
                        </span>

                    <?php elseif ($patient["follow_up_status"] === "UPCOMING"): ?>

                        <span class="badge text-bg-success">
                            Upcoming
                        </span>

                    <?php else: ?>

                        <span class="badge text-bg-secondary">
                            No Follow-Up
                        </span>

                    <?php endif; ?>

                </div>

            </div>

        </div>

    </div>


    <!-- Patient Visits Button -->

    <div class="mt-4">

        <a
            href="../visits/patient_visits.php?id=<?= $patient["patient_id"] ?>"
            class="btn btn-outline-primary">
            View Visit History
        </a>

    </div>

</div>


<?php require_once "../includes/footer.php"; ?>