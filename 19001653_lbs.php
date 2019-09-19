###[DEF]###
[name				= DPT19DateTime							]

[e#1	important	= DPT19 DateTime						]





[a#1				= Timestamp							]
[a#2				= Timestring						]
[a#3				= Year								]
[a#4				= Month								]
[a#5				= Day								]
[a#6				= Hours								]
[a#7				= Minutes							]
[a#8				= Seconds							]
[a#9				= Day of week						]
[a#10				= F									]
[a#11				= WD								]
[a#12				= NWD								]
[a#13				= NY								]
[a#14				= ND								]
[a#15				= NDoW								]
[a#16				= NT								]
[a#17				= SUTI								]
[a#18				= CLQ								]



###[/DEF]###

###[HELP]###
Inputs:
E1 - DPT19:			Connect this to a DPT 19.001 GA (Type "KNX Rohdaten")


Outputs:
A1 - Timestamp:		Seconds since the epoch (also called Unix Time)
A2 - Timestring:	Timestring which can be interpreted by most time functions in PHP
A3 - Year:			Year
A4 - Month:			Month [1-12]
A5 - Day:			Day of month [1-31]
A6 - Hours:			Hours [0-23]
A7 - Minutes:		Minutes [0-59]
A8 - Seconds:		Seconds [0-59]
A9 - Day of week:	Day of week [0=any day, 1=Monday .. 7=Sunday]
A10 - F:			Fault: 0 = No Fault, 1 = Fault
A11 - WD:			Working Day; 0 = no working day, 1 = working day
A12 - NWD:			No WD: 0 = WD field valid, 1 = WD field not valid
A13 - NY:			No Year: 0 = Year field valid, 1 = Year field not valid
A14 - ND:			No Date: 0 = Month and Day of Month fields valid, 1 = Month and Day of Month fields not valid
A15 - NDoW:			No Day of Week: 0 = Day of week field valid, 1 = Day of week field not valid
A16 - NT:			No Time: 0 = Hour of day, Minutes and Seconds fields valid, 1 = Hour of day, Minutes and Seconds fields not valid
A17 - SUTI:			Standard Summer Time: 0 = Time = UT+X, 1 = Time = UT+X+1
A18 - CLQ:			Quality of Clock: 0 = clock without ext. sync signal, 1 = clock with ext. sync signal


This LBS decodes a telegram with datapoint type 19.001 / DPT_DateTime.

All fields from the telegram can be found at the outputs.
Additionally, some calculated outputs are there for your comfort - A1 and A2. They can be used to get a well formated time string or whatever you have in your mind.
A1 and A2 are only calculated when F = NY = ND = NT = 0

Timezone hints:
This LBS works in "one timezone". Normally, DPT_DateTime is sent with local time, and in this case, all output is also in local time (also A1 !)
If E1 is in UTC, all output is also in UTC.



Versions:
V0.20	2019-09-19	SirSydom

Open Issues:
- Test 24:00:00
- check the ranges and the reserved bits..


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
			
			$flag_F		= ($array_dec[7] >> 7 ) & 0x01;
			$flag_WD	= ($array_dec[7] >> 6 ) & 0x01;
			$flag_NWD	= ($array_dec[7] >> 5 ) & 0x01;
			$flag_NY	= ($array_dec[7] >> 4 ) & 0x01;
			$flag_ND	= ($array_dec[7] >> 3 ) & 0x01;
			$flag_NDoW	= ($array_dec[7] >> 2 ) & 0x01;
			$flag_NT	= ($array_dec[7] >> 1 ) & 0x01;
			$flag_SUTI	= ($array_dec[7]      ) & 0x01;
			$flag_CLQ	= ($array_dec[8] >> 7 ) & 0x01;
			
			if($flag_F + $flag_WD + $flag_NY + $flag_ND == 0)
			{
				$iso_timestring = $year . "-" . $month . "-" . $day . " " . $hour . ":" . $minutes . ":" . $seconds;
				$timestamp = strtotime($iso_timestring);
			}
			else
			{
				$iso_timestring = null;
				$timestamp = null;
			}
			
			
			logic_setOutput($id,1,$timestamp);
			logic_setOutput($id,2,$iso_timestring);
			
			logic_setOutput($id,3,$year);
			logic_setOutput($id,4,$month);
			logic_setOutput($id,5,$day);
			logic_setOutput($id,9,$day_of_week);
			logic_setOutput($id,6,$hour);
			logic_setOutput($id,7,$minutes);
			logic_setOutput($id,8,$seconds);
			
			logic_setOutput($id,10,$flag_F);
			logic_setOutput($id,11,$flag_WD);
			logic_setOutput($id,12,$flag_NWD);
			logic_setOutput($id,13,$flag_NY);
			logic_setOutput($id,14,$flag_ND);
			logic_setOutput($id,15,$flag_NDoW);
			logic_setOutput($id,16,$flag_NT);
			logic_setOutput($id,17,$flag_SUTI);
			logic_setOutput($id,18,$flag_CLQ);
		}
		else
		{
		}
	}
}


?>
###[/LBS]###


###[EXEC]###
<?




?>
###[/EXEC]###