###[DEF]###
[name				= Add2Datenarchiv					]

[e#1	important	= Data								]
[e#2	important	= Timestamp							]
[e#3	important	= Milliseconds						]
[e#4	important	= Datenarchiv ID					]
	




[a#1				= Error								]
[a#10				= Debug								]



###[/DEF]###

###[HELP]###
Inputs:
E1 - DPT19:			


Outputs:
A1 - Timestamp:		



Versions:
V0.01	2019-09-19	SirSydom

Open Issues:
- 


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
	if ($E=logic_getInputs($id))
	{
		if ($E[1]['refresh'] == 1 && $E[1]['value'] != null)
		{
			$archivDb='archivKoData';
			$archivId=$E[3]['value'];
			$value = $E[1]['value'];
			
			$query = "INSERT INTO `archivKoData` (`datetime`, `ms`, `targetid`, `gavalue`) VALUES ('" . . "', '" . ."', '" . strVal($archivId) . "', '" . $value . "')";


			//$con = mysql_connect("localhost","root","");
			//mysql_select_db("edomiLive", $con);
			//$result = mysql_query($query, $con);
			
			
			logic_setOutput($id,10,$query);
}


?>
###[/LBS]###


###[EXEC]###
<?




?>
###[/EXEC]###