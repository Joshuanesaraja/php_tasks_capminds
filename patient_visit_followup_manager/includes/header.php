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
    strpos($project_path, "/reports") !== false
) {

    $project_path = dirname($project_path);
}

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

</head>


<body class="bg-light">


    <!-- ================================================= -->
    <!-- NAVBAR -->
    <!-- ================================================= -->

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

                <ul class="navbar-nav ms-auto">


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


                            <!-- FOLLOW-UPS -->

                            <li>

                                <a
                                    class="dropdown-item"
                                    href="<?= $project_path ?>/reports/followups.php">

                                    Follow-Ups

                                </a>

                            </li>


                            <!-- MONTHLY -->

                            <li>

                                <a
                                    class="dropdown-item"
                                    href="<?= $project_path ?>/reports/monthly.php">

                                    Monthly Report

                                </a>

                            </li>


                            <!-- BIRTHDAYS -->

                            <li>

                                <a
                                    class="dropdown-item"
                                    href="<?= $project_path ?>/reports/birthdays.php">

                                    Birthdays

                                </a>

                            </li>


                            <!-- SUMMARY -->

                            <li>

                                <a
                                    class="dropdown-item"
                                    href="<?= $project_path ?>/reports/summary.php">

                                    Full Summary

                                </a>

                            </li>


                        </ul>

                    </li>


                </ul>

            </div>

        </div>

    </nav>


    <main>