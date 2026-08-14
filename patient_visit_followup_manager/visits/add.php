<?php

require_once "../config/db.php";

$page_title = "Add Visit";

$message = "";
$message_type = "";

$patient_id = isset($_GET["patient_id"])
    ? (int) $_GET["patient_id"]
    : 0;

$name = "";
$visit_date = "";
$consultation_fee = "";
$lab_fee = "";


/*
|--------------------------------------------------------------------------
| If patient_id came through POST, use that instead
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $patient_id = (int) ($_POST["patient_id"] ?? 0);

    $visit_date = $_POST["visit_date"] ?? "";
    $consultation_fee = trim($_POST["consultation_fee"] ?? "");
    $lab_fee = trim($_POST["lab_fee"] ?? "");
}


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
| Fetch Patient
|--------------------------------------------------------------------------
*/

$patient_sql = "
    SELECT
        patient_id,
        name
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


$name = $patient["name"];


/*
|--------------------------------------------------------------------------
| Handle Form Submission
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {


    /*
    |--------------------------------------------------------------------------
    | Required Field Validation
    |--------------------------------------------------------------------------
    */

    if (
        $visit_date === "" ||
        $consultation_fee === "" ||
        $lab_fee === ""
    ) {

        $message = "Please fill in all required fields.";
        $message_type = "danger";
    } elseif (
        !is_numeric($consultation_fee) ||
        !is_numeric($lab_fee)
    ) {

        $message = "Fees must contain valid numeric values.";
        $message_type = "danger";
    } elseif (
        (float) $consultation_fee < 0 ||
        (float) $lab_fee < 0
    ) {

        $message = "Fees cannot be negative.";
        $message_type = "danger";
    } else {


        /*
        |--------------------------------------------------------------------------
        | SQL Date Validation
        |--------------------------------------------------------------------------
        |
        | The visit date must be a valid date.
        |
        | We are allowing future dates only if the task requires
        | appointment-like scheduling. For an actual visit record,
        | we will restrict it to today or earlier.
        |
        */

        $validation_sql = "
            SELECT

                CASE

                    WHEN STR_TO_DATE(?, '%Y-%m-%d') IS NULL
                        THEN 'INVALID_DATE'

                    WHEN STR_TO_DATE(?, '%Y-%m-%d') > CURDATE()
                        THEN 'FUTURE_DATE'

                    ELSE 'VALID'

                END AS validation_status
        ";

        $stmt = $conn->prepare($validation_sql);

        $stmt->bind_param(
            "ss",
            $visit_date,
            $visit_date
        );

        $stmt->execute();

        $result = $stmt->get_result();

        $validation = $result->fetch_assoc();

        $validation_status = $validation["validation_status"];

        $stmt->close();


        /*
        |--------------------------------------------------------------------------
        | Handle Date Validation
        |--------------------------------------------------------------------------
        */

        if ($validation_status === "INVALID_DATE") {

            $message = "Please enter a valid visit date.";
            $message_type = "danger";
        } elseif ($validation_status === "FUTURE_DATE") {

            $message = "Visit date cannot be in the future.";
            $message_type = "danger";
        } else {


            /*
            |--------------------------------------------------------------------------
            | Insert Visit
            |--------------------------------------------------------------------------
            |
            | IMPORTANT:
            |
            | follow_up_due is NOT calculated in PHP.
            |
            | MySQL calculates:
            |
            | visit_date + 7 days
            |
            */

            $insert_sql = "
                INSERT INTO visits
                (
                    patient_id,
                    visit_date,
                    consultation_fee,
                    lab_fee,
                    follow_up_due
                )
                VALUES
                (
                    ?,
                    ?,
                    ?,
                    ?,
                    DATE_ADD(?, INTERVAL 7 DAY)
                )
            ";

            $stmt = $conn->prepare($insert_sql);


            if (!$stmt) {

                $message = "Database error while adding visit.";
                $message_type = "danger";
            } else {


                /*
                |--------------------------------------------------------------------------
                | Bind Parameters
                |--------------------------------------------------------------------------
                |
                | i = patient_id
                | s = visit_date
                | d = consultation_fee
                | d = lab_fee
                | s = visit_date
                |
                */

                $stmt->bind_param(
                    "isdds",
                    $patient_id,
                    $visit_date,
                    $consultation_fee,
                    $lab_fee,
                    $visit_date
                );


                if ($stmt->execute()) {

                    $message = "Visit added successfully.";
                    $message_type = "success";

                    /*
                    * Clear visit fields after success.
                    */

                    $visit_date = "";
                    $consultation_fee = "";
                    $lab_fee = "";
                } else {

                    $message = "Failed to add visit.";
                    $message_type = "danger";
                }

                $stmt->close();
            }
        }
    }
}


