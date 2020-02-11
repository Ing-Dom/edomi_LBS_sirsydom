###[DEF]###
[name = 	ModBus TCP Master Read]
[e#1 trigger= 		Autostart #init=1]
[e#2 important= 		Host #init=e3dc.knx.pattison.de] ModBus Slave IP or Host
[e#3 optional= 		Port #init=502] Modbus Port
[e#4 = 		Mode 0/1 #init=1] 0 UDP, 1 TCP
[e#5 optional= 		SlaveID #init=0]
[e#6 = 		Logging #init=6]
[e#7 =		Loop #init=0]
[e#8 =		Delay #init=1000]
[e#9 important= 		Address1 #init=40072]
[e#10 important= 	Length1 #init=1] #Word size=2bytes
[e#11 important= 	Type1 #init=0] 0=Int, 1=uInt, 2=String
[e#12 important=		Function1 #init=3]
[e#13 option=Address2]
[e#14 option=Length2]
[e#15 option=Type2]
[e#16 option=Function2 #init=3]
[e#17 option=Address3]
[e#18 option=Length3]
[e#19 option=Type3]
[e#20 option=Function3 #init=3]
[e#21 option=Address4]
[e#22 option=Lengt4]
[e#23 option=Type4]
[e#24 option=Function4 #init=3]
[e#25 option=Address5]
[e#26 option=Length5]
[e#27 option=Type5]
[e#28 option=Function5 #init=3]

[a#1 = Value1]
[a#2 = Value2]
[a#3 = Value3]
[a#4 = Value4]
[a#5 = Value5]
[a#9 = Trigger Ext]
[a#10 = Error]

[v#1             = 0 ]
[v#2             = 0 ]
[v#3			       = 0 ]
[v#4             = 0 ]
[v#5             = 0]
[v#100           = 0.5 ]
[v#101           = 19001030 ]
[v#102           = ModBusMaster Read]
[v#103           = 0 ]
[v#104           = 0 ]
[v#105           = 0 ]
###[/DEF]###


###[HELP]###

INSTALLATION:
This LBS requires phpmodbus.php to be present on Edomi.
You can either copy the included zip file and extract the content to /usr/local/edomi/main/include/php/ or
download phpModbus yourself. The version included is slightly modified to reduce logging output.

USAGE:
This LSB allows to query up to 5 Values from a Modbus TCP Slave.
It can either run once on a trigger or run continously.
E2 is the IP or Hostname, E3 the Port wich should default to 502.
E4 is TCP or UDP Connection and E5 is the Slave ID which is usually not changed.
E6: Sets the Logging Level meinly 0 or 8 for debug. In debug mode it will write a lot so be warned!
E7: Loop or no Loop
E8 is the delay in ms between loops, default is 1000 which is 1s.
E9 is mandatory address where we start to read. Be aware that addresses are counted from 0 but not all documentation take this into account.
You might need to play with Address+-1 to get good values.
E10 is the length of data we read in Dword! Meaning 1 is 2 bytes long. Again check the device doc.
E11 is the data type that is returned so the LBS can convert accordingly.
E12 is the Function to use, at the moment only F03 is implemented!
From now it just repeats for differents inputs.
The Result is the put to A1 to A5.
A9 is a trigger that can be used to cascade several LBS. After each run but before the delay starts it will be triggerd with a 1.
A10 indicates an error which usually means the LBS has stopped and need to be restarted.


E1: Autostart, Default=1
E2: Host or IP of Modbus Slave
E3: Port (Currently not used)
E4: Mode: 0=UDP, 1=TCP
E5: Modbus Slave ID, Default=1
E6: Logging, 0=none, 8=Debuggen
E7: Loop, Default=0, 0=False=Run once,1=True=Run till Edomi END
E8: Delay between runs in ms, Default=1000 (1sec)
E9: Address1 Datapoint
E10: Lenght to read 1, Default=1 (1=1 Dword=2 Bytes)
E11: Type of Data returned 1, 0=Int, 1=uInt, 2=Float, 3=Byte, 4=Bit, 5=String, 6=Swap Float
E12: Function to use (Currently only FC3 Read_Multiple_Registers is implemented)
E13: Address2 (see E9)
E14: Length2 (see E10)
E15: Type2 (see E11)
E16: Function2 (see E12)
E17: Address3 (see E9)
E18: Length3 (see E10)
E19: Type3 (see E11)
E20: Function3 (see E12)
E21: Address4 (see E9)
E22: Length4 (see E10)
E23: Type4 (see E11)
E24: Function4 (see E12)
E25: Address5 (see E9)
E26: Length5 (see E10)
E27: Type5 (see E11)
E28: Function5 (see E12)

A1: Result of Query 1
A2: Result of Query 2
A3: Result of Query 3
A4: Result of Query 4
A5: Result of Query 5
A9: Trigger ext, Can be used to chain LBS together, will send a 1 at the end of each loop
A10: Triggers on Daemon Error 1=Generic Error, 2=Timeout Error

V1: Indicator whether daemon is running
V2: PID of EXEC Daemon
V3: Array of used Inputs
V4: Endianness 0/1
V5: Timout Counter
V100: Version
V101: LBS Number
V102: Log file name
V103: Log level
V104: One log file per LBS instance
V105: log ID in each log entry

#Changelog
v0.1 Initial version
v0.2 small bugfix + new Datatype "Swap Float"
v0.3 "Swap Float" fix
v0.4 Implemented Endianess Setting in V4
v0.5 Refactoring of EXEC part to avoid potential Memory Leak. EXEC Part is not a daemon anymore. increased Debug Output.
v0.6 Suppress wrong data due to corrupted Modbus Message. Bugfix for Looping and E1 Trigger.
v0.61 fixed loop bug
###[/HELP]###

###[LBS]###
<?
function LB_LBSID_logging($id, $msg, $var = NULL, $priority = 8)
{
  $E = getLogicEingangDataAll($id);
  $logLevel = getLogicElementVar($id, 103);
  if (is_int($priority) && $priority <= $logLevel && $priority > 0) {
    $logLevelNames = array(
      'none',
      'emerg',
      'alert',
      'crit',
      'err',
      'warning',
      'notice',
      'info',
      'debug'
    );
    $version = getLogicElementVar($id, 100);
    $lbsNo = getLogicElementVar($id, 101);
    $logName = getLogicElementVar($id, 102) . "-LBS$lbsNo";
    $logName = preg_replace('/ /', '', $logName);
    if (logic_getVar($id, 104))
    $logName .= "-$id";
    if (logic_getVar($id, 105) == 1)
    $msg .= " ($id)";
    strpos($_SERVER['SCRIPT_NAME'], $lbsNo) ? $scriptname = 'EXE' . $lbsNo : $scriptname = 'LBS' . $lbsNo;
    writeToCustomLog($logName, str_pad($logLevelNames[$logLevel], 7), $scriptname . " [v$version]:\t" . $msg);
    if (isset($var)) {
      writeToCustomLog($logName, str_pad($logLevelNames[$logLevel], 7), $scriptname . " [v$version]:\t================ ARRAY/OBJECT START ================");
      writeToCustomLog($logName, str_pad($logLevelNames[$logLevel], 7), $scriptname . " [v$version]:\t" . json_encode($var));
      writeToCustomLog($logName, str_pad($logLevelNames[$logLevel], 7), $scriptname . " [v$version]:\t================ ARRAY/OBJECT  END  ================");
    }
  }
}

function LB_LBSID($id) {




  if ($E=logic_getInputs($id)) {
    $loop=$E[7]['value'];
    $delay=$E[8]['value'];
    if (logic_getState($id)==1 && $E[1]['value']==0){
      logic_setState($id,0);
      LB_LBSID_logging($id, 'LBS ended');
    } else {
      setLogicElementVar($id, 103, $E[6]['value']); // set loglevel to #VAR 103
      if (logic_getState($id)==0) {
        LB_LBSID_logging($id, 'LBS started');
      }
      $activeInputs=array();
      if ($E[9]['value']!=""){
        $activeInputs[]=9;
      }
      if ($E[13]['value']!=""){
        $activeInputs[]=13;
      }
      if ($E[17]['value']!=""){
        $activeInputs[]=17;
      }
      if ($E[21]['value']!=""){
        $activeInputs[]=21;
      }
      if ($E[25]['value']!=""){
        $activeInputs[]=25;
      }

      setLogicElementVar($id,3,implode(";",$activeInputs));
      if (getLogicElementVar($id,1)!=1) {
        setLogicElementVar($id,5,1);
        setLogicElementVar($id,1,1);                        //setzt V1=1, um einen mehrfachen Start des EXEC-Scripts zu verhindern
        callLogicFunctionExec(LBSID,$id);                 //EXEC-Script starten (garantiert nur einmalig)
      } elseif (logic_getVar($id,5)>9){
        setLogicElementVar($id,5,0);
        setLogicElementVar($id,1,0);
        logic_SetOutput($id,10,2);
      } else {
        $v5=logic_getVar($id,5);
        logic_setVar($id,5,$v5+1);
      }
      if ($loop==1){
        	logic_setState($id,1,$delay);
        } else {
          logic_setState($id,0);
        }
    }

  }
}
?>
###[/LBS]###
###[EXEC]###
<?
require(dirname(__FILE__)."/../../../../main/include/php/incl_lbsexec.php");
require_once(dirname(__FILE__)."/../../../../main/include/php/ModbusMaster.php");
set_time_limit(0);                                       //Wichtig! Script soll endlos laufen
sql_connect();
logging($id, "START ModbusMaster Exec",null,6);
setLogicElementVar($id, 2, getmypid());
$suppressModBusError='0';
//
// error_off() : switch off error reporting
// error_on() : switch on error reporting
//
function myErrorHandler($errno, $errstr, $errfile, $errline)
{
  global $id;
  if(($errfile=='/usr/local/edomi/main/include/php/ModbusMaster.php' AND $errno=='8' AND $errline='506')OR($errfile=='/usr/local/edomi/main/include/php/ModbusMaster.php' AND $errno=='2048')OR($errfile=='/usr/local/edomi/main/include/php/IecType.php' AND $errno=='2048')){return;}

  logging($id, "File: $errfile | Error: $errno | Line: $errline | $errstr ",null,4);
}
function myErrorHandlerModBus($errno, $errstr, $errfile, $errline)
{
  global $id, $suppressModBusError;
  if(($errfile=='/usr/local/edomi/main/include/php/ModbusMaster.php' AND $errno=='2048')OR($errfile=='/usr/local/edomi/main/include/php/IecType.php' AND $errno=='2048')){return;}

  if(($errfile=='/usr/local/edomi/main/include/php/ModbusMaster.php' AND $errno=='8' AND $errline='506')){
    $suppressModBusError='1';
    logging($id, "Corrupt ModBus Message received");
    return;}

  logging($id, "File: $errfile | Error: $errno | Line: $errline | $errstr ",null,4);
}

function error_off()
{
  $error_handler = set_error_handler("myErrorHandler");
  error_reporting(0);
}

function error_on()
{
  restore_error_handler();
  error_reporting(E_ALL);
}

function logging($id, $msg, $var = NULL, $priority = 8)
{
  //$E = getLogicEingangDataAll($id);
  $logLevel = getLogicElementVar($id, 103);
  if (is_int($priority) && $priority <= $logLevel && $priority > 0) {
    $logLevelNames = array(
      'none',
      'emerg',
      'alert',
      'crit',
      'err',
      'warning',
      'notice',
      'info',
      'debug'
    );
    $version = getLogicElementVar($id, 100);
    $lbsNo = getLogicElementVar($id, 101);
    $logName = getLogicElementVar($id, 102) . "-LBS$lbsNo";
    $logName = preg_replace('/ /', '_', $logName);
    if (logic_getVar($id, 104) == 1)
    $logName .= "-$id";
    if (logic_getVar($id, 105) == 1)
    $msg .= " ($id)";
    strpos($_SERVER['SCRIPT_NAME'], $lbsNo) ? $scriptname = 'EXE' . $lbsNo : $scriptname = 'LBS' . $lbsNo;
    writeToCustomLog($logName, str_pad($logLevelNames[$logLevel], 7), $scriptname . " [v$version]:\t" . $msg);
    if (isset($var)) {
      writeToCustomLog($logName, str_pad($logLevelNames[$logLevel], 7), $scriptname . " [v$version]:\t================ ARRAY/OBJECT START ================");
      writeToCustomLog($logName, str_pad($logLevelNames[$logLevel], 7), $scriptname . " [v$version]:\t" . json_encode($var));
      writeToCustomLog($logName, str_pad($logLevelNames[$logLevel], 7), $scriptname . " [v$version]:\t================ ARRAY/OBJECT  END  ================");
    }
  }
}


date_default_timezone_set('Europe/Berlin');

error_off();


if ($E=getLogicEingangDataAll($id)) {
  $endianness=getLogicElementVar($id, 4);
  $v3=getLogicElementVar($id, 3);
  $inputs=explode(";",$v3);
  $querys=array();
  logging($id,"v3:" . $v3);
  logging($id,"inputs:" , $inputs);
  $retries=0;

  // Create Modbus object
  //$modbus = new ModbusMaster($E[2]['value'], ($E[4]['value']==0 ? "UDP" : "TCP"));

  //$Looping = true;
  //while (getSysInfo(1)>=1 AND $Looping){
  //logging($id,"Memory before New ModBusMaster: " . (string)memory_get_usage());
  // Create Modbus object
  $modbus = new ModbusMaster($E[2]['value'], ($E[4]['value']==0 ? "UDP" : "TCP"));
  //logging($id,"Memory after New ModBusMaster: " . (string)memory_get_usage());
  for($i = 0, $groesse = count($inputs); $i < $groesse; ++$i) {
    logging($id,"i:" . $i . "; input:" . $inputs[$i] . "; address:" . $E[$inputs[$i]]['value'] . "; length:" . $E[$inputs[$i]+1]['value'] . "; type:" . $E[$inputs[$i]+2]['value']);
    //logging($id,"i:" . $i);
    //logging($id,"input:" . $inputs[$i]);
    //logging($id,"address:" . $E[$inputs[$i]]['value']);
    //$query=Array('address'=>$E[$inputs[$i]]['value'],'length'=>$E[$inputs[$i]+1]['value'],'type'=>$E[$inputs[$i]+2]['value'],'function'=>$E[$inputs[$i]+3]['value']);
    //logging($id,"Query:" , $query);
    //$querys[]=$query;

    //logging($id,"Querys:" , $querys);


    //for($i = 0, $groesse = count($querys); $i < $groesse; ++$i) {
    try {
      // FC 3
      if ($E[$inputs[$i]+3]['value']==3){
        set_error_handler("myErrorHandlerModBus");
        $suppressModBusError='0';
        $recData = $modbus->readMultipleRegisters($E[5]['value'], $E[$inputs[$i]]['value'], $E[$inputs[$i]+1]['value']);
        restore_error_handler();
        //logging($id,"Memory after Read: " . (string)memory_get_usage());
      }
    }
    catch (Exception $e) {
      // Print error information if any
      logging($id, "Modbus Error: ", $modbus,4);
      logging($id, "Modbus Error: ", $e,4);
      logging($id, "Retries:" . $retries);
      //logging($id,"Memory in error: " . (string)memory_get_usage(),4);
      if ($retries<3){
        $retries+=1;
        break;
      }else{
        setLogicLinkAusgang($id,10,1); //send error to A10
        setLogicElementVar($id,1,0);  //allow to restart
        logging($id, "Error: Retry counter reached max. Exiting!",null,4);
        exit;
      }
    }
    // Print status information
    logging($id,"Status:" . $modbus);

    // Print read data
    if (isset($recData) AND $recData!=false AND $suppressModBusError=='0'){
      logging($id,"Type:" . $E[$inputs[$i]+2]['value'] . "Raw Data:",$recData);
      //0=Int, 1=uInt, 2=Float, 3=Byte, 4=Bit, 5=String
      if ($E[$inputs[$i]+2]['value']==0){
        $ret=PhpType::bytes2signedInt($recData, $endianness);
      }elseif ($E[$inputs[$i]+2]['value']==1){
        logging($id,"unsigned start");
        $ret=PhpType::bytes2unsignedInt($recData, $endianness);
        logging($id,"unsigned stop");
      }elseif ($E[$inputs[$i]+2]['value']==2){
        $ret=PhpType::bytes2float($recData, $endianness);
      }elseif ($E[$inputs[$i]+2]['value']==3){
        $ret=$recData[0];
      }elseif ($E[$inputs[$i]+2]['value']==4){
        $ret=0;
      }elseif ($E[$inputs[$i]+2]['value']==5){
        $ret=PhpType::bytes2string($recData, $endianness);
      }elseif ($E[$inputs[$i]+2]['value']==6){
        if (getLogicElementVar($id, 4) == 0){
          $ret = (($recData[3] & 0xFF)) |
          (($recData[2] & 0xFF)<<8) |
          (($recData[1] & 0xFF))<<16 |
          (($recData[0] & 0xFF)<<24);
        }else{
          $ret = (($recData[3] & 0xFF)<<24) |
          (($recData[2] & 0xFF)<<16) |
          (($recData[1] & 0xFF)<<8) |
          (($recData[0] & 0xFF));
        }
        $ulong = pack('L*', $ret);
        $float = unpack('f*', $ulong);
        $ret = $float[1];
      }


      logging($id,"Data:" . $ret);
      setLogicLinkAusgang($id,$i+1,$ret);
    }
  }
  //Loop
  //if ($E[7]['value']==0){
  //  $Looping=false;
  //  logging($id,"Looping:" . var_export($Looping,true));
  //}
  setLogicLinkAusgang($id,9,1);
  //Delay
  //logging($id,"Delay:" . $E[8]['value']);
  unset($modbus);
  //usleep(1000*$E[8]['value']);
  setLogicLinkAusgang($id,9,0);
  logging($id,"end of delay");
  // Create Modbus object
  //$modbus = new ModbusMaster($E[2]['value'], ($E[4]['value']==0 ? "UDP" : "TCP"));
}
//}

setLogicElementVar($id,1,0); //allow to restart
//logging($id,"Memory at End: " . (string)memory_get_usage());
logging($id, "STOP ModbusMaster EXEC",null,6);
error_on();
sql_disconnect();
?>
###[/EXEC]###
