<?php


//echo str_pad(rand(0,999), 5, "0", STR_PAD_LEFT);


$value_M = (rand(1, 9) * 10000);
$value_S = (rand(1, 9) * 1000);
$value_H = (rand(1, 9) * 100);
$value_T = (rand(1, 9) * 100);

$value = ($value_M + $value_S  + $value_H + $value_T);

echo $value;







function n_digit_random($digits) {
  $temp = "";

  for ($i = 0; $i < $digits; $i++) {
    $temp .= rand(0, 9);
  }

  return (int)$temp;
}





?>