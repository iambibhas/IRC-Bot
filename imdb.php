<?php

/**
 * @author MESMERiZE
 * @copyright 2010
 */

require_once 'simple_html_dom.php';

$title=urlencode($_REQUEST['title']);
$rank="";
$found=false;
$long_title="";
$url="http://www.imdb.com/search/title?production_status=released,filming&title={$title}&title_type=feature";
$html = file_get_html($url);
foreach($html->find('span') as $element){
    if($element->class="rating-rating"){
        $innertxt=$element->innertext;
        if(stripos($innertxt,"/10")!==false){
            $t_a=explode("<span>",$element->innertext,2);
            $rank=implode("",$t_a);
            $found=true;
            break;
        }
    }
}
if($found){
    foreach($html->find('a') as $element){
        $innertxt=$element->href;
        if(stripos($innertxt,"/title/")!==false){
            echo $element->title . " " . $rank . " http://imdb.com".$element->href;
            break;
        }
    }
}else{
    echo "Not found!";
}

?>