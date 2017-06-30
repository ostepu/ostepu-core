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

Die DBInvitation ermöglicht den Zugriff auf die `Invitation` Tabelle der Datenbank. Hier werden Einladungen für die Gruppenverwaltung gespeichert. Dazu wird bei einem `POST /platform` Aufruf die nachstehende Tabelle erzeugt.

## Eingänge
---------------

||addPlatform|
| :----------- |:-----: |
|Beschreibung| installiert die zugehörige Tabelle und die Prozeduren für diese Plattform|
|Befehl| POST<br>/platform|
|Eingabetyp| Platform|
|Ausgabetyp| Platform|

||deleteInvitation|
| :----------- |:-----: |
|Beschreibung| entfernt eine Einladung|
|Befehl| DELETE<br>/invitation/user/:userid/exercisesheet/:esid/user/:memberid|
|Eingabetyp| -|
|Ausgabetyp| Invitation|
|||
||Patzhalter|
|Name|userid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID eines Nutzers oder ein Nuzername (`User`)|
|Name|esid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID einer Übungsserie (`ExerciseSheet`)|
|Name|memberid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die Nutzer-ID des Gruppenmitglieds|

||editInvitation|
| :----------- |:-----: |
|Beschreibung| editiert eine Einladung|
|Befehl| PUT<br>/invitation/user/:userid/exercisesheet/:esid/user/:memberid|
|Eingabetyp| Invitation|
|Ausgabetyp| Invitation|
|||
||Patzhalter|
|Name|userid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID eines Nutzers oder ein Nuzername (`User`)|
|Name|esid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID einer Übungsserie (`ExerciseSheet`)|
|Name|memberid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die Nutzer-ID des Gruppenmitglieds|

||getSheetLeaderInvitations|
| :----------- |:-----: |
|Beschreibung| ermittelt die Einladungen eines Gruppenleiters für eine Übungsserie|
|Befehl| GET<br>/invitation/leader/exercisesheet/:esid/user/:userid|
|Eingabetyp| -|
|Ausgabetyp| Invitation|
|||
||Patzhalter|
|Name|esid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID einer Übungsserie (`ExerciseSheet`)|
|Name|userid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID eines Nutzers oder ein Nuzername (`User`)|

||getLeaderInvitations|
| :----------- |:-----: |
|Beschreibung| ermittelt die Einladungen eines Nutzers (als Gruppenleiter)|
|Befehl| GET<br>/invitation/leader/user/:userid|
|Eingabetyp| -|
|Ausgabetyp| Invitation|
|||
||Patzhalter|
|Name|userid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID eines Nutzers oder ein Nuzername (`User`)|

||getExistsPlatform|
| :----------- |:-----: |
|Beschreibung| prüft, ob die Tabelle und die Prozeduren existieren und die Komponente generell vollständig installiert ist|
|Befehl| GET<br>/link/exists/platform|
|Eingabetyp| -|
|Ausgabetyp| Platform|

||getMemberInvitations|
| :----------- |:-----: |
|Beschreibung| ermittelt alle Einladungen eines Nutzers (als Mitglied)|
|Befehl| GET<br>/invitation/member/user/:userid|
|Eingabetyp| -|
|Ausgabetyp| Invitation|
|||
||Patzhalter|
|Name|userid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID eines Nutzers oder ein Nuzername (`User`)|

||getAllInvitations|
| :----------- |:-----: |
|Beschreibung| ermittelt alle Einladungen|
|Befehl| GET<br>/invitation|
|Eingabetyp| -|
|Ausgabetyp| Invitation|

||getSheetInvitations|
| :----------- |:-----: |
|Beschreibung| ermittelt alle Einladungen einer Übungsserie|
|Befehl| GET<br>/invitation/exercisesheet/:esid|
|Eingabetyp| -|
|Ausgabetyp| Invitation|
|||
||Patzhalter|
|Name|esid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID einer Übungsserie (`ExerciseSheet`)|

||addInvitation|
| :----------- |:-----: |
|Beschreibung| fügt eine neue Einladung hinzu|
|Befehl| POST<br>/invitation|
|Eingabetyp| Invitation|
|Ausgabetyp| Invitation|

||deletePlatform|
| :----------- |:-----: |
|Beschreibung| entfernt die Komponente und ihre installierten Bestandteile aus der Plattform|
|Befehl| DELETE<br>/platform|
|Eingabetyp| -|
|Ausgabetyp| Platform|

||getSheetMemberInvitations|
| :----------- |:-----: |
|Beschreibung| ermittelt alle Einladungen einer Übungsserie eines Nutzers (als Mitglied)|
|Befehl| GET<br>/invitation/member/exercisesheet/:esid/user/:userid|
|Eingabetyp| -|
|Ausgabetyp| Invitation|
|||
||Patzhalter|
|Name|esid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID einer Übungsserie (`ExerciseSheet`)|
|Name|userid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID eines Nutzers oder ein Nuzername (`User`)|

||getApiProfiles|
| :----------- |:-----: |
|Beschreibung| liefert `GateProfile`-Objekte, welche unsere Befehle in die Standardprofile von CGate einsortieren|
|Befehl| GET<br>/api/profiles|
|Eingabetyp| -|
|Ausgabetyp| GateProfile|


## Ausgänge
---------------

||editInvitation|
| :----------- |:-----: |
|Ziel| DBQueryWrite|
|Befehl| POST<br>/query|
|Beschreibung| für den Befehl editInvitation|

||deleteInvitation|
| :----------- |:-----: |
|Ziel| DBQueryWrite|
|Befehl| POST<br>/query|
|Beschreibung| für den Befehl deleteInvitation|

||addInvitation|
| :----------- |:-----: |
|Ziel| DBQueryWrite|
|Befehl| POST<br>/query|
|Beschreibung| für den Befehl addInvitation|

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

||getAllInvitations|
| :----------- |:-----: |
|Ziel| DBQueryRead|
|Befehl| GET<br>/query/procedure/DBInvitationGetAllInvitations|
|Beschreibung| für den Befehl getAllInvitations|

||getLeaderInvitations|
| :----------- |:-----: |
|Ziel| DBQueryRead|
|Befehl| GET<br>/query/procedure/DBInvitationGetLeaderInvitations/:userid|
|Beschreibung| für den Befehl getLeaderInvitations|

||getMemberInvitations|
| :----------- |:-----: |
|Ziel| DBQueryRead|
|Befehl| GET<br>/query/procedure/DBInvitationGetMemberInvitations/:userid|
|Beschreibung| für den Befehl getMemberInvitations|

||getSheetInvitations|
| :----------- |:-----: |
|Ziel| DBQueryRead|
|Befehl| GET<br>/query/procedure/DBInvitationGetSheetInvitations/:esid|
|Beschreibung| für den Befehl getSheetInvitations|

||getSheetLeaderInvitations|
| :----------- |:-----: |
|Ziel| DBQueryRead|
|Befehl| GET<br>/query/procedure/DBInvitationGetSheetLeaderInvitations/:esid/:userid|
|Beschreibung| für den Befehl getSheetLeaderInvitations|

||getSheetMemberInvitations|
| :----------- |:-----: |
|Ziel| DBQueryRead|
|Befehl| GET<br>/query/procedure/DBInvitationGetSheetMemberInvitations/:esid/:userid|
|Beschreibung| für den Befehl getSheetMemberInvitations|

||getExistsPlatform|
| :----------- |:-----: |
|Ziel| DBQueryRead|
|Befehl| GET<br>/query/procedure/DBCourseGetExistsPlatform|
|Beschreibung| für den Befehl getExistsPlatform|


## Anbindungen
---------------

|Ausgang|request|
| :----------- |:-----: |
|Ziel| CLocalObjectRequest|
|Beschreibung| damit DBInvitation als lokales Objekt aufgerufen werden kann|

|Ausgang|postPlatform|
| :----------- |:-----: |
|Ziel| CInstall|
|Beschreibung| der Installationsassistent soll uns bei der Plattforminstallation aufrufen|

|Ausgang|getDescFiles|
| :----------- |:-----: |
|Ziel| TDocuView|
|Beschreibung| die Entwicklerdokumentation soll unsere Beschreibungsdatei nutzen|

|Ausgang|getComponentProfiles|
| :----------- |:-----: |
|Ziel| TApiConfiguration|
|Beschreibung| damit unsere Aufrufe in die Standardprofile der CGate einsortiert werden|


Stand 30.06.2017
