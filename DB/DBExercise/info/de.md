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
 -->

Die DBExercise ermöglicht den Zugriff auf die `Exercise` Tabelle der Datenbank. Sie verwaltet die einzelnen Aufgaben einer Übungsserie. Dazu wird bei einem `POST /platform` Aufruf die nachstehende Tabelle erzeugt.

## Eingänge
---------------

||getExistsPlatform|
| :----------- |:-----: |
|Beschreibung| prüft, ob die Tabelle und die Prozeduren existieren und die Komponente generell vollständig installiert ist|
|Befehl| GET<br>/link/exists/platform|
|Eingabetyp| -|
|Ausgabetyp| Platform|

||deleteExercise|
| :----------- |:-----: |
|Beschreibung| entfernt eine Aufgabe|
|Befehl| DELETE<br>/exercise/exercise/:eid|
|Eingabetyp| -|
|Ausgabetyp| Exercise|
|||
||Patzhalter|
|Name|eid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID einer Aufgabe (`Exercise`)|

||editExercise|
| :----------- |:-----: |
|Beschreibung| editiert eine Aufgabe|
|Befehl| PUT<br>/exercise/exercise/:eid|
|Eingabetyp| Exercise|
|Ausgabetyp| Exercise|
|||
||Patzhalter|
|Name|eid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID einer Aufgabe (`Exercise`)|

||getExercise|
| :----------- |:-----: |
|Beschreibung| liefert eine einzelne Aufgabe (sub=nosubmission bedeutet ohne Einsendungen)|
|Befehl| GET<br>/exercise/exercise/:eid(/:sub)|
|Eingabetyp| -|
|Ausgabetyp| Exercise|
|||
||Patzhalter|
|Name|eid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID einer Aufgabe (`Exercise`)|

||getAllExercises|
| :----------- |:-----: |
|Beschreibung| liefert alle Aufgaben (sub=nosubmission bedeutet ohne Einsendungen)|
|Befehl| GET<br>/exercise(/:sub)|
|Eingabetyp| -|
|Ausgabetyp| Exercise|

||postSamples|
| :----------- |:-----: |
|Beschreibung| erzeugt Zufallsdaten (courseAmount = Anzahl der Veranstaltungen, userAmount = Anzahl der Nutzer), anhand der Vorgabe|
|Befehl| POST<br>/samples/course/:courseAmount/user/:userAmount|
|Eingabetyp| -|
|Ausgabetyp| Query|

||getSheetExercises|
| :----------- |:-----: |
|Beschreibung| liefert die Aufgaben einer Übungsserie (sub=nosubmission bedeutet ohne Einsendungen)|
|Befehl| GET<br>/exercise/exercisesheet/:esid(/:sub)|
|Eingabetyp| -|
|Ausgabetyp| Exercise|
|||
||Patzhalter|
|Name|esid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID einer Übungsserie (`ExerciseSheet`)|

||getCourseExercises|
| :----------- |:-----: |
|Beschreibung| liefert die Aufgaben einer Veranstaltung (sub=nosubmission bedeutet ohne Einsendungen)|
|Befehl| GET<br>/exercise/course/:courseid(/:sub)|
|Eingabetyp| -|
|Ausgabetyp| Exercise|
|||
||Patzhalter|
|Name|courseid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|eine Veranstaltungs ID (`Course`)|

||deletePlatform|
| :----------- |:-----: |
|Beschreibung| entfernt die Komponente und ihre installierten Bestandteile aus der Plattform|
|Befehl| DELETE<br>/platform|
|Eingabetyp| -|
|Ausgabetyp| Platform|

||addExercise|
| :----------- |:-----: |
|Beschreibung| fügt eine Aufgabe ein|
|Befehl| POST<br>/exercise|
|Eingabetyp| Exercise|
|Ausgabetyp| Exercise|

||getSamplesInfo|
| :----------- |:-----: |
|Beschreibung| liefert die Bezeichner der betroffenen Tabellen|
|Befehl| GET<br>/samples|
|Eingabetyp| -|
|Ausgabetyp| -|

