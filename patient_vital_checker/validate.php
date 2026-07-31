<?php

// This is the Higher-Order Function because it accepts another function as a parameter.

function validateVital($vitalData, $ruleFunction)
{
    return $ruleFunction($vitalData);
}

?>