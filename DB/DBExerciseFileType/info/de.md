<!--
  - @file de.md
  -
  - @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
  -
  - @package OSTEPU (https://github.com/ostepu/ostepu-core)
  - @since 0.3.5
  -
  - @author Till Uhlig <till.uhlig@student.uni-halle.de>
  - @date 2015,2017
  -
 -->

Die DBExerciseFileType ermöglicht den Zugriff auf die `ExerciseFileType` Tabelle der Datenbank. Hier werden die erlaubten Dateitypen für den Dateiupload der Studenten hinterlegt. Dazu wird bei einem `POST /course` Aufruf die nachstehende Tabelle erzeugt.

| Themen |
| :- |
| [Befehle/Eingänge (Commands.json)](#eingaenge) |
| [Ausgänge (Component.json => Links)](#ausgaenge) |
| [Anbindungen (Component.json => Connector)](#anbindungen) |

## <a name='eingaenge'></a>Befehle/Eingänge (Commands.json)
Diese Befehle bietet diese Komponente als Aufruf an.

||addPlatform|
| :----------- |:----- |
|Beschreibung| installiert die zugehörige Tabelle und die Prozeduren für diese Plattform|
|Befehl| POST /platform|
|Eingabetyp| Platform|
|Ausgabetyp| Platform|

||deleteExerciseFileType|
| :----------- |:----- |
|Beschreibung| entfernt den Eintrag mit der genannten ID|
|Befehl| DELETE /exercisefiletype/exercisefiletype/:eftid|
|Eingabetyp| -|
|Ausgabetyp| ExerciseFileType|
|||
||Patzhalter|
|Name|eftid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID eines Dateityps für eine Aufgabe (`ExerciseFileType`)|

||editExerciseFileType|
| :----------- |:----- |
|Beschreibung| editiert einen Eintrag|
|Befehl| PUT /exercisefiletype/exercisefiletype/:eftid|
|Eingabetyp| ExerciseFileType|
|Ausgabetyp| ExerciseFileType|
|||
||Patzhalter|
|Name|eftid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID eines Dateityps für eine Aufgabe (`ExerciseFileType`)|

||getExerciseExerciseFileTypes|
| :----------- |:----- |
|Beschreibung| liefert alle Einträge zu einer Aufgabe|
|Befehl| GET /exercisefiletype/exercise/:eid|
|Eingabetyp| -|
|Ausgabetyp| ExerciseFileType|
|||
||Patzhalter|
|Name|eid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID einer Aufgabe (`Exercise`)|

||deleteExerciseSheetExerciseFileType|
| :----------- |:----- |
|Beschreibung| löscht alle Einträge einer Übungsserie|
|Befehl| DELETE /exercisefiletype/exercisesheet/:esid|
|Eingabetyp| -|
|Ausgabetyp| ExerciseFileType|
|||
||Patzhalter|
|Name|eftid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID eines Dateityps für eine Aufgabe (`ExerciseFileType`)|

||getExistsPlatform|
| :----------- |:----- |
|Beschreibung| prüft, ob die Tabelle und die Prozeduren existieren und die Komponente generell vollständig installiert ist|
|Befehl| GET /link/exists/platform|
|Eingabetyp| -|
|Ausgabetyp| Platform|

||addExerciseFileType|
| :----------- |:----- |
|Beschreibung| fügt einen neuen Eintrag hinzu|
|Befehl| POST /exercisefiletype|
|Eingabetyp| ExerciseFileType|
|Ausgabetyp| ExerciseFileType|

||getExerciseFileType|
| :----------- |:----- |
|Beschreibung| ruft einen einzelnen Eintrag ab|
|Befehl| GET /exercisefiletype/exercisefiletype/:eftid|
|Eingabetyp| -|
|Ausgabetyp| ExerciseFileType|
|||
||Patzhalter|
|Name|eftid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID eines Dateityps für eine Aufgabe (`ExerciseFileType`)|

||getAllExerciseFileTypes|
| :----------- |:----- |
|Beschreibung| liefert alle Einträge|
|Befehl| GET /exercisefiletype|
|Eingabetyp| -|
|Ausgabetyp| ExerciseFileType|

||deleteExerciseExerciseFileType|
| :----------- |:----- |
|Beschreibung| löscht alle Einträge zu einer Aufgabe|
|Befehl| DELETE /exercisefiletype/exercise/:eid|
|Eingabetyp| -|
|Ausgabetyp| ExerciseFileType|
|||
||Patzhalter|
|Name|eid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID einer Aufgabe (`Exercise`)|

||deletePlatform|
| :----------- |:----- |
|Beschreibung| entfernt die Komponente und ihre installierten Bestandteile aus der Plattform|
|Befehl| DELETE /platform|
|Eingabetyp| -|
|Ausgabetyp| Platform|

||getSheetExerciseFileTypes|
| :----------- |:----- |
|Beschreibung| liefert alle Einträge einer Übungsserie|
|Befehl| GET /exercisefiletype/exercisesheet/:esid|
|Eingabetyp| -|
|Ausgabetyp| ExerciseFileType|
|||
||Patzhalter|
|Name|esid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID einer Übungsserie (`ExerciseSheet`)|

||getApiProfiles|
| :----------- |:----- |
|Beschreibung| liefert `GateProfile`-Objekte, welche unsere Befehle in die Standardprofile von CGate einsortieren|
|Befehl| GET /api/profiles|
|Eingabetyp| -|
|Ausgabetyp| GateProfile|


## <a name='ausgaenge'></a>Ausgänge (Component.json => Links)
Wenn eine Komponente selbst noch Unteranfragen an anderen Komponenten stellen möchte, dann werden diese über die `Ausgänge` bearbeitet.
Dabei kann ein Ausgang bereits auf eine Komponente gerichtet sein (`Ziel`) oder durch die Zielkomponente selbst angebunden werden (`Connector`)

||editExerciseFileType|
| :----------- |:----- |
|Ziel| DBQueryWrite|
|Befehl| POST /query|
|Beschreibung| für den Befehl editExerciseFileType|

||deleteExerciseFileType|
| :----------- |:----- |
|Ziel| DBQueryWrite|
|Befehl| POST /query|
|Beschreibung| für den Befehl deleteExerciseFileType|

||deleteExerciseExerciseFileType|
| :----------- |:----- |
|Ziel| DBQueryWrite|
|Befehl| POST /query|
|Beschreibung| für den Befehl deleteExerciseExerciseFileType|

||deleteExerciseSheetExerciseFileType|
| :----------- |:----- |
|Ziel| DBQueryWrite|
|Befehl| POST /query|
|Beschreibung| für den Befehl deleteExerciseSheetExerciseFileType|

||addExerciseFileType|
| :----------- |:----- |
|Ziel| DBQueryWrite|
|Befehl| POST /query|
|Beschreibung| für den Befehl addExerciseFileType|

||deletePlatform|
| :----------- |:----- |
|Ziel| DBQuerySetup|
|Befehl| POST /query|
|Beschreibung| für den Befehl deletePlatform|

||addPlatform|
| :----------- |:----- |
|Ziel| DBQuerySetup|
|Befehl| POST /query|
|Beschreibung| für den Befehl addPlatform|

||getExerciseFileType|
| :----------- |:----- |
|Ziel| DBQueryRead|
|Befehl| GET /query/procedure/DBExerciseFileTypeGetExerciseFileType/:eftid|
|Beschreibung| für den Befehl getExerciseFileType|

||getAllExerciseFileTypes|
| :----------- |:----- |
|Ziel| DBQueryRead|
|Befehl| GET /query/procedure/DBExerciseFileTypeGetAllExerciseFileTypes|
|Beschreibung| für den Befehl getAllExerciseFileTypes|

||getExerciseExerciseFileTypes|
| :----------- |:----- |
|Ziel| DBQueryRead|
|Befehl| GET /query/procedure/DBExerciseFileTypeGetExerciseExerciseFileTypes/:eid|
|Beschreibung| für den Befehl getExerciseExerciseFileTypes|

||getSheetExerciseFileTypes|
| :----------- |:----- |
|Ziel| DBQueryRead|
|Befehl| GET /query/procedure/DBExerciseFileTypeGetSheetExerciseFileTypes/:esid|
|Beschreibung| für den Befehl getSheetExerciseFileTypes|

||getExistsPlatform|
| :----------- |:----- |
|Ziel| DBQueryRead|
|Befehl| GET /query/procedure/DBExerciseFileTypeGetExistsPlatform|
|Beschreibung| für den Befehl getExistsPlatform|


## <a name='anbindungen'></a>Anbindungen (Component.json => Connector)
Eine Anbindung verlangt von einer anderen Komponente (`Ziel`) die Anbindung/Verbindung zu dieser Komponente.
Wenn eine Anbindung den aufzurufenden Befehl vorgibt, dann ist die Notation: METHODE URL (PRIORITÄT).

|Ausgang|request|
| :----------- |:----- |
|Ziel| CLocalObjectRequest|
|Beschreibung| damit DBExerciseFileType als lokales Objekt aufgerufen werden kann|

|Ausgang|postPlatform|
| :----------- |:----- |
|Ziel| CInstall|
|Beschreibung| der Installationsassistent soll uns bei der Plattforminstallation aufrufen|

|Ausgang|getDescFiles|
| :----------- |:----- |
|Ziel| TDocuView|
|Beschreibung| die Entwicklerdokumentation soll unsere Beschreibungsdatei nutzen|

|Ausgang|getComponentProfiles|
| :----------- |:----- |
|Ziel| TApiConfiguration|
|Beschreibung| damit unsere Aufrufe in die Standardprofile der CGate einsortiert werden|


Stand 25.07.2017
