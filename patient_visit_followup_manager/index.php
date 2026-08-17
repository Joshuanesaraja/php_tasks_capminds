<?php

session_start();

if (!isset($_SESSION["user_id"])) {

    header("Location: account/login.php");

    exit;
}


require "config/db.php";

$page_title = "Dashboard";

require "includes/header.php";

?>

<div class="container-fluid py-4">

    <!-- ================================================= -->
    <!-- PAGE HEADER -->
    <!-- ================================================= -->

    <div class="mb-4">

        <h2 class="fw-bold mb-1">
            Patient Visit & Follow-Up Manager
        </h2>

        <p class="text-muted mb-0">
            Healthcare management dashboard
        </p>

    </div>


    <!-- ================================================= -->
    <!-- MAIN MODULES -->
    <!-- ================================================= -->

    <div class="row g-4">


        <!-- ================================================= -->
        <!-- PATIENTS -->
        <!-- ================================================= -->

        <div class="col-12 col-md-6 col-xl-4">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body p-4">

                    <div class="mb-3">

                        <div
                            class="bg-primary bg-opacity-10
                            rounded-circle d-inline-flex
                            align-items-center
                            justify-content-center"
                            style="width: 55px; height: 55px;">

                            <span class="text-primary fs-4">
                                👤
                            </span>

                        </div>

                    </div>


                    <h4 class="fw-bold">
                        Patients
                    </h4>

                    <p class="text-muted">

                        Register, edit, view and manage
                        patient information.

                    </p>


                    <div class="d-flex gap-2 flex-wrap">

                        <a
                            href="patients/list.php"
                            class="btn btn-primary">
                            View Patients
                        </a>

                        <?php if ($_SESSION["role"] === "admin"): ?>
                            <a
                                href="patients/add.php"
                                class="btn btn-outline-primary">
                                Add Patient
                            </a>
                        <?php endif; ?>

                    </div>

                </div>

            </div>

        </div>



                <!-- ================================================= -->
        <!-- VISITS -->
        <!-- ================================================= -->

        <div class="col-12 col-md-6 col-xl-4">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body p-4">

                    <div class="mb-3">

                        <div
                            class="bg-success bg-opacity-10
                            rounded-circle d-inline-flex
                            align-items-center
                            justify-content-center"
                            style="width: 55px; height: 55px;">

                            <span class="text-success fs-4">
                                📋
                            </span>

                        </div>

                    </div>


                    <h4 class="fw-bold">
                        Visits
                    </h4>

                    <p class="text-muted">

                        Record patient visits, fees and
                        manage follow-up schedules.

                    </p>


                    <div class="d-flex gap-2 flex-wrap">

                        <a
                            href="visits/list.php"
                            class="btn btn-success">
                            View Visits
                        </a>

                        <?php if ($_SESSION["role"] === "admin"): ?>

                            <a
                                href="patients/list.php?action=add_visit"
                                class="btn btn-outline-success">
                                Add Visit
                            </a>

                        <?php endif; ?>

                    </div>

                </div>

            </div>

        </div>

        <!-- ================================================= -->
        <!-- REPORTS -->
        <!-- ================================================= -->

        <div class="col-12 col-md-6 col-xl-4">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body p-4">

                    <div class="mb-3">

                        <div
                            class="bg-warning bg-opacity-10
                            rounded-circle d-inline-flex
                            align-items-center
                            justify-content-center"
                            style="width: 55px; height: 55px;">

                            <span class="text-warning fs-4">
                                📊
                            </span>

                        </div>

                    </div>


                    <h4 class="fw-bold">
                        Reports
                    </h4>

                    <p class="text-muted">

                        View follow-ups, monthly reports,
                        birthdays and patient summaries.

                    </p>


                    <div class="d-flex gap-2 flex-wrap">

                        <a
                            href="reports/followups.php"
                            class="btn btn-warning">
                            Follow-Ups
                        </a>

                        <a
                            href="reports/summary.php"
                            class="btn btn-outline-warning">
                            Summary
                        </a>

                    </div>

                </div>

            </div>

        </div>

    </div>





    <!-- ================================================= -->
    <!-- REPORT SHORTCUTS -->
    <!-- ================================================= -->

    <div class="card border-0 shadow-sm mt-4">

        <div class="card-body p-4">

            <h5 class="fw-bold mb-3">
                Reports & Analytics
            </h5>


            <div class="row g-3">


                <div class="col-12 col-md-6 col-lg-3">

                    <a
                        href="reports/followups.php"
                        class="btn btn-outline-primary w-100">
                        Follow-Up Report
                    </a>

                </div>


                <div class="col-12 col-md-6 col-lg-3">

                    <a
                        href="reports/monthly.php"
                        class="btn btn-outline-success w-100">
                        Monthly Report
                    </a>

                </div>


                <div class="col-12 col-md-6 col-lg-3">

                    <a
                        href="reports/birthdays.php"
                        class="btn btn-outline-warning w-100">
                        Birthday Report
                    </a>

                </div>


                <div class="col-12 col-md-6 col-lg-3">

                    <a
                        href="reports/summary.php"
                        class="btn btn-outline-dark w-100">
                        Full Summary
                    </a>

                </div>


            </div>

        </div>

    </div>



    <!-- ================================================= -->
    <!-- QUICK NAVIGATION -->
    <!-- ================================================= -->

    <div class="row g-4 mt-1">


        <div class="col-12 col-lg-6">

            <div class="card border-0 shadow-sm">

                <div class="card-body p-4">

                    <h5 class="fw-bold">
                        Patient Management
                    </h5>

                    <p class="text-muted">
                        Manage your patient records.
                    </p>


                    <div class="list-group">

                        <?php if ($_SESSION["role"] === "admin"): ?>
                            <a
                                href="patients/add.php"
                                class="list-group-item
                            list-group-item-action">
                                Add New Patient
                            </a>
                        <?php endif; ?>

                        <a
                            href="patients/list.php"
                            class="list-group-item
                            list-group-item-action">
                            View All Patients
                        </a>

                    </div>

                </div>

            </div>

        </div>



        <div class="col-12 col-lg-6">

            <div class="card border-0 shadow-sm">

                <div class="card-body p-4">

                    <h5 class="fw-bold">
                        Visit Management
                    </h5>

                    <p class="text-muted">
                        Manage patient visits and follow-ups.
                    </p>


                    <div class="list-group">


                        <a
                            href="visits/list.php"
                            class="list-group-item
                            list-group-item-action">
                            View All Visits
                        </a>

                        <?php if ($_SESSION["role"] === "admin"): ?>

                            <a
                                href="patients/list.php?action=add_visit"
                                class="list-group-item
                            list-group-item-action">
                                Add a new visit
                            </a>

                        <?php endif; ?>

                    </div>  

                </div>

            </div>

        </div>


    </div>

</div>


<?php

require_once "includes/footer.php";

?>