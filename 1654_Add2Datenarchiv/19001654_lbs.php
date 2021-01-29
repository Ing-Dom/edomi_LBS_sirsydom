###[DEF]###
[name			= Add2Datenarchiv LBS1654 V0.21		]

[e#1	important	= Data								]
[e#2	important	= Timestamp							]
[e#3	important	= Microseconds		#init=0			]
[e#4	important	= Datenarchiv ID					]
[e#5			= Timeperiod						]
[e#6			= Timeperiod Mode					]




[a#1			= Error								]
[a#10			= Debug								]



###[/DEF]###

###[HELP]###
Inputs:
E1 - Data:					Data to save
E2 - Timestamp:				The Timestamp in Seconds since epoch (UNIX/POSIX Timestamp) with optional fraction after a decimal point
E3 - Microseconds:			Microseconds of the Second (0-999999), only used if E2 has no fraction
E4 - Datenarchiv ID:		The ID of the edomi Datenarchiv
E5 - PointOfTime:			Alternative to E2 - select the database enry timestamp for the data (E1) based on current time
E6 - PointOfTime Mode:		Modifier for E5



Outputs:
A1 - Error:					reserved
A10 - Debug:				The SQL-Query used


This LBS inserts data into a Datenarchiv with the given timestamp or a selected PointOfTime.

Possible Options for E5:
1: Last Minute
2: Last Hour
3: Yesterday
4: Last Month
5: Last Year

Possible Options for E6:
1: Start of period (e.q. Start of Minute 34:00 , Start of Hour 02:00:00 , Start of Day 25.02.2021 00:00:00, ...)
2: Middle of period (e.q. Middle of Minute 34:30 , Middle of Hour 02:30:00 , Middle of Day 25.02.2021 12:00:00, ...)
3: End of period (e.q. End of Minute 34:59 , End of Hour 02:59:00 , End of Day 25.02.2021 23:59:00, ...)


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
			
			if($timestamp == null) // Funktioniert das wirklich? 
			{
				// use E5/E6
				
				// Last Minute(1):
				if($E[5]['value'] == 1 && $E[6]['value'] == 1)
				{
					$datetime = new DateTime("now");
					$seconds = $datetime->format('s');
					$datetime->modify('-' . $seconds . ' seconds');
					$datetime->modify("-1 minute");
				}

				else if($E[5]['value'] == 1 && $E[6]['value'] == 2)
				{
					$datetime = new DateTime("now",new DateTimeZone('Europe/Zurich')); // Zeitzone nur zum Test verifizieren.
					$seconds = $datetime->format('s');
					$datetime->modify('-' . $seconds . ' seconds');
					$datetime->modify("-1 minute");
					$datetime->modify("+30 seconds");
				}

				else if($E[5]['value'] == 1 && $E[6]['value'] == 3)
				{
					$datetime = new DateTime("now",new DateTimeZone('Europe/Zurich')); // Zeitzone nur zum Test verifizieren.
					$seconds = $datetime->format('s');
					$datetime->modify('-' . $seconds . ' seconds');
					$datetime->modify("-1 minute");
					$datetime->modify("+59 seconds");
				}

				// Last Hour (2):
				else if($E[5]['value'] == 2 && $E[6]['value'] == 1)
				{
					$datetime = new DateTime("now",new DateTimeZone('Europe/Zurich')); // Zeitzone nur zum Test verifizieren.
					$datetime->setTime($datetime->format('G'), 0); 
					$datetime->modify("-1 hour");
				}

				else if($E[5]['value'] == 2 && $E[6]['value'] == 2)
				{
					$datetime = new DateTime("now",new DateTimeZone('Europe/Zurich')); // Zeitzone nur zum Test verifizieren.
					$datetime->setTime($datetime->format('G'), 30); 
					$datetime->modify("-1 hour");
				}

				else if($E[5]['value'] == 2 && $E[6]['value'] == 3)
				{
					$datetime = new DateTime("now",new DateTimeZone('Europe/Zurich')); // Zeitzone nur zum Test verifizieren.
					$datetime->setTime($datetime->format('G'), 59); 
					$datetime->modify("-1 hour");
					// echo $datetime->format('Y-m-d H:i:s') . "\n";
				}

				// Yesterday(3):
				else if($E[5]['value'] == 3 && $E[6]['value'] == 1)
				{
					$datetime = new DateTime("yesterday 00:00:00");
				}

				else if($E[5]['value'] == 3 && $E[6]['value'] == 2)
				{
					$datetime = new DateTime("yesterday 12:00:00");
				}

				else if($E[5]['value'] == 3 && $E[6]['value'] == 3)
				{
					$datetime = new DateTime("yesterday 23:59:00");
				}

				// Last Month (4):
				else if($E[5]['value'] == 4 && $E[6]['value'] == 1)
				{
					$datetime = new DateTime("first day of last month 00:00:00");
				}

				else if($E[5]['value'] == 4 && $E[6]['value'] == 2)
				{
					$datetime = new DateTime("first day of last month 12:00:00");
					$datetime->modify("+14 day");
				}

				else if($E[5]['value'] == 4 && $E[6]['value'] == 3)
				{
					$datetime = new DateTime("last day of last month 23:59:00");
				}

				// Last Year (5):
				else if($E[5]['value'] == 5 && $E[6]['value'] == 1)
				{
					$datetime = new DateTime("first day of last year 00:00:00");
				}

				else if($E[5]['value'] == 5 && $E[6]['value'] == 2)
				{
					$datetime = new DateTime("first day of last year 12:00:00");
					$datetime->modify("+14 day");
					$datetime->modify("+5 month");
				}

				else if($E[5]['value'] == 5 && $E[6]['value'] == 3)
				{
					$datetime = new DateTime("first day of last year 23:59:00");
					$datetime->modify("+30 day");
					$datetime->modify("+11 month");
				}
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