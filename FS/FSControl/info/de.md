<!--
  - @file de.md
  -
  - @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
  -
  - @package OSTEPU (https://github.com/ostepu/ostepu-core)
  - @since -
  -
  - @author Till Uhlig <till.uhlig@student.uni-halle.de>
  - @date 2017
  -
 -->

Die FSControl ist ein zentraler Ansprechpartner für alle Komponenten der Dateisystemschicht.

## Eingänge
---------------

|||
| :----------- |:-----: |
|Beschreibung| diese Anfrage behandelt eingehende POST-Anfragen|
|Befehl| POST<br>/:data+|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Beschreibung| diese Anfrage behandelt eingehende PUT-Anfragen|
|Befehl| PUT<br>/:data+|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Beschreibung| diese Anfrage behandelt eingehende GET-Anfragen|
|Befehl| GET<br>/:data+|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Beschreibung| diese Anfrage behandelt eingehende DELETE-Anfragen|
|Befehl| DELETE<br>/:data+|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Befehl| INFO<br>/:data+|
|Eingabetyp| -|
|Ausgabetyp| -|


## Ausgänge
---------------


## Anbindungen
---------------

|Ausgang|request|
| :----------- |:-----: |
|Ziel| CLocalObjectRequest|
|Beschreibung| damit FSControl als lokales Objekt aufgerufen werden kann|


Stand 30.06.2017
