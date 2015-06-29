#### Eingänge
- userid = ??? 
- esid = ??? 

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
|addPlatform|Platform|Platform|POST<br>/platform| ??? |
|deletePlatform|-|Platform|DELETE<br>/platform| ??? |
|getExistsPlatform|-|Platform|GET<br>/link/exists/platform| ??? |

#### Ausgänge
- userid = ??? 
- esid = ??? 

| Bezeichnung  | Ziel  | Verwendung | Beschreibung |
| :----------- |:----- | :--------- | :----------- |
|out|DBQuery|POST<br>/query| ??? |
|out2|DBQuery2|POST<br>/query| ??? |
|getAllInvitations|DBQuery2|GET<br>/query/procedure/DBInvitationGetAllInvitations| ??? |
|getLeaderInvitations|DBQuery2|GET<br>/query/procedure/DBInvitationGetLeaderInvitations/:userid| ??? |
|getMemberInvitations|DBQuery2|GET<br>/query/procedure/DBInvitationGetMemberInvitations/:userid| ??? |
|getSheetInvitations|DBQuery2|GET<br>/query/procedure/DBInvitationGetSheetInvitations/:esid| ??? |
|getSheetLeaderInvitations|DBQuery2|GET<br>/query/procedure/DBInvitationGetSheetLeaderInvitations/:esid/:userid| ??? |
|getSheetMemberInvitations|DBQuery2|GET<br>/query/procedure/DBInvitationGetSheetMemberInvitations/:esid/:userid| ??? |
|getExistsPlatform|DBQuery2|GET<br>/query/procedure/DBCourseGetExistsPlatform| ??? |

#### Anbindungen
| Bezeichnung  | Ziel  | Priorität | Beschreibung |
| :----------- |:----- | :--------:| :------------|
|request|CLocalObjectRequest|-| ??? |

Stand 29.06.2015