/*
|--------------------------------------------------------------------------
| Include Common Header
|--------------------------------------------------------------------------
*/

require_once "../includes/header.php";

?>

<div class="container py-5">

    <div class="row justify-content-center">

        <div class="col-lg-8 col-md-10">


            <!-- Page Heading -->

            <div class="mb-4">

                <h2 class="fw-bold mb-1">
                    Add Patient Visit
                </h2>

                <p class="text-muted mb-0">
                    Record a new visit and automatically schedule the
                    follow-up through SQL.
                </p>

            </div>


            <!-- Visit Card -->

            <div class="card border-0 shadow-sm">


                <div class="card-header bg-primary text-white">

                    <h5 class="mb-0">
                        Visit Information
                    </h5>

                </div>


                <div class="card-body p-4">


                    <!-- Message -->

                    <?php if ($message !== ""): ?>

                        <div
                            class="alert alert-<?= htmlspecialchars($message_type) ?> alert-dismissible fade show"
                            role="alert">

                            <?= htmlspecialchars($message) ?>

                            <button
                                type="button"
                                class="btn-close"
                                data-bs-dismiss="alert"></button>

                        </div>

                    <?php endif; ?>


                    <form method="POST">


                        <!-- Patient ID -->

                        <input
                            type="hidden"
                            name="patient_id"
                            value="<?= $patient["patient_id"] ?>">


                        <!-- Patient -->

                        <div class="mb-4">

                            <label class="form-label fw-semibold">
                                Patient
                            </label>

                            <div class="form-control bg-light">

                                <strong>
                                    <?= htmlspecialchars($patient["name"]) ?>
                                </strong>

                                <span class="text-muted">
                                    — Patient #<?= $patient["patient_id"] ?>
                                </span>

                            </div>

                        </div>


                        <!-- Visit Date -->

                        <div class="mb-3">

                            <label
                                for="visit_date"
                                class="form-label fw-semibold">

                                Visit Date

                                <span class="text-danger">
                                    *
                                </span>

                            </label>

                            <input
                                type="date"
                                class="form-control"
                                id="visit_date"
                                name="visit_date"
                                value="<?= htmlspecialchars($visit_date) ?>"
                                required>

                            <div class="form-text">

                                Visit date cannot be in the future.

                            </div>

                        </div>


                        <!-- Consultation Fee -->

                        <div class="mb-3">

                            <label
                                for="consultation_fee"
                                class="form-label fw-semibold">

                                Consultation Fee

                                <span class="text-danger">
                                    *
                                </span>

                            </label>

                            <div class="input-group">

                                <span class="input-group-text">
                                    ₹
                                </span>

                                <input
                                    type="number"
                                    class="form-control"
                                    id="consultation_fee"
                                    name="consultation_fee"
                                    value="<?= htmlspecialchars($consultation_fee) ?>"
                                    min="0"
                                    step="0.01"
                                    placeholder="0.00"
                                    required>

                            </div>

                        </div>


                        <!-- Lab Fee -->

                        <div class="mb-4">

                            <label
                                for="lab_fee"
                                class="form-label fw-semibold">

                                Lab Fee

                                <span class="text-danger">
                                    *
                                </span>

                            </label>

                            <div class="input-group">

                                <span class="input-group-text">
                                    ₹
                                </span>

                                <input
                                    type="number"
                                    class="form-control"
                                    id="lab_fee"
                                    name="lab_fee"
                                    value="<?= htmlspecialchars($lab_fee) ?>"
                                    min="0"
                                    step="0.01"
                                    placeholder="0.00"
                                    required>

                            </div>

                        </div>


                        <!-- Follow-up Information -->

                        <div class="alert alert-info">

                            <strong>
                                Follow-Up:
                            </strong>

                            The follow-up date will automatically be
                            calculated as 7 days after the visit date
                            using SQL.

                        </div>


                        <!-- Buttons -->

                        <div class="d-flex flex-wrap gap-2">

                            <button
                                type="submit"
                                class="btn btn-primary">

                                Add Visit

                            </button>


                            <a
                                href="patient_visits.php?id=<?= $patient["patient_id"] ?>"
                                class="btn btn-outline-primary">

                                View Visit History

                            </a>


                            <a
                                href="../patients/view.php?id=<?= $patient["patient_id"] ?>"
                                class="btn btn-outline-secondary">

                                Back to Patient

                            </a>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>


<?php require_once "../includes/footer.php"; ?>