Die FSZip erstellt ZIP-Archive. Diese können temporär oder dauerhaft im Dateisystem hinterlegt werden.

## Eingänge
---------------

||addZipPermanent|
| :----------- |:-----: |
|Beschreibung| erzeugt eine ZIP und speichert diese im Dateisystem|
|Befehl| POST<br>/zip|
|Eingabetyp| File|
|Ausgabetyp| File|

||addZipTemporary|
| :----------- |:-----: |
|Beschreibung| diese ZIP wird nur für den aktuellen Aufruf erzeugt und zurückgegeben|
|Befehl| POST<br>/zip/:filename|
|Eingabetyp| File|
|Ausgabetyp| binary|

||deleteZip|
| :----------- |:-----: |
|Beschreibung| löscht ein zuvor erzeugtes Archiv|
|Befehl| DELETE<br>/zip/:a/:b/:c/:file|
|Eingabetyp| -|
|Ausgabetyp| File|

||getZipData|
| :----------- |:-----: |
|Beschreibung| liefert die Daten rund um das Archiv (Größe, Speicherort, etc.)|
|Befehl| GET<br>/zip/:a/:b/:c/:file|
|Eingabetyp| -|
|Ausgabetyp| File|

||getZipDocument|
| :----------- |:-----: |
|Beschreibung| liefert den Dateiinhalt des Archivs|
|Befehl| GET<br>/zip/:a/:b/:c/:file/:filename|
|Eingabetyp| -|
|Ausgabetyp| binary|

||addPlatform|
| :----------- |:-----: |
|Beschreibung| installiert die zugehörige Tabelle und die Prozeduren für diese Plattform|
|Befehl| POST<br>/platform|
|Eingabetyp| Platform|
|Ausgabetyp| Platform|

||deletePlatform|
| :----------- |:-----: |
|Beschreibung| entfernt die Komponente und ihre installierten Bestandteile aus der Plattform|
|Befehl| DELETE<br>/platform|
|Eingabetyp| -|
|Ausgabetyp| Platform|

||getExistsPlatform|
| :----------- |:-----: |
|Beschreibung| prüft, ob die Tabelle und die Prozeduren existieren und die Komponente generell vollständig installiert ist|
|Befehl| GET<br>/link/exists/platform|
|Eingabetyp| -|
|Ausgabetyp| Platform|

||getApiProfiles|
| :----------- |:-----: |
|Beschreibung| liefert `GateProfile`-Objekte, welche unsere Befehle in die Standardprofile von CGate einsortieren|
|Befehl| GET<br>/api/profiles|
|Eingabetyp| -|
|Ausgabetyp| GateProfile|


## Anbindungen
---------------

|Ausgang|request|
| :----------- |:-----: |
|Ziel| CLocalObjectRequest|
|Beschreibung| damit FSZip als lokales Objekt aufgerufen werden kann|

|Ausgang|postPlatform|
| :----------- |:-----: |
|Ziel| CInstall|
|Beschreibung| der Installationsassistent soll uns bei der Plattforminstallation aufrufen|

|Ausgang|getComponentProfiles|
| :----------- |:-----: |
|Ziel| TApiConfiguration|
|Beschreibung| damit unsere Aufrufe in die Standardprofile der CGate einsortiert werden|


Stand 30.06.2017
