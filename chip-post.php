<?
// Login de un Foro IPB
// by deerme.org


function get($host,$puerto,$patch,$referer,$cookie)
{
$fp = fsockopen($host,$puerto,$errno, $errstr,3);
fputs($fp,
"GET $patch HTTP/1.1
Host: $host
User-Agent: Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.11) Gecko/20101012 Firefox/3.6.11
Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8,application/json
Accept-Language: en-us,en;q=0.7,bn;q=0.3
Accept-Encoding: gzip,deflate
Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7
Keep-Alive: 115
Connection: keep-alive
Referer: $referer
Cookie: $cookie
Connection: Close\r\n\r\n

");

while (!feof($fp))
{
$salida .= fread($fp, 8192);
}
fclose($fp);
return $salida;
}


function post($host,$puerto,$patch,$referer,$cookie,$data_lenght,
$data)
{

$fp = fsockopen($host,$puerto,$errno, $errstr,3);
fputs($fp,
"POST $patch HTTP/1.1
Host: $host
User-Agent: Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.11) Gecko/20101012 Firefox/3.6.11
Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8,application/json
Accept-Language: en-us,en;q=0.7,bn;q=0.3
Accept-Encoding: gzip,deflate
Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7
Keep-Alive: 115
Connection: keep-alive
Referer: $referer
Cookie: $cookie
Content-Type: application/x-www-form-urlencoded
Content-Length: $data_lenght

$data
Connection: Close\r\n\r\n
");

while (!feof($fp))
{
$salida .= fread($fp, 8192);
}
fclose($fp);
return $salida;



}

$user = "bivunlim";
$pass = "priyamany351";

$datos = "UserName=".$user."&PassWord=".$pass."&x=23&y=17";

$salida = post("www.chip.in",
                80,
                "/forums/posting.php?mode=reply&f=20&sid=6fdd34ab0e0a1e18bdb94c4af4a79d4e&t=28467",
                "http://www.chip.in/forums/viewtopic.php?f=20&t=28467&p=579853",
                "style_cookie=printonly; __utma=141993696.1569261560.1263994505.1287760154.1287765079.1041; __utmz=141993696.1282376610.848.9.utmccn=(organic)|utmcsr=google|utmctr=%22sam.log%22|utmcmd=organic; phpbb3_7beuj_u=48272; phpbb3_7beuj_k=bf27c0c3293ec3aa; phpbb3_7beuj_sid=6fdd34ab0e0a1e18bdb94c4af4a79d4e; __utmc=141993696; __utmb=141993696",
                strlen($datos),
                $datos);

// Capturamo la Cookie del Servidor
// Creamos Parse para la Cookie
$parser="|Set-Cookie: (.*?)\n|is";
if( preg_match_all($parser, $salida, $capturado) )
{
// Guardamos Cookie
for($i=0;$i<count($capturado[1]);$i++)
{
if ( $i == (count($capturado[1]) -1 ) )
$cookie .= $capturado[1][$i];
else
$cookie .= $capturado[1][$i].'; ';
}
// Eliminamos Enters
$cookie = str_replace("\r","",$cookie);
}


echo $cookie;

// Entramos a la URL para la Cookie
$salida = get("www.aq2chile.cl",80,"/foro/index.php?
showtopic=2738","http://www.aq2chile.uni.cc/foro/index.php?" ,$cookie);

echo $salida;





//echo "Vamos a Comenzar<br>";
//echo $salida;


?> 