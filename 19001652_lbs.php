###[DEF]###
[name				= Counter2Changerate					]

[e#1	important	= OnOff									]
[e#2	important	= X										]
[e#3	important	= Y										]
[e#4	important	= Z										]

[e#10				= Loglevel 			#init=8				]



[a#1				= Changerate							]
[a#2				= A										]
[a#3				= B										]


[v#100				= 0.01 ]
[v#101 				= 19001652 ]
[v#102 				= Counter2Changerate ]
[v#103 				= 0 ]
[v#104 				= -1 ]
[v#105 				= 0 ]

###[/DEF]###

###[HELP]###


Inputs:
E1 - abc:		bla

Outputs:
A1 - abc:	bla

Versions:
V0.01	2019-09-03	SirSydom

Open Issues:


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
			if($E[1]['refresh'] == 1)
			{
				// new value
				$old_counter = getLogicElementVar($id,104);
				$old_time = getLogicElementVar($id,105);
				if($old_counter >= 0)
				{
					
				}
			}
		}
		
		



		
	}
	
	
	/*
	getLogicElementVar($id,1)
	setLogicElementVar($id,1,1)
	logic_setOutput($id,1,$mystate);
	logging($id, "main while cycle end", null, 8);
	*/
}



?>
###[/LBS]###


###[EXEC]###
<?

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