<?php
function replaceCharAtPosition($str, $position, $newChar) {
  if ($position < 0 || $position >= strlen($str)) {
      return $str;
  }

  $before = substr($str, 0, $position);
  $after = substr($str, $position + 1);

  return $before . $newChar . $after;
}

function countOfWord($string) {
  $word = explode(" ", $string);
  $count = 0;

  $temp = [];

  // Punctuation
  $exclude = [
    [
      "content" => ".",
      "position" => "end"
    ],
    [
      "content" => ",",
      "position" => "end"
    ],
    [
      "content" => "!",
      "position" => "end"
    ],
    
    [
      "content" => "?",
      "position" => "end"
    ],
    [
      "content" => '"',
      "position" => "start_end"
    ],
    [
      "content" => "'",
      "position" => "anywhere"
    ],
    [
      "content" => "-",
      "position" => "middle"
    ]
  ];

  foreach ($word as $key => $value) {
    $found = 0;

    foreach ($exclude as $key2 => $value2) {
      $position = strpos($value, $value2["content"]);
      if($position !== false) {
        if($value2["position"] == "start" && $position == 0) {
          $value = replaceCharAtPosition($value, $position, "");
        }

        if($value2["position"] == "end" && $position == strlen($value)-1) {
          $value = replaceCharAtPosition($value, $position, "");
        }

        if($value2["position"] === "start_end" && ($position == strlen($value)-1 || $position == 0)) {
          $value = replaceCharAtPosition($value, $position, "");
        }

        if($value2["position"] == "middle" && $position !== strlen($value)-1 && $position !== 0) {
          $value = replaceCharAtPosition($value, $position, "");
        }

        if($value2["position"] == "anywhere") {
          $value = replaceCharAtPosition($value, $position, "");
        }
      }
    }

    if (!preg_match('/^[a-zA-Z0-9]+$/', $value)) {
      $found++;
    }

    if($found == 0) {
      $count++;
    }
  }

  return $count;
}

$string = "Kemarin Shopia per[gi ke mall.";
print_r(countOfWord($string));