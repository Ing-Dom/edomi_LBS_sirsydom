###[DEF]###
[name		= EXP LBS1657 V0.01	]

[e#1 important		= Base ]
[e#2 trigger		= Exponent ]

[a#1		= Value					]
###[/DEF]###


###[HELP]###
This LBS calculates A1 = E1 * 10 ^ E2

Inputs:
E1 - Base
E2 - Exponent

Outputs:
A1 - Value

Versions:
V0.01	2020-01-22	SirSydom		initial version

Open Issues:


Author:
SirSydom - com@sirsydom.de
Copyright (c) 2020 SirSydom


Github:
https://github.com/SirSydom/edomi_LBS_sirsydom


Links:



###[/HELP]###


###[LBS]###
<?

function LB_LBSID($id)
{
    if ($E = logic_getInputs($id))
	{
		logic_setOutput($id, 1, $E[1]['value'] * pow(10, $E[2]['value']));
    }
}
?>
###[/LBS]###


###[EXEC]###
<?

?>
###[/EXEC]###