#!/usr/bin/php

<?php
error_reporting(E_ALL);

define( "km200_gateway_host", 'IP Adresse', true );
define( "km200_crypt_key_private", hextobin('hier den Key eintragen'));

# mögliche Werte
#/notifications
#/dhwCircuits
#/gateway
#/heatingCircuits
#/heatSources
#/recordings
#/solarCircuits
#/system

$paths = array('/notifications', '/dhwCircuits', '/gateway', '/heatingCircuits', '/heatSources', '/recordings', '/solarCircuits', '/system');


foreach ($paths as $path) {

	$data = km200_GetData($path);

	if ($data['type'] == 'refEnum') {
		$references = get_references_data($data['references']);

		var_dump($references);
	} else
		var_dump($data);

}


function get_references_data ($references) {
	$x = 0;
	foreach ($references as $reference) {
		if (strpos($reference['id'], 'chimney') === false && strpos($reference['id'], 'delete') === false) {
			$data = km200_GetData($reference['id']);
			if ($data['type'] == 'refEnum')
				$references = get_references_data($data['references']);
			else
				var_dump($data);
			$x++;
		}
	}
	
	return $reference;
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

function km200_GetData( $REST_URL )
{
  $options = array(
    'http' => array(
      'method' => "GET",
      'header' => "Accept: application/json\r\n" .
                  "User-Agent: TeleHeater/2.2.3\r\n"
    )
  );
  $context = stream_context_create( $options );
  
  $encoded_data = file_get_contents(
					'http://' . km200_gateway_host . $REST_URL,
					false,
					$context
					);
  
  #var_dump($encoded_data);
  
  return json_decode(
    km200_Decrypt($encoded_data
    ),
    true //Achtung! Hier das true (und drüber das Komma) macht aus dem decodierten Objekt ein Array zur weiteren Bearbeitung)
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
	
	#var_dump($http_response_header);
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