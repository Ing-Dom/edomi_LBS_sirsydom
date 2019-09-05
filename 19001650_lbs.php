###[DEF]###
[name				= MinMaxFilter							]

[e#1				= Wert									]
[e#2				= Min									]
[e#3				= Max									]



[a#1				= Wert									]
[a#2				= Wert ungueltig						]
[a#3				= Vgl									]


###[/DEF]###

###[HELP]###
This LBS connects checks if the value at E1 is greater or equal than E2 and less or equal than E3. If so, the value from E1 will be sent to A1 and A3 will be 1, otherwise to A2 and A3 will be 0.

Inputs:
E1 - Wert:				
E2 - Min:				
E3 - Max:				


Outputs:
A1 - Wert:				
A2 - Wert ungueltig:	
A3 - Vgl:				


Versions:
V0.10	2018-06-10	SirSydom	first aplha version

Open Issues:
Help

Author:
SirSydom - com@sirsydom.de
Copyright (c) 2018 SirSydom

Github:
https://github.com/SirSydom/edomi_LBS_logik

Links:
tbd



###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id)
{
	if($E=getLogicEingangDataAll($id))
	{
		
		if($E[1]['refresh']) // new telegram on E1
		{
			if($E[1]['value'] >= $E[2]['value'] && $E[1]['value'] <= $E[3]['value'])	// E2 <= E1 <= E3
			{
				logic_setOutput($id,1,$E[1]['value']);
				logic_setOutput($id,3,1);
			}
			else
			{
				logic_setOutput($id,2,$E[1]['value']);
				logic_setOutput($id,3,0);
			}
		}
	}
}


?>
###[/LBS]###

###[EXEC]###
<?
?>
###[/EXEC]###