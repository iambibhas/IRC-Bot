<?php
/*
* PHP IRC Bot
* Written By Trip-Out for www.TheGeeks.us
* 
* You may modify this for your own purposes,
* all I ask is that you drop us off a greet 
* at www.TheGeeks.us
*/
set_time_limit(0);
//Open socket to server,port
$socket = fsockopen("irc.freenode.net",6667);
//Sends USER HOSTNAME IDENT :REAL NAME
fputs($socket,"USER PHPBot thegeeks.us PHP :PHP Bot\n");

//Sends the NICK to server
fputs($socket,"NICK biv-bot-2\n");

//Join #lamerchan
//fputs($socket,"JOIN #bibhas_field\n");
fputs($socket,"JOIN #chip-india\n");
$commands = array (
   "!version",
   "!say",
   "!insult",
   "!tw",
   "!imdb",
   "!g",
   "!answerme",
   "!restart1",
   "!exit1"
);
//Sends the script into an infinite loop
while (1) {

   //Recieves the data into $data in 128 bytes.
    while($data=fgets($socket,128)) {
      //puts the data in an array by word
      $get = explode(' ', $data);
      //Server Pinged us lets reply!
      if ($get[0] == "PING") {
		fputs ($socket, "PONG ".$get[1]."\n");
      }
//The following code sets $nick and $chan variables from the text last entered in the channel
    if (substr_count($get[2],"#")) {
	$nick = explode(':',$get[0]);
	$nick = explode('!',$nick[1]);
	$nick = $nick[0];
	$chan = $get[2];
	$num = 3;
	if ($num == 3) {
		$split = explode(':',$get[3],2);
		$text = rtrim($split[1]);
                //>	0=>:Zhe_Viking!~chatzilla@117.194.203.189 1=>PRIVMSG 2=>#bibhas_field 3=>:!say 4=>Hello 5=>World.
                //This is where we start processing the commands we entered in earlier
        	if (in_array($text,$commands)) {
        		switch(rtrim($text)) {
				case "!version":
					fputs($socket,"PRIVMSG $chan : PHP Bot Developed mostly by Bibhas.\n");
				break;
                case "!insult":
                    $msg=get_insult();
                    $target=trim($get[4]);
					fputs($socket,"PRIVMSG $chan :$target, $msg\n");
				break;
                case "!tw":
                    $msg="";
                    $handle=$get[4];
                    $msg= get_last_tweet($handle);
                    $msg=html_entity_decode($msg, ENT_QUOTES);
                    fputs($socket,"PRIVMSG $chan :$msg\n");
                    unset($msg);
                break;
                case "!imdb":
                    $arraysize = sizeof($get);
                    $movie="";
                    $count = 4;
                    $title="";
                	while ($count <= $arraysize) {
                		$title = $title." ".$get[$count];
                		$count++;
                	}
                    $title=urlencode($title);
                    $movie=get_rating($title);
                    $movie=html_entity_decode($movie, ENT_QUOTES);
                    $movie = preg_replace('~&#x0*([0-9a-f]+);~ei', 'chr(hexdec("\\1"))', $movie);
                    fputs($socket,"PRIVMSG $chan :$nick => $movie\n");
                    unset($movie);
                break;
                case "!g":
                    $arraysize = sizeof($get);
                    $count = 4;
                    $query=""; $result="";
                	while ($count <= $arraysize) {
                		$query = $query." ".$get[$count];
                		$count++;
                	}
                    $result=from_google($query);
                    $result=html_entity_decode($result, ENT_QUOTES);
                    fputs($socket,"PRIVMSG $chan :$nick => $result\n");
                    unset($result);
                break;
                case "!say":
                	$arraysize = sizeof($get);
                        //1,2,3 are just nick and chan, 4 is where text starts 
                	$count = 0;
                    $saytext="";
                	while ($count <= $arraysize) {
                		$saytext = $saytext." " . $count . "=>".$get[$count];
                		$count++;
                	}
                    echo $saytext . "<br />";
                    $saytext = "Array Size $arraysize " . $saytext;
                	fputs($socket,"PRIVMSG $chan :$saytext\n");
                	unset($saytext);
                break;
                case "!answerme":
                    $arraysize = sizeof($get);
                    $count = 4;
                    $query=""; $result="";
                	while ($count <= $arraysize) {
                		$query = $query." ".$get[$count];
                		$count++;
                	}
                    $query=trim($query);
                    $result=trim(answerMe($query));
                    fputs($socket,"PRIVMSG $chan :$nick $result\n");
                    unset($result);
                break;
                }
            }
        }
    }else if(substr_count($get[3],"#")){
        //print_r($get);
        $chan=trim(str_replace(":","",$get[3]));
        $command=trim($get[4]);
        switch ($command) {
           case "!exit":
                fputs($socket,"PRIVMSG $chan :Shutting Down PHPBot!\n");
            	fputs($socket,"QUIT Client Disconnected!\n");
                //IMPORANT TO HAVE THE DIE(), this throws the script out of the infinite while loop!
                exit;
           break;
           case '!restart':
                echo "<meta http-equiv=\"refresh\" content=\"5\">";
                exit;
           break;
        }
    }
        //Shows the text in the browser as Time - Text
    echo nl2br(date('G:i:s')."-".$data);
    
    //Flush it out to the browser
    flush();
    }
}
function get_last_tweet($handle){
    $url = "http://search.twitter.com/search.json?"
        . "q=from:" . $handle;
    // sendRequest
    $ch = curl_init();
    $msg="";
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_REFERER, "http://asfd.in");
    $body = curl_exec($ch);
    curl_close($ch);

    // now, process the JSON string
    $json = json_decode($body);
    // now have some fun with the results...
    $result=object_to_array(get_object_vars($json));
    if($result['results'][0]['text']!=""){
        $msg="Twitter: http://twitter.com/{$result['results'][0]['from_user']} => " . $result['results'][0]['text'];
        return $msg;
    }else{
        $msg="Twitter: Nothing!";
        return $msg;
    }
    //return $result['results'][0]['text'];
}
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

