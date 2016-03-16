<!--
  - @file de.md
  -
  - @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
  -
  - @package OSTEPU (https://github.com/ostepu/system)
  - @since 0.3.5
  -
  - @author Till Uhlig <till.uhlig@student.uni-halle.de>
  - @date 2015
 -->

#### Eingänge
- userid = eine Veranstaltungs ID (`User`)
- esid = die ID einer Übungsserie (`ExerciseSheet`)

| Bezeichnung  | Eingabetyp  | Ausgabetyp | Befehl | Beschreibung |
| :----------- |:-----------:| :---------:| :----- | :----------- |
|editInvitation|Invitation|Invitation|PUT<br>/invitation/user/:userid/exercisesheet/:esid/user/:memberid| ??? |
|deleteInvitation|-|Invitation|DELETE<br>/invitation/user/:userid/exercisesheet/:esid/user/:memberid| ??? |
|addInvitation|Invitation|Invitation|POST<br>/invitation| ??? |
|getLeaderInvitations|-|Invitation|GET<br>/invitation/leader/user/:userid| ??? |
|getMemberInvitations|-|Invitation|GET<br>/invitation/member/user/:userid| ??? |
|getAllInvitations|-|Invitation|GET<br>/invitation(/invitation)| ??? |
|getSheetLeaderInvitations|-|Invitation|GET<br>/invitation/leader/exercisesheet/:esid/user/:userid| ??? |
|getSheetMemberInvitations|-|Invitation|GET<br>/invitation/member/exercisesheet/:esid/user/:userid| ??? |
|getSheetInvitations|-|Invitation|GET<br>/invitation/exercisesheet/:esid| ??? |
|addPlatform|Platform|Platform|POST<br>/platform|installiert dies zugehörige Tabelle und die Prozeduren für diese Plattform|
|deletePlatform|-|Platform|DELETE<br>/platform|entfernt die Tabelle und Prozeduren aus der Plattform|
|getExistsPlatform|-|Platform|GET<br>/link/exists/platform| prüft, ob die Tabelle und die Prozeduren existieren |

#### Ausgänge
- userid = eine Veranstaltungs ID (`User`)
- esid = die ID einer Übungsserie (`ExerciseSheet`)

| Bezeichnung  | Ziel  | Verwendung | Beschreibung |
| :----------- |:----- | :--------- | :----------- |
|out|DBQuery|POST<br>/query| wird für EDIT, DELETE<br>und POST<br>SQL-Templates verwendet |
|out2|DBQuery2|POST<br>/query| wird für EDIT, DELETE<br>und POST<br>SQL-Templates verwendet |
|getAllInvitations|DBQuery2|GET<br>/query/procedure/DBInvitationGetAllInvitations| Prozeduraufruf |
|getLeaderInvitations|DBQuery2|GET<br>/query/procedure/DBInvitationGetLeaderInvitations/:userid| Prozeduraufruf |
|getMemberInvitations|DBQuery2|GET<br>/query/procedure/DBInvitationGetMemberInvitations/:userid| Prozeduraufruf |
|getSheetInvitations|DBQuery2|GET<br>/query/procedure/DBInvitationGetSheetInvitations/:esid| Prozeduraufruf |
|getSheetLeaderInvitations|DBQuery2|GET<br>/query/procedure/DBInvitationGetSheetLeaderInvitations/:esid/:userid| Prozeduraufruf |
|getSheetMemberInvitations|DBQuery2|GET<br>/query/procedure/DBInvitationGetSheetMemberInvitations/:esid/:userid| Prozeduraufruf |
|getExistsPlatform|DBQuery2|GET<br>/query/procedure/DBCourseGetExistsPlatform| Prozeduraufruf |

#### Anbindungen
| Bezeichnung  | Ziel  | Priorität | Beschreibung |
| :----------- |:----- | :--------:| :------------|
|request|CLocalObjectRequest|-| damit DBInvitation als lokales Objekt aufgerufen werden kann |

Stand 29.06.2015
