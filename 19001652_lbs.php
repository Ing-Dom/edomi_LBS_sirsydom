###[DEF]###
[name				= Counter2Changerate					]

[e#1	important	= Countervalue							]
[e#2	important	= Scale				#init=1				]
[e#3	important	= Min Intervall [s] #init=10			]
[e#4	important	= Saved Countervalue					]
[e#5	important	= Saved	Timestamp						]

[e#10				= Loglevel 			#init=3				]



[a#1				= Changerate							]
[a#2				= Countervalue							]
[a#3				= Timestamp								]


[v#100				= 0.10 ]
[v#101 				= 19001652 ]
[v#102 				= Counter2Changerate ]
[v#103 				= 8 ]

###[/DEF]###

###[HELP]###


Inputs:
E1 - Countervalue:			Connect this to a accumulating counter
E2 - Scale:					Factor for the Changerate
E3 - Min Intervall:			Minimal Intervall in seconds for calculating a rate
E4 - Saved Countervalue:	Input for the previous Countervalue
E5 - Saved Timestamp:		Input for the Timestamp to E4

Outputs:
A1 - Changerate:			Calculated Changerate
A2 - Countervalue:			Output for the previous Countervalue
A3 - Timestamp:				Output for the Timestamp to A2

Versions:
V0.10	2019-09-04	SirSydom

Open Issues:


Author:
SirSydom - com@sirsydom.de
Copyright (c) 2019 SirSydom

Github:
https://github.com/SirSydom/edomi_LBS_sirsydom

Links:
https://knx-user-forum.de/forum/projektforen/edomi/1400676-lbs-dev-aus-pulsen-z%C3%A4hlerst%C3%A4nden-in-%C3%A4nderungsrate-berechnen-s0-stromz%C3%A4hler


Contributions:



###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id)
{
	logging($id, "LBS called", null, 8);
	if($E=getLogicEingangDataAll($id))
	{
		logging($id, "getLogicEingangDataAll true", null, 8);
		setLogicElementVar($id, 103, $E[10]['value']); //set loglevel to #VAR 103
		
		$min_intervall = 0;
		if(is_numeric($E[3]['value']))
		{
			$min_intervall = $E[3]['value'];
		}
		else
		{
			$min_intervall = 10;
			logging($id, "Config Fault: E3 is invalid / not numeric. Using default value 10s", null, 3);
		}
		
		if($E[1]['value'] != NULL && $E[1]['refresh'] == 1)	// ON received
		{
			logging($id, "refresh 1 value != NULL", null, 8);
			// new value
			$old_counter = $E[4]['value'];
			$old_time = $E[5]['value'];
			
			$new_counter = $E[1]['value'];
			$result = gettimeofday();
			$new_time = $result['sec']+$result['usec']/1000000.0;
			
			if($old_counter > 0)
			{
				logging($id, "old > 0 ($old_counter)", null, 8);
				if($new_time - $old_time >= 10 && $new_counter - $old_counter > 0)
				{
					logging($id, "$new_time - $old_time >= 10", null, 8);
					$changerate = ($new_counter - $old_counter) / ($new_time - $old_time);
					logic_setOutput($id,1,$changerate * $E[2]['value']);
					
					logic_setOutput($id,2,$new_counter);
					logic_setOutput($id,3,$new_time);
				}
			}
			else
			{
				logging($id, "old <= 0 ($old_counter)", null, 8);
				logic_setOutput($id,2,$new_counter);
				logic_setOutput($id,3,$new_time);
			}
			

		}
	}
}



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
###[/LBS]###


###[EXEC]###
<?




?>
###[/EXEC]###