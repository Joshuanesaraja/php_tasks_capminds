<?php

include "vitals.php";
include "validate.php";
include "rules.php";
include "scanner.php";

echo "<h2>Patient Vital Status Checker</h2>";

foreach ($vitals as $vital)
{

    switch ($vital["vital_type"])
    {

        case "Temperature":
            $result = validateVital($vital, "checkTemperature");
            break;

        case "Pulse":
            $result = validateVital($vital, "checkPulse");
            break;

        case "BP":
            $result = validateVital($vital, "checkBloodPressure");
            break;

        default:
            break;

    }

    echo "Patient : " . $result["patient_name"] . "<br>";
    echo "Vital : " . $result["vital_type"] . "<br>";
    echo "Value : " . $result["value"] . "<br>";
    echo "Status : " . $result["status"] . "<br>";
    echo "Message : " . $result["message"] . "<br>";

    echo "<hr>";

}

echo "<h2>Project Files</h2>";

scanFolder(__DIR__);

?>