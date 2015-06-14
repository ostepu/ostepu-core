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
|editGroup|Group|Group|PUT<br>/group/user/:userid/exercisesheet/:esid| ??? |
|deleteGroup|-|Group|DELETE<br>/group/user/:userid/exercisesheet/:esid| ??? |
|addGroup|Group|Group|POST<br>/group| ??? |
|getUserGroups|-|Group|GET<br>/group/user/:userid| ??? |
|getAllGroups|-|Group|GET<br>/group(/group)| ??? |
|getUserSheetGroup|-|Group|GET<br>/group/user/:userid/exercisesheet/:esid| ??? |
|getSheetGroups|-|Group|GET<br>/group/exercisesheet/:esid| ??? |
|getCourseGroups|-|Group|GET<br>/group/course/:courseid| ??? |
|addPlatform|Platform|Platform|POST<br>/platform|installiert dies zugehörige Tabelle und die Prozeduren für diese Plattform|
|deletePlatform|-|Platform|DELETE<br>/platform|entfernt die Tabelle und Prozeduren aus der Plattform|
|getExistsPlatform|-|Platform|GET<br>/link/exists/platform| prüft, ob die Tabelle und die Prozeduren existieren |

#### Ausgänge
userid = die ID eines Nutzers (`User`)
courseid = die ID einer Veranstlatung (`Course`)
esid = die ID einer Übungsserie (`ExerciseSheet`)

| Bezeichnung  | Ziel  | Verwendung | Beschreibung |
| :----------- |:----- | :--------- | :----------- |
|out2|DBQuery2|POST<br>/query| wird für EDIT, DELETE<br>und POST<br>SQL-Templates verwendet |
|out2|DBQuery|POST<br>/query| wird für EDIT, DELETE<br>und POST<br>SQL-Templates verwendet |
|getUserGroups|DBQuery|GET<br>/query/procedure<br>/DBGroupGetUserGroups/:userid| Prozeduraufruf |
|getSheetGroups|DBQuery|GET<br>/query/procedure<br>/DBGroupGetSheetGroups/:esid| Prozeduraufruf |
|getUserSheetGroup|DBQuery|GET<br>/query/procedure<br>/DBGroupGetUserSheetGroups/:userid/:esid| Prozeduraufruf |
|getCourseGroups|DBQuery|GET<br>/query/procedure<br>/DBGroupGetCourseGroups/:courseid| Prozeduraufruf |
|getAllGroups|DBQuery|GET<br>/query/procedure<br>/DBGroupGetAllGroups| Prozeduraufruf |
|getExistsPlatform|DBQuery2|GET<br>/query/procedure<br>/DBGroupGetExistsPlatform| Prozeduraufruf |

#### Anbindungen
| Bezeichnung  | Ziel  | Priorität | Beschreibung |
| :----------- |:----- | :--------:| :------------|
|request|CLocalObjectRequest|-| damit DBGroup als lokales Objekt aufgerufen werden kann |

Stand 13.06.2015