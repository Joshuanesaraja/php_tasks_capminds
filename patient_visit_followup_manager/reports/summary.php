<?php
require_once "../account/auth.php";
require_once "../config/db.php";

$page_title = "Summary Report";


// ============================================================
// FULL SUMMARY REPORT
// ============================================================
//
// SQL calculates:
//
// 1. Age
// 2. Total visits
// 3. Last visit date
// 4. Days since last visit
// 5. Next follow-up
//
// PHP only displays the SQL output.
// ============================================================

$summary_sql = "

    SELECT

        p.patient_id,

        p.name,

        p.phone,

        p.join_date,

        /*
        --------------------------------------------------------
        AGE
        --------------------------------------------------------
        Exact age in completed years.
        --------------------------------------------------------
        */

        TIMESTAMPDIFF(
            YEAR,
            p.dob,
            CURDATE()
        ) AS age,


        /*
        --------------------------------------------------------
        TOTAL VISITS
        --------------------------------------------------------
        COALESCE() converts NULL to 0 for patients
        who have no visits. But here we dont need that because COUNT(NULL) = 0
        --------------------------------------------------------
        */

        COUNT(v.visit_id) AS total_visits,


        /*
        --------------------------------------------------------
        LAST VISIT
        --------------------------------------------------------
        */

        MAX(v.visit_date) AS last_visit_date,


        /*
        --------------------------------------------------------
        DAYS SINCE LAST VISIT
        --------------------------------------------------------
        DATEDIFF(NULL, CURDATE()) would return NULL.
        So we use CASE for patients with no visits.
        --------------------------------------------------------
        */

        CASE

            WHEN MAX(v.visit_date) IS NULL

            THEN NULL

            ELSE DATEDIFF(
                CURDATE(),
                MAX(v.visit_date)
            )

        END AS days_since_last_visit,

        CASE

            WHEN MAX(v.visit_date) IS NULL
            
            THEN NULL

            WHEN DATEDIFF(
                CURDATE(),
                MAX(v.visit_date)
            ) >= 180

            THEN 'Inactive'

            ELSE 'Active'

        END AS activity_status,

        /*
        --------------------------------------------------------
        NEXT FOLLOW-UP
        --------------------------------------------------------
        The earliest follow-up date that is today or later.
        --------------------------------------------------------
        */

        MIN(
            CASE

                WHEN v.follow_up_due >= CURDATE()

                THEN v.follow_up_due

                ELSE NULL

            END
        ) AS next_follow_up


    FROM patients p


    /*
    ------------------------------------------------------------
    LEFT JOIN
    ------------------------------------------------------------
    We use LEFT JOIN so that patients with ZERO visits
    are still included in the report.
    ------------------------------------------------------------
    */

    LEFT JOIN visits v

        ON p.patient_id = v.patient_id


    GROUP BY

        p.patient_id,
        p.name,
        p.phone,
        p.join_date,
        p.dob


    ORDER BY

        p.name ASC

";


$summary_result = $conn->query($summary_sql);


if (!$summary_result) {

    die("Summary query failed: "
        . $conn->error);
}

?>

<?php require_once "../includes/header.php"; ?>


<div class="container-fluid py-4">


    <!-- ================================================= -->
    <!-- PAGE HEADER -->
    <!-- ================================================= -->

    <div class="mb-4">

        <h2 class="fw-bold mb-1">
            Full Patient Summary
        </h2>

        <p class="text-muted mb-0">
            Complete patient and visit summary
        </p>

    </div>



    <!-- ================================================= -->
    <!-- SUMMARY TABLE -->
    <!-- ================================================= -->

    <div class="card border-0 shadow-sm">


        <div class="card-header bg-primary text-white">

            <div class="d-flex justify-content-between align-items-center">

                <h5 class="mb-0">
                    Patient Summary
                </h5>

                <span class="badge bg-light text-primary">

                    <?= $summary_result->num_rows ?>

                    Patients

                </span>

            </div>

        </div>


        <div class="card-body p-0">


            <div class="table-responsive">


                <table class="table table-hover align-middle mb-0">


                    <thead class="table-light">

                        <tr>

                            <th>
                                Patient
                            </th>

                            <th>
                                Age
                            </th>

                            <th>
                                Total Visits
                            </th>

                            <th>
                                Last Visit
                            </th>

                            <th>
                                Days Since Last Visit
                            </th>

                            <th>
                                Activity Status
                            </th>

                            <th>
                                Next Follow-Up
                            </th>

                        </tr>

                    </thead>


                    <tbody>


                        <?php if ($summary_result->num_rows > 0): ?>


                            <?php while ($row = $summary_result->fetch_assoc()): ?>


                                <tr>


                                    <!-- PATIENT -->

                                    <td>

                                        <strong>

                                            <?= htmlspecialchars(
                                                $row["name"]
                                            ) ?>

                                        </strong>

                                        <br>

                                        <small class="text-muted">

                                            Patient #
                                            <?= htmlspecialchars(
                                                $row["patient_id"]
                                            ) ?>

                                        </small>

                                    </td>



                                    <!-- AGE -->

                                    <td>

                                        <span class="badge text-bg-secondary">

                                            <?= $row["age"] ?>

                                            years

                                        </span>

                                    </td>



                                    <!-- TOTAL VISITS -->

                                    <td>

                                        <?php if ($row["total_visits"] > 0): ?>

                                            <span class="badge text-bg-primary">

                                                <?= $row["total_visits"] ?>

                                            </span>

                                        <?php else: ?>

                                            <span class="badge text-bg-warning">

                                                No visits

                                            </span>

                                        <?php endif; ?>

                                    </td>



                                    <!-- LAST VISIT -->

                                    <td>

                                        <?php if ($row["last_visit_date"] !== null): ?>

                                            <?= htmlspecialchars(
                                                $row["last_visit_date"]
                                            ) ?>

                                        <?php else: ?>

                                            <span class="text-muted">
                                                No visits
                                            </span>

                                        <?php endif; ?>

                                    </td>



                                    <!-- DAYS SINCE LAST VISIT -->

                                    <td>

                                        <?php if (
                                            $row["days_since_last_visit"] !== null
                                        ): ?>

                                            <span class="badge text-bg-info">

                                                <?= $row["days_since_last_visit"] ?>

                                                days

                                            </span>

                                        <?php else: ?>

                                            <span class="text-muted">
                                                —
                                            </span>

                                        <?php endif; ?>

                                    </td>


                                    <!-- Activity Status -->

                                    <td>

                                        <?php if ($row["activity_status"] === "Inactive"): ?>

                                            <span class="badge text-bg-danger">
                                                Inactive
                                            </span>

                                        <?php elseif ($row["activity_status"] === "Active"): ?>

                                            <span class="badge text-bg-success">
                                                Active
                                            </span>

                                        <?php else: ?>

                                            <span class="badge text-bg-secondary">
                                                No Visits
                                            </span>

                                        <?php endif; ?>

                                    </td>

                                    <!-- NEXT FOLLOW-UP -->

                                    <td>

                                        <?php if ($row["next_follow_up"] !== null): ?>

                                            <?php

                                            $followup = $row["next_follow_up"];

                                            ?>

                                            <strong>

                                                <?= htmlspecialchars(
                                                    $followup
                                                ) ?>

                                            </strong>

                                        <?php else: ?>

                                            <span class="text-muted">
                                                —
                                            </span>

                                        <?php endif; ?>

                                    </td>


                                </tr>


                            <?php endwhile; ?>


                        <?php else: ?>


                            <tr>

                                <td
                                    colspan="6"
                                    class="text-center py-4 text-muted">

                                    No patient records found.

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