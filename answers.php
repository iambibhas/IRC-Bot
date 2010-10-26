<?php

/**
 * @author MESMERiZE
 * @copyright 2010
 */

$query="phpbb fluxbb";
$query=urlencode($query);
$url="http://answers.yahooapis.com/AnswersService/V1/questionSearch?"
            . "appid=Ad1hrrPV34Fh3zYyVhTCvpQ9sTGVph6pq7lSTXl7xx1epJEGdcxux_e8Xb5Q9ao-"
            . "&query=$query"
            . "&type=resolved"

            . "&output=json";
$output = file_get_contents($url);

$output_a=object_to_array(json_decode($output));
//$output_a=$output_a['all']['questions'];
//echo " *Question:* " . $output_a[0]['Subject'] . " *Answer:* " . $output_a[0]['ChosenAnswer'] . " *Link:* " . $output_a[0]['Link'];
echo $output_a[0]['ChosenAnswer'];
?>
<pre><?php print_r($output_a); ?></pre>
<?php
function object_to_array($data) // Converts a Nested stdObject to a full associative Array
{ // Not used everywhere, because found this solution much later
    if(is_array($data) || is_object($data)) //
    {
        $result = array();
        foreach($data as $key => $value)
        {
            $result[$key] = object_to_array($value);
        }
        return $result;
    }
    return $data;
}
?>