<?php

/**
 * @author MESMERiZE
 * @copyright 2010
 */

require_once 'simple_html_dom.php';

$url="http://www.mydala.com/kolkata";
$html = new simple_html_dom();
$html->load_file($url);
foreach ($html->find('div[id=dealname]') as $div) {
    $dealtext=$div->plaintext;
    break;
}
foreach ($html->find('div#DealDetails img') as $img) {
    $dealimg=$img->src;
    break;
}
$dealtext=iconv('UTF-8', 'ASCII//TRANSLIT', $dealtext);
echo $dealtext . "<br />";
getThumb($dealimg);
$html->clear();

function getThumb($imageLocation){
    $file = $imageLocation; 
    $size = 0.35; 
    $save = 'mydala.jpg';
    list($width, $height) = getimagesize($file); 
    $modwidth = $width * $size; 
    $modheight = $height * $size; 
    $tn= imagecreatetruecolor($modwidth, $modheight); 
    $source = imagecreatefromjpeg($file); 
    imagecopyresized($tn, $source, 0, 0, 0, 0, $modwidth, $modheight, $width, $height); 
    imagejpeg($tn, $save, 100) ;
}
?>
<img src="mydala.jpg" />