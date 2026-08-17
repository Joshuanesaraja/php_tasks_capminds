<?php

$current_page = basename($_SERVER["PHP_SELF"]);

/*
|--------------------------------------------------------------------------
| Project Base Path
|--------------------------------------------------------------------------
|
| Automatically finds the project folder.
|
*/


$project_path = rtrim(
    dirname($_SERVER["SCRIPT_NAME"]),
    "/\\"
);

/*
|--------------------------------------------------------------------------
| If the current page is inside a subfolder,
| move back to the project root.
|--------------------------------------------------------------------------
*/


if (
    strpos($project_path, "/patients") !== false ||
    strpos($project_path, "/visits") !== false ||
    strpos($project_path, "/account") !== false ||
    strpos($project_path, "/reports") !== false
) {

    $project_path = dirname($project_path);
}


// ============================================================
// SESSION DATA
// ============================================================

$username = $_SESSION["username"] ?? "";

$role = $_SESSION["role"] ?? "";

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>
        <?= isset($page_title)
            ? htmlspecialchars($page_title)
            : "Patient Visit Manager"
        ?>
    </title>


    <!-- Bootstrap 5 -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">


    <!-- Bootstrap Icons -->

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

</head>


<body class="bg-light">


    <!-- ======================================================== -->
    <!-- NAVBAR -->
    <!-- ======================================================== -->

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">

        <div class="container">


            <!-- BRAND -->

            <a
                class="navbar-brand fw-bold"
                href="<?= $project_path ?>/index.php">

                Patient Visit Manager

            </a>


            <!-- MOBILE TOGGLE -->

            <button
                class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#mainNavbar"
                aria-controls="mainNavbar"
                aria-expanded="false"
                aria-label="Toggle navigation">

                <span class="navbar-toggler-icon"></span>

            </button>


            <!-- NAVIGATION -->

            <div
                class="collapse navbar-collapse"
                id="mainNavbar">

                <ul class="navbar-nav ms-auto align-items-lg-center">


                    <!-- DASHBOARD -->

                    <li class="nav-item">

                        <a
                            class="nav-link"
                            href="<?= $project_path ?>/index.php">

                            Dashboard

                        </a>

                    </li>


                    <!-- PATIENTS -->

                    <li class="nav-item">

                        <a
                            class="nav-link"
                            href="<?= $project_path ?>/patients/list.php">

                            Patients

                        </a>

                    </li>


                    <!-- VISITS -->

                    <li class="nav-item">

                        <a
                            class="nav-link"
                            href="<?= $project_path ?>/visits/list.php">

                            Visits

                        </a>

                    </li>


                    <!-- REPORTS -->

                    <li class="nav-item dropdown">

                        <a
                            class="nav-link dropdown-toggle"
                            href="#"
                            role="button"
                            data-bs-toggle="dropdown"
                            aria-expanded="false">

                            Reports

                        </a>


                        <ul class="dropdown-menu">


                            <li>

                                <a
                                    class="dropdown-item"
                                    href="<?= $project_path ?>/reports/followups.php">

                                    Follow-Ups

                                </a>

                            </li>


                            <li>

                                <a
                                    class="dropdown-item"
                                    href="<?= $project_path ?>/reports/monthly.php">

                                    Monthly Report

                                </a>

                            </li>


                            <li>

                                <a
                                    class="dropdown-item"
                                    href="<?= $project_path ?>/reports/birthdays.php">

                                    Birthdays

                                </a>

                            </li>


                            <li>

                                <a
                                    class="dropdown-item"
                                    href="<?= $project_path ?>/reports/summary.php">

                                    Full Summary

                                </a>

                            </li>


                            <li>

                                <a
                                    class="dropdown-item"
                                    href="<?= $project_path ?>/reports/chart.php">

                                    Visit Chart

                                </a>

                            </li>


                        </ul>

                    </li>


                    <!-- ================================================= -->
                    <!-- ACCOUNT -->
                    <!-- ================================================= -->

                    <li class="nav-item dropdown ms-lg-3">


                        <a
                            class="nav-link dropdown-toggle d-flex align-items-center gap-2"
                            href="#"
                            role="button"
                            data-bs-toggle="dropdown"
                            aria-expanded="false">

                            <i class="bi bi-person-circle fs-5"></i>

                            <span>
                                <?= htmlspecialchars($username) ?>
                            </span>

                        </a>


                        <ul class="dropdown-menu dropdown-menu-end">


                            <!-- USERNAME -->

                            <li>

                                <span class="dropdown-item-text">

                                    <strong>
                                        <?= htmlspecialchars($username) ?>
                                    </strong>

                                </span>

                            </li>


                            <!-- ROLE -->

                            <li>

                                <span class="dropdown-item-text">

                                    <span class="badge bg-primary">

                                        <?= htmlspecialchars(
                                            ucfirst($role)
                                        ) ?>

                                    </span>

                                </span>

                            </li>


                            <li>
                                <hr class="dropdown-divider">
                            </li>


                            <!-- SWITCH ACCOUNT -->

                            <li>

                                <a
                                    class="dropdown-item"
                                    href="<?= $project_path ?>/account/logout.php">

                                    <i class="bi bi-arrow-left-right me-2"></i>

                                    Switch Account

                                </a>

                            </li>


                            <!-- LOGOUT -->

                            <li>

                                <a
                                    class="dropdown-item text-danger"
                                    href="<?= $project_path ?>/account/logout.php">

                                    <i class="bi bi-box-arrow-right me-2"></i>

                                    Logout

                                </a>

                            </li>


                        </ul>

                    </li>


                </ul>

            </div>

        </div>

    </nav>


    <main>