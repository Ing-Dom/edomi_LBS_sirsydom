###[DEF]###
[name =Archive: Analyze Period LBS1656 V0.03]

[e#1 =Trigger ]
[e#2 =Start ]
[e#3 =End ]
[e#4 =Period ]
[e#5 =ArchiveID ]

[a#1 =Sum]
[a#2 =Avg1 ]
[a#3 =Avg2 ]
[a#4 =Min ]
[a#5 =Max ]
[a#6 =Start of period ]
[a#7 =End of period ]
[a#8 =Error ]

[v#100				= 0.03 ]
[v#101 				= 19001656 ]
[v#102 				= Archive: Advanced Query ]
###[/DEF]###


###[HELP]###
This LBS calculates statistic data based on a data archive with for a given period.

Avg2 is for calculationg average values in case the data records are not even spaced (like: every 5min).
The value is weighted with the time until the next records occures.


Inputs:
E1 - Trigger: LBS will be executed only when a new telegram = 1 arrives
E2 - Start: a custom start date/time can be specified here. (>= E2) NULL: no restriction regarding time
E3 - End: a custom end date/time can be specified here. (< E3) NULL: no restriction regarding time
E4 - Period: A year (e.g. 2020), a month (e.g. 2020-01) or a day (e.g. 2020-01-08) can be specified. NULL: no restriction regarding time
E5 - ArchiveID: the data archive id from edomi NULL: no Archive ID will be used

Outputs:
A1 - Sum: sum of all data in the given period
A2 - Avg1: Arithmetic Average - sum of all data in the given period divided through the number of data records
A3 - Avg2: average weighted by time (see description)
A4 - Min: minimal data value
A5 - Max: maximal data value
A6 - Start of period: Timestamp of first evaluated data record
A7 - End of period: Timestamp of last evaluated data record
A8 - Error

Versions:
V0.01	2020-01-15	SirSydom		initial version
V0.02	2020-01-16	SirSydom		fixed periodstr
V0.03	2020-01-16	SirSydom		changed E3 from <= to <


Open Issues:


Author:
SirSydom - com@sirsydom.de
Copyright (c) 2020 SirSydom

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
			$archivId = $E[5]['value'];
			$period = $E[4]['value'];
			$timestartstr = '';
			$timeendstr = '';
			$periodstr = '';
			$error=true;
			$query='';
			$mydata = array();
			$sum = 0;
			$cnt = 1;
			$min = 0;
			$max = 0;
			$weighted_avg = 0;
			$min_datetime='';
			$max_datetime='';
			$time = 0;
			
			if($period != NULL)
			{
				$periode_arr = explode('-', $period);
				
				if(count($periode_arr) == 1)
				{
					$periodstr = "datetime >= '".$periode_arr[0]."-01-01 00:00:00' AND datetime < '". ($periode_arr[0]+1) ."-01-01 00:00:00' AND ";
				}
				else if(count($periode_arr) == 2)
				{
					$periodstr = "datetime >= '".$periode_arr[0]."-".$periode_arr[1]."-01 00:00:00' AND datetime < '". $periode_arr[0] ."-". ($periode_arr[1]+1)."-01 00:00:00' AND ";
				}
				else if(count($periode_arr) == 3)
				{
					$periodstr = "datetime >= '".$periode_arr[0]."-".$periode_arr[1]."-".$periode_arr[2]." 00:00:00' AND datetime < '". $periode_arr[0] ."-". ($periode_arr[1])."-". ($periode_arr[2]+1)." 00:00:00' AND ";
				}
				else
				{
					logic_setOutput($id,8,"E4 has wrong format");
					exit();
				}
			}
			else
			{
				if($E[2]['value'] != NULL)
				{
					$timestartstr = "datetime >= '" . $E[2]['value'] . "' AND ";
				}
				
				if($E[3]['value'] != NULL)
				{
					$timeendstr = "datetime < '".$E[3]['value']."' AND ";
				}
			}
			
			if($archivId != NULL)
			{
				$archivIdstr = "targetID=".strVal($archivId)." ";
				$archivIdstr = "targetID=".strVal($archivId)." ";
			}


			
			$query = "SELECT gavalue, datetime FROM `archivKoData` WHERE ".$timestartstr.$timeendstr.$periodstr.$archivIdstr . " ORDER BY datetime ASC";
			logic_setOutput($id,8,$query);

			$con = mysql_connect("localhost","root","");
			mysql_select_db("edomiLive", $con);
			if ($result = mysql_query($query, $con))
			{
				$quantity=mysql_num_rows($result);
				if($quantity == 0)
				{
					$error = false;
				}
				else
				{
					if ($row = mysql_fetch_array($result))
					{
						$error = false;

						$lasttime = $row[1];
						$lastvalue = $row[0];
						
						$min_datetime = $lasttime;
						
						$max = $lastvalue;
						$min = $lastvalue;
						
						$sum = $lastvalue;
						$cnt = 1;
					}
					while ($row = mysql_fetch_array($result))
					{
						$time = $row[1];
						$value = $row[0];
						
						$weighted_avg += (strtotime($time) - strtotime($lasttime)) * $lastvalue;
						
						if($value > $max)
							$max = $value;
						if($value < $min)
							$min = $value;
						
						$sum += $value;
						$cnt++;
						
						$lasttime = $time;
						$lastvalue = $value;
					}
					$max_datetime = $time;
					
					$weighted_avg = $weighted_avg / (strtotime($max_datetime) - strtotime($min_datetime));
				}
			}
			else
			{
				//writeToCustomLog(0,true,'Error db access');
			}

			mysql_close($con);

			if ($error)
			{
				logic_setOutput($id,6,1);
				//writeToCustomLog(0,true,'No data availible');
			}
			else
			{
				logic_setOutput($id,1,$sum);
				logic_setOutput($id,2,$sum/$cnt);
				logic_setOutput($id,3,$weighted_avg);
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