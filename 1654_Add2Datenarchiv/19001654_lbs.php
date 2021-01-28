###[DEF]###
[name				= Add2Datenarchiv LBS1654 V0.20		]

[e#1	important	= Data								]
[e#2	important	= Timestamp							]
[e#3	important	= Microseconds		#init=0			]
[e#4	important	= Datenarchiv ID					]
[e#5				= Timeperiod						]
[e#6				= Timeperiod Mode					]




[a#1				= Error								]
[a#10				= Debug								]



###[/DEF]###

###[HELP]###
Inputs:
E1 - Data:					Data to save
E2 - Timestamp:				The Timestamp in Seconds since epoch (UNIX/POSIX Timestamp) with optional fraction after a decimal point
E3 - Microseconds:			Microseconds of the Second (0-999999), only used if E2 has no fraction
E4 - Datenarchiv ID:		The ID of the edomi Datenarchiv
E5 - PointOfTime:			Alternative to E2 - select the time for the data based on current time
E6 - PointOfTime Mode:		Modifier for E5



Outputs:
A1 - Error					reserved
A10 - Debug:				The SQL-Query used


This LBS inserts data into a Datenarchiv with the given timestamp.
Tested with edomi 1.64 only. Use with caution!

Versions:
V0.10	2019-09-20	SirSydom
V0.20	2021-01-28	SirSydom	added E5/6, used sql_call

Open Issues:
- A1


Author:
SirSydom - com@sirsydom.de
Copyright (c) 2021 SirSydom

Github:
https://github.com/SirSydom/edomi_LBS_sirsydom/releases/tag/19001654_V0.20

Links:



Contributions:



###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id)
{
	if ($E=logic_getInputs($id))
	{
		if ($E[1]['refresh'] == 1 && $E[1]['value'] != null)
		{
			$archivDb='archivKoData';
			$archivId=$E[4]['value'];
			$value = $E[1]['value'];
			$timestamp = $E[2]['value'];
			
			$time_arr = explode(".", $timestamp);
			
			
			if(count($time_arr) > 1)
			{
				$datetime = $time_arr[0];
				
				$digits = strlen($time_arr[1]);
				$ms = $time_arr[1] * pow(10,(6 - $digits));
			}
			else
			{
				$datetime = $timestamp;
				$ms = $E[3]['value'];

			}
			
			if($timestamp == null)
			{
				// use E5/E6
				
				if($E[5]['value'] == 1 && $E[5]['value'] == 1)
				{
					//Last Minute:
					$t = new DateTime("now");
					$seconds = $t->format('s');
					$t->modify('-' . $seconds . ' seconds');
					$t->modify("-1 minute");
					$datetime = getTimestamp();
				}

				// case 12:
				// $t = new DateTime("now",new DateTimeZone('Europe/Zurich')); // Zeitzone nur zum Test verifizieren.
				// $seconds = $t->format('s');
				// $t->modify('-' . $seconds . ' seconds');
				// $t->modify("-1 minute");
				// $t->modify("+30 seconds");

				// case 13:
				// $t = new DateTime("now",new DateTimeZone('Europe/Zurich')); // Zeitzone nur zum Test verifizieren.
				// $seconds = $t->format('s');
				// $t->modify('-' . $seconds . ' seconds');
				// $t->modify("-1 minute");
				// $t->modify("+59 seconds");

				// //Last Hour:
				// case 21:
				// $t = new DateTime("now",new DateTimeZone('Europe/Zurich')); // Zeitzone nur zum Test verifizieren.
				// $t->setTime($t->format('G'), 0); 
				// $t->modify("-1 hour");

				// case 22:
				// $t = new DateTime("now",new DateTimeZone('Europe/Zurich'));
				// $t->setTime($t->format('G'), 30); 
				// $t->modify("-1 hour");

				// case 23:
				// $t = new DateTime("now",new DateTimeZone('Europe/Zurich'));
				// $t->setTime($t->format('G'), 59); 
				// $t->modify("-1 hour");
				// echo $t->format('Y-m-d H:i:s') . "\n";

				else if($E[5]['value'] == 3 && $E[5]['value'] == 1)
				{
					//Yesterday:
					$t = new DateTime("yesterday 00:00:00");
					$datetime = getTimestamp();
				}

				// case 32:
				// $t = new DateTime("yesterday 12:00:00");

				// case 33:
				// $t = new DateTime("yesterday 23:59:00");


				// //Last Month:
				// case 41:
				// $t = new DateTime("first day of last month 00:00:00");

				// case 42:
				// $t = new DateTime("first day of last month 12:00:00");
				// $t->modify("+14 day");

				// case 43:
				// $t = new DateTime("last day of last month 23:59:00");


				// //Last Year:
				// case 51:
				// $t = new DateTime("first day of last year 00:00:00");

				// case 52:
				// $t = new DateTime("first day of last year 12:00:00");
				// $t->modify("+14 day");
				// $t->modify("+5 month");

				// case 53:
				// $t = new DateTime("first day of last year 23:59:00");
				// $t->modify("+30 day");
				// $t->modify("+11 month");
			}
			
			
			$query = "INSERT INTO `archivKoData` (`datetime`, `ms`, `targetid`, `gavalue`) VALUES (FROM_UNIXTIME(" . $datetime . "), '" . $ms ."', '" . strVal($archivId) . "', '" . $value . "')";


			
			$result = sql_call($con);
			
			
			logic_setOutput($id,10,$query);
		}
	}
}


?>
###[/LBS]###


###[EXEC]###
<?




?>
###[/EXEC]###