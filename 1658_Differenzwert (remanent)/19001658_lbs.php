###[DEF]###
[name		=Differenzwert (remanent) LBS1658 V0.10	]

[e#1 TRIGGER=Trigger/Stop #init=INIT		]
[e#2		=Messwert #init=INIT			]
[e#3		=Messwertkorrektur #init=INIT			]

[a#1		=&Delta;Wert					]
[a#3		=Archiv							]

[v#1	remanent	=						]
###[/DEF]###


###[HELP]###
Dieser Baustein berechnet die Differenz von nacheinander eintreffenden Werten, z.B. um die Differenz eines Zählerstandes in Abhängigkeit eines Ereignisses zu ermitteln.

Ein Telegramm &ne;0 an E1 startet die Messung, bzw. startet die Messung neu:
A1 wird auf 0 gesetzt. Der Wert an E2 wird als Referenzwert intern gespeichert.
A3 wird auf den Messwert der letzten Differenzmessung gesetzt (nur beim Neustart / Stoppen).
Jedes eintreffende Telegramm an E2 während einer Messung (E1&ne;0) führt zu einer Aktualisierung von A1 (Wertdifferenz).

Ein Telegramm =0 an E1 beendet die Messung:
A1 bleibt unverändert, da eine Wertänderung an E2 während der Messung A1 bereits entsprechend gesetzt hat.
A3 wird auf den Messwert der letzten Differenzmessung gesetzt.


Hinweise:
Telegramme =0 an E1 werden ignoriert, wenn zuvor keine Messung mit einem Telegramm &ne;0 an E1 gestartet wurde.

 
Achtung:
Eine Aktualisierung von A1 erfolgt nur beim Starten und Stoppen einer Messung und bei eintreffenden Telegrammen an E2 während einer Messung. Es erfolgt <i>keine</i> zyklische Änderung von A1!
 
 
E1: Starten (&ne;0) bzw. Stoppen (=0) einer Messung (Achtung: um unerwünschte Effekte bei der Initialisierung zu unterbinden, sollte E1 mit 'INIT' initialisiert werden(default))
E2: Messwert (nummerisch), dessen Differenz berechnet werden soll (z.B. ein Zählerstand) (Achtung: um unerwünschte Effekte bei der Initialisierung zu unterbinden, sollte E2 mit 'INIT' initialisiert werden(default))
E3: Ein Telegram setzt den Startwert von E2 auf den Wert von E3. 
A1: Messwert-Differenz (nummerisch): wird beim Start auf 0 gesetzt, dann bei jedem eintreffenden Telegramm an E2 auf die Wertdifferenz, beim Beenden der Messung erfolgt keine Änderung (Bei negativem A1 wird kein Wert ausgegeben)
A3: Letzter Differenzwert beim Stoppen oder Neustarten einer Messung (z.B. zur Archivierung) (Bei negativem A3 wird kein Wert ausgegeben)

Versions:
V0.10	2021-02-04	SirSydom/BigBear2nd

Open Issues:


Author:
SirSydom - com@sirsydom.de
Copyright (c) 2021 SirSydom

Github:
https://github.com/SirSydom/edomi_LBS_sirsydom/releases/tag/19001658_V0.10

Links:



Contributions:

###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id)
{
	if (($E=logic_getInputs($id)) && ($V=logic_getVars($id))) {
		
		//Messwert korrigieren 
		if ($E[3]['refresh']==1 && $E[3]['value']!=='INIT' ) {
			logic_setVar($id,1,$E[3]['value']);
		}
		
		//Differenzen ausgeben
		if (!isEmpty($V[1]) && $E[2]['refresh']==1 && $E[2]['value']!=='INIT' ) 
		{
			$out = $E[2]['value']-$V[1];
			if ($out >= 0)
			{
				logic_setOutput($id,1,$out);
			}
		}
	
		if ($E[1]['refresh']==1)
		{	// neues trigger telegram
			if ($E[1]['value']==0 && $E[1]['value']!=='INIT')
			{	//Stop
				if (!isEmpty($V[1]))
				{
					logic_setOutput($id,3,$E[2]['value']-$V[1]);
					logic_setVar($id,1,null);
				}
				else
				{
					//### ignorieren bzw. resetten
				}
			}
			else if ($E[1]['value']==1)
			{
				//Start
				// variablen schreiben, ausgänge neu setzen
				if(!isEmpty($V[1])) // Neustart
				{
					$out = $E[2]['value']-$V[1];
					if ($out >= 0)
					{
						logic_setOutput($id,3,$out);
					}
				}
				logic_setVar($id,1,$E[2]['value']);
				logic_setOutput($id,1,0);
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