function get_rating($title){
    require_once 'simple_html_dom.php';
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
                $rank=str_replace("</span>","",$rank);
                $found=true;
                break;
            }
        }
    }
    if($found){
        foreach($html->find('a') as $element){
            $innertxt=$element->href;
            if(stripos($innertxt,"/title/")!==false){
                $t_title=html_entity_decode($element->title);
                return $t_title . " " . $rank . " http://imdb.com".$element->href;
                break;
            }
        }
    }else{
        return "Not found!";
    }
    $html->clear();
}

function get_insult(){
    $array= array();
    $file = fopen("insults.txt", "r");
    //Output a line of the file until the end is reached
    while(!feof($file)){
        array_push($array, trim(fgets($file)));
    }
    fclose($file);
    $a_size=count($array);
    $a_size--;
    $rand=rand(0,$a_size);
    return $array[$rand];
}

function from_google($query){
    $query=urlencode($query);
    $array=array(); 
    $url = "http://ajax.googleapis.com/ajax/services/search/web?v=1.0&"
        . "q=" . $query . "&rsz=large";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_REFERER, "http://forums.com");
    $body = curl_exec($ch);
    curl_close($ch);
    $json = json_decode($body);
    $array = object_to_array($json);
    return $array['responseData']['results'][0]['titleNoFormatting'] . " => " . $array['responseData']['results'][0]['url']; 
}

function answerMe($query){
    $output_a=array();
    $query=urlencode($query);
    $url="http://answers.yahooapis.com/AnswersService/V1/questionSearch?"
                . "appid=Ad1hrrPV34Fh3zYyVhTCvpQ9sTGVph6pq7lSTXl7xx1epJEGdcxux_e8Xb5Q9ao-"
                . "&query=$query"
                . "&type=resolved"
                . "&search_in=question"
                . "&output=json";
    $output = file_get_contents($url);
    $output_a=object_to_array(json_decode($output));
    if($output_a['all']['count']>0){
        $output_a=$output_a['all']['questions'];
        
        //print_r($output_a);
        return " *Possible Question:* " . $output_a[0]['Subject'] . " *Answer:* " . $output_a[0]['ChosenAnswer'] . " *Link:* " . $output_a[0]['Link'];
        //return $output_a[0]['ChosenAnswer'];
    }else{
        return "Dont have any answer to that!";
    }
}
?>
