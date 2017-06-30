<!--
  - @file de.md
  -
  - @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
  -
  - @package OSTEPU (https://github.com/ostepu/ostepu-core)
  - @since 0.3.4
  -
  - @author Till Uhlig <till.uhlig@student.uni-halle.de>
  - @date 2015
  -
 -->

Die DBCourseStatus ermöglicht den Zugriff auf die `CourseStatus` Tabelle der Datenbank. Sie verwaltet den Kurststatus der Nutzer zu den einzelnen Veranstaltungen (Beispiel: Admin, Student, Tutor). Dazu wird bei einem `POST /platform` Aufruf die nachstehende Tabelle erzeugt.

| Spalte        | Struktur  | Beschreibung | Besonderheit |
| :------       |:---------:| :------------| -----------: |
|C_id|INT NOT NULL| ein Verweis auf eine Veranstaltung |-|
|U_id|INT NOT NULL| ein Verweis auf ein Nutzerkonto |-|
|CS_status|INT NOT NULL DEFAULT 0| die Statuskennung (siehe CourseStatus::getStatusDefinition()) |-|

## Eingänge
---------------

||getExistsPlatform|
| :----------- |:-----: |
|Beschreibung| prüft, ob die Tabelle und die Prozeduren existieren und die Komponente generell vollständig installiert ist|
|Befehl| GET<br>/link/exists/platform|
|Eingabetyp| -|
|Ausgabetyp| Platform|

||editMemberRight|
| :----------- |:-----: |
|Beschreibung| editiert einen Eintrag|
|Befehl| PUT<br>/coursestatus/course/:courseid/user/:userid|
|Eingabetyp| User|
|Ausgabetyp| CourseStatus|
|||
||Patzhalter|
|Name|courseid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|eine Veranstaltungs ID (`Course`)|
|Name|userid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID eines Nutzers oder ein Nuzername (`User`)|

||getCourseRights|
| :----------- |:-----: |
|Beschreibung| liefert die Rechte einer Veranstaltung|
|Befehl| GET<br>/coursestatus/course/:courseid|
|Eingabetyp| -|
|Ausgabetyp| User|
|||
||Patzhalter|
|Name|courseid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|eine Veranstaltungs ID (`Course`)|

||addCourseMember|
| :----------- |:-----: |
|Beschreibung| fügt einen neuen Kurststatus ein (Achtung: es muss ein User-Objekt gebaut werden)|
|Befehl| POST<br>/coursestatus|
|Eingabetyp| User|
|Ausgabetyp| CourseStatus|

||postSamples|
| :----------- |:-----: |
|Beschreibung| erzeugt Zufallsdaten (courseAmount = Anzahl der Veranstaltungen, userAmount = Anzahl der Nutzer), anhand der Vorgabe|
|Befehl| POST<br>/samples/course/:courseAmount/user/:userAmount|
|Eingabetyp| -|
|Ausgabetyp| Query|

||getMemberRight|
| :----------- |:-----: |
|Beschreibung| liefert die Berechntigung eines Nutzers zu einer Veranstaltung|
|Befehl| GET<br>/coursestatus/course/:courseid/user/:userid|
|Eingabetyp| -|
|Ausgabetyp| User|
|||
||Patzhalter|
|Name|courseid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|eine Veranstaltungs ID (`Course`)|
|Name|userid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID eines Nutzers oder ein Nuzername (`User`)|

||getMemberRights|
| :----------- |:-----: |
|Beschreibung| ermittelt die Berechtigungen eines Nutzers (in allen Veranstaltungen)|
|Befehl| GET<br>/coursestatus/user/:userid|
|Eingabetyp| -|
|Ausgabetyp| User|
|||
||Patzhalter|
|Name|userid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID eines Nutzers oder ein Nuzername (`User`)|

||deletePlatform|
| :----------- |:-----: |
|Beschreibung| entfernt die Komponente und ihre installierten Bestandteile aus der Plattform|
|Befehl| DELETE<br>/platform|
|Eingabetyp| -|
|Ausgabetyp| Platform|

||removeCourseMember|
| :----------- |:-----: |
|Beschreibung| entfernt einen Nutzer aus einer Veranstaltung|
|Befehl| DELETE<br>/coursestatus/course/:courseid/user/:userid|
|Eingabetyp| -|
|Ausgabetyp| CourseStatus|
|||
||Patzhalter|
|Name|courseid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|eine Veranstaltungs ID (`Course`)|
|Name|userid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID eines Nutzers oder ein Nuzername (`User`)|

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

||editMemberRight|
| :----------- |:-----: |
|Ziel| DBQueryWrite|
|Befehl| POST<br>/query|
|Beschreibung| für den Befehl editMemberRight|

||removeCourseMember|
| :----------- |:-----: |
|Ziel| DBQueryWrite|
|Befehl| POST<br>/query|
|Beschreibung| für den Befehl removeCourseMember|

||addCourseMember|
| :----------- |:-----: |
|Ziel| DBQueryWrite|
|Befehl| POST<br>/query|
|Beschreibung| für den Befehl addCourseMember|

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

||getMemberRight|
| :----------- |:-----: |
|Ziel| DBQueryRead|
|Befehl| GET<br>/query/procedure/DBCourseStatusGetMemberRight/:courseid/:userid|
|Beschreibung| für den Befehl getMemberRight|

||getMemberRights|
| :----------- |:-----: |
|Ziel| DBQueryRead|
|Befehl| GET<br>/query/procedure/DBCourseStatusGetMemberRights/:userid|
|Beschreibung| für den Befehl getMemberRights|

||getCourseRights|
| :----------- |:-----: |
|Ziel| DBQueryRead|
|Befehl| GET<br>/query/procedure/DBCourseStatusGetCourseRights/:courseid|
|Beschreibung| für den Befehl getCourseRights|

||getExistsPlatform|
| :----------- |:-----: |
|Ziel| DBQueryRead|
|Befehl| GET<br>/query/procedure/DBUserGetExistsPlatform|
|Beschreibung| für den Befehl getExistsPlatform|

||getSamplesInfo|
| :----------- |:-----: |
|Ziel| DBQueryRead|
|Befehl| GET<br>/query/procedure/DBCourseStatusGetExistsPlatform|
|Beschreibung| für den Befehl getSamplesInfo|


## Anbindungen
---------------

|Ausgang|request|
| :----------- |:-----: |
|Ziel| CLocalObjectRequest|
|Beschreibung| damit DBCourseStatus als lokales Objekt aufgerufen werden kann|

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
