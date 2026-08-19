<?php

header("Content-Type: application/json");

require_once "config.php";

function validateAppointment($data)
{
    if (
        empty($data["patient_name"]) ||
        empty($data["email"]) ||
        empty($data["mobile"]) ||
        empty($data["appointment_date"]) ||
        empty($data["appointment_time"])
    ) {
        return "All fields are required";
    }


    if (!filter_var($data["email"], FILTER_VALIDATE_EMAIL)) {
        return "Invalid email format";
    }


    if (!preg_match("/^\d{10}$/", $data["mobile"])) {
        return "Mobile number must contain 10 digits";
    }


    $today = date("Y-m-d");

    if ($data["appointment_date"] < $today) {
        return "Appointment date cannot be in the past";
    }


    return null;
}


if ($_SERVER["REQUEST_METHOD"] === "GET") {

    $sql = "SELECT * FROM appointments ORDER BY id DESC";

    $result = $conn->query($sql);

    if ($result) {

        $appointments = [];

        while ($row = $result->fetch_assoc()) {
            $appointments[] = $row;
        }

        echo json_encode([
            "status" => "success",
            "data" => $appointments
        ]);
    } else {

        http_response_code(500);

        echo json_encode([
            "status" => "error",
            "message" => "Database error"
        ]);
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $data = json_decode(
        file_get_contents("php://input"),
        true
    );


    $error = validateAppointment($data);

    if ($error !== null) {

        http_response_code(422);

        echo json_encode([
            "status" => "error",
            "message" => $error
        ]);

        exit;
    }

    //  file_get_contents("php://input"), this recieves raw json and convert it into an assoc array

    $patientName = $data["patient_name"];
    $email = $data["email"];
    $mobile = $data["mobile"];
    $appointmentDate = $data["appointment_date"];
    $appointmentTime = $data["appointment_time"];

    $sql = "INSERT INTO appointments
            (patient_name, email, mobile, appointment_date, appointment_time)
            VALUES (?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "sssss",
        $patientName,
        $email,
        $mobile,
        $appointmentDate,
        $appointmentTime
    );

    if ($stmt->execute()) {

        http_response_code(201);
        //  201 -> resource created

        echo json_encode([
            "status" => "success",
            "message" => "Appointment created successfully"
        ]);
    } else {

        http_response_code(500);

        echo json_encode([
            "status" => "error",
            "message" => "Database error"
        ]);
    }
}



if ($_SERVER["REQUEST_METHOD"] === "PUT") {

    $data = json_decode(
        file_get_contents("php://input"),
        true
    );

    $id = $data["id"];


    // Status update

    if (isset($data["status"])) {

        $status = $data["status"];

        $allowedStatuses = [
            "Pending",
            "Confirmed",
            "Cancelled"
        ];

        if (!in_array($status, $allowedStatuses)) {

            http_response_code(400);

            echo json_encode([
                "status" => "error",
                "message" => "Invalid status"
            ]);

            exit;
        }


        $sql = "UPDATE appointments
                SET status = ?
                WHERE id = ?";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param(
            "si",
            $status,
            $id
        );


        if ($stmt->execute()) {

            if ($stmt->affected_rows > 0) {

                echo json_encode([
                    "status" => "success",
                    "message" => "Status updated successfully"
                ]);
            } else {

                echo json_encode([
                    "status" => "error",
                    "message" => "Appointment not found"
                ]);
            }
        } else {

            http_response_code(500);

            echo json_encode([
                "status" => "error",
                "message" => "Database error"
            ]);
        }

        exit;
    }

    $error = validateAppointment($data);

    if ($error !== null) {

        http_response_code(422);

        echo json_encode([
            "status" => "error",
            "message" => $error
        ]);

        exit;
    }

    // Full appointment update
    $patientName = $data["patient_name"];
    $email = $data["email"];
    $mobile = $data["mobile"];
    $appointmentDate = $data["appointment_date"];
    $appointmentTime = $data["appointment_time"];


    $sql = "UPDATE appointments
            SET patient_name = ?,
                email = ?,
                mobile = ?,
                appointment_date = ?,
                appointment_time = ?
            WHERE id = ?";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "sssssi",
        $patientName,
        $email,
        $mobile,
        $appointmentDate,
        $appointmentTime,
        $id
    );


    if ($stmt->execute()) {

        if ($stmt->affected_rows > 0) {

            echo json_encode([
                "status" => "success",
                "message" => "Appointment updated successfully"
            ]);
        } else {

            echo json_encode([
                "status" => "error",
                "message" => "Appointment not found"
            ]);
        }
    } else {

        http_response_code(500);

        echo json_encode([
            "status" => "error",
            "message" => "Database error"
        ]);
    }
}



if ($_SERVER["REQUEST_METHOD"] === "DELETE") {

    $data = json_decode(
        file_get_contents("php://input"),
        true
    );

    $id = $data["id"];

    $sql = "DELETE FROM appointments WHERE id = ?";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "i",
        $id
    );

    if ($stmt->execute()) {

        if ($stmt->affected_rows > 0) {

            echo json_encode([
                "status" => "success",
                "message" => "Appointment deleted successfully"
            ]);
        } else {

            echo json_encode([
                "status" => "error",
                "message" => "Appointment not found"
            ]);
        }
    } else {

        http_response_code(500);

        echo json_encode([
            "status" => "error",
            "message" => "Database error"
        ]);
    }
}
