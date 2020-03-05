###[DEF]###
[name		= FroniusInverter LBS2100 V0.10	]

[e#1 trigger= 		Trigger]
[e#2 important= 	Host] ModBus Slave IP or Hostname
[e#3 optional= 		Port #init=502] Modbus Port

[e#5 optional= 		SlaveID #init=1]
[e#6 = 				Logging #init=6]
[e#7 =				Loop #init=0]
[e#8 =				Delay #init=10000]

[a#1  	= AC Total Current       ]
[a#2  	= AC Phase-A Current     ]
[a#3  	= AC Phase-B Current     ]
[a#4  	= AC Phase-C Current     ]
[a#5  	= AC Voltage Phase-AB    ]
[a#6  	= AC Voltage Phase-BC    ]
[a#7  	= AC Voltage Phase-CA    ]
[a#8  	= AC Voltage Phase-A-to-N]
[a#9  	= AC Voltage Phase-B-to-N]
[a#10 	= AC Voltage Phase-C-to-N]
[a#11 	= AC Power               ]
[a#12 	= AC Frequency           ]
[a#13 	= Apparent Power         ]
[a#14 	= Reactive Power         ]
[a#15 	= Power Factor           ]
[a#16 	= AC Lifetime Energy     ]
[a#17 	= AC Daily Energy        ]
[a#18 	= AC Yearly Energy       ]
[a#19 	= DC Current             ]
[a#20 	= DC Voltage             ]
[a#21 	= DC Power               ]
[a#22 	= Vendor Operating State ]
[a#23 	= State Code             ]
[a#24 	= DC Current MPPT 1      ]
[a#25 	= DC Voltage MPPT 1      ]
[a#26 	= DC Power MPPT 1        ]
[a#27 	= DC Energy MPPT 1       ]
[a#28 	= Operating State MPPT 1 ]
[a#29 	= DC Current MPPT 2      ]
[a#30 	= DC Voltage MPPT 2      ]
[a#31 	= DC Power MPPT 2        ]
[a#32 	= DC Energy MPPT 2       ]
[a#33 	= Operating State MPPT 2 ]
[a#40 	= LBS Error              ]


[v#1             = 0 ]
[v#2             = 0 ]
[v#5             = 0 ]
[v#100           = V0.10 ]
[v#101           = 19002100 ]
[v#102           = FroniusInverter LBS2100]
[v#103           = 0 ]
###[/DEF]###


###[HELP]###
This LBS polls data from a Fronius Inverter over Modbus TCP.
It is tested with a Fronius Symo 7.0.0-3M but should work for all Primo and Symo models.

INSTALLATION:
This LBS requires phpmodbus.php to be present on Edomi.
You can either copy the included zip file and extract the content to /usr/local/edomi/main/include/php/ or
download phpModbus yourself. Be carefull and compare for differences!

USAGE:
The LBS can work manually triggerd, the request is done once at trigger.
With E7 Loop = 1 the LBS will continously poll the data, the frequency can be adjusted by E8 Delay in milliseconds.
With Loop = 1, Trigger is ignored.

You should set following option in the inverter settings for 'MODBUS' (at the webinterface):
TCP / 502 / String Control Offset 101 / Sunspec Model float / no Demo



Outputs:
Current is in A, Voltage in V, Power in W and Energy in Wh.

(Vendor) Operating State:
1  Off
2  Sleeping (auto-shutdown)
3  Starting up
4  Tracking power point
5  Forced power reduction
6  Shutting down
7  One or more faults exist
8  Standby (service on unit)*might be in Events
9  No SolarNet communication
10 No communication with inverter
11 Overcurrent on SolarNet plug detected
12 Inverter is being updated
13 AFCI Event

StateCode:
102	AC voltage too high
103	AC voltage too low
105	AC frequency too high
106	AC frequency too low
107	No AC grid detected / Wrong AC Grid State detected
108	Islanding detected
112	Residual Current Detected
240	Arc Detected
241	Arc detection confirmation 1
242	Arc detection confirmation 2
245	Arc Fault Cicruit Interrupter (AFCI) Selftest failed
247	Arc Fault Cicruit Interrupter (AFCI) current sensor error
249	Arc Fault Cicruit Interrupter (AFCI) detected unplausibel measurement values
301	Overcurrent AC
302	Overcurrent DC
303	Channel 1 Overtemperature
306	Power Low
307	DC Voltage Low
308	Intermediate circuit voltage too high
309	DC1 Input Overvoltage
313	DC2 Input Overvoltage
406	Error temperature sensor on DC board
407	Error temperature sensor on AC board
408	DC detected in grid
412	DC1 Fix Voltage Out Of Range
415	Wired Shutdown Triggered
416	RECERBO - Power Stack Communication Error
417	Hardware-ID Collision
425	Data Exchange Timeout
426	Intermediate Circuit Loading Timeout
427	Power Stack Ready Timeout
431	Power Stack In Bootmode
432	Consistent error in power stack management
433	Allocation error of dynamic addresses
436	Invalid Bitmap Received
437	Power Stack Event Handling Error
438	Problem while error transmission from power stack to Recerbo
445	Invalid Parameters detected
447	Isolation Error
448	No Neutral Wire
450	Guard Controller - Communication Error
451	Memory Check Error
452	Power Stack - Filter Communication Error
453	Guard Controller - AC voltage error
454	Guard Controller - AC Frequency Error
456	Guard Controller - Anti Islanding Selftest Error
457	Grid Relay Error
458	Residual Current Monitoring Unit (RCMU) Error
459	Guard Controller - Isolation Selftest Error
460	Power Stack / Filter Print Reference Voltage Error
461	RAM Error / Collective Fault
462	Guard Controller - DC injection into grid detected
463	AC Poles reversed
472	Ground-Fault Detector Interrupter Fuse Broken
474	Residual Current Sensor defect
475	Isolation Too Low Error
476	Power Stack Supply Missing
480	Incompatible feature
481	Not supported feature
482	Installation wizard aborted
483	DC fix voltage out of range
484	CAN transfer timeout
485	CAN transmit buffer full
502	Warning - Isolation Too Low
509	No Feed In For 24 Hours
516	Power Stack EEPROM Error
517	Power Derating Due To Overtemperature
519	Filter Board - EEPROM Error
520	MPP Tracker #1 - No Feed In For 24 Hours
521	MPP Tracker #2 - No Feed In For 24 Hours
522	DC1 Input Voltage To Low
523	DC2 Input Voltage Low
558	Incompatible Power Stack Software
559	Incompatible RECERBO Feature
560	Power Derating Due To High Grid Frequency
565	Arc Fault Cicruit Interrupter SD Card Error [US only]
566	Arc Fault Cicruit Interrupter Deactivated Warning [US only]
567	Grid Voltage Dependent Power Derating
701	Solar Net - Node Type Out Of Range
702	Solar Net - Receive Buffer Full
703	Solar Net - Send Buffer Full
705	Solar Net - Node Type Conflict
706	CapKey - Get Version Failed
707	CapKey - Update Failed
711	EEPROM Write - Wrong Data Length
712	EEPROM Write - Descriptor Not Found
713	EEPROM Read - Descriptor Not Found
714	EEPROM Read Warning - CRC Header Fail Will Retry
715	EEPROM Read - CRC Header Fail Give It Up
721	EEPROM - Reinitialized
722	EEPROM - Wait Busy Timeout
723	EEPROM Write - Verify Fail Will Retry
724	EEPROM Write - Verify Fail Give It Up
725	EEPROM Read - CRC Data Fail Will Retry
726	EEPROM Read - CRC Data Fail Give It Up
727	EEPROM Check Data Warning - All Data Corrupt
730	EEPROM Check Data - Data Restored
731	USB Flash Drive Initializing Error
732	USB Flash Drive Overcurrent
733	No USB Flash Drive Inserted
734	No Software Update Found On USB Flash Drive
735	No Supported Software Update Found On USB Flash Drive
736	USB Flash Drive Flash Drive Read/Write Error
737	Software Update Can Not Be Read From The USB Flash Drive
738	Log-File Can't Be Created On The USB Flash Drive
740	USB Flash Drive Enumeration Error
741	USB Flash Drive Logging Write Error
743	Software Update Failed
745	Software Update Checksum Wrong
746	Product Matrix Code (PMC) Read Error During Software Update
751	Real Time Clock - Time Lost
752	Real Time Clock - Hardware Error
754	Real Time Clock - Time Set
755	EEPROM Data Written
757	Real Time Clock - Hardware Error
758	Real Time Clock - Emergency Mode
760	System Crystal Broken
761	Device Data Unit Memory Read Error
762	Plugged Device Data Unit Read Error
765	Random Number Generator Error
766	Power Limit Not Found
767	Power Limiter Communication Error
768	Power Limit Not Identical
772	Memory Not Available
773	Update Group Zero
775	Power Stack Product Matrix Code Invalid
782	Update Flash - CRC Error
783	Update Flash - Header CRC Error
784	Update Flash - Timeout
789	Update Flash - Read Value (Header CRC Error)



Versions:
V0.10	2020-03-04	SirSydom		first release version
V0.01	2020-02-11	SirSydom		initial version

Open Issues:
- Improve Handling when Modbus fails
- Add a timestamp


Author:
SirSydom - com@sirsydom.de
Copyright (c) 2020 SirSydom


Github:
https://github.com/SirSydom/edomi_LBS_sirsydom


Links:
https://knx-user-forum.de/forum/projektforen/edomi/1477233-lbs19002100-froniusinverter-modbus


with credits to LBS 19001030 V0.6 by Michael Pattison



###[/HELP]###


###[LBS]###
<?
function LB_LBSID_logging($id, $msg, $var = NULL, $priority = 8)
{
  $E = getLogicEingangDataAll($id);
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
    strpos($_SERVER['SCRIPT_NAME'], $lbsNo) ? $scriptname = 'EXE' . $lbsNo : $scriptname = 'LBS' . $lbsNo;
    writeToCustomLog($logName, str_pad($logLevelNames[$logLevel], 7), $scriptname . " [v$version]:\t" . $msg);
    if (isset($var)) {
      writeToCustomLog($logName, str_pad($logLevelNames[$logLevel], 7), $scriptname . " [v$version]:\t================ ARRAY/OBJECT START ================");
      writeToCustomLog($logName, str_pad($logLevelNames[$logLevel], 7), $scriptname . " [v$version]:\t" . json_encode($var));
      writeToCustomLog($logName, str_pad($logLevelNames[$logLevel], 7), $scriptname . " [v$version]:\t================ ARRAY/OBJECT  END  ================");
    }
  }
}

function LB_LBSID($id)
{
	if ($E=logic_getInputs($id))
	{
		$loop=$E[7]['value'];
		$delay=$E[8]['value'];
		
		setLogicElementVar($id, 103, $E[6]['value']); // set loglevel to #VAR 103
		
		if($loop)	// if E7 is true, the LBS will be executed in an loop until E7 will be set to false. Trigger is ignored.
		{
			if(!logic_getState($id)) // LBS is not executed cylic
			{
				logic_setState($id,1,$delay,true);	// start cyclic execution
			}
			setLogicElementVar($id,1,1);                      //setzt V1=1, um einen mehrfachen Start des EXEC-Scripts zu verhindern
			callLogicFunctionExec(LBSID,$id);                 //EXEC-Script starten (garantiert nur einmalig)
			LB_LBSID_logging($id, 'LBS started (loop)');
		}
		else		// no loop
		{
			if(logic_getState($id)) // LBS is  executed cylic
			{
				logic_setState($id,0);	// stop cyclic execution
				LB_LBSID_logging($id, 'LBS stopped (loop)');
			}
			
			if($E[1]['value'] && $E[1]['refresh'])	// a new 'true' telegram arrived at E1, execute LBS once
			{
				setLogicElementVar($id,1,1);                      //setzt V1=1, um einen mehrfachen Start des EXEC-Scripts zu verhindern
				callLogicFunctionExec(LBSID,$id);                 //EXEC-Script starten (garantiert nur einmalig)
				LB_LBSID_logging($id, 'LBS started (trig)');
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
    strpos($_SERVER['SCRIPT_NAME'], $lbsNo) ? $scriptname = 'EXE' . $lbsNo : $scriptname = 'LBS' . $lbsNo;
    writeToCustomLog($logName, str_pad($logLevelNames[$logLevel], 7), $scriptname . " [v$version]:\t" . $msg);
    if (isset($var)) {
      writeToCustomLog($logName, str_pad($logLevelNames[$logLevel], 7), $scriptname . " [v$version]:\t================ ARRAY/OBJECT START ================");
      writeToCustomLog($logName, str_pad($logLevelNames[$logLevel], 7), $scriptname . " [v$version]:\t" . json_encode($var));
      writeToCustomLog($logName, str_pad($logLevelNames[$logLevel], 7), $scriptname . " [v$version]:\t================ ARRAY/OBJECT  END  ================");
    }
  }
}

function ExtractFloatData($array, $offset)
{
	$ret = (($array[$offset+3] & 0xFF)) |
	(($array[$offset+2] & 0xFF)<<8) |
	(($array[$offset+1] & 0xFF))<<16 |
	(($array[$offset+0] & 0xFF)<<24);

	$ulong = pack('L*', $ret);
	$float = unpack('f*', $ulong);
	$ret = $float[1];
	return $ret;
}

function ExtractUInt16Data($array, $offset)
{
	$temp_array[0] = $array[$offset+0];
	$temp_array[1] = $array[$offset+1];
	$value = PhpType::bytes2unsignedInt($temp_array, 0);
	
	if($value == 0xFFFF)
		return null;
	else
		return $value;
}

function ExtractInt16Data($array, $offset)
{
	$temp_array[0] = $array[$offset+0];
	$temp_array[1] = $array[$offset+1];
	
	if($temp_array[0] == 0xFF && $temp_array[1] == 0xFF)
		return null;
	else
		return PhpType::bytes2signedInt($temp_array, 0);
}

function ExtractUInt32Data($array, $offset)
{
	$temp_array[0] = $array[$offset+0];
	$temp_array[1] = $array[$offset+1];
	$temp_array[2] = $array[$offset+2];
	$temp_array[3] = $array[$offset+3];
	
	if($temp_array[0] == 0xFF && $temp_array[1] == 0xFF && $temp_array[2] == 0xFF && $temp_array[3] == 0xFF)
		return null;
	else
		return PhpType::bytes2unsignedInt($temp_array, 0);
}

function ExtractAcc32Data($array, $offset)
{
	$temp_array[0] = $array[$offset+2];
	$temp_array[1] = $array[$offset+3];
	$temp_array[2] = $array[$offset+0];
	$temp_array[3] = $array[$offset+1];
	
	if($temp_array[0] == 0xFF && $temp_array[1] == 0xFF && $temp_array[2] == 0xFF && $temp_array[3] == 0xFF)
		return null;
	else
		return PhpType::bytes2signedInt($temp_array, 0);
}

function ExtractUInt64Data($array, $offset)
{
	if(	$array[$offset+0] == 0xFF && $array[$offset+1] == 0xFF && $array[$offset+2] == 0xFF && $array[$offset+3] == 0xFF &&
		$array[$offset+4] == 0xFF && $array[$offset+5] == 0xFF && $array[$offset+6] == 0xFF && $array[$offset+7] == 0xFF)
		return null;
	
	$value =
	(($array[$offset+0] & 0xFF)<<56) |
	(($array[$offset+1] & 0xFF)<<48) |
	(($array[$offset+2] & 0xFF)<<40) |
	(($array[$offset+3] & 0xFF)<<32) |
	(($array[$offset+4] & 0xFF)<<24) |
	(($array[$offset+5] & 0xFF)<<16) |
	(($array[$offset+6] & 0xFF)<<8) |
	(($array[$offset+7] & 0xFF));
	return $value;
}




date_default_timezone_set('Europe/Berlin');

error_off();


if($E=getLogicEingangDataAll($id))
{
	$retries=0;

	// Create Modbus object
	$modbus = new ModbusMaster($E[2]['value'],"TCP");

	try
	{
		set_error_handler("myErrorHandlerModBus");
		$suppressModBusError='0';
		$recData = $modbus->readMultipleRegisters($E[5]['value'], 40069, 62);	//  SunSpec Inverter Float Modbus Map
		restore_error_handler();
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
		}
		else
		{
			setLogicLinkAusgang($id,40,1); //send error to A40
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
		setLogicLinkAusgang($id,40,0);
		logging($id,"Raw Data:",$recData);
		
		// AC Total Current value 40072
		$ret = ExtractFloatData($recData, (40072-40070)*2);
		logging($id,"Data:" . $ret);
		if(is_numeric($ret) && !is_nan($ret))
			setLogicLinkAusgang($id,1,round($ret,1));
		
		// AC Phase-A Current value 40074
		$ret = ExtractFloatData($recData, (40074-40070)*2);
		logging($id,"Data:" . $ret);
		if(is_numeric($ret) && !is_nan($ret))
			setLogicLinkAusgang($id,2,round($ret,3));
		
		// AC Phase-B Current value 40076
		$ret = ExtractFloatData($recData, (40076-40070)*2);
		logging($id,"Data:" . $ret);
		if(is_numeric($ret) && !is_nan($ret))
			setLogicLinkAusgang($id,3,round($ret,3));
		
		// AC Phase-C Current value 40078
		$ret = ExtractFloatData($recData, (40078-40070)*2);
		logging($id,"Data:" . $ret);
		if(is_numeric($ret) && !is_nan($ret))
			setLogicLinkAusgang($id,4,round($ret,3));
		
		// AC Voltage Phase-AB value 40080
		$ret = ExtractFloatData($recData, (40080-40070)*2);
		logging($id,"Data:" . $ret);
		if(is_numeric($ret) && !is_nan($ret))
			setLogicLinkAusgang($id,5,round($ret,2));
		
		// AC Voltage Phase-BC value 40082
		$ret = ExtractFloatData($recData, (40082-40070)*2);
		logging($id,"Data:" . $ret);
		if(is_numeric($ret) && !is_nan($ret))
			setLogicLinkAusgang($id,6,round($ret,2));
		
		// AC Voltage Phase-CA value 40084
		$ret = ExtractFloatData($recData, (40084-40070)*2);
		logging($id,"Data:" . $ret);
		if(is_numeric($ret) && !is_nan($ret))
			setLogicLinkAusgang($id,7,round($ret,2));
		
		// L1 Voltage 40086
		$ret = ExtractFloatData($recData, (40086-40070)*2);
		logging($id,"Data:" . $ret);
		if(is_numeric($ret) && !is_nan($ret))
			setLogicLinkAusgang($id,8,round($ret,2));
		
		// L2 Voltage 40088
		$ret = ExtractFloatData($recData, (40088-40070)*2);
		logging($id,"Data:" . $ret);
		if(is_numeric($ret) && !is_nan($ret))
			setLogicLinkAusgang($id,9,round($ret,2));
		
		// L3 Voltage 40090
		$ret = ExtractFloatData($recData, (40090-40070)*2);
		logging($id,"Data:" . $ret);
		if(is_numeric($ret) && !is_nan($ret))
			setLogicLinkAusgang($id,10,round($ret,2));
		
		// AC Power value 40092
		$ret = ExtractFloatData($recData, (40092-40070)*2);
		logging($id,"Data:" . $ret);
		if(is_numeric($ret) && !is_nan($ret))
			setLogicLinkAusgang($id,11,round($ret,1));
		
		// AC Frequency value 40094
		$ret = ExtractFloatData($recData, (40094-40070)*2);
		logging($id,"Data:" . $ret);
		if(is_numeric($ret) && !is_nan($ret))
			setLogicLinkAusgang($id,12,round($ret,3));
		
		// Apparent Power 40096
		$ret = ExtractFloatData($recData, (40096-40070)*2);
		logging($id,"Data:" . $ret);
		if(is_numeric($ret) && !is_nan($ret))
			setLogicLinkAusgang($id,13,round($ret,1));
		
		// Reactive Power 40098
		$ret = ExtractFloatData($recData, (40098-40070)*2);
		logging($id,"Data:" . $ret);
		if(is_numeric($ret) && !is_nan($ret))
			setLogicLinkAusgang($id,14,round($ret,1));
		
		// Power Factor 40100
		$ret = ExtractFloatData($recData, (40100-40070)*2);
		logging($id,"Data:" . $ret);
		if(is_numeric($ret) && !is_nan($ret))
			setLogicLinkAusgang($id,15,round($ret,3));
		
		// AC Lifetime Energy production 40102
		$ret = ExtractFloatData($recData, (40102-40070)*2);
		logging($id,"Data:" . $ret);
		if(is_numeric($ret) && !is_nan($ret))
			setLogicLinkAusgang($id,16,round($ret,0));
		
		// DC Current value 400104
		$ret = ExtractFloatData($recData, (40104-40070)*2);
		logging($id,"Data:" . $ret);
		if(is_numeric($ret) && !is_nan($ret))
			setLogicLinkAusgang($id,19,round($ret,3));
		
		// DC Voltage value 40106
		$ret = ExtractFloatData($recData, (40106-40070)*2);
		logging($id,"Data:" . $ret);
		if(is_numeric($ret) && !is_nan($ret))
			setLogicLinkAusgang($id,20,round($ret,1));
		
		// DC Power value 40108
		$ret = ExtractFloatData($recData, (40108-40070)*2);
		logging($id,"Data:" . $ret);
		if(is_numeric($ret) && !is_nan($ret))
			setLogicLinkAusgang($id,21,round($ret,1));
		
		// Operating State 40118
		//$ret = ExtractUInt16Data($recData, (40118-40070)*2);
		//logging($id,"Data:" . $ret);
		//setLogicLinkAusgang($id,$i++,$ret);
		
		// Vendor Defined Operating State 40119
		$ret = ExtractUInt16Data($recData, (40119-40070)*2);
		logging($id,"Data:" . $ret);
		if(is_numeric($ret) && !is_nan($ret))
			setLogicLinkAusgang($id,22,$ret);
		
		
	}
	
	try
	{
		set_error_handler("myErrorHandlerModBus");
		$suppressModBusError='0';
		$recData = $modbus->readMultipleRegisters($E[5]['value'], 213, 1);	//  SunSpec 
		restore_error_handler();
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
		}
		else
		{
			setLogicLinkAusgang($id,40,1); //send error to A40
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
		setLogicLinkAusgang($id,40,0);
		logging($id,"Raw Data:",$recData);
		
		// Current active state code of inverter 214
		$statecode = ExtractUInt16Data($recData, 0);
		logging($id,"State Code:" . $statecode);
		if(is_numeric($statecode) && !is_nan($statecode))
			setLogicLinkAusgang($id,23, $statecode);
	}
	
	
	
	try
	{
		set_error_handler("myErrorHandlerModBus");
		$suppressModBusError='0';
		$recData = $modbus->readMultipleRegisters($E[5]['value'], 501, 12);	//  SunSpec 
		restore_error_handler();
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
		}
		else
		{
			setLogicLinkAusgang($id,40,1); //send error to A40
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
		setLogicLinkAusgang($id,40,0);
		logging($id,"Raw Data:",$recData);
		
		// Total energy for current day of all connected inverters. 502
		$day_wh = ExtractUInt64Data($recData, 0);
		logging($id,"Data:" . $day_wh);
		if(is_numeric($day_wh) && !is_nan($day_wh))		
			setLogicLinkAusgang($id,17, $day_wh);
		
		// Total energy for last year of all connected inverters. 506
		$year_wh = ExtractUInt64Data($recData, 8);
		logging($id,"Data:" . $year_wh);	
		if(is_numeric($year_wh) && !is_nan($year_wh))			
			setLogicLinkAusgang($id,18, $year_wh);
		
		//Total energy of all connected inverters. 510
		$total_wh = ExtractUInt64Data($recData, 16);
		logging($id,"Data:" . $total_wh);
		//if(is_numeric($total_wh) && !is_nan($total_wh))			
		//	setLogicLinkAusgang($id,???, $total_wh);
	}
	
	
	
	try
	{
		set_error_handler("myErrorHandlerModBus");
		$suppressModBusError='0';
		$recData = $modbus->readMultipleRegisters($E[5]['value'], 40263, 50);	//  SunSpec Multiple MPPT Inverter Extension Model Mode
		restore_error_handler();
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
		}
		else
		{
			setLogicLinkAusgang($id,40,1); //send error to A40
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
		setLogicLinkAusgang($id,40,0);
		logging($id,"Raw Data:",$recData);
		
		// Current Scale Factor 40266
		$dca_sf = ExtractInt16Data($recData, (40266-40264)*2);
		logging($id,"Data:" . $dca_sf);
		
		// Voltage Scale Factor 40267
		$dcv_sf = ExtractInt16Data($recData, (40267-40264)*2);
		logging($id,"Data:" . $dcv_sf);
		
		// Power Scale Factor 40268
		$dcw_sf = ExtractInt16Data($recData, (40268-40264)*2);
		logging($id,"Data:" . $dcw_sf);
		
		// Energy Scale Factor 40269
		$dcwh_sf = ExtractInt16Data($recData, (40269-40264)*2);
		logging($id,"Data:" . $dcwh_sf);
		
		
		// DC Current 40283
		$dca_1 = ExtractUInt16Data($recData, (40283-40264)*2);
		logging($id,"Data:" . $dca_1);
		
		// DC Voltage 40284
		$dcv_1 = ExtractUInt16Data($recData, (40284-40264)*2);
		logging($id,"Data:" . $dcv_1);
		
		// DC Power 40285
		$dcw_1 = ExtractUInt16Data($recData, (40285-40264)*2);
		logging($id,"Data:" . $dcw_1);
		
		// DC Energy 40286
		$dcwh_1 = ExtractAcc32Data($recData, (40286-40264)*2);
		logging($id,"Data:" . $dcwh_1);
		
		// Operating State 40291
		$dcst_1 = ExtractUInt16Data($recData, (40291-40264)*2);
		logging($id,"Data:" . $dcst_1);
		
		// Module Events 40292
		$dcevt_1 = ExtractUInt32Data($recData, (40292-40264)*2);
		logging($id,"Data:" . $dcevt_1);
		
		
		// DC Current 40303
		$dca_2 = ExtractUInt16Data($recData, (40303-40264)*2);
		logging($id,"Data:" . $dca_2);
		
		// DC Voltage 40304
		$dcv_2 = ExtractUInt16Data($recData, (40304-40264)*2);
		logging($id,"DC Voltage 40304:" . $dcv_2);
		
		// DC Power 40305
		$dcw_2 = ExtractUInt16Data($recData, (40305-40264)*2);
		logging($id,"DC Power 40305:" . $dcw_2);
		
		// DC Energy 40306
		$dcwh_2 = ExtractAcc32Data($recData, (40306-40264)*2);
		logging($id,"DC Energy 40306:" . $dcwh_2);
		
		// Operating State 40311
		$dcst_2 = ExtractUInt16Data($recData, (40311-40264)*2);
		logging($id,"Operating State 40311:" . $dcst_1);
		
		// Module Events 40312
		$dcevt_2 = ExtractUInt32Data($recData, (40312-40264)*2);
		logging($id,"Module Events 40312:" . $dcevt_1);
		
		if(is_numeric($dca_1) && is_numeric($dca_sf))
			setLogicLinkAusgang($id,24, $dca_1 *  pow(10,$dca_sf));
		if(is_numeric($dcv_1) && is_numeric($dcv_sf))
			setLogicLinkAusgang($id,25, $dcv_1 *  pow(10,$dcv_sf));
		if(is_numeric($dcw_1) && is_numeric($dcw_sf))
			setLogicLinkAusgang($id,26, $dcw_1 *  pow(10,$dcw_sf));
		if(is_numeric($dcwh_1) && is_numeric($dcwh_sf))
			setLogicLinkAusgang($id,27, $dcwh_1 *  pow(10,$dcwh_sf));
		if(is_numeric($dcst_1))
			setLogicLinkAusgang($id,28, $dcst_1);
		
		if(is_numeric($dca_2) && is_numeric($dca_sf))
			setLogicLinkAusgang($id,29, $dca_2 *  pow(10,$dca_sf));
		if(is_numeric($dcv_2) && is_numeric($dcv_sf))
			setLogicLinkAusgang($id,30, $dcv_2 *  pow(10,$dcv_sf));
		if(is_numeric($dcw_2) && is_numeric($dca_sf))
			setLogicLinkAusgang($id,31, $dcw_2 *  pow(10,$dcw_sf));
		if(is_numeric($dcwh_2) && is_numeric($dcw_sf))
			setLogicLinkAusgang($id,32, $dcwh_2 *  pow(10,$dcwh_sf));
		if(is_numeric($dcst_2))
			setLogicLinkAusgang($id,33, $dcst_2);
	}
	unset($modbus);
}

setLogicElementVar($id,1,0); //allow to restart
//logging($id,"Memory at End: " . (string)memory_get_usage());
logging($id, "STOP ModbusMaster EXEC",null,6);
error_on();
sql_disconnect();
?>
###[/EXEC]###