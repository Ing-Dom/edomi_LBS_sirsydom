###[DEF]###
[name				= Wegfahrautomatik 0.1					]

[e#1	important	= OnOff									]
[e#2	important	= Trigger_Open							]
[e#3	important	= Wait_Open [s]							]
[e#4	important	= Trigger_Close							]

[e#10				= Loglevel 			#init=8				]



[a#1				= state									]
[a#2				= OpenDoors								]
[a#3				= CloseDoors							]


[v#100				= 0.01 ]
[v#101 				= 19001651 ]
[v#102 				= Wegfahrautomatik ]
[v#103 				= 0 ]

###[/DEF]###

###[HELP]###
Dieser LBS öffnet und schließt Tore

Inputs:
E1 - abc:		bla

Outputs:
A1 - abc:	bla

Versions:
V0.01	2019-04-12	SirSydom

Open Issues:
Timeout

Author:
SirSydom - com@sirsydom.de
Copyright (c) 2019 SirSydom

Github:
https://github.com/SirSydom/edomi_LBS_sirsydom

Links:



Contributions:



###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id)
{
	if($E=getLogicEingangDataAll($id))
	{
		setLogicElementVar($id, 103, $E[10]['value']); //set loglevel to #VAR 103
		
		if($E[1]['value'] == 1 && $E[1]['refresh'] == 1)	// ON received
		{
			if(getLogicElementVar($id,1)!=1) // not running => Start
			{
				setLogicElementVar($id,1,1);                      //setzt V1=1, um einen mehrfachen Start des EXEC-Scripts zu verhindern
				callLogicFunctionExec(LBSID,$id);                 //EXEC-Script starten (garantiert nur einmalig)
			}
		}
		
		



		
	}
}



?>
###[/LBS]###


###[EXEC]###
<?
require(dirname(__FILE__)."/../../../../main/include/php/incl_lbsexec.php");
set_time_limit(0);                                       //Wichtig! Script soll endlos laufen
sql_connect();
logging($id, "EXEC started", null, 5);
$E = logic_getInputs($id);

$mystate = "initial";
logic_setOutput($id,1,$mystate);
$timer = 0;
$run = 1;


while (getSysInfo(1)>=1 && $run)
{
	logging($id, "main while cycle start", null, 8);
	$E = logic_getInputs($id);
	
	if($mystate = "initial")
	{
		if($E[2]['value'] == 1)
		{
			$mystate = "countdown_to_open";
			logic_setOutput($id,1,$mystate);
			$timer = time();
		}
		else
		{
			usleep(500000);
		}
	}
	else if($mystate = "countdown_to_open")
	{
		if($timer > time() + $E[3]['value'])
		{
			$mystate = "wait_to_close";
			logic_setOutput($id,1,$mystate);
			
			logic_setOutput($id,2,1);	// open the doors with "1" on the output
		}
		else
		{
			usleep(500000);
		}
			
	}
	else if($mystate = "wait_to_close")
	{
		if($E[4]['value'] == 1)
		{
			$mystate = "wait_for_closing";
			logic_setOutput($id,1,$mystate);
			
			logic_setOutput($id,3,1);	// close the doors with "1" on the output
		}
		else
		{
			usleep(500000);
		}
			
	}
	else if($mystate = "wait_for_closing")
	{
		//do stuff here to supervice door closing
		$run = 0; // exit EXEC
	}
	logging($id, "main while cycle end", null, 8);
}
logging($id, "Cycle exit", null, 8);
sql_disconnect();
logging($id, "EXEC exit", null, 5);
setLogicElementVar($id,1,0);                      //setzt V1=0, um einen Start des EXEC-Scripts zu erlauben

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


?>
###[/EXEC]###