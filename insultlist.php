<?php

/**
 * @author MESMERiZE
 * @copyright 2010
 */

$array= array();
$file = fopen("insults.txt", "r") or exit("Unable to open file!");
//Output a line of the file until the end is reached
while(!feof($file))
  {
  array_push($array, trim(fgets($file)));
  }
fclose($file);

$rand=rand(0,76);
echo $array[$rand];
?>
<pre><?php //print_r($array); ?></pre>