###[DEF]###
[name=SmlReader v1.6]

[e#1=obis1]
[e#2=obis2]
[e#3=obis3]
[e#4=obis4]
[e#5=obis5]
[e#6=obis6]

[e#7=port]
[e#8=baudrate]
[e#9=enable]
[e#10=trace]

[a#1=value1]
[a#2=value2]
[a#3=value3]
[a#4=value4]
[a#5=value5]
[a#6=value6]

[v#1=0]

###[/DEF]###

###[HELP]###
SmlReader V1.6

Dieser LBS verarbeitet SML-kodierte Binärdaten von elektronischen Haushaltszählern (eHZ)
mit einer optischen Schnittstelle. eHZ mit der Datenausgabe im Klartext werden nicht
unterstützt.

Zum Auslesen wird ein IR-Lesekopf benötigt (z.B. von volkszaehler.org).

Die Daten werden über eine serielle Schnittstelle eingelesen, dekodiert und gefiltert.
Die gefilterten Daten werden über LBS-Ausgänge ausgegeben.

Der Baustein benötigt das Device, an dem ein Lesekopf angeschlossen ist, die vom Zählers
unterstützte Übertragungsgeschwindigkeit (typischerweise 9600) sowie OBIS-Kennzahlen von
den gewünschten Werten. Es können bis zu 6 OBIS-Kennzahlen angegeben werden, nach denen im
Datenstrom gesucht werden soll. Die gefilterten Werte werden gegebenenfalls am Ausgang mit
dem gleichen Index ausgegeben.

Einheiten der Daten werden nicht ausgegeben, um die Verarbeitung nicht zu erschweren.

Eingänge
========

E1 - E6 (obis1 - obis6)
-------------------------------------
OBIS-Kennzahlen (nicht benötigte Eingänge leer lassen)

Die vom Zähler gelieferten Datensätze hängen vom Zähler und seiner Konfiguration ab. Manche
geben nur das Nötigste aus, manche mehr. Typischerweise werden folgende Daten ausgegeben:
OBIS-Kennzahl  -   Datentyp
1-0:1.8.0*255  -   Wirkarbeit, Bezug kumuliert
1-0:1.8.1*255  -   Wirkarbeit Tarif 1, Bezug
1-0:1.8.2*255  -   Wirkarbeit Tarif 2, Bezug
1-0:16.7.0*255 -   aktuelle Wirkleistung, Bezug kumuliert

E7: serieller Port, an dem der Lesekopf angeschlossen ist (z.B. /dev/ttyUSB0)
E8: Datenrate des Zählers (typischerweise 9600)
E9: LBS-Aktivierung: 0 - inaktiv, 1 - aktiv
E10: Debug-Trace: 0 - inaktiv, 1 - aktiv

Ausgänge
========
A1 - A6 (value1 - value6)
--------------------------------------
eHZ-Daten, die der OBIS-Kennzahl am Eingang mit dem gleichen Index entsprechen.

Versionen
=========
1.6 (SirSydom <com@sirsydom.de> 2020-03-01
- -icrnl -inlcr hinzugefügt

1.5 (SirSydom <com@sirsydom.de> 2020-02-22
- ocrnl hinzugefügt

1.4 (SirSydom <com@sirsydom.de>
- Wartezeiten und Retries verändert, um den Aufbau einer Queue im Serial Stream zu vermeiden (führt zu Delays bei den gelieferten Werten bis hin zu Minuten)

1.3
- den Logging-Fehler behoben
- Verifikation von numerischen Werten verbessert

1.2
- Verbesserung der Stabilität und Performance
- den Code auf die neue API umgestellt

1.1
- Code bereinigt

1.0
- Initiale Version

###[/HELP]###

###[LBS]###
<?
function LB_LBSID($id)
{
    if ($args = logic_getInputs($id))
    {
        if (strlen($args[7]['value']) > 0 && $args[8]['value'] > 0 && $args[9]['value'] == 1)
        {
            // port and baudrate are set
            if  (logic_getVar($id, 1) == 0)
            {
                // start the LBS and its EXEC
                writeToCustomLog ("LBSLBSID_${id}", "INFO:", "SmlReader v1.3 started with parameters {$args[7]['value']}, {$args[8]['value']}");
                logic_setVar($id, 1, 1);
                logic_callExec(LBSID, $id);
            }
        }
    }
}
?>
###[/LBS]###

###[EXEC]###
<?
require(dirname(__FILE__)."/../../../../main/include/php/incl_lbsexec.php");

sql_connect();

set_time_limit(0);

$trace = 0;

function printLog($message)
{
    global $id;
    global $trace;
    if($trace)
    {
        writeToCustomLog("LBSLBSID_${id}", "INFO:", $message);
    }
}

class SmlReader {
    private $parseOffset = 0;
    private $units = array( 27 => 'W',    28 => 'VA',   29 => 'var',   30 => 'Wh',
                            31 => 'WAh',  32 => 'varh', 33 => 'A',     35 => 'V' );
    private $serialPort = "";
    private $serialFd = 0;
    private $baudrate = 0;
    private $obis = array();


    function __construct ($args)
    {
        $this->serialPort = $args[7]['value'];
        $this->baudrate = $args[8]['value'];
        foreach($args as $key => $val)
        {
            if(isset($args[$key]['value']))
            {
                $this->obis[$key] = $args[$key]['value'];
                //printLog("{$this->obis[$key]}");
            }
        }
    }

    private function readBlock($data)
    {
          $result = array();

          if($this->parseOffset >= count($data))
          {
              printLog("Requested more bytes than available.");
              return;
          }

          // decode the type and length fields
          $byte = $data[$this->parseOffset];
          $type = ($byte & 0x70) >> 4;
          $ext = $byte & 0x80;
          $len = $byte & 0x0f;

          //printLog("offset {$this->parseOffset}, type {$type}, len {$len}");

          $this->parseOffset++;

          if($ext > 0)
          {
              // more bytes to append
              $byte = $data[$this->parseOffset];
              $len = ($len << 4) + ($byte & 15) - 1;
              printLog("ext: offset {$this->parseOffset}, len {$len}");
              $this->parseOffset++;
          }

          // actual payload length
          $len--;

          if($len == 0)
          {
              // empty value
              return;
          }

          if(($this->parseOffset + $len) >= count($data))
          {
              $diff = count($data) - $this->parseOffset;
              printLog("Trying to read {$len} bytes, but only have {$diff}.");
              return;
          }

          if($type == 0)
          {
              // octet string
              $result = array_slice($data, $this->parseOffset, $len);
          }

          else if($type == 5 || $type == 6)
          {
              // signed / unsigned integers
              // collect the bytes in big-endian order
              // since the number of bytes might be arbitrary PHP unpack() cannot be used

              $result = (int)$data[$this->parseOffset];
              for($i = 1; $i < $len; $i++)
              {
                  $result = $result * 256 + $data[$this->parseOffset + $i];
              }

              if(($type == 5) && ($data[$this->parseOffset] > 127))
              {
                  // signed and negative, update the results
                  $result = $result - pow(256, $len);
              }
          }

          else if($type == 7)
          {
              // parse a list of entries

              $result = array();
              foreach(range(0, $len) as $i)
              {
                  $d = $this->readBlock($data);
                  if(!is_array($d))
                  {
                      $d = array($d);
                  }
                  $result = array_merge($result, $d);
              }
              return $result;
          }
          else
          {
              printLog("Unknown field type {$type}");
          }

          $this->parseOffset += $len;

          return $result;
    }

    public function parse(&$data)
    {
          $result = array();
          $headerSize = 7;
          $this->parseOffset = 0;
          while( $this->parseOffset < count($data)-$headerSize)
          {

              // search for SML list entries starting with OBIS enclosed by 0x77 0x07 and 0xff
              if(($data[$this->parseOffset] == 0x77) &&
                 ($data[$this->parseOffset + 1] == 0x07) &&
                 ($data[$this->parseOffset + $headerSize] == 0xff))
              {
                  $packetstart = $this->parseOffset;
                  $this->parseOffset++;
                  try
                  {
                      $entry = array();

                      // attempt to read the entries in the specified order
                      $entry['objName']   = $this->readBlock($data);
                      $entry['status']    = $this->readBlock($data);
                      $entry['valTime']   = $this->readBlock($data);
                      $entry['unit']      = $this->readBlock($data);
                      $entry['scaler']    = $this->readBlock($data);
                      $entry['value']     = $this->readBlock($data);
                      $entry['signature'] = $this->readBlock($data);

                      // prepare output
                      $objName = $entry['objName'];
                      $entry['obis'] = "{$objName[0]}-{$objName[1]}:{$objName[2]}.{$objName[3]}.{$objName[4]}*{$objName[5]}";

                      if(is_numeric($entry['value']) && is_numeric($entry['scaler']))
                      {
                          // seems to be a numeric value
                          $entry['dispValue'] = $entry['value'] * pow(10, $entry['scaler']);
                          if(isset($this->units[$entry['unit']]))
                          {
                              // unit is present, append it
                              //$entry['dispValue'] = "{$entry['dispValue']} {$this->units[$entry['unit']]}";;
                          }
                      }
                      else
                      {
                          if(is_array($entry['value']))
                          {
                              // convert array values into a hex string
                              $tmp = "";
                              foreach($entry['value'] as $i)
                              {
                                  $tmp = $tmp . sprintf("%02x ", $i);
                              }
                              $entry['dispValue'] = $tmp;
                          }
                          else
                          {
                              // just copy the original value
                              $entry['dispValue'] = $entry['value'];
                          }
                      }

                      $result[$entry['obis']] = $entry;
                  }
                  catch(Exception $e)
                  {
                      if($this->parseOffset < count($data) - 1)
                      {
                          // parse error occured
                          $this->parseOffset = $packetstart + $headerSize - 1;
                      }
                  }
              }
              else
              {
                  $this->parseOffset++;
              }
          }

          return $result;
    }

    private function readMsg()
    {
        $ret = "";

        if($this->serialFd)
        {
            $length = 0;
            $retry = 0;
            $timeout = 1000;
            $startFound = 0;
            $head = "\x1b\x1b\x1b\x1b\1\1\1\1";
            $tail = "\x1b\x1b\x1b\x1b\x1a";

            while(1)
            {
                $tmp = fread($this->serialFd, 1);
                $stat = fstat($this->serialFd);
                if($stat['nlink'] == 0)
                {
                  printLog("Device disconnected");
                  fclose($this->serialFd);
                  return -1;
                }

                if(strlen($tmp) > 0)
                {
                    $retry = 0;

                    if(($length == 0) && ($tmp != "\x1b"))
                    {
                      continue;
                    }

                    $ret = $ret . $tmp;
                    $length++;

                    if($startFound == 0)
                    {
                      // no message start found yet
                      if($length == 8)
                      {
                        if($ret == $head)
                        {
                          // start sequence found
                          $startFound = 1;
                        }
                        else
                        {
                          // not a start sequence, discard
                          $length = 0;
                          $ret = "";
                        }
                      }
                    }

                    if($length > 100)
                    {
                      $tmp = substr($ret, $length - 8, 5);
                      if($tmp == $tail)
                      {
                        // message body found
                        break;
                      }
                    }
                }
                else
                {
                    $retry++;
                    if($retry > 15)
                    {
                      // exceeded maximum attempts
                      return "";
                    }
                    else
                    {
                      // sleep
                      usleep($timeout*100);
                    }
                }
            }
        }

        return $ret;
    }

    private function connect()
    {
        printLog("Connecting to {$this->serialPort}");

        // set the baudrate
        // without additional packages this is the only method to control the tty device
        system("stty -F {$this->serialPort} {$this->baudrate} cs8 ignbrk -brkint -imaxbel -opost -onlcr -ocrnl -icrnl -inlcr -isig -icanon -iexten -echo -echoe -echok -echoctl -echoke noflsh -ixon -crtscts > /dev/nulli 2>&1");

        // open the port and set it to non-blocking
        $this->serialFd = fopen($this->serialPort, "rb");
        stream_set_blocking($this->serialFd, 0);
    }

    public function mainLoop()
    {
        global $id;

        $this->connect();
        while(1)
        {
            $retryCount = 0;
            while($retryCount < 10)
            {
                if ($args = logic_getInputs($id))
                {
                    if($args[9]['value'] != 1)
                    {
                        // LBS disabled, exit main loop
                        fclose($this->serialFd);
                        return;
                    }
                }

                $retryCount++;
                try
                {
                    $tmp = $this->readMsg();
                    $len = strlen($tmp);
                    printLog("Read {$len} bytes.");

                    if($tmp == -1)
                    {
                      break;
                    }

                    if(strlen($tmp))
                    {
                        $retryCount = 0;
                        // create a 0-based array (unpack returns a 1-based array by default)
                        $tmp = array_merge(unpack("C*", $tmp));
                        $ret = $this->parse($tmp);

                        foreach($ret as $i => $val)
                        {
                            printLog("OBIS {$val['obis']}: {$val['dispValue']}");
                        }
                        foreach($this->obis as $key => $val)
                        {
                            if(isset($ret[$val]) && isset($ret[$val]['dispValue']))
                            {
                                // input key is set and the value is available
                                logic_setOutput($id, $key, $ret[$val]['dispValue']);
                            }

                        }
                    }
                }
                catch (Exception $e)
                {
                    printLog("Caught exception in the main loop.");
                }

                usleep(500000);
            }

            // disconnect
            printLog("No data received, attempting to reconnect");
            fclose($this->serialFd);

            sleep(1);

            $this->connect();
        }
    }
}

if ($args = logic_getInputs($id))
{
    // set the debug trace flag as provided
    global $trace;
    $trace = $args[10]['value'];
    printLog("Inputs: {$args[7]['value']} {$args[8]['value']}");

    $smlReader = new SmlReader($args);
    $smlReader->mainLoop();

    logic_setVar($id, 1, 0);
    printLog("SmlReader terminated");
}

sql_disconnect();
?>
###[/EXEC]###
