<?php

$a = 25;
$b = 5;

function add($a, $b){
    return $a + $b;
}
function subtract($a, $b){
    return $a - $b;
}
function multiply($a, $b){
    return $a * $b;
}
function divide($a, $b){
    return $a / $b;
}
echo "----- Calculator Results ----- <br> <br> " ;
echo "Addition: " .add($a, $b) ."<br> <br>";
echo "subtraction: " .subtract($a, $b)."<br> <br>";
echo "multiplication: " .multiply($a, $b)."<br> <br>";
echo "division: " .divide($a, $b)."<br> <br>";

echo "-----Student Grade Calculator----- <br><br>";

$mark1 = 78;
$mark2 = 85;
$mark3 = 90;

$total = $mark1+$mark2+$mark3;
$avg = round($total/3, 2);

echo"Total Marks: " .$total ."<br><br>";
echo"Average :" .$avg ."<br><br>";

if($avg >= 90){
    echo "Grade A";
}
elseif($avg >= 75){
    echo "Grade B";
}
elseif($avg >= 50){
    echo "Grade C";
}
else{
    echo "Fail";
}

?>