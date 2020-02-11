###[DEF]###
[name		= FroniusInverter LBS2100 V0.01	]

[e#1 trigger= 		Autostart #init=1]
[e#2 important= 	Host] ModBus Slave IP or Hostname
[e#3 optional= 		Port #init=502] Modbus Port
[e#4 = 				Reserved] Reserved
[e#5 optional= 		SlaveID #init=0]
[e#6 = 				Logging #init=6]
[e#7 =				Loop #init=0]
[e#8 =				Delay #init=1000]

[a#1		= Value					]

[v#1             = 0 ]
[v#2             = 0 ]
[v#5             = 0 ]
[v#100           = V0.01 ]
[v#101           = 19002010 ]
[v#102           = FroniusInverter LBS2100]
[v#103           = 0 ]
[v#104           = 0 ]
[v#105           = 0 ]
###[/DEF]###


###[HELP]###
This LBS polls data from a Fronius Inverter via Modbus TCP.
It is tested with a Fronius Symo 7.0.0-3M but should work for all Primo and Symo modells.

INSTALLATION:
This LBS requires phpmodbus.php to be present on Edomi.
You can either copy the included zip file and extract the content to /usr/local/edomi/main/include/php/ or
download phpModbus yourself. The version included is slightly modified to reduce logging output.

USAGE:
The LBS can work manually triggerd, the request is done once at trigger.
With E7 Loop = 1 the LBS will continously poll the data, the frequency can be adjusted by E8 Delay in milliseconds.

Inputs:

Outputs:


Versions:
V0.01	2020-02-11	SirSydom		initial version

Open Issues:


Author:
SirSydom - com@sirsydom.de
Copyright (c) 2020 SirSydom


Github:
https://github.com/SirSydom/edomi_LBS_sirsydom


Links:
with credits to LBS 1030 V0.6 by Michael Pattison



###[/HELP]###


###[LBS]###
<?

function LB_LBSID($id)
{
 if ($E=logic_getInputs($id))
 {
  $loop=$E[7]['value'];
  $delay=$E[8]['value'];
  if (logic_getState($id) == 1 && $E[1]['value'] == 0)	// if LBS is executed cylic AND Trigger is 0
  {
   logic_setState($id,0);	// stop cyclic execution
   LB_LBSID_logging($id, 'LBS ended');
  }
  else
  {
   setLogicElementVar($id, 103, $E[6]['value']); // set loglevel to #VAR 103
   if(logic_getState($id) == 0) // LBS is not executed cylic
   {
    LB_LBSID_logging($id, 'LBS started');
   }
   
   if(getLogicElementVar($id,1) != 1)
   {
    setLogicElementVar($id,5,1);
    setLogicElementVar($id,1,1);                        //setzt V1=1, um einen mehrfachen Start des EXEC-Scripts zu verhindern
    callLogicFunctionExec(LBSID,$id);                 //EXEC-Script starten (garantiert nur einmalig)
   }
   elseif(logic_getVar($id,5)>9)
   {
    setLogicElementVar($id,5,0);
    setLogicElementVar($id,1,0);
    logic_SetOutput($id,10,2);		// Error Output
   }
   else
   {
    $v5=logic_getVar($id,5);
    logic_setVar($id,5,$v5+1);
   }
   if ($loop==1)
   {
    logic_setState($id,1,$delay);
   }
   else
   {
    logic_setState($id,0);
   }
  }
 }
}
?>
###[/LBS]###


###[EXEC]###
<?
require(dirname(__FILE__)."/../../../../main/include/php/incl_lbsexec.php");
require_once(dirname(__FILE__)."/../../../../main/include/php/ModbusMaster.php");
set_time_limit(0);                                       //Wichtig! Script soll endlos laufen
sql_connect();
logging($id, "START EXEC",null,6);
setLogicElementVar($id, 2, getmypid());
$suppressModBusError='0';
//
// error_off() : switch off error reporting
// error_on() : switch on error reporting
//
function myErrorHandler($errno, $errstr, $errfile, $errline)
{
  global $id;
  if(($errfile=='/usr/local/edomi/main/include/php/ModbusMaster.php' AND $errno=='8' AND $errline='506')OR($errfile=='/usr/local/edomi/main/include/php/ModbusMaster.php' AND $errno=='2048')OR($errfile=='/usr/local/edomi/main/include/php/IecType.php' AND $errno=='2048')){return;}

  logging($id, "File: $errfile | Error: $errno | Line: $errline | $errstr ",null,4);
}
function myErrorHandlerModBus($errno, $errstr, $errfile, $errline)
{
  global $id, $suppressModBusError;
  if(($errfile=='/usr/local/edomi/main/include/php/ModbusMaster.php' AND $errno=='2048')OR($errfile=='/usr/local/edomi/main/include/php/IecType.php' AND $errno=='2048')){return;}

  if(($errfile=='/usr/local/edomi/main/include/php/ModbusMaster.php' AND $errno=='8' AND $errline='506')){
    $suppressModBusError='1';
    logging($id, "Corrupt ModBus Message received");
    return;}

  logging($id, "File: $errfile | Error: $errno | Line: $errline | $errstr ",null,4);
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

function logging($id, $msg, $var = NULL, $priority = 8)
{
  //$E = getLogicEingangDataAll($id);
  $logLevel = getLogicElementVar($id, 103);
  if (is_int($priority) && $priority <= $logLevel && $priority > 0) {
    $logLevelNames = array(
      'none',
      'emerg',
      'alert',
      'crit',
      'err',
      'warning',
      'notice',
      'info',
      'debug'
    );
    $version = getLogicElementVar($id, 100);
    $lbsNo = getLogicElementVar($id, 101);
    $logName = getLogicElementVar($id, 102) . "-LBS$lbsNo";
    $logName = preg_replace('/ /', '_', $logName);
    if (logic_getVar($id, 104) == 1)
    $logName .= "-$id";
    if (logic_getVar($id, 105) == 1)
    $msg .= " ($id)";
    strpos($_SERVER['SCRIPT_NAME'], $lbsNo) ? $scriptname = 'EXE' . $lbsNo : $scriptname = 'LBS' . $lbsNo;
    writeToCustomLog($logName, str_pad($logLevelNames[$logLevel], 7), $scriptname . " [v$version]:\t" . $msg);
    if (isset($var)) {
      writeToCustomLog($logName, str_pad($logLevelNames[$logLevel], 7), $scriptname . " [v$version]:\t================ ARRAY/OBJECT START ================");
      writeToCustomLog($logName, str_pad($logLevelNames[$logLevel], 7), $scriptname . " [v$version]:\t" . json_encode($var));
      writeToCustomLog($logName, str_pad($logLevelNames[$logLevel], 7), $scriptname . " [v$version]:\t================ ARRAY/OBJECT  END  ================");
    }
  }
}


date_default_timezone_set('Europe/Berlin');

error_off();


if($E=getLogicEingangDataAll($id))
{
	$endianness=0;
	
	$v3=getLogicElementVar($id, 3);

	$querys=array();

	$retries=0;

	// Create Modbus object
	$modbus = new ModbusMaster($E[2]['value'],"TCP");

	try
	{
		// FC 3
		if ($E[$inputs[$i]+3]['value']==3)
		{
			set_error_handler("myErrorHandlerModBus");
			$suppressModBusError='0';
			$recData = $modbus->readMultipleRegisters($E[5]['value'], 40091, 2);	// AC Power in W
			restore_error_handler();
		}
	}
	catch (Exception $e)
	{
		// Print error information if any
		logging($id, "Modbus Error: ", $modbus,4);
		logging($id, "Modbus Error: ", $e,4);
		logging($id, "Retries:" . $retries);

		if ($retries<3)
		{
			$retries+=1;
			break;
		}
		else
		{
			setLogicLinkAusgang($id,10,1); //send error to A10
			setLogicElementVar($id,1,0);  //allow to restart
			logging($id, "Error: Retry counter reached max. Exiting!",null,4);
			exit;
		}
    }
    // Print status information
    logging($id,"Status:" . $modbus);

    // Print read data
    if (isset($recData) AND $recData!=false AND $suppressModBusError=='0')
	{
		logging($id,"Type:" . $E[$inputs[$i]+2]['value'] . "Raw Data:",$recData);

        if($endianness == 0)
		{
			$ret = (($recData[3] & 0xFF)) |
			(($recData[2] & 0xFF)<<8) |
			(($recData[1] & 0xFF))<<16 |
			(($recData[0] & 0xFF)<<24);
		}
		else
		{
			$ret = (($recData[3] & 0xFF)<<24) |
			(($recData[2] & 0xFF)<<16) |
			(($recData[1] & 0xFF)<<8) |
			(($recData[0] & 0xFF));
        }
        $ulong = pack('L*', $ret);
        $float = unpack('f*', $ulong);
        $ret = $float[1];
      }


      logging($id,"Data:" . $ret);
      setLogicLinkAusgang($id,$i+1,$ret);
    }
  }

  setLogicLinkAusgang($id,9,1);

  unset($modbus);

  setLogicLinkAusgang($id,9,0);
  logging($id,"end of delay");
}

setLogicElementVar($id,1,0); //allow to restart
//logging($id,"Memory at End: " . (string)memory_get_usage());
logging($id, "STOP ModbusMaster EXEC",null,6);
error_on();
sql_disconnect();
?>
###[/EXEC]###