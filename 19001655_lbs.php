###[DEF]###
[name =Archive: Analyze temperature data LBS1655 V0.11]

[e#1 =Trigger ]
[e#2 =Start ]
[e#3 =End ]
[e#4 =ArchiveID ]

[a#1 =Wachstumsgradtage]
[a#2 =GTS ]
[a#3 =Avg ]
[a#4 =Min ]
[a#5 =Max ]
[a#6 =Start of period ]
[a#7 =End of period ]
[a#8 =Error ]

[v#100				= 0.11 ]
[v#101 				= 19001655 ]
[v#102 				= Archive: Analyze temperature data ]
###[/DEF]###


###[HELP]###
This LBS calculates statistic data based on a data archive with temperature values for a given period (default: current year).
The distance of time between the data points is taken into account and the temperature value is weighted in accordance.

The main outputs - Wachstumsgradtage and GTS (=Gründlandtemperatursumme) - are mainly used to determine the ideal time for sewing, planting or fertilization e.g. the lawn or other plants.
A good time to fertilize the lawn is when 150 Wachstummsgradtage are reached (most years in march).

Inputs:
E1 - Trigger: LBS will be executed only when a new telegram = 1 arrives
E2 - Start: a custom start date/time can be specified here. NULL: 01.01 of current year
E3 - End: a custom end date/time can be specified here. NULL: all data availible after Start
E4 - ArchiveID: the data archive id from edomi

Outputs:
A1 - Wachstumsgradtage
A2 - Grünlandtemperatursumme
A3 - Average temperature
A4 - Minimum temperature
A5 - Maximum temperature
A6 - Start of period
A7 - End of period
A8 - Error: 1 means Error has occured

Versions:
V0.10	2019-03-07	SirSydom		initial version
V0.11	2020-01-08	SirSydom		renamed to 1655
V0.12	2020-01-15	SirSydom		removed logging functions

Open Issues:


Author:
SirSydom - com@sirsydom.de
Copyright (c) 2019-2020 SirSydom

Github:


Links:


###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id)
{
	if ($E=logic_getInputs($id))
	{
		if ($E[1]['refresh'] == 1 && $E[1]['value'] == 1)
		{
			$archivDb='archivKoData';
			$archivId=$E[4]['value'];
			$timestart='';
			$timeendstr = '';
			$error=true;
			$query='';
			$mydata = array();
			$sum = 0;
			$sum2 = 0;
			$sum3 = 0;
			$min = 100;
			$max = -100;
			$min_datetime='';
			$max_datetime='';
			
			if($E[2]['value'] != NULL)
			{
				$timestart=$E[2]['value'];
			}
			else
			{
				$timestart = date('Y-01-01');
			}
			
			if($E[3]['value'] != NULL)
			{
				$timeendstr = " AND (datetime) <= '".$E[3]['value']."'";
			}


			
			$query = "SELECT gavalue, datetime FROM `archivKoData` WHERE (targetID=".strVal($archivId).") AND (datetime) >= '".$timestart."'".$timeendstr." ORDER BY datetime ASC";


			$con = mysql_connect("localhost","root","");
			mysql_select_db("edomiLive", $con);
			if ($result = mysql_query($query, $con))
			{
				$quantity=mysql_num_rows($result);
				if ($row = mysql_fetch_array($result))
				{
					$error = false;
					$min_datetime = $row[1];
					$lasttime = $row[1];
					$lasttemp = $row[0];
					
					if($lasttemp > $max)
						$max = $lasttemp;
					if($lasttemp < $min)
						$min = $lasttemp;
				}
				while ($row = mysql_fetch_array($result))
				{
					$time = $row[1];
					$temp = $row[0];
					
					$dayofyear = date('z', strtotime($lasttime));
					$weighted_avg_temp = ((strtotime($time) - strtotime($lasttime)) / 86400) * $lasttemp;
					if(array_key_exists($dayofyear, $mydata))
						$mydata[$dayofyear] = $mydata[$dayofyear] + $weighted_avg_temp;
					else
						$mydata[$dayofyear] = $weighted_avg_temp;
					
					$lasttime = $time;
					$lasttemp = $temp;
					
					if($lasttemp > $max)
						$max = $lasttemp;
					if($lasttemp < $min)
						$min = $lasttemp;
					
					
				}
				$max_datetime = $time;
			}
			else
			{
				//writeToCustomLog(0,true,'Error db access');
			}

			foreach ($mydata as $key => $value)
			{
				if($value > 0)
				{
					$sum += $value;
					
					if($key <=30)
						$sum2 += $value/2;
					else if($key <=59)
						$sum2 += $value*3/4;
					else
						$sum2 += $value;
				}
				$sum3 += $value;
			}
			
			$days_in_period = (strtotime($max_datetime) - strtotime($min_datetime)) / 86400;
			$avg_temp = $sum3 / $days_in_period;


			mysql_close($con);

			if ($error)
			{
				logic_setOutput($id,6,1);
				//writeToCustomLog(0,true,'No data availible');
			}
			else
			{
				logic_setOutput($id,1,$sum);
				logic_setOutput($id,2,$sum2);
				logic_setOutput($id,3,$avg_temp);
				logic_setOutput($id,4,$min);
				logic_setOutput($id,5,$max);
				logic_setOutput($id,6,$min_datetime);
				logic_setOutput($id,7,$max_datetime);

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