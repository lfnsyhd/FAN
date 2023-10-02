<?php
function countOfPair($array = []) {
  if(!is_array($array)) return;

  $arrayCounts = array_count_values($array);
  $count = 0;

  foreach ($arrayCounts as $key => $value) {
    $divided = floor($value / 2);
    if($divided > 0) {
      $count += $divided;
    }
  }

  return $count;
}

$array = [10, 20, 20, 10, 10, 30, 50, 10, 20];
print_r(countOfPair($array));
?>