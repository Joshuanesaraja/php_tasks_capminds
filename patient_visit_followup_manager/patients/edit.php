<?php
require_once "../account/auth.php";

requireAdmin();

require_once "../config/db.php";

$page_title = "Edit Patient";

$message = "";
$message_type = "";

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
| Handle Form Submission
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $patient_id = (int) ($_POST["patient_id"] ?? 0);

    $name = trim($_POST["name"] ?? "");
    $dob = $_POST["dob"] ?? "";
    $join_date = $_POST["join_date"] ?? "";
    $phone = trim($_POST["phone"] ?? "");
    $address = trim($_POST["address"] ?? "");


    /*
    |--------------------------------------------------------------------------
    | Required Field Validation
    |--------------------------------------------------------------------------
    */

    if ($name === "" || $dob === "" || $join_date === "") {

        $message = "Please fill in all required fields.";
        $message_type = "danger";

    } else {


        /*
        |--------------------------------------------------------------------------
        | SQL Date Validation
        |--------------------------------------------------------------------------
        */

        $validation_sql = "
            SELECT

                CASE

                    WHEN STR_TO_DATE(?, '%Y-%m-%d') IS NULL
                        THEN 'INVALID_DOB'

                    WHEN STR_TO_DATE(?, '%Y-%m-%d') > CURDATE()
                        THEN 'FUTURE_DOB'

                    WHEN STR_TO_DATE(?, '%Y-%m-%d') IS NULL
                        THEN 'INVALID_JOIN_DATE'

                    WHEN STR_TO_DATE(?, '%Y-%m-%d') > CURDATE()
                        THEN 'FUTURE_JOIN_DATE'

                    WHEN STR_TO_DATE(?, '%Y-%m-%d')
                         < STR_TO_DATE(?, '%Y-%m-%d')
                        THEN 'JOIN_BEFORE_DOB'

                    ELSE 'VALID'

                END AS validation_status
        ";

        $stmt = $conn->prepare($validation_sql);


        if (!$stmt) {

            $message = "Database error while validating dates.";
            $message_type = "danger";

        } else {

            $stmt->bind_param(
                "ssssss",
                $dob,
                $dob,
                $join_date,
                $join_date,
                $join_date,
                $dob
            );

            $stmt->execute();

            $result = $stmt->get_result();

            $validation = $result->fetch_assoc();

            $validation_status = $validation["validation_status"];

            $stmt->close();


            /*
            |--------------------------------------------------------------------------
            | Handle Validation Result
            |--------------------------------------------------------------------------
            */

            if ($validation_status === "INVALID_DOB") {

                $message = "Please enter a valid date of birth.";
                $message_type = "danger";

            } elseif ($validation_status === "FUTURE_DOB") {

                $message = "Date of birth cannot be in the future.";
                $message_type = "danger";

            } elseif ($validation_status === "INVALID_JOIN_DATE") {

                $message = "Please enter a valid join date.";
                $message_type = "danger";

            } elseif ($validation_status === "FUTURE_JOIN_DATE") {

                $message = "Join date cannot be in the future.";
                $message_type = "danger";

            } elseif ($validation_status === "JOIN_BEFORE_DOB") {

                $message = "Join date cannot be before the patient's date of birth.";
                $message_type = "danger";

            } else {


                /*
                |--------------------------------------------------------------------------
                | Update Patient
                |--------------------------------------------------------------------------
                */

                $update_sql = "
                    UPDATE patients

                    SET
                        name = ?,
                        dob = ?,
                        join_date = ?,
                        phone = ?,
                        address = ?

                    WHERE patient_id = ?
                ";

                $stmt = $conn->prepare($update_sql);


                if (!$stmt) {

                    $message = "Database error while updating patient.";
                    $message_type = "danger";

                } else {

                    $stmt->bind_param(
                        "sssssi",
                        $name,
                        $dob,
                        $join_date,
                        $phone,
                        $address,
                        $patient_id
                    );


                    if ($stmt->execute()) {

                        $message = "Patient updated successfully.";
                        $message_type = "success";

                    } else {

                        $message = "Failed to update patient.";
                        $message_type = "danger";

                    }

                    $stmt->close();
                }
            }
        }
    }
}


/*
|--------------------------------------------------------------------------
| Fetch Patient Information
|--------------------------------------------------------------------------
*/

$sql = "
    SELECT
        patient_id,
        name,
        dob,
        join_date,
        phone,
        address

    FROM patients

    WHERE patient_id = ?
";

$stmt = $conn->prepare($sql);

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
| Common Header
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
                    Edit Patient
                </h2>

                <p class="text-muted mb-0">
                    Update patient information.
                </p>

            </div>


            <!-- Edit Card -->

            <div class="card border-0 shadow-sm">


                <div class="card-header bg-primary text-white">

                    <h5 class="mb-0">

                        Patient #<?= $patient["patient_id"] ?>

                    </h5>

                </div>


                <div class="card-body p-4">


                    <!-- Message -->

                    <?php if ($message !== ""): ?>

                        <div
                            class="alert alert-<?= htmlspecialchars($message_type) ?> alert-dismissible fade show"
                            role="alert"
                        >

                            <?= htmlspecialchars($message) ?>

                            <button
                                type="button"
                                class="btn-close"
                                data-bs-dismiss="alert"
                            ></button>

                        </div>

                    <?php endif; ?>


                    <form method="POST">


                        <!-- Patient ID -->

                        <input
                            type="hidden"
                            name="patient_id"
                            value="<?= $patient["patient_id"] ?>"
                        >


                        <!-- Patient Name -->

                        <div class="mb-3">

                            <label
                                for="name"
                                class="form-label fw-semibold"
                            >

                                Patient Name

                                <span class="text-danger">
                                    *
                                </span>

                            </label>

                            <input
                                type="text"
                                class="form-control"
                                id="name"
                                name="name"
                                value="<?= htmlspecialchars($patient["name"]) ?>"
                                required
                            >

                        </div>


                        <!-- DOB and Join Date -->

                        <div class="row">


                            <!-- DOB -->

                            <div class="col-md-6 mb-3">

                                <label
                                    for="dob"
                                    class="form-label fw-semibold"
                                >

                                    Date of Birth

                                    <span class="text-danger">
                                        *
                                    </span>

                                </label>

                                <input
                                    type="date"
                                    class="form-control"
                                    id="dob"
                                    name="dob"
                                    value="<?= htmlspecialchars($patient["dob"]) ?>"
                                    required
                                >

                                <div class="form-text">

                                    DOB must not be a future date.

                                </div>

                            </div>


                            <!-- Join Date -->

                            <div class="col-md-6 mb-3">

                                <label
                                    for="join_date"
                                    class="form-label fw-semibold"
                                >

                                    Join Date

                                    <span class="text-danger">
                                        *
                                    </span>

                                </label>

                                <input
                                    type="date"
                                    class="form-control"
                                    id="join_date"
                                    name="join_date"
                                    value="<?= htmlspecialchars($patient["join_date"]) ?>"
                                    required
                                >

                                <div class="form-text">

                                    Join date must not be in the future
                                    or before DOB.

                                </div>

                            </div>

                        </div>


                        <!-- Phone -->

                        <div class="mb-3">

                            <label
                                for="phone"
                                class="form-label fw-semibold"
                            >

                                Phone Number

                            </label>

                            <input
                                type="tel"
                                class="form-control"
                                id="phone"
                                name="phone"
                                value="<?= htmlspecialchars($patient["phone"]) ?>"
                                placeholder="Enter phone number"
                            >

                        </div>


                        <!-- Address -->

                        <div class="mb-4">

                            <label
                                for="address"
                                class="form-label fw-semibold"
                            >

                                Address

                            </label>

                            <textarea
                                class="form-control"
                                id="address"
                                name="address"
                                rows="3"
                                placeholder="Enter patient address"
                            ><?= htmlspecialchars($patient["address"]) ?></textarea>

                        </div>


                        <!-- Buttons -->

                        <div class="d-flex flex-wrap gap-2">


                            <button
                                type="submit"
                                class="btn btn-primary"
                            >

                                Save Changes

                            </button>


                            <a
                                href="view.php?id=<?= $patient["patient_id"] ?>"
                                class="btn btn-outline-primary"
                            >

                                View Patient

                            </a>


                            <a
                                href="list.php"
                                class="btn btn-outline-secondary"
                            >

                                Cancel

                            </a>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>


<?php require_once "../includes/footer.php"; ?>