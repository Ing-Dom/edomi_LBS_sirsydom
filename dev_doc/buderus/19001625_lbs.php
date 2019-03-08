###[DEF]###
[name				= Buderus Connect						]

[e#1	important	= trigger		#init=0					]
[e#2	important	= IP-address							]
[e#3	important	= Private-Key (Hex)						]

[e#10				= RestUrl01								]


[e#50				= Loglevel #init=8						]


[a#1				= notifications							]

[a#10				= Data01								]


[v#100				= 0.01 ]
[v#101 				= 19001625 ]
[v#102 				= Buderus Connect ]
[v#103 				= 0 ]

###[/DEF]###

###[HELP]###
Mit diesem LBS kann man Werte einer Buderus Heizungsanlage (mit EMS plus Bus), über das Buderus Gateway Logamatic web (KM50/KM100(KM200), auslesen.
Dieser LBS wurde bisher allerdings lediglich mit dem KM200 an einer Wärmepumpe vom Typ WPLS .2 getestet, laut Buderus sind die Funktionen der unterschiedlichen Gateways aber identisch und nur für unterschiedliche Anlagen-Typen vorgesehen.
Die Eingänge ab e#29 werden noch nicht beachtet, da der Fokus zunächst auf das Auslesen und nicht das Ändern von Werten gerichtet war.
Je nach Anlagen-Typ liefern auch nur bestimmte Ausgänge überhaupt Daten.
Es gibt auch noch die Möglichkeit PV-Daten auszulesen, wenn die Anlage über ein entsprechendes Modul verfügt.
Dies konnte allerdings noch nicht getestet werden.

Erarbeitet wurde diese Möglichkeit ursprünglich von einem Benutzer aus dem SYMCON-Forum:
https://www.symcon.de/forum/threads/25211-Buderus-Logamatic-Web-KM200-Reloaded
Die dort vorhandenen Scripte wurden zu diesem LBS zusammengesetzt.
Hier im Forum gibt es ebenfalls einen Thread dazu:
https://knx-user-forum.de/forum/supportforen/eibpc/823726-buderus-km200-kommunikationsmodul-an-eibpc

Die Kommunikation mit dem Gateway erfolgt mit verschlüsseltem Payload (kein TLS), welcher über mcrypt mit dem Gateway-Passwort, dem Benutzer-Passwort und einem von Buderus definierten "Salt" (alles zusammen bildet dann den private key an e#3) verschlüsselt wird.
Den für den LBS benötigten private key an e#3 kann man über die folgende Webseite (vom Ersteller des Threads im SYMCON-Forum) generieren lassen:
https://ssl-account.com/km200.andreashahn.info/
Da mcrypt kein Bestandteil der PHP-Installation von EDOMI ist, muss dieses nachträglich installiert werden.
Die Funktion mycrypt ist schon etwas betagt bzw. überholt, daher muss die Installaion aus Dritt-Repositories erfolgen, eine Migration zu OpenSSL ist leider noch nicht gelungen.
Evtl. kann ein "Kryptografie-Experte" aus dem KNX-UF etwas dazu beisteuern? :)


Ein- und Ausgänge

E1: Trigger, um die Daten einmalig auszulesen
E2:	IP Adresse des Buderus Gateway Logamatic web (KM50/KM100/KM200)
E3: Private-Key (in Hex)

E10: abzufragende Url Ausgabe auf Axx

A10: ausgelesener Wert zu Exx


V100: Version
V101: LBS Number
V102: Log file name
V103: Log level

Changelog:
==========
v0.01: initiale Test-Version

###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id) {
	if ($E=logic_getInputs($id)) {
		setLogicElementVar($id, 103, $E[50]['value']); //set loglevel to #VAR 103
		if ($E[1]['refresh'] == 1 && $E[1]['value'] == 1) {
			callLogicFunctionExec(LBSID, $id);
		}
	}
}
?>
###[/LBS]###


###[EXEC]###
<?
require(dirname(__FILE__)."/../../../../main/include/php/incl_lbsexec.php");
sql_connect();
set_time_limit(60);

$curl_errno = 0;

logging($id, "Buderus Connect gestartet");

if ($E = logic_getInputs($id)) {
	
	$ipaddress = $E[2]['value'];
	$private_key_hex = $E[3]['value'];

	define( "km200_gateway_host", $ipaddress, true );
	define( "km200_crypt_key_private", hextobin($private_key_hex));

	### notifications ###
	$data = km200_GetData('/notifications');
	
	if ($curl_errno == 0) {
		if (count($data['values']) > 0)
			logic_setOutput($id,1,1);
		else 
			logic_setOutput($id,1,0);

		### system ###
		$data = km200_GetData($E[10]['value']);
		logic_setOutput($id,10,$data['value']);

		
	}
}

logging($id, "Buderus Connect beendet");
sql_disconnect();

function myErrorHandler($errno, $errstr, $errfile, $errline)
{
	global $id;
	logging($id, "File: $errfile | Error: $errno | Line: $errline | $errstr ");
}

function error_off()
{
	$error_handler = set_error_handler("myErrorHandler");
	error_reporting(0);
}

function error_on()
{
	restore_error_handler();
	error_reporting(E_ALL);
}

function logging($id,$msg, $var=NULL, $priority=8)
{
	$E=getLogicEingangDataAll($id);
	$logLevel = getLogicElementVar($id,103);
	if (is_int($priority) && $priority<=$logLevel && $priority>0)
	{
		$logLevelNames = array('none','emerg','alert','crit','err','warning','notice','info','debug');
		$version = getLogicElementVar($id,100);
		$lbsNo = getLogicElementVar($id,101);
		$logName = getLogicElementVar($id,102) . ' --- LBS'.$lbsNo;
		strpos($_SERVER['SCRIPT_NAME'],$lbsNo) ? $scriptname='EXE'.$lbsNo : $scriptname = 'LBS'.$lbsNo;
		writeToCustomLog($logName,str_pad($logLevelNames[$logLevel],7), $scriptname." [v$version]:\t".$msg);
		
		if (is_object($var)) $var = get_object_vars($var); // transfer object into array
		if (is_array($var)) // print out array
		{
			writeToCustomLog($logName,str_pad($logLevelNames[$logLevel],7), $scriptname." [v$version]:\t================ ARRAY/OBJECT START ================");
			foreach ($var as $index => $line)
				writeToCustomLog($logName,str_pad($logLevelNames[$logLevel],7), $scriptname." [v$version]:\t".$index." => ".$line);
			writeToCustomLog($logName,str_pad($logLevelNames[$logLevel],7), $scriptname." [v$version]:\t================ ARRAY/OBJECT END ================");
		}
	}
}

function km200_Decrypt( $decryptData )
{
	$decrypt = (mcrypt_decrypt( MCRYPT_RIJNDAEL_128, km200_crypt_key_private, base64_decode($decryptData), MCRYPT_MODE_ECB, '' ) );
	// remove zero padding
	$decrypt = rtrim( $decrypt, "\x00" );
	// remove PKCS #7 padding
	$decrypt_len = strlen( $decrypt );
	$decrypt_padchar = ord( $decrypt[ $decrypt_len - 1 ] );
	for ( $i = 0; $i < $decrypt_padchar ; $i++ )
	{
		if ( $decrypt_padchar != ord( $decrypt[$decrypt_len - $i - 1] ) )
		break;
	}
	if ( $i != $decrypt_padchar )
		return $decrypt;
	else
		return substr(
			$decrypt,
			0,
			$decrypt_len - $decrypt_padchar
		);
}

function km200_GetData( $REST_URL )
{
  global $id, $curl_errno;
  error_off();
  
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, 'http://' . km200_gateway_host . $REST_URL);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
  curl_setopt($ch, CURLOPT_USERAGENT,'TeleHeater/2.2.3');
  curl_setopt($ch, CURLOPT_HEADER, 1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  $output = curl_exec($ch);
  
  $curl_errno = curl_errno($ch);
  
  $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
  $header = substr($output, 0, $header_size);
  $body = substr($output, $header_size);
  
  if (empty($output) or $curl_errno != 0) {
	logging($id, 'Fehler: ' . curl_error($ch));
  } else {
    $info = curl_getinfo($ch);

    if ($info['http_code'] != 200) {
		if (empty($info['http_code'])) {
			logging($id, "HTTP Fehler (ohne Code) beim Aufruf der URL " . $info['url']);
		} else {
			logging($id, "HTTP Fehler-Code " . $info['http_code'] . " beim Aufruf der URL " . $info['url']);
		}
	} else {
		  return json_decode(
			km200_Decrypt(
			  $body
			),
			true //Achtung! Hier das true (und drüber das Komma) macht aus dem decodierten Objekt ein Array zur weiteren Bearbeitung)
		  );
	}
  }
  
  curl_close($ch);  

  error_on();
}

function km200_Encrypt( $encryptData )
{
	// add PKCS #7 padding
	$blocksize = mcrypt_get_block_size(
		MCRYPT_RIJNDAEL_128,
		MCRYPT_MODE_ECB
	);
	$encrypt_padchar = $blocksize - ( strlen( $encryptData ) % $blocksize );
	$encryptData .= str_repeat( chr( $encrypt_padchar ), $encrypt_padchar );
	// encrypt
	return base64_encode(
		mcrypt_encrypt(
			MCRYPT_RIJNDAEL_128,
			km200_crypt_key_private,
			$encryptData,
			MCRYPT_MODE_ECB,
			''
		)
	);
}

function km200_SetData( $REST_URL, $Value )
{
	$content = json_encode(
		array(
			"value" => $Value
		)
	);
	$options = array(
		'http' => array(
	   	'method' => "PUT",
	    	'header' => "Content-type: application/json\r\n" .
	                	"User-Agent: TeleHeater/2.2.3\r\n",
			'content' => km200_Encrypt( $content )
		)
	);
	$context = stream_context_create( $options );
	@file_get_contents(
		'http://' . km200_gateway_host . $REST_URL,
		false,
		$context
	);
}

function hextobin($hexstr) 
{ 
	$n = strlen($hexstr); 
	$sbin="";   
	$i=0; 
	while($i<$n) 
	{       
		$a =substr($hexstr,$i,2);           
		$c = pack("H*",$a); 
		if ($i==0){$sbin=$c;} 
		else {$sbin.=$c;} 
		$i+=2; 
	} 
	return $sbin; 
} 

?>
###[/EXEC]###