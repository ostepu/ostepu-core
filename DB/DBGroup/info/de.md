#### Datenbank
Die DBGroup ermöglicht den Zugriff auf die `Group` Tabelle der Datenbank, dabei sollen
Arbeitsgruppen verwaltet werden.
Dazu wird bei einem `POST /platform` Aufruf die nachstehende Tabelle erzeugt.

| Spalte        | Struktur  | Beschreibung | Besonderheit |
| :------       |:---------:| :------------| -----------: |
|U_id_leader|INT NOT NULL| ??? |-|
|U_id_member|INT NOT NULL| ??? |-|
|C_id|INT NULL| ??? |-|
|ES_id|INT NOT NULL| ??? |-|

Jeder Nutzer besitzt in jeder Übungsserie einen solchen Eintrag. Dabei steht `U_id_leader`
für den Besitzer der Zeile und `U_id_member` für die ID des Nutzers, in
dessen Gruppen der `U_id_leader` in dieser Übungsserie ist (beim Anlegen der 
Übungsserie wird daher `U_id_leader`=`U_id_member` gelten, da jeder zunächst seiner eigenen 
Gruppe zugeordnet ist.

#### Datenstruktur
Zu dieser Tabelle gehört die `Group` Datenstruktur.

#### Eingänge
| Bezeichnung  | Eingabetyp  | Ausgabetyp | Befehl | Beschreibung |
| :----------- |:-----------:| :---------:| :----- | :----------- |
|editGroup|Group|Group|PUT /group/user/:userid/exercisesheet/:esid| ??? |
|deleteGroup|-|Group|DELETE /group/user/:userid/exercisesheet/:esid| ??? |
|addGroup|Group|Group|POST /group| ??? |
|getUserGroups|-|Group|GET /group/user/:userid| ??? |
|getAllGroups|-|Group|GET /group(/group)| ??? |
|getUserSheetGroup|-|Group|GET /group/user/:userid/exercisesheet/:esid| ??? |
|getSheetGroups|-|Group|GET /group/exercisesheet/:esid| ??? |
|getCourseGroups|-|Group|GET /group/course/:courseid| ??? |
|addPlatform|Platform|Platform|POST<br>/platform|installiert dies zugehörige Tabelle und die Prozeduren für diese Plattform|
|deletePlatform|-|Platform|DELETE<br>/platform|entfernt die Tabelle und Prozeduren aus der Plattform|
|getExistsPlatform|-|Platform|GET<br>/link/exists/platform| prüft, ob die Tabelle und die Prozeduren existieren |

#### Ausgänge
userid = die ID eines Nutzers (`User`)
courseid = die ID einer Veranstlatung (`Course`)
esid = die ID einer Übungsserie (`ExerciseSheet`)

| Bezeichnung  | Ziel  | Verwendung | Beschreibung |
| :----------- |:----- | :--------- | :----------- |
|out2|DBQuery2|POST<br>/query| wird für EDIT, DELETE und POST SQL-Templates verwendet |
|out2|DBQuery|POST<br>/query| wird für EDIT, DELETE und POST SQL-Templates verwendet |
|getUserGroups|DBQuery|GET /query/procedure/DBGroupGetUserGroups/:userid| ??? |
|getSheetGroups|DBQuery|GET /query/procedure/DBGroupGetSheetGroups/:esid| ??? |
|getUserSheetGroup|DBQuery|GET /query/procedure/DBGroupGetUserSheetGroups/:userid/:esid| ??? |
|getCourseGroups|DBQuery|GET /query/procedure/DBGroupGetCourseGroups/:courseid| ??? |
|getAllGroups|DBQuery|GET /query/procedure/DBAllGetAllGroups| ??? |
|getExistsPlatform|DBQuery2|GET /query/procedure/DBFileGetExistsPlatform| Prozeduraufruf |

#### Anbindungen
| Bezeichnung  | Ziel  | Priorität | Beschreibung |
| :----------- |:----- | :--------:| :------------|
|request|CLocalObjectRequest|-| damit DBGroup als lokales Objekt aufgerufen werden kann |

Stand 13.06.2015