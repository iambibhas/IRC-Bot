<?php

/**
 * @author MESMERiZE
 * @copyright 2010
 */

$url="http://www.google.com/dictionary/json?callback=dict_api.callbacks.id100&q=gooda&sl=en&tl=en&restrict=pr%2Cde&client=te";
$content=file_get_contents($url);
//$content=trim(str_replace("dict_api.callbacks.id100","",$content));
//
$content=str_replace("dict_api.callbacks.id100(","",$content);
$content=str_replace(",200,null)","",$content);
$content=str_replace("\\","",$content);
$content=str_replace("/","",$content);
$array=json_decode($content);
//echo $content;
?>
<pre><?php
print_r($array);
 ?></pre>