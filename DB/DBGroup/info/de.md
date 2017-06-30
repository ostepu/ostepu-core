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

Die DBGroup ermöglicht den Zugriff auf die `Group` Tabelle der Datenbank, dabei sollen Arbeitsgruppen verwaltet werden. Dazu wird bei einem `POST /platform` Aufruf die nachstehende Tabelle erzeugt. Zu dieser Tabelle gehört die `Group` Datenstruktur.

| Spalte        | Struktur  | Beschreibung | Besonderheit |
| :------       |:---------:| :------------| -----------: |
|U_id_leader|INT NOT NULL| ein Verweis auf ein Nutzerkonto, dem dieser Eintrag gehört |-|
|U_id_member|INT NOT NULL| ein Verweis auf ein Nutzerkonto, in dessen Gruppe dieser Nutzer ist |-|
|C_id|INT NULL| ein Verweis auf eine Veranstaltung |-|
|ES_id|INT NOT NULL| ein Verweis auf die zugehörige Übungsserie |-|

## Eingänge
---------------

||editGroup|
| :----------- |:-----: |
|Beschreibung| editiert einen Gruppeneintrag|
|Befehl| PUT<br>/group/user/:userid/exercisesheet/:esid|
|Eingabetyp| Group|
|Ausgabetyp| Group|
|||
||Patzhalter|
|Name|userid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID eines Nutzers oder ein Nuzername (`User`)|
|Name|esid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID einer Übungsserie (`ExerciseSheet`)|

||postSamples|
| :----------- |:-----: |
|Beschreibung| erzeugt Zufallsdaten (courseAmount = Anzahl der Veranstaltungen, userAmount = Anzahl der Nutzer), anhand der Vorgabe|
|Befehl| POST<br>/samples/course/:courseAmount/user/:userAmount|
|Eingabetyp| -|
|Ausgabetyp| Query|

||getUserSheetGroup|
| :----------- |:-----: |
|Beschreibung| ermittelt die Gruppe eines Nutzers für eine Übungsserie|
|Befehl| GET<br>/group/user/:userid/exercisesheet/:esid|
|Eingabetyp| -|
|Ausgabetyp| Group|
|||
||Patzhalter|
|Name|userid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID eines Nutzers oder ein Nuzername (`User`)|
|Name|esid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID einer Übungsserie (`ExerciseSheet`)|

||addGroup|
| :----------- |:-----: |
|Beschreibung| fügt eine neue Gruppe ein|
|Befehl| POST<br>/group|
|Eingabetyp| Group|
|Ausgabetyp| Group|

||getExistsPlatform|
| :----------- |:-----: |
|Beschreibung| prüft, ob die Tabelle und die Prozeduren existieren und die Komponente generell vollständig installiert ist|
|Befehl| GET<br>/link/exists/platform|
|Eingabetyp| -|
|Ausgabetyp| Platform|

||getUserGroups|
| :----------- |:-----: |
|Beschreibung| ermittelt die Gruppen eines Nutzers|
|Befehl| GET<br>/group/user/:userid|
|Eingabetyp| -|
|Ausgabetyp| Group|
|||
||Patzhalter|
|Name|userid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID eines Nutzers oder ein Nuzername (`User`)|

||getAllGroups|
| :----------- |:-----: |
|Beschreibung| ermittelt alle Gruppeneinträge|
|Befehl| GET<br>/group|
|Eingabetyp| -|
|Ausgabetyp| Group|

||getCourseGroups|
| :----------- |:-----: |
|Beschreibung| ermittelt alle Gruppen einer Veranstaltung|
|Befehl| GET<br>/group/course/:courseid|
|Eingabetyp| -|
|Ausgabetyp| Group|
|||
||Patzhalter|
|Name|courseid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|eine Veranstaltungs ID (`Course`)|

||deleteGroup|
| :----------- |:-----: |
|Beschreibung| entfernt einen Gruppeneintrag|
|Befehl| DELETE<br>/group/user/:userid/exercisesheet/:esid|
|Eingabetyp| -|
|Ausgabetyp| Group|
|||
||Patzhalter|
|Name|userid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID eines Nutzers oder ein Nuzername (`User`)|
|Name|esid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID einer Übungsserie (`ExerciseSheet`)|

||deletePlatform|
| :----------- |:-----: |
|Beschreibung| entfernt die Komponente und ihre installierten Bestandteile aus der Plattform|
|Befehl| DELETE<br>/platform|
|Eingabetyp| -|
|Ausgabetyp| Platform|

||getSheetGroups|
| :----------- |:-----: |
|Beschreibung| ermittelt alle Gruppeneinträge einer Übungsserie|
|Befehl| GET<br>/group/exercisesheet/:esid|
|Eingabetyp| -|
|Ausgabetyp| Group|
|||
||Patzhalter|
|Name|esid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID einer Übungsserie (`ExerciseSheet`)|

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

||editGroup|
| :----------- |:-----: |
|Ziel| DBQueryWrite|
|Befehl| POST<br>/query|
|Beschreibung| für den Befehl editGroup|

||deleteGroup|
| :----------- |:-----: |
|Ziel| DBQueryWrite|
|Befehl| POST<br>/query|
|Beschreibung| für den Befehl deleteGroup|

||addGroup|
| :----------- |:-----: |
|Ziel| DBQueryWrite|
|Befehl| POST<br>/query|
|Beschreibung| für den Befehl addGroup|

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

||getUserGroups|
| :----------- |:-----: |
|Ziel| DBQueryRead|
|Befehl| GET<br>/query/procedure/DBGroupGetUserGroups/:userid|
|Beschreibung| für den Befehl getUserGroups|

||getSheetGroups|
| :----------- |:-----: |
|Ziel| DBQueryRead|
|Befehl| GET<br>/query/procedure/DBGroupGetSheetGroups/:esid|
|Beschreibung| für den Befehl getSheetGroups|

||getUserSheetGroup|
| :----------- |:-----: |
|Ziel| DBQueryRead|
|Befehl| GET<br>/query/procedure/DBGroupGetUserSheetGroups/:userid/:esid|
|Beschreibung| für den Befehl getUserSheetGroup|

||getCourseGroups|
| :----------- |:-----: |
|Ziel| DBQueryRead|
|Befehl| GET<br>/query/procedure/DBGroupGetCourseGroups/:courseid|
|Beschreibung| für den Befehl getCourseGroups|

||getAllGroups|
| :----------- |:-----: |
|Ziel| DBQueryRead|
|Befehl| GET<br>/query/procedure/DBGroupGetAllGroups|
|Beschreibung| für den Befehl getAllGroups|

||getExistsPlatform|
| :----------- |:-----: |
|Ziel| DBQueryRead|
|Befehl| GET<br>/query/procedure/DBGroupGetExistsPlatform|
|Beschreibung| für den Befehl getExistsPlatform|


## Anbindungen
---------------

|Ausgang|request|
| :----------- |:-----: |
|Ziel| CLocalObjectRequest|
|Beschreibung| damit DBGroup als lokales Objekt aufgerufen werden kann|

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
