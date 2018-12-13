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

Die DBExerciseSheet ermöglicht den Zugriff auf die `EcerciseSheet` Tabelle der Datenbank. Sie verwaltet Übungsserien. Dazu wird bei einem `POST /platform` Aufruf die nachstehende Tabelle erzeugt.

| Themen |
| :- |
| [Befehle/Eingänge (Commands.json)](#eingaenge) |
| [Ausgänge (Component.json => Links)](#ausgaenge) |
| [Anbindungen (Component.json => Connector)](#anbindungen) |

## <a name='eingaenge'></a>Befehle/Eingänge (Commands.json)
Diese Befehle bietet diese Komponente als Aufruf an.

||deletePlatform|
| :----------- |:----- |
|Beschreibung| entfernt die Komponente und ihre installierten Bestandteile aus der Plattform|
|Befehl| DELETE /platform|
|Eingabetyp| -|
|Ausgabetyp| Platform|

||editExerciseSheet|
| :----------- |:----- |
|Beschreibung| editiert eine Übungsserie|
|Befehl| PUT /exercisesheet/exercisesheet/:esid|
|Eingabetyp| ExerciseSheet|
|Ausgabetyp| ExerciseSheet|
|||
||Patzhalter|
|Name|esid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID einer Übungsserie (`ExerciseSheet`)|

||getCourseSheets|
| :----------- |:----- |
|Beschreibung| ermittelt alle Übungsserien einer Veranstaltung|
|Befehl| GET /exercisesheet/course/:courseid(/:exercise)|
|Eingabetyp| -|
|Ausgabetyp| -|
|||
||Patzhalter|
|Name|courseid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|eine Veranstaltungs ID (`Course`)|

||addExerciseSheet|
| :----------- |:----- |
|Beschreibung| fügt eine neue Übungsserie ein|
|Befehl| POST /exercisesheet|
|Eingabetyp| ExerciseSheet|
|Ausgabetyp| ExerciseSheet|

||postSamples|
| :----------- |:----- |
|Beschreibung| erzeugt Zufallsdaten (courseAmount = Anzahl der Veranstaltungen, userAmount = Anzahl der Nutzer), anhand der Vorgabe|
|Befehl| POST /samples/course/:courseAmount/user/:userAmount|
|Eingabetyp| -|
|Ausgabetyp| Query|

||getExerciseSheetURL|
| :----------- |:----- |
|Beschreibung| liefert die Dateiadresse der Übungsserie (das Aufgabenblatt)|
|Befehl| GET /exercisesheet/exercisesheet/:esid/url|
|Eingabetyp| -|
|Ausgabetyp| -|
|||
||Patzhalter|
|Name|esid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID einer Übungsserie (`ExerciseSheet`)|

||getCourseSheetURLs|
| :----------- |:----- |
|Beschreibung| liefert die Dateiadressen der Übungsserien einer Veranstaltung|
|Befehl| GET /exercisesheet/course/:courseid/url|
|Eingabetyp| -|
|Ausgabetyp| -|
|||
||Patzhalter|
|Name|courseid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|eine Veranstaltungs ID (`Course`)|

||addPlatform|
| :----------- |:----- |
|Beschreibung| installiert die zugehörige Tabelle und die Prozeduren für diese Plattform|
|Befehl| POST /platform|
|Eingabetyp| Platform|
|Ausgabetyp| Platform|

||deleteExerciseSheet|
| :----------- |:----- |
|Beschreibung| entfernt eine Übungsserie|
|Befehl| DELETE /exercisesheet/exercisesheet/:esid|
|Eingabetyp| -|
|Ausgabetyp| ExerciseSheet|
|||
||Patzhalter|
|Name|esid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID einer Übungsserie (`ExerciseSheet`)|

||getSamplesInfo|
| :----------- |:----- |
|Beschreibung| liefert die Bezeichner der betroffenen Tabellen|
|Befehl| GET /samples|
|Eingabetyp| -|
|Ausgabetyp| -|

||getExerciseSheet|
| :----------- |:----- |
|Beschreibung| liefert eine einzelne Übungsserie anhand ihrer ID|
|Befehl| GET /exercisesheet/exercisesheet/:esid(/:exercise)|
|Eingabetyp| -|
|Ausgabetyp| -|
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
Wenn eine Komponente selbst noch Unteranfragen an andere Komponenten stellen möchte, dann werden diese über die `Ausgänge` bearbeitet.
Dabei kann ein Ausgang bereits auf eine Komponente gerichtet sein (`Ziel`) oder durch die Zielkomponente selbst angebunden werden (`Connector`)

||editExerciseSheet|
| :----------- |:----- |
|Ziel| DBQueryWrite|
|Befehl| POST /query|
|Beschreibung| für den Befehl editExerciseSheet|

||deleteExerciseSheet|
| :----------- |:----- |
|Ziel| DBQueryWrite|
|Befehl| POST /query|
|Beschreibung| für den Befehl deleteExerciseSheet|

||addExerciseSheet|
| :----------- |:----- |
|Ziel| DBQueryWrite|
|Befehl| POST /query|
|Beschreibung| für den Befehl addExerciseSheet|

||postSamples|
| :----------- |:----- |
|Ziel| DBQueryWrite|
|Befehl| POST /query|
|Beschreibung| für den Befehl postSamples|

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

||getCourseExercises|
| :----------- |:----- |
|Ziel| DBQueryRead|
|Befehl| GET /query/procedure/DBExerciseSheetGetCourseExercises/:courseid|
|Beschreibung| für den Befehl getCourseExercises|

||getCourseSheets|
| :----------- |:----- |
|Ziel| DBQueryRead|
|Befehl| GET /query/procedure/DBExerciseSheetGetCourseSheets/:courseid|
|Beschreibung| für den Befehl getCourseSheets|

||getCourseSheetURLS|
| :----------- |:----- |
|Ziel| DBQueryRead|
|Befehl| GET /query/procedure/DBExerciseSheetGetCourseSheetURLs/:courseid|
|Beschreibung| für den Befehl getCourseSheetURLS|

||getExerciseSheet|
| :----------- |:----- |
|Ziel| DBQueryRead|
|Befehl| GET /query/procedure/DBExerciseSheetGetExerciseSheet/:esid|
|Beschreibung| für den Befehl getExerciseSheet|

||getExerciseSheetURL|
| :----------- |:----- |
|Ziel| DBQueryRead|
|Befehl| GET /query/procedure/DBExerciseSheetGetExerciseSheetURL/:esid|
|Beschreibung| für den Befehl getExerciseSheetURL|

||getSheetExercises|
| :----------- |:----- |
|Ziel| DBQueryRead|
|Befehl| GET /query/procedure/DBExerciseSheetGetSheetExercises/:esid|
|Beschreibung| für den Befehl getSheetExercises|

||getExistsPlatform|
| :----------- |:----- |
|Ziel| DBQueryRead|
|Befehl| GET /query/procedure/DBExerciseSheetGetExistsPlatform|
|Beschreibung| für den Befehl getExistsPlatform|


## <a name='anbindungen'></a>Anbindungen (Component.json => Connector)
Eine Anbindung verlangt von einer anderen Komponente (`Ziel`) die Anbindung/Verbindung zu dieser Komponente.
Wenn eine Anbindung den aufzurufenden Befehl vorgibt, dann ist die Notation: METHODE URL (PRIORITÄT).

|Ausgang|request|
| :----------- |:----- |
|Ziel| CLocalObjectRequest|
|Beschreibung| damit DBExerciseSheet als lokales Objekt aufgerufen werden kann|

|Ausgang|postPlatform|
| :----------- |:----- |
|Ziel| CInstall|
|Beschreibung| der Installationsassistent soll uns bei der Plattforminstallation aufrufen|

|Ausgang|postSamples|
| :----------- |:----- |
|Ziel| CInstall|
|Beschreibung| wir wollen bei Bedarf Beispieldaten erzeugen|

|Ausgang|getDescFiles|
| :----------- |:----- |
|Ziel| TDocuView|
|Beschreibung| die Entwicklerdokumentation soll unsere Beschreibungsdatei nutzen|

|Ausgang|getComponentProfiles|
| :----------- |:----- |
|Ziel| TApiConfiguration|
|Beschreibung| damit unsere Aufrufe in die Standardprofile der CGate einsortiert werden|


Stand 25.07.2017
