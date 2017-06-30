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

Die LSubmission bietet Aufrufe zum Handhaben von Einsendungen an (Submission's)

## Eingänge
---------------

|||
| :----------- |:-----: |
|Beschreibung| speichert eine Einsendung|
|Befehl| post<br>/submission|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Beschreibung| löscht eine Einsendung|
|Befehl| delete<br>/submission/submission/:submissionid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Beschreibung| erzeugt aus den Einsendungen der Nutzergruppe eine ZIP und gibt das Datei-Objekt zurück|
|Befehl| get<br>/submission/exercisesheet/:sheetid/user/:userid|
|Eingabetyp| -|
|Ausgabetyp| -|


## Ausgänge
---------------

||file|
| :----------- |:-----: |
|Ziel| LFile|
|Befehl| POST<br>/file|
|Beschreibung| für den Befehl file|

||submission|
| :----------- |:-----: |
|Ziel| DBSubmission|
|Befehl| GET<br>/submission/group/user/:userid/exercisesheet/:sheetid/selected|
|Beschreibung| für den Befehl submission|

||submission|
| :----------- |:-----: |
|Ziel| DBSubmission|
|Befehl| DELETE<br>/submission/:submissionid|
|Beschreibung| für den Befehl submission|

||submission|
| :----------- |:-----: |
|Ziel| DBSubmission|
|Befehl| POST<br>/submission|
|Beschreibung| für den Befehl submission|

||selectedSubmission|
| :----------- |:-----: |
|Ziel| DBSelectedSubmission|
|Befehl| PUT<br>/selectedsubmission/leader/:leaderid/exercise/:exerciseid|
|Beschreibung| für den Befehl selectedSubmission|

||selectedSubmission|
| :----------- |:-----: |
|Ziel| DBSelectedSubmission|
|Befehl| POST<br>/selectedsubmission|
|Beschreibung| für den Befehl selectedSubmission|

||zip|
| :----------- |:-----: |
|Ziel| FSZip|
|Befehl| POST<br>/zip/:zipname|
|Beschreibung| für den Befehl zip|


## Anbindungen
---------------

|Ausgang|request|
| :----------- |:-----: |
|Ziel| CLocalObjectRequest|
|Beschreibung| damit LSubmission als lokales Objekt aufgerufen werden kann|


Stand 30.06.2017
