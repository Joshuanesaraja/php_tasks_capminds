<?php

require_once "../config/db.php";

$page_title = "Birthday Report";


// ============================================================
// 1. BIRTHDAYS IN NEXT 30 DAYS
// ============================================================
//
// SQL calculates:
//
// - This year's birthday
// - Next year's birthday when this year's birthday has passed
// - Days until birthday
//
// This also handles December -> January correctly.
//
// DATE_ADD() is used by SQL.
// No PHP date calculations are performed.
// ============================================================

$birthday_sql = "

    SELECT

        patient_id,
        name,
        dob,
        phone,

        TIMESTAMPDIFF(
            YEAR,
            dob,
            CURDATE()
        ) AS current_age,

        CASE

            WHEN
                DATE_ADD(
                    dob,
                    INTERVAL
                    YEAR(CURDATE()) - YEAR(dob)
                    YEAR
                ) >= CURDATE()

            THEN
                DATE_ADD(
                    dob,
                    INTERVAL
                    YEAR(CURDATE()) - YEAR(dob)
                    YEAR
                )

            ELSE
                DATE_ADD(
                    DATE_ADD(
                        dob,
                        INTERVAL
                        YEAR(CURDATE()) - YEAR(dob)
                        YEAR
                    ),
                    INTERVAL 1 YEAR
                )

        END AS next_birthday,

        DATEDIFF(

            CASE

                WHEN
                    DATE_ADD(
                        dob,
                        INTERVAL
                        YEAR(CURDATE()) - YEAR(dob)
                        YEAR
                    ) >= CURDATE()

                THEN
                    DATE_ADD(
                        dob,
                        INTERVAL
                        YEAR(CURDATE()) - YEAR(dob)
                        YEAR
                    )

                ELSE
                    DATE_ADD(
                        DATE_ADD(
                            dob,
                            INTERVAL
                            YEAR(CURDATE()) - YEAR(dob)
                            YEAR
                        ),
                        INTERVAL 1 YEAR
                    )

            END,

            CURDATE()

        ) AS days_until_birthday

    FROM patients

    WHERE

        CASE

            WHEN
                DATE_ADD(
                    dob,
                    INTERVAL
                    YEAR(CURDATE()) - YEAR(dob)
                    YEAR
                ) >= CURDATE()

            THEN
                DATE_ADD(
                    dob,
                    INTERVAL
                    YEAR(CURDATE()) - YEAR(dob)
                    YEAR
                )

            ELSE
                DATE_ADD(
                    DATE_ADD(
                        dob,
                        INTERVAL
                        YEAR(CURDATE()) - YEAR(dob)
                        YEAR
                    ),
                    INTERVAL 1 YEAR
                )

        END

        BETWEEN CURDATE()
        AND DATE_ADD(
            CURDATE(),
            INTERVAL 30 DAY
        )

    ORDER BY
        next_birthday ASC,
        name ASC

";

$birthday_result = $conn->query($birthday_sql);


if (!$birthday_result) {

    die("Birthday query failed: "
        . $conn->error);
}


// ============================================================
// 2. PATIENTS TURNING 40 / 50 / 60 THIS YEAR
// ============================================================
//
// Example:
//
// Current year = 2026
//
// DOB = 1986
// 2026 - 1986 = 40
//
// Therefore the patient turns 40 this year.
//
// All calculations are done inside SQL.
// ============================================================

$milestone_sql = "

    SELECT

        patient_id,
        name,
        dob,
        phone,

        YEAR(CURDATE()) - YEAR(dob) AS turning_age,

        DATE_ADD(
            dob,
            INTERVAL
            YEAR(CURDATE()) - YEAR(dob)
            YEAR
        ) AS birthday_this_year

    FROM patients

    WHERE

        YEAR(CURDATE()) - YEAR(dob)
        IN (40, 50, 60)

    ORDER BY

        turning_age ASC,
        birthday_this_year ASC,
        name ASC

";

$milestone_result = $conn->query($milestone_sql);


if (!$milestone_result) {

    die("Birthday milestone query failed: "
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
            Birthday Report
        </h2>

        <p class="text-muted mb-0">
            Upcoming birthdays and age milestones
        </p>

    </div>



    <!-- ================================================= -->
    <!-- 1. BIRTHDAYS IN NEXT 30 DAYS -->
    <!-- ================================================= -->

    <div class="card border-0 shadow-sm mb-4">


        <div class="card-header bg-primary text-white">

            <div class="d-flex justify-content-between align-items-center">

                <h5 class="mb-0">
                    Birthdays in Next 30 Days
                </h5>

                <span class="badge bg-light text-primary">

                    <?= $birthday_result->num_rows ?>

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
                                Phone
                            </th>

                            <th>
                                Date of Birth
                            </th>

                            <th>
                                Current Age
                            </th>

                            <th>
                                Next Birthday
                            </th>

                            <th>
                                Days Until Birthday
                            </th>

                        </tr>

                    </thead>


                    <tbody>


                        <?php if ($birthday_result->num_rows > 0): ?>


                            <?php while ($row = $birthday_result->fetch_assoc()): ?>


                                <tr>


                                    <td>

                                        <strong>
                                            <?= htmlspecialchars($row["name"]) ?>
                                        </strong>

                                        <br>

                                        <small class="text-muted">

                                            Patient #
                                            <?= htmlspecialchars($row["patient_id"]) ?>

                                        </small>

                                    </td>


                                    <td>

                                        <?= htmlspecialchars($row["phone"]) ?>

                                    </td>


                                    <td>

                                        <?= htmlspecialchars($row["dob"]) ?>

                                    </td>


                                    <td>

                                        <span class="badge text-bg-secondary">

                                            <?= $row["current_age"] ?>

                                            years

                                        </span>

                                    </td>


                                    <td>

                                        <strong>

                                            <?= htmlspecialchars(
                                                $row["next_birthday"]
                                            ) ?>

                                        </strong>

                                    </td>


                                    <td>

                                        <?php

                                        $days = (int)$row["days_until_birthday"];

                                        ?>

                                        <?php if ($days === 0): ?>

                                            <span class="badge text-bg-success">

                                                Today

                                            </span>

                                        <?php else: ?>

                                            <span class="badge text-bg-primary">

                                                <?= $days ?>

                                                days

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

                                    No birthdays in the next 30 days.

                                </td>

                            </tr>


                        <?php endif; ?>


                    </tbody>

                </table>

            </div>

        </div>

    </div>



    <!-- ================================================= -->
    <!-- 2. AGE MILESTONES -->
    <!-- ================================================= -->

    <div class="card border-0 shadow-sm mb-4">


        <div class="card-header bg-success text-white">

            <div class="d-flex justify-content-between align-items-center">

                <h5 class="mb-0">
                    Patients Turning 40 / 50 / 60 This Year
                </h5>

                <span class="badge bg-light text-success">

                    <?= $milestone_result->num_rows ?>

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
                                Phone
                            </th>

                            <th>
                                Date of Birth
                            </th>

                            <th>
                                Turning Age
                            </th>

                            <th>
                                Birthday This Year
                            </th>

                        </tr>

                    </thead>


                    <tbody>


                        <?php if ($milestone_result->num_rows > 0): ?>


                            <?php while ($row = $milestone_result->fetch_assoc()): ?>


                                <tr>


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


                                    <td>

                                        <?= htmlspecialchars(
                                            $row["phone"]
                                        ) ?>

                                    </td>


                                    <td>

                                        <?= htmlspecialchars(
                                            $row["dob"]
                                        ) ?>

                                    </td>


                                    <td>

                                        <?php

                                        $age = (int)$row["turning_age"];

                                        ?>

                                        <?php if ($age === 40): ?>

                                            <span class="badge text-bg-info">

                                                40 years

                                            </span>

                                        <?php elseif ($age === 50): ?>

                                            <span class="badge text-bg-warning">

                                                50 years

                                            </span>

                                        <?php else: ?>

                                            <span class="badge text-bg-danger">

                                                60 years

                                            </span>

                                        <?php endif; ?>

                                    </td>


                                    <td>

                                        <?= htmlspecialchars(
                                            $row["birthday_this_year"]
                                        ) ?>

                                    </td>


                                </tr>


                            <?php endwhile; ?>


                        <?php else: ?>


                            <tr>

                                <td
                                    colspan="5"
                                    class="text-center py-4 text-muted">

                                    No patients are turning
                                    40, 50, or 60 this year.

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