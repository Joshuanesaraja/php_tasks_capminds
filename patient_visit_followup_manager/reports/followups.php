<?php

require_once "../config/db.php";

$page_title = "Follow-Up Report";


/*
|--------------------------------------------------------------------------
| Upcoming Follow-Ups
|--------------------------------------------------------------------------
|
| Follow-ups from today through the next 7 days.
|
| CURDATE()              = today
| DATE_ADD(CURDATE(), 7) = seven days from today
|
*/

$upcoming_sql = "
    SELECT

        v.visit_id,
        v.patient_id,
        p.name AS patient_name,
        p.phone,

        v.visit_date,
        v.follow_up_due,

        DATEDIFF(
            v.follow_up_due,
            CURDATE()
        ) AS days_until_followup

    FROM visits v

    INNER JOIN patients p
        ON v.patient_id = p.patient_id

    WHERE
        v.follow_up_due >= CURDATE()

        AND

        v.follow_up_due <= DATE_ADD(
            CURDATE(),
            INTERVAL 7 DAY
        )

    ORDER BY
        v.follow_up_due ASC,
        p.name ASC
";

$upcoming_result = $conn->query($upcoming_sql);


if (!$upcoming_result) {

    die("Upcoming follow-up query failed: " . $conn->error);
}


/*
|--------------------------------------------------------------------------
| Overdue Follow-Ups
|--------------------------------------------------------------------------
|
| Follow-up date is before today.
|
*/

$overdue_sql = "
    SELECT

        v.visit_id,
        v.patient_id,
        p.name AS patient_name,
        p.phone,

        v.visit_date,
        v.follow_up_due,

        DATEDIFF(
            CURDATE(),
            v.follow_up_due
        ) AS days_overdue

    FROM visits v

    INNER JOIN patients p
        ON v.patient_id = p.patient_id

    WHERE
        v.follow_up_due < CURDATE()

    ORDER BY
        v.follow_up_due ASC,
        p.name ASC
";

$overdue_result = $conn->query($overdue_sql);


if (!$overdue_result) {

    die("Overdue follow-up query failed: " . $conn->error);
}


/*
|--------------------------------------------------------------------------
| Missed Follow-Ups
|--------------------------------------------------------------------------
|
| A follow-up is missed when:
|
| 1. The follow-up date has already passed.
|
| 2. The patient has NOT had another visit
|    on or after the follow-up due date.
|
| All date calculations are performed inside SQL.
|
*/

$missed_sql = "
    SELECT

        v.visit_id,
        v.patient_id,
        p.name AS patient_name,
        p.phone,

        v.visit_date,
        v.follow_up_due,

        DATEDIFF(
            CURDATE(),
            v.follow_up_due
        ) AS days_missed

    FROM visits v

    INNER JOIN patients p
        ON v.patient_id = p.patient_id

    WHERE

        v.follow_up_due < CURDATE()

        AND NOT EXISTS
        (
            SELECT 1

            FROM visits v2

            WHERE
                v2.patient_id = v.patient_id

                AND v2.visit_date >= v.follow_up_due
        )

    ORDER BY
        v.follow_up_due ASC,
        p.name ASC
";

$missed_result = $conn->query($missed_sql);


if (!$missed_result) {

    die("Missed follow-up query failed: "
        . $conn->error);
}


require_once "../includes/header.php";

?>

<div class="container-fluid py-4">


    <!-- Page Header -->

    <div class="mb-4">

        <h2 class="fw-bold mb-1">
            Follow-Up Report
        </h2>

        <p class="text-muted mb-0">
            Upcoming, overdue and missed patient follow-ups.
            All date calculations are performed by SQL.
        </p>

    </div>


    <!-- ========================================================= -->
    <!-- Upcoming Follow-Ups -->
    <!-- ========================================================= -->

    <div class="card border-0 shadow-sm mb-4">


        <div class="card-header bg-success text-white">

            <div class="d-flex justify-content-between align-items-center">

                <h5 class="mb-0">
                    Upcoming Follow-Ups
                </h5>

                <span class="badge bg-light text-success">

                    <?= $upcoming_result->num_rows ?>

                </span>

            </div>

        </div>


        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">


                    <thead class="table-success">

                        <tr>

                            <th>
                                Patient
                            </th>

                            <th>
                                Phone
                            </th>

                            <th>
                                Previous Visit
                            </th>

                            <th>
                                Follow-Up Due
                            </th>

                            <th>
                                Days Until
                            </th>

                            <th>
                                Action
                            </th>

                        </tr>

                    </thead>


                    <tbody>


                        <?php if ($upcoming_result->num_rows > 0): ?>


                            <?php while ($row = $upcoming_result->fetch_assoc()): ?>


                                <tr>


                                    <td>

                                        <div class="fw-semibold">

                                            <?= htmlspecialchars(
                                                $row["patient_name"]
                                            ) ?>

                                        </div>

                                        <small class="text-muted">

                                            Patient #<?= $row["patient_id"] ?>

                                        </small>

                                    </td>


                                    <td>

                                        <?= htmlspecialchars(
                                            $row["phone"] ?? "-"
                                        ) ?>

                                    </td>


                                    <td>

                                        <?= htmlspecialchars(
                                            $row["visit_date"]
                                        ) ?>

                                    </td>


                                    <td>

                                        <?= htmlspecialchars(
                                            $row["follow_up_due"]
                                        ) ?>

                                    </td>


                                    <td>


                                        <?php if (
                                            $row["days_until_followup"] == 0
                                        ): ?>


                                            <span class="badge text-bg-warning">

                                                Due Today

                                            </span>


                                        <?php else: ?>


                                            <span class="badge text-bg-success">

                                                <?= $row["days_until_followup"] ?>

                                                days

                                            </span>


                                        <?php endif; ?>


                                    </td>


                                    <td>

                                        <a
                                            href="../visits/patient_visits.php?id=<?= $row["patient_id"] ?>"
                                            class="btn btn-sm btn-outline-primary">

                                            History

                                        </a>

                                    </td>


                                </tr>


                            <?php endwhile; ?>


                        <?php else: ?>


                            <tr>

                                <td
                                    colspan="6"
                                    class="text-center py-4 text-muted">

                                    No follow-ups due in the next 7 days.

                                </td>

                            </tr>


                        <?php endif; ?>


                    </tbody>

                </table>

            </div>

        </div>

    </div>


    <!-- ========================================================= -->
    <!-- Overdue Follow-Ups -->
    <!-- ========================================================= -->

    <div class="card border-0 shadow-sm mb-4">


        <div class="card-header bg-danger text-white">

            <div class="d-flex justify-content-between align-items-center">

                <h5 class="mb-0">
                    Overdue Follow-Ups
                </h5>

                <span class="badge bg-light text-danger">

                    <?= $overdue_result->num_rows ?>

                </span>

            </div>

        </div>


        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">


                    <thead class="table-danger">

                        <tr>

                            <th>
                                Patient
                            </th>

                            <th>
                                Phone
                            </th>

                            <th>
                                Previous Visit
                            </th>

                            <th>
                                Follow-Up Due
                            </th>

                            <th>
                                Days Overdue
                            </th>

                            <th>
                                Action
                            </th>

                        </tr>

                    </thead>


                    <tbody>


                        <?php if ($overdue_result->num_rows > 0): ?>


                            <?php while ($row = $overdue_result->fetch_assoc()): ?>


                                <tr>


                                    <td>

                                        <div class="fw-semibold">

                                            <?= htmlspecialchars(
                                                $row["patient_name"]
                                            ) ?>

                                        </div>

                                        <small class="text-muted">

                                            Patient #<?= $row["patient_id"] ?>

                                        </small>

                                    </td>


                                    <td>

                                        <?= htmlspecialchars(
                                            $row["phone"] ?? "-"
                                        ) ?>

                                    </td>


                                    <td>

                                        <?= htmlspecialchars(
                                            $row["visit_date"]
                                        ) ?>

                                    </td>


                                    <td>

                                        <?= htmlspecialchars(
                                            $row["follow_up_due"]
                                        ) ?>

                                    </td>


                                    <td>

                                        <span class="badge text-bg-danger">

                                            <?= $row["days_overdue"] ?>

                                            days

                                        </span>

                                    </td>


                                    <td>

                                        <a
                                            href="../visits/patient_visits.php?id=<?= $row["patient_id"] ?>"
                                            class="btn btn-sm btn-outline-primary">

                                            History

                                        </a>

                                    </td>


                                </tr>


                            <?php endwhile; ?>


                        <?php else: ?>


                            <tr>

                                <td
                                    colspan="6"
                                    class="text-center py-4 text-muted">

                                    No overdue follow-ups.

                                </td>

                            </tr>


                        <?php endif; ?>


                    </tbody>

                </table>

            </div>

        </div>

    </div>


    <!-- ========================================================= -->
    <!-- Missed Follow-Ups -->
    <!-- ========================================================= -->

    <div class="card border-0 shadow-sm mb-4">


        <div class="card-header bg-dark text-white">

            <div class="d-flex justify-content-between align-items-center">

                <h5 class="mb-0">
                    Missed Follow-Ups
                </h5>

                <span class="badge bg-light text-dark">

                    <?= $missed_result->num_rows ?>

                </span>

            </div>

        </div>


        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">


                    <thead class="table-dark">

                        <tr>

                            <th>
                                Patient
                            </th>

                            <th>
                                Phone
                            </th>

                            <th>
                                Visit Date
                            </th>

                            <th>
                                Follow-Up Due
                            </th>

                            <th>
                                Days Missed
                            </th>

                            <th>
                                Action
                            </th>

                        </tr>

                    </thead>


                    <tbody>


                        <?php if ($missed_result->num_rows > 0): ?>


                            <?php while ($row = $missed_result->fetch_assoc()): ?>


                                <tr>


                                    <td>

                                        <div class="fw-semibold">

                                            <?= htmlspecialchars(
                                                $row["patient_name"]
                                            ) ?>

                                        </div>

                                        <small class="text-muted">

                                            Patient #<?= $row["patient_id"] ?>

                                        </small>

                                    </td>


                                    <td>

                                        <?= htmlspecialchars(
                                            $row["phone"] ?? "-"
                                        ) ?>

                                    </td>


                                    <td>

                                        <?= htmlspecialchars(
                                            $row["visit_date"]
                                        ) ?>

                                    </td>


                                    <td>

                                        <?= htmlspecialchars(
                                            $row["follow_up_due"]
                                        ) ?>

                                    </td>


                                    <td>

                                        <span class="badge text-bg-dark">

                                            <?= $row["days_missed"] ?>

                                            days

                                        </span>

                                    </td>


                                    <td>

                                        <a
                                            href="../visits/patient_visits.php?id=<?= $row["patient_id"] ?>"
                                            class="btn btn-sm btn-outline-primary">

                                            History

                                        </a>

                                    </td>


                                </tr>


                            <?php endwhile; ?>


                        <?php else: ?>


                            <tr>

                                <td
                                    colspan="6"
                                    class="text-center py-4 text-muted">

                                    No missed follow-ups found.

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