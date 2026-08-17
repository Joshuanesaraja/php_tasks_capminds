<?php
require_once "../account/auth.php";
require_once "../config/db.php";

$page_title = "Visit Chart";

require "../includes/header.php";


// ============================================================
// MONTHLY VISIT COUNT - LAST 6 MONTHS
// ============================================================

$sql = "
    SELECT

        DATE_FORMAT(
            visit_date,
            '%Y-%m'
        ) AS visit_month,

        DATE_FORMAT(
            visit_date,
            '%b %Y'
        ) AS month_label,

        COUNT(*) AS visit_count

    FROM visits

    WHERE visit_date >= DATE_SUB(
        DATE_FORMAT(CURDATE(), '%Y-%m-01'),
        INTERVAL 5 MONTH
    )

    AND visit_date < DATE_ADD(
        DATE_FORMAT(CURDATE(), '%Y-%m-01'),
        INTERVAL 1 MONTH
    )

    GROUP BY
        YEAR(visit_date),
        MONTH(visit_date)

    ORDER BY
        YEAR(visit_date),
        MONTH(visit_date)
";


$result = $conn->query($sql);


if (!$result) {

    die("Chart query failed: "
        . $conn->error);
}


$months = [];
$visit_counts = [];


while ($row = $result->fetch_assoc()) {

    $months[] = $row["month_label"];

    $visit_counts[] = (int) $row["visit_count"];
}

// ============================================================
// PATIENT REGISTRATIONS - LAST 6 MONTHS
// ============================================================

$patient_sql = "
    SELECT

        DATE_FORMAT(
            join_date,
            '%b %Y'
        ) AS month_label,

        COUNT(*) AS patient_count

    FROM patients

    WHERE join_date >= DATE_SUB(
        DATE_FORMAT(CURDATE(), '%Y-%m-01'),
        INTERVAL 5 MONTH
    )

    AND join_date < DATE_ADD(
        DATE_FORMAT(CURDATE(), '%Y-%m-01'),
        INTERVAL 1 MONTH
    )

    GROUP BY
        YEAR(join_date),
        MONTH(join_date)

    ORDER BY
        YEAR(join_date),
        MONTH(join_date)
";


$patient_result = $conn->query($patient_sql);


if (!$patient_result) {

    die("Patient chart query failed: "
        . $conn->error);
}


$patient_months = [];
$patient_counts = [];


while ($row = $patient_result->fetch_assoc()) {

    $patient_months[] = $row["month_label"];

    $patient_counts[] = (int) $row["patient_count"];
}

?>

<div class="container-fluid py-4">


    <!-- ================================================= -->
    <!-- PAGE HEADER -->
    <!-- ================================================= -->

    <div class="mb-4">

        <h2 class="fw-bold mb-1">
            Monthly Visit Report
        </h2>

        <p class="text-muted mb-0">
            Patient visits for the last 6 months
        </p>

    </div>


    <!-- ================================================= -->
    <!-- CHART -->
    <!-- ================================================= -->

    <div class="card border-0 shadow-sm">

        <div class="card-body p-4">

            <h5 class="fw-bold mb-4">
                Visits Per Month
            </h5>


            <div style="height: 400px;">

                <canvas id="monthlyVisitsChart"></canvas>

            </div>

        </div>

    </div>

    <div class="card border-0 shadow-sm mt-4">

        <div class="card-body p-4">

            <h5 class="fw-bold mb-4">
                Patients Joined Per Month
            </h5>


            <div style="height: 400px;">

                <canvas id="patientJoinChart"></canvas>

            </div>

        </div>

    </div>

</div>


<!-- ================================================= -->
<!-- CHART.JS -->
<!-- ================================================= -->

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


<script>
    const months = <?= json_encode($months) ?>;

    const visitCounts = <?= json_encode($visit_counts) ?>;


    const ctx = document
        .getElementById("monthlyVisitsChart");


    new Chart(ctx, {

        type: "bar",

        data: {

            labels: months,

            datasets: [

                {

                    label: "Number of Visits",

                    data: visitCounts,

                    borderWidth: 1

                }

            ]

        },

        options: {

            responsive: true,

            maintainAspectRatio: false,

            scales: {

                y: {

                    beginAtZero: true,

                    ticks: {

                        precision: 0

                    }

                }

            }

        }

    });

    const patientMonths =
        <?= json_encode($patient_months) ?>;

    const patientCounts =
        <?= json_encode($patient_counts) ?>;


    const patientCtx =
        document.getElementById("patientJoinChart");


    new Chart(patientCtx, {

        type: "line",

        data: {

            labels: patientMonths,

            datasets: [

                {

                    label: "Patients Joined",

                    data: patientCounts,

                    borderWidth: 2,

                    tension: 0.3,

                    fill: false

                }

            ]

        },

        options: {

            responsive: true,

            maintainAspectRatio: false,

            scales: {

                y: {

                    beginAtZero: true,

                    ticks: {

                        precision: 0

                    }

                }

            }

        }

    });
</script>


<?php

require_once "../includes/footer.php";

?>