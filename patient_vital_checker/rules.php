<?php

// These are the Callback Functions.

function checkTemperature($vital)
{
    $temp = $vital["value"];

    if ($temp > 100.4) {
        $vital["status"] = "HIGH";
        $vital["message"] = "Fever detected";
    }
    elseif ($temp < 97) {
        $vital["status"] = "LOW";
        $vital["message"] = "Body temperature low";
    }
    else {
        $vital["status"] = "NORMAL";
        $vital["message"] = "Temperature normal";
    }

    return $vital;
}



function checkPulse($vital)
{
    $pulse = $vital["value"];

    if ($pulse > 100) {
        $vital["status"] = "HIGH";
        $vital["message"] = "Pulse rate high";
    }
    elseif ($pulse < 60) {
        $vital["status"] = "LOW";
        $vital["message"] = "Pulse rate low";
    }
    else {
        $vital["status"] = "NORMAL";
        $vital["message"] = "Pulse rate normal";
    }

    return $vital;
}



function checkBloodPressure($vital)
{
    list($sys, $dia) = explode("/", $vital["value"]);

    if ($sys > 140 || $dia > 90) {

        $vital["status"] = "HIGH";
        $vital["message"] = "Blood Pressure High";

    }
    elseif ($sys < 90 || $dia < 60) {

        $vital["status"] = "LOW";
        $vital["message"] = "Blood Pressure Low";

    }
    else {

        $vital["status"] = "NORMAL";
        $vital["message"] = "Blood Pressure Normal";

    }

    return $vital;
}

?>