###[DEF]###
[name				= DPT19DateTime							]

[e#1	important	= DPT19 DateTime						]


[e#10				= Loglevel 			#init=3				]



[a#1				= A1							]
[a#2				= A2							]
[a#3				= A3								]


[v#100				= 0.10 ]
[v#101 				= 19001653 ]
[v#102 				= DPT19DateTime ]
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

This LBS calculates a change rate based on counter values.
Assumed "something" is counted (regardless the unit) the resulting change rate is unit per second.
The calculated change rate can be scaled by the factor Scale at E2. So, if the unit of the counted items is "Wh", you get Wh/s which is the unit 3600W.
To get the usefull unit W the Scale must be set to 3600.

A1 calculates this way:

A1 = ((E1 - E4) / (<currenttime> - E5) ) * E2

The "old" counter value and its timestamp are saved externally in KOs because you can set them to remanent and therefor you get true values for the changerate also over a edomi restart.
A2 and A3 should be used to set a remanent KO which is feed back to E4 and E5.

When the countervalues increases very fast like > 1Hz the jitter of the edomi time and calculating time result in jittering changerates.
The changerate is only calculated and output when E1 - E4 > E3 which smoothes the jitter of the time.



Versions:
V0.10	2019-09-04	SirSydom

Open Issues:


Author:
SirSydom - com@sirsydom.de
Copyright (c) 2019 SirSydom

Github:
https://github.com/SirSydom/edomi_LBS_sirsydom Tag: 19001652_V0.10

Links:
https://knx-user-forum.de/forum/projektforen/edomi/1400676-lbs-dev-aus-pulsen-z%C3%A4hlerst%C3%A4nden-in-%C3%A4nderungsrate-berechnen-s0-stromz%C3%A4hler


Contributions:



###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id)
{
	//logging($id, "LBS called", null, 8);
	if($E=getLogicEingangDataAll($id))
	{
		
		
		$str = var_export($E[1]['value'], true);
		
		logic_setOutput($id,1,$str);

	}
}


?>
###[/LBS]###


###[EXEC]###
<?




?>
###[/EXEC]###