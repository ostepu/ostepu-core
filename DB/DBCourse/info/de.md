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

Die DBCourse ermöglicht den Zugriff auf die `Course` Tabelle der Datenbank, dabei sollen Veranstaltungen verwaltet werden. Dazu wird bei einem `POST /platform` Aufruf die nachstehende Tabelle erzeugt. Zu dieser Tabelle gehört die `Course` Datenstruktur.

| Spalte           | Struktur  | Beschreibung | Besonderheit |
| :------------    |:--------:| :---------------| -----: |
|C_id              |INT NOT NULL|Der Primärschlüssel einer Veranstaltung|UNIQUE,<br>AUTO_INCREMENT|
|C_name            |VARCHAR(120) NULL|Der Name der Veranstaltung|-|
|C_semester        |VARCHAR(60) NULL|Das Semester. Bsp.: SS 2015 und WS 2014/2015, dieses Format muss eingehalten werden|-|
|C_defaultGroupSize|INT NOT NULL DEFAULT 1|Die Standardgruppengröße, als Vorgabe beim erzeugen neuer Übungsserien|-|

## Eingänge
---------------

||getExistsPlatform|
| :----------- |:-----: |
|Beschreibung| prüft, ob die Tabelle und die Prozeduren existieren und die Komponente generell vollständig installiert ist|
|Befehl| GET<br>/link/exists/platform|
|Eingabetyp| -|
|Ausgabetyp| Platform|

||editCourse|
| :----------- |:-----: |
|Beschreibung| editiert einen Eintrag|
|Befehl| PUT<br>/course/course/:courseid|
|Eingabetyp| Course|
|Ausgabetyp| Course|
|||
||Patzhalter|
|Name|courseid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|eine Veranstaltungs ID (`Course`)|

||getUserCourses|
| :----------- |:-----: |
|Beschreibung| ermittelt die Veranstaltungen eines Nutzers|
|Befehl| GET<br>/course/user/:userid|
|Eingabetyp| -|
|Ausgabetyp| Course|
|||
||Patzhalter|
|Name|userid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID eines Nutzers oder ein Nuzername (`User`)|

||addCourse|
| :----------- |:-----: |
|Beschreibung| fügt DBCourse zur Veranstaltung hinzu bzw. fügt eine neue Veranstaltung ein|
|Befehl| POST<br>/course|
|Eingabetyp| Course|
|Ausgabetyp| Course|

||postSamples|
| :----------- |:-----: |
|Beschreibung| erzeugt Zufallsdaten (courseAmount = Anzahl der Veranstaltungen, userAmount = Anzahl der Nutzer), anhand der Vorgabe|
|Befehl| POST<br>/samples/course/:courseAmount/user/:userAmount|
|Eingabetyp| -|
|Ausgabetyp| Query|

||getCourse|
| :----------- |:-----: |
|Beschreibung| liefert einen einzelnen Eintrag|
|Befehl| GET<br>/course/course/:courseid|
|Eingabetyp| -|
|Ausgabetyp| Course|
|||
||Patzhalter|
|Name|courseid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|eine Veranstaltungs ID (`Course`)|

||getAllCourses|
| :----------- |:-----: |
|Beschreibung| liefert alle Einträge|
|Befehl| GET<br>/course|
|Eingabetyp| -|
|Ausgabetyp| Course|

||deletePlatform|
| :----------- |:-----: |
|Beschreibung| entfernt die Komponente und ihre installierten Bestandteile aus der Plattform|
|Befehl| DELETE<br>/platform|
|Eingabetyp| -|
|Ausgabetyp| Platform|

||deleteCourse|
| :----------- |:-----: |
|Beschreibung| entfernt die Komponente aus der Veranstaltung bzw. löscht den Eintrag|
|Befehl| DELETE<br>/course/course/:courseid|
|Eingabetyp| -|
|Ausgabetyp| Course|
|||
||Patzhalter|
|Name|courseid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|eine Veranstaltungs ID (`Course`)|

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

||editCourse|
| :----------- |:-----: |
|Ziel| DBQueryWrite|
|Befehl| POST<br>/query|
|Beschreibung| für den Befehl editCourse|

||deleteCourse|
| :----------- |:-----: |
|Ziel| DBQueryWrite|
|Befehl| POST<br>/query|
|Beschreibung| für den Befehl deleteCourse|

||addCourse|
| :----------- |:-----: |
|Ziel| DBQueryWrite|
|Befehl| POST<br>/query|
|Beschreibung| für den Befehl addCourse|

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

||postSamples|
| :----------- |:-----: |
|Ziel| DBQueryWrite|
|Befehl| POST<br>/query|
|Beschreibung| für den Befehl postSamples|

||getCourse|
| :----------- |:-----: |
|Ziel| DBQueryRead|
|Befehl| GET<br>/query/procedure/DBCourseGetCourse/:profile/:exerciseSheetProfile/:settingProfile/:courseid|
|Beschreibung| für den Befehl getCourse|

||getAllCourses|
| :----------- |:-----: |
|Ziel| DBQueryRead|
|Befehl| GET<br>/query/procedure/DBCourseGetAllCourses/:profile/:exerciseSheetProfile|
|Beschreibung| für den Befehl getAllCourses|

||getUserCourses|
| :----------- |:-----: |
|Ziel| DBQueryRead|
|Befehl| GET<br>/query/procedure/DBCourseGetUserCourses/:profile/:exerciseSheetProfile/:userid|
|Beschreibung| für den Befehl getUserCourses|

||getExistsPlatform|
| :----------- |:-----: |
|Ziel| DBQueryRead|
|Befehl| GET<br>/query/procedure/DBCourseGetExistsPlatform/:profile|
|Beschreibung| für den Befehl getExistsPlatform|

||getSamplesInfo|
| :----------- |:-----: |
|Ziel| DBQueryRead|
|Befehl| GET<br>/query/procedure/DBCourseGetExistsPlatform/:profile|
|Beschreibung| für den Befehl getSamplesInfo|


## Anbindungen
---------------

|Ausgang|request|
| :----------- |:-----: |
|Ziel| CLocalObjectRequest|
|Beschreibung| damit DBCourse als lokales Objekt aufgerufen werden kann|

|Ausgang|postPlatform|
| :----------- |:-----: |
|Ziel| CInstall|
|Beschreibung| der Installationsassistent soll uns bei der Plattforminstallation aufrufen|

|Ausgang|postSamples|
| :----------- |:-----: |
|Ziel| CInstall|
|Beschreibung| wir wollen bei Bedarf Beispieldaten erzeugen|

|Ausgang|postCourse|
| :----------- |:-----: |
|Ziel| LCourse|
|Beschreibung| wenn eine neue Veranstaltung angelegt wird, dann wollen wir auch aufgerufen werden|

|Ausgang|getDescFiles|
| :----------- |:-----: |
|Ziel| TDocuView|
|Beschreibung| die Entwicklerdokumentation soll unsere Beschreibungsdatei nutzen|

|Ausgang|getComponentProfiles|
| :----------- |:-----: |
|Ziel| TApiConfiguration|
|Beschreibung| damit unsere Aufrufe in die Standardprofile der CGate einsortiert werden|


Stand 30.06.2017
