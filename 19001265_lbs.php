###[DEF]###
[name				= Buderus EMS plus (v1.1)				]

[e#1	option		= trigger (request)		#init=0			]
[e#2	important	= IP-address							]
[e#3	important	= Private-Key (Hexadezimal)				]
[e#4	option		= dump all data to log	#init=0			]
[e#5	option		= loglevel #init=8						]


[e#7	option		= write URL 1							]
[e#8	option		= write URL 1 data						]
[e#9	option		= write URL 2							]
[e#10	option		= write URL 2 data						]
[e#11				= request URL 1							]
[e#12				= request URL 2							]
[e#13				= request URL 3							]
[e#14				= request URL 4							]
[e#15				= request URL 5							]
[e#16				= request URL 6							]
[e#17				= request URL 7							]
[e#18				= request URL 8							]
[e#19				= request URL 9							]
[e#20				= request URL 10						]
[e#21				= request URL 11						]
[e#22				= request URL 12						]
[e#23				= request URL 13						]
[e#24				= request URL 14						]
[e#25				= request URL 15						]
[e#26				= request URL 16						]
[e#27				= request URL 17						]
[e#28				= request URL 18						]
[e#29				= request URL 19						]
[e#30				= request URL 20						]

[a#1				= error									]
[a#2				= notifications							]
[a#3				= notifications error list				]

[a#7				= write URL 1 error						]
[a#8				= write URL 1 response header			]
[a#9				= write URL 2 error						]
[a#10				= write URL 2 response header			]

[a#11				= data URL 1							]
[a#12				= data URL 2							]
[a#13				= data URL 3							]
[a#14				= data URL 4							]
[a#15				= data URL 5							]
[a#16				= data URL 6							]
[a#17				= data URL 7							]
[a#18				= data URL 8							]
[a#19				= data URL 9							]
[a#20				= data URL 10							]
[a#21				= data URL 11							]
[a#22				= data URL 12							]
[a#23				= data URL 13							]
[a#24				= data URL 14							]
[a#25				= data URL 15							]
[a#26				= data URL 16							]
[a#27				= data URL 17							]
[a#28				= data URL 18							]
[a#29				= data URL 19							]
[a#30				= data URL 20							]

[v#100				= 1.1									]
[v#101 				= 19001265								]
[v#102 				= Buderus EMS plus 						]
[v#103 				= 0										]

###[/DEF]###

###[HELP]###
Mit diesem LBS kann man Werte einer Buderus Heizungsanlage (mit EMS plus Bus), über das Buderus Gateway Logamatic web (KM50/KM100(KM200), auslesen.
Dieser LBS wurde bisher allerdings lediglich mit dem KM200 an einer Wärmepumpe vom Typ WPLS .2 getestet, laut Buderus sind die Funktionen der unterschiedlichen Gateways aber identisch und nur für unterschiedliche Anlagen-Typen vorgesehen.

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

E1:   Trigger, um die Daten einmalig zu schreiben (sofern URL an E9 und Wert an E10 angegeben wurde) bzw. auszulesen (sofern URLs an E11-20 angegeben wurden)
E2:	  IP Adresse des Buderus Gateway Logamatic web (KM50/KM100/KM200)
E3:   Private-Key (Hexadezimal)
E4:   Durchsucht das Buderus Gateway nach allen verfügbaren URLs sowie den zugehörigen Daten und gibt diese in der Log-Datei aus (die write-URL an E9, sowie die request-URLs an E11-20 werden alle ignoriert, sofern der Eingang E4 auf 1 gesetzt ist!)
E5:   Bestimmt das Log level für die Ausgabe in der Log-Datei

E7:   Schreibt den Wert an E8 an die angegebene URL (Achtung! Wenn der Eingang E7 und E8 einen Wert enthält, werden die Daten jedes mal geschrieben, sofern der Trigger an E1 ausgelöst wird, es empfiehlt sich daher zwei getrennte LBS für das Schreiben und das Lesen zu verwenden.)
E8:  Wert für den Schreibvorgang an die URL an E9

E9:   Schreibt den Wert an E10 an die angegebene URL (Achtung! Wenn der Eingang E9 und E10 einen Wert enthält, werden die Daten jedes mal geschrieben, sofern der Trigger an E1 ausgelöst wird, es empfiehlt sich daher zwei getrennte LBS für das Schreiben und das Lesen zu verwenden.)
E10:  Wert für den Schreibvorgang an die URL an E9

E11..30: Abzufragende URLs, welche mit E4 ermittelt werden können

A1:   funktioniert noch nicht korrekt
A2:   gibt eine 0 aus, sofern keine Benachrichtigungen von der Anlage vorliegen und eine 1 aus, wenn Benachrichtigungen vorliegen
A3:   gibt die Benachrichtigungen von der Anlage in Form von Störungs-Codes, mit einem ";" getrennt, aus
A7:   funktioniert noch nicht korrekt
A8:   gibt den zurückgegebenen HTTP-Header des Schreibvorgangs von E7/E8 aus
A9:   funktioniert noch nicht korrekt
A10:  gibt den zurückgegebenen HTTP-Header des Schreibvorgangs von E9/E10 aus

A11..30: jeweils ausgelesener Wert


V100: Version
V101: LBS Number
V102: Log file name
V103: Log level

Changelog:
==========
v1.1  Erweiterung für mehr Ein- und Ausgänge
v1.0: ACHTUNG! Redesign des Bausteins! Es werden nun keine voreingestellten URLs (Datenpunkte) mehr abgefragt,
      sondern man muss diese zunächst mit E4 = 1 (und E1 = 1) für seine Anlage ermitteln und diese anschließend ab E11 eintragen.
	  Sollten Fehlermeldungen wie "HTTP Fehler-Code 403 beim Aufruf der URL" im Log zu sehen sein, liegt das daran, dass zwar URLs gefunden
	  wurden, diese aber von Buderus nicht für die Abfrage freigegeben sind.
	  Danach ist E4 wieder auf 0 zu setzen, da ansonsten keine URLs (Datenpunkte) abgefragt werden.
v0.3: Ausgabe der Störungscodes an A51 hinzugefügt
v0.2: Umbau der Kommunikationsfunktion auf cURL, Custom-Log und Fehlerbehandlung hinzugefügt
v0.1: initiale Version

###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id) {
	if ($E=logic_getInputs($id)) {
		setLogicElementVar($id, 103, $E[5]['value']); //set loglevel to #VAR 103
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

logging($id, "Buderus EMS plus LBS gestartet", NULL, 7);

if ($E = logic_getInputs($id)) {
	
	$ipaddress = $E[2]['value'];
	$private_key_hex = $E[3]['value'];

	define( "km200_gateway_host", $ipaddress, true );
	define( "km200_crypt_key_private", hextobin($private_key_hex));

	if (!empty($E[7]['value']) && !empty($E[8]['value'])) {
		
		$response = km200_SetData($E[7]['value'],$E[8]['value']);
		
		if ($curl_errno == 0) {
			logic_setOutput($id,7,0);
			logic_setOutput($id,8,$response);
		} else {
			### wenn nicht A7 (error) auf 1 setzen ###
			logic_setOutput($id,7,1);
			logic_setOutput($id,8,$response);
		}
	} else
		logic_setOutput($id,10,'');
	
	if (!empty($E[9]['value']) && !empty($E[10]['value'])) {
		
		logging($id, "SetData2", NULL, 7);
		$response = km200_SetData($E[9]['value'],$E[10]['value']);
		
		if ($curl_errno == 0) {
			logic_setOutput($id,9,0);
			logic_setOutput($id,10,$response);
			logging($id, "SetData2 ok", NULL, 7);
		} else {
			### wenn nicht A9 (error) auf 1 setzen ###
			logic_setOutput($id,9,1);
			logic_setOutput($id,10,$response);
			logging($id, "SetData2 error", NULL, 7);
		}
	} else
		logic_setOutput($id,10,'');
	
	### notifications abfragen, um sicher zu gehen, dass das Gerät antwortet ###
	$data = km200_GetData('/notifications');

	if ($curl_errno == 0) {
		logic_setOutput($id,1,0);
		if (count($data['values']) > 0) {
			logic_setOutput($id,2,1);
			$notifications_error_list = '';
			foreach ($data['values'] as $value) {
				if (strlen($notifications_error_list) == 0)
					$notifications_error_list .= $value['dcd'] . '/' . $value['ccd'];
				else
					$notifications_error_list .= ';' . $value['dcd'] . '/' . $value['ccd'];
			}
			logic_setOutput($id,3,$notifications_error_list);
		} else { 
			logic_setOutput($id,2,0);
			logic_setOutput($id,3,'');
		}		

		if ($E[4]['value'] == 1) {

			logging($id, "prüfe verfügbare URLs", NULL, 7);

			$paths = array('/notifications', '/dhwCircuits', '/gateway', '/heatingCircuits', '/heatSources', '/recordings', '/solarCircuits', '/system');

			foreach ($paths as $path) {
				$data = km200_GetData($path);
				if ($data['type'] == 'refEnum') {
					$data = get_references_data($data['references']);
				} else {
					if (is_array($data)) {
						logging($id, $data['id'] . (isset($data['value']) ?  ' = ' . $data['value'] : '') . (isset($data['unitOfMeasure']) ? ' ' . $data['unitOfMeasure'] : ''), NULL, 7);
						logging($id, 'Details:' , $data, 7);
					}
				}
			}

		} else {

			### request URLs ###
			for ($i = 11; $i <= 30; $i++) {
				if (!empty($E[$i]['value'])) {
					$data = km200_GetData($E[$i]['value']);
					logic_setOutput($id,$i,$data['value']);
				}
			}

		}

	} else {
		### wenn nicht A1 (error) auf 1 setzen ###
		logic_setOutput($id,1,1);
	}
}

logging($id, "Buderus EMS plus LBS beendet", NULL, 7);
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
  
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, 'http://' . km200_gateway_host . $REST_URL);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
  curl_setopt($ch, CURLOPT_USERAGENT, 'TeleHeater/2.2.3');
  curl_setopt($ch, CURLOPT_HEADER, 1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

  error_off();
  $output = curl_exec($ch);
  error_on();

  $curl_errno = curl_errno($ch);

  $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
  $header = substr($output, 0, $header_size);
  $body = substr($output, $header_size);

  if (empty($output) or $curl_errno != 0) {
	logging($id, 'Fehler: ' . curl_error($ch), NULL, 4);
  } else {
    $info = curl_getinfo($ch);

    if ($info['http_code'] != 200) {
		if (empty($info['http_code'])) {
			logging($id, "HTTP Fehler (ohne Code) beim Aufruf der URL " . $info['url'], NULL, 5);
		} else {
			logging($id, "HTTP Fehler-Code " . $info['http_code'] . " beim Aufruf der URL " . $info['url'], NULL, 5);
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
  global $id, $curl_errno;

  if (is_numeric($Value))
	  $Value = intval($Value);
  
  $content = json_encode(array("value" => $Value));
  $content_encrypted = km200_Encrypt($content);
  
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, 'http://' . km200_gateway_host . $REST_URL);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
  curl_setopt($ch, CURLOPT_USERAGENT, 'TeleHeater/2.2.3');
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
  curl_setopt($ch, CURLOPT_HEADER, 1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $content_encrypted);

  error_off();
  $output = curl_exec($ch);
  error_on();

  $curl_errno = curl_errno($ch);
  
  $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
  $header = substr($output, 0, $header_size);
  $body = substr($output, $header_size);
  
  if (empty($output) or $curl_errno != 0) {
	logging($id, 'Fehler: ' . curl_error($ch), NULL, 4);
  } else {
    $info = curl_getinfo($ch);

    if (($info['http_code'] != 200) && ($info['http_code'] != 204)) {
		if (empty($info['http_code'])) {
			logging($id, "HTTP Fehler (ohne Code) beim Beschreiben der URL " . $info['url'], NULL, 5);
		} else {
			logging($id, "HTTP Fehler-Code " . $info['http_code'] . " beim Beschreiben der URL " . $info['url'], NULL, 5);
		}
		return $header;
	} else {
		return $header;
	}
  }
  
  curl_close($ch);  

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

function get_references_data ($references) {
	global $id;
	foreach ($references as $reference) {
			$data = km200_GetData($reference['id']);
			if ($data['type'] == 'refEnum')
				$references = get_references_data($data['references']);
			else {
				if (is_array($data)) {
					logging($id, $data['id'] . (isset($data['value']) ?  ' = ' . $data['value'] : '') . (isset($data['unitOfMeasure']) ? ' ' . $data['unitOfMeasure'] : ''), NULL, 7);
					logging($id, 'Details:' , $data, 7);
				}
			}
	}
}

?>
###[/EXEC]###