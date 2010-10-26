<?php

/**
* Simple PHP IRC Bot
*
* PHP Version 5
*
* LICENSE: This source file is subject to Creative Commons Attribution
* 3.0 License that is available through the world-wide-web at the following URI:
* http://creativecommons.org/licenses/by/3.0/.  Basically you are free to adapt
* and use this script commercially/non-commercially. My only requirement is that
* you keep this header as an attribution to my work. Enjoy!
*
* @category   Chat Room Scipt
* @package    Simple PHP IRC Bot
* @author     Super3boy <admin@wildphp.com>
* @copyright  2010, The Nystic Network
* @license    http://creativecommons.org/licenses/by/3.0/
* @link       http://wildphp.com (Visit for updated versions and more free scripts!)
* @version    1.0.0 (Last updated 03-20-2010)
*
*/

//So the bot doesnt stop.
set_time_limit(0);
ini_set('display_errors', 'on');

//Sample connection data.
$config = array(
'server' => 'chat.freenode.net',
'port'   => 6667,
'channel' => '#bibhas_field',
'name'   => 'biv-bot-Dont-ban-just-testing',
'nick'   => 'biv-bot',
'pass'   => '',
);

/*
//Set your connection data.
$config = array(
'server' => 'example.com',
'port'   => 6667,
'channel' => '#channel',
'name'   => 'real name',
'nick'   => 'user',
'pass'   => 'pass',
);
*/

class IRCBot {
    
    //This is going to hold our TCP/IP connection
    var $socket;
    
    //This is going to hold all of the messages both server and client
    var $ex = array();
    
    /*
    
    Construct item, opens the server connection, logs the bot in
    @param array
    
    */
    
    function __construct($config)
    
    {
    $this->socket = fsockopen($config['server'], $config['port']);
    $this->login($config);
    $this->main($config);
    }
    
    /*
    
    Logs the bot in on the server
    @param array
    
    */
    
    function login($config)
    {
    $this->send_data('USER', $config['nick'].' wildphp.com '.$config['nick'].' :'.$config['name']);
    $this->send_data('NICK', $config['nick']);
    $this->join_channel($config['channel']);
    }
    
    /*
    
    This is the workhorse function, grabs the data from the server and displays on the browser
    
    */
    
    function main($config)
    {
    $data = fgets($this->socket, 256);
    
    echo nl2br($data);
    
    flush();
    
    $this->ex = explode(' ', $data);
    
    if($this->ex[0] == 'PING')
    {
    $this->send_data('PONG', $this->ex[1]); //Plays ping-pong with the server to stay connected.
    }
    
    $command = str_replace(array(chr(10), chr(13)), '', $this->ex[3]);
    
    switch($command) //List of commands the bot responds to from a user.
    {
    case ':!join':
    $this->join_channel($this->ex[4]);
    break;
    case ':!part':
    $this->send_data('PART '.$this->ex[4].' :', 'Wildphp.com Free IRC Bot Script');
    break;
    
    case ':!tw' :
        $tweet=$this->get_last_tweet($this->ex[5]);
        //$message = "";
//        for($i=0; $i <= (count($this->ex)); $i++)
//        {
//        $message .= $this->ex[$i]." ";
//        }
        //$tweet="Hello";
        $this->send_data('SAY', $tweet);
    break;
    
    case ':!say':
    $message = "";
    for($i=5; $i <= (count($this->ex)); $i++)
    {
    $message .= $this->ex[$i]." ";
    }
    
    $this->send_data('PRIVMSG '.$this->ex[5].' :', $message);
    break;
    
    case ':!restart':
    echo "<meta http-equiv=\"refresh\" content=\"5\">";
    exit;
    case ':!shutdown':
    $this->send_data('QUIT', 'Wildphp.com Free IRC Bot Script');
    exit;
    }
    
    $this->main($config);
    }
    
    function send_data($cmd, $msg = null) //displays stuff to the broswer and sends data to the server.
    {
    if($msg == null)
    {
    fputs($this->socket, $cmd."\r\n");
    echo '<strong>'.$cmd.'</strong><br />';
    } else {
        fputs($this->socket, $cmd.' '.$msg."\r\n");
        echo '<strong>'.$cmd.' '.$msg.'</strong><br />';
            }
    
    }
    
    function join_channel($channel) //Joins a channel, used in the join function.
    {
    
        if(is_array($channel))
        {
            foreach($channel as $chan)
            {
                $this->send_data('JOIN', $chan);
            }
            
        } else {
            $this->send_data('JOIN', $channel);
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
        curl_setopt($ch, CURLOPT_REFERER, "http://bibhas.in");
        $body = curl_exec($ch);
        curl_close($ch);

        // now, process the JSON string
        $json = json_decode($body);
        // now have some fun with the results...
        $result=IRCBot::object_to_array(get_object_vars($json));
        if($result['results'][0]['text']!=""){
            $msg=$handle . " Said " . $result['results'][0]['text'];
            return $msg;
        }else{
            $msg=$handle . " didnt say anything!";
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
          $result[$key] = IRCBot::object_to_array($value);
        }
        return $result;
      }
      return $data;
    }
}

//Start the bot
$bot = new IRCBot($config);
?>
