###[DEF]###
[name				= DPT19DateTime							]

[e#1	important	= DPT19 DateTime						]


[e#10				= Loglevel 			#init=3				]



[a#1				= A1							]
[a#2				= A2							]
[a#3				= A3							]
[a#4				= A4							]
[a#5				= A5							]
[a#6				= A6							]
[a#7				= A7							]
[a#8				= A8							]
[a#9				= A9							]


[v#100				= 0.10 ]
[v#101 				= 19001653 ]
[v#102 				= DPT19DateTime ]
[v#103 				= 8 ]

###[/DEF]###

###[HELP]###
Inputs:
E1 - Countervalue:			Connect this to a accumulating counter


Outputs:
A1 - Changerate:			Calculated Changerate


This LBS ....



Versions:
V0.10	2019-09-04	SirSydom

Open Issues:


Author:
SirSydom - com@sirsydom.de
Copyright (c) 2019 SirSydom

Github:
https://github.com/SirSydom/edomi_LBS_sirsydom Tag: ???

Links:



Contributions:



###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id)
{
	//logging($id, "LBS called", null, 8);
	if($E=getLogicEingangDataAll($id))
	{
		ob_start();
		var_dump($E[1]['value']);
		$str = ob_get_clean();
		
		$array = explode(",", $E[1]['value']);
		
		if(sizeof($array) == 9)
		{
			for($i = 0;$i<9;$i++)
			{
				$array_dec[$i] = hexdec($array[$i]);
			}
			
			$year = 1900 + $array_dec[1];
			$month = $array_dec[2];
			$day = $array_dec[3];
			$day_of_week = ($array_dec[4] >> 5) & 0x07;
			$hour = ($array_dec[4]) & 0x1F;
			$minutes = $array_dec[5];
			$seconds = $array_dec[6];
			
			
			$iso_timestring = $year . "-" . $month . "-" . $day . " " . $hour . ":" . $minutes . ":" . $seconds;
			$timestamp = strtotime($iso_timestring);
			
			
			
			logic_setOutput($id,1,$year);
			logic_setOutput($id,2,$month);
			logic_setOutput($id,3,$day);
			logic_setOutput($id,4,$day_of_week);
			logic_setOutput($id,5,$hour);
			logic_setOutput($id,6,$minutes);
			logic_setOutput($id,7,$seconds);
			logic_setOutput($id,8,$iso_timestring);
			logic_setOutput($id,9,$timestamp);
		}
		else
		{
		}
		
		//$str = var_export($E[1]['value'], true);
		


	}
}


?>
###[/LBS]###


###[EXEC]###
<?




?>
###[/EXEC]###