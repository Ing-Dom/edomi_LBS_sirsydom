###[DEF]###
[name				= Add2Datenarchiv					]

[e#1	important	= Data								]
[e#2	important	= Timestamp							]
[e#3	important	= Microseconds		#init=0			]
[e#4	important	= Datenarchiv ID					]
	




[a#1				= Error								]
[a#10				= Debug								]



###[/DEF]###

###[HELP]###
Inputs:
E1 - Data:					Data to save
E2 - Timestamp:				The Timestamp in Seconds since epoch (UNIX/POSIX Timestamp) with optional fraction after a decimal point
E3 - Microseconds:			Microseconds of the Second (0-999999), only used if E2 has no fraction
E4 - Datenarchiv ID:		The ID of the edomi Datenarchiv



Outputs:
A1 - Error					reserved
A10 - Debug:				The SQL-Query used


This LBS inserts data into a Datenarchiv with the given timestamp.
Tested with edomi 1.64 only. Use with caution!

Versions:
V0.10	2019-09-20	SirSydom

Open Issues:
- A1


Author:
SirSydom - com@sirsydom.de
Copyright (c) 2019 SirSydom

Github:
https://github.com/SirSydom/edomi_LBS_sirsydom/releases/tag/19001654_V0.10

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
			
			$query = "INSERT INTO `archivKoData` (`datetime`, `ms`, `targetid`, `gavalue`) VALUES (FROM_UNIXTIME(" . $datetime . "), '" . $ms ."', '" . strVal($archivId) . "', '" . $value . "')";


			$con = mysql_connect("localhost","root","");
			mysql_select_db("edomiLive", $con);
			$result = mysql_query($query, $con);
			
			
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