||addPlatform|
| :----------- |:-----: |
|Beschreibung| installiert die zugehörige Tabelle und die Prozeduren für diese Plattform|
|Befehl| POST<br>/platform|
|Eingabetyp| Platform|
|Ausgabetyp| Platform|

||getApiProfiles|
| :----------- |:-----: |
|Beschreibung| liefert `GateProfile`-Objekte, welche unsere Befehle in die Standardprofile von CGate einsortieren|
|Befehl| GET<br>/api/profiles|
|Eingabetyp| -|
|Ausgabetyp| GateProfile|


## Ausgänge
---------------

||editExercise|
| :----------- |:-----: |
|Ziel| DBQueryWrite|
|Befehl| POST<br>/query|
|Beschreibung| für den Befehl editExercise|

||deleteExercise|
| :----------- |:-----: |
|Ziel| DBQueryWrite|
|Befehl| POST<br>/query|
|Beschreibung| für den Befehl deleteExercise|

||addExercise|
| :----------- |:-----: |
|Ziel| DBQueryWrite|
|Befehl| POST<br>/query|
|Beschreibung| für den Befehl addExercise|

||postSamples|
| :----------- |:-----: |
|Ziel| DBQueryWrite|
|Befehl| POST<br>/query|
|Beschreibung| für den Befehl postSamples|

||deletePlatform|
| :----------- |:-----: |
|Ziel| DBQuerySetup|
|Befehl| POST<br>/query|
|Beschreibung| für den Befehl deletePlatform|

||addPlatform|
| :----------- |:-----: |
|Ziel| DBQuerySetup|
|Befehl| POST<br>/query|
|Beschreibung| für den Befehl addPlatform|

||getExercise|
| :----------- |:-----: |
|Ziel| DBQueryRead|
|Befehl| GET<br>/query/procedure/DBExerciseGetExercise/:eid/:sub|
|Beschreibung| für den Befehl getExercise|

||getAllExercises|
| :----------- |:-----: |
|Ziel| DBQueryRead|
|Befehl| GET<br>/query/procedure/DBExerciseGetAllExercises/:sub|
|Beschreibung| für den Befehl getAllExercises|

||getCourseExercises|
| :----------- |:-----: |
|Ziel| DBQueryRead|
|Befehl| GET<br>/query/procedure/DBExerciseGetCourseExercises/:courseid/:sub|
|Beschreibung| für den Befehl getCourseExercises|

||getSheetExercises|
| :----------- |:-----: |
|Ziel| DBQueryRead|
|Befehl| GET<br>/query/procedure/DBExerciseGetSheetExercises/:esid/:sub|
|Beschreibung| für den Befehl getSheetExercises|

||getExistsPlatform|
| :----------- |:-----: |
|Ziel| DBQueryRead|
|Befehl| GET<br>/query/procedure/DBExerciseGetExistsPlatform|
|Beschreibung| für den Befehl getExistsPlatform|


## Anbindungen
---------------

|Ausgang|request|
| :----------- |:-----: |
|Ziel| CLocalObjectRequest|
|Beschreibung| damit DBExercise als lokales Objekt aufgerufen werden kann|

|Ausgang|postPlatform|
| :----------- |:-----: |
|Ziel| CInstall|
|Beschreibung| der Installationsassistent soll uns bei der Plattforminstallation aufrufen|

|Ausgang|postSamples|
| :----------- |:-----: |
|Ziel| CInstall|
|Beschreibung| wir wollen bei Bedarf Beispieldaten erzeugen|

|Ausgang|getDescFiles|
| :----------- |:-----: |
|Ziel| TDocuView|
|Beschreibung| die Entwicklerdokumentation soll unsere Beschreibungsdatei nutzen|

|Ausgang|getComponentProfiles|
| :----------- |:-----: |
|Ziel| TApiConfiguration|
|Beschreibung| damit unsere Aufrufe in die Standardprofile der CGate einsortiert werden|


Stand 30.06.2017
