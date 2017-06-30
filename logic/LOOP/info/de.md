<!--
  - @file de.md
  -
  - @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
  -
  - @package OSTEPU (https://github.com/ostepu/ostepu-core)
  - @since 0.3.4
  -
  - @author Till Uhlig <till.uhlig@student.uni-halle.de>
  - @date 2015,2017
 -->

Die LOOP Komponente wird beim Erstellen von Übungsserien als Verarbeitung verwendet, dabei bietet sie im wesentlichen die Möglichkeit Java Einsendungen zu compilieren und im Fehlerfall abzulehnen.

## Eingänge
---------------

|||
| :----------- |:-----: |
|Befehl| post<br>/process|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Befehl| post<br>/postprocess|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Befehl| post<br>/course|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Befehl| delete<br>/course/:courseid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Befehl| get<br>/link/exists/course/:courseid|
|Eingabetyp| -|
|Ausgabetyp| -|

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

|||
| :----------- |:-----: |
|Befehl| get<br>/start/:count|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Befehl| get<br>/compute/:count|
|Eingabetyp| -|
|Ausgabetyp| -|


## Ausgänge
---------------

||pdf|
| :----------- |:-----: |
|Ziel| FSPdf|
|Befehl| POST<br>/pdf|
|Beschreibung| für den Befehl pdf|

||postProcess|
| :----------- |:-----: |
|Ziel| DBProcessList|
|Befehl| POST<br>/process|
|Beschreibung| für den Befehl postProcess|

||deleteProcess|
| :----------- |:-----: |
|Ziel| DBProcessList|
|Befehl| DELETE<br>/process/process/:processid|
|Beschreibung| für den Befehl deleteProcess|

||getProcess|
| :----------- |:-----: |
|Ziel| DBProcessList|
|Befehl| GET<br>/process/course/:courseid/component/:componentid|
|Beschreibung| für den Befehl getProcess|

||postCourse|
| :----------- |:-----: |
|Ziel| DBOOP|
|Befehl| POST<br>/course|
|Beschreibung| für den Befehl postCourse|

||deleteCourse|
| :----------- |:-----: |
|Ziel| DBOOP|
|Befehl| DELETE<br>/course/:courseid|
|Beschreibung| für den Befehl deleteCourse|

||postTestcase|
| :----------- |:-----: |
|Ziel| DBOOP|
|Befehl| POST<br>/insert|
|Beschreibung| für den Befehl postTestcase|

||popTestcase|
| :----------- |:-----: |
|Ziel| DBOOP|
|Befehl| GET<br>/pop|
|Beschreibung| für den Befehl popTestcase|

||editTestcase|
| :----------- |:-----: |
|Ziel| DBOOP|
|Befehl| POST<br>/testcase/testcase/:testcaseid|
|Beschreibung| für den Befehl editTestcase|

||getTestcase|
| :----------- |:-----: |
|Ziel| DBOOP|
|Befehl| GET<br>/testcase/submission/:sid/course/:cid|
|Beschreibung| für den Befehl getTestcase|

||getExercise|
| :----------- |:-----: |
|Ziel| DBExercise|
|Befehl| GET<br>/exercise/exercise/:eid/nosubmission|
|Beschreibung| für den Befehl getExercise|

||marking|
| :----------- |:-----: |
|Ziel| LMarking|
|Befehl| POST<br>/marking|
|Beschreibung| für den Befehl marking|


## Anbindungen
---------------

|Ausgang|extension|
| :----------- |:-----: |
|Ziel| LExtension|

|Ausgang|request|
| :----------- |:-----: |
|Ziel| CLocalObjectRequest|
|Beschreibung| damit LOOP als lokales Objekt aufgerufen werden kann|

|Ausgang|request|
| :----------- |:-----: |
|Ziel| CHelp|
|Beschreibung| hier werden Hilfedateien beim zentralen Hilfesystem angemeldet, sodass sie über ihre globale Adresse abgerufen werden können|

|Ausgang|postPlatform|
| :----------- |:-----: |
|Ziel| CInstall|
|Beschreibung| der Installationsassistent soll uns bei der Plattforminstallation aufrufen|


Stand 30.06.2017
