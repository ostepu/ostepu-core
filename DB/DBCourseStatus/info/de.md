#### Datenbank
Die DBCourseStatus ermöglicht den Zugriff auf die `CourseStatus` Tabelle der Datenbank, dabei soll
der Status eines Nutzers in einer Veranstaltung verwaltet werden.
Dazu wird bei einem `POST /platform` Aufruf die nachstehende Tabelle erzeugt.

| Spalte        | Struktur  | Beschreibung | Besonderheit |
| :------       |:---------:| :------------| -----------: |
|C_id|INT NOT NULL| ??? |-|
|U_id|INT NOT NULL| ??? |-|
|CS_status|INT NOT NULL DEFAULT 0| ??? |-|

#### Datenstruktur
Zu dieser Tabelle gehört die `CourseStatus` Datenstruktur.
Teile dieser Datenstruktur werden in der `User` Datenstruktur verwaltet.

#### Eingänge
| Bezeichnung  | Eingabetyp  | Ausgabetyp | Befehl | Beschreibung |
| :----------- |:-----------:| :---------:| :----- | :----------- |
|editMemberRight|User|CourseStatus|PUT /coursestatus/course/:courseid/user/:userid| ??? |
|removeCourseMember|-|CourseStatus|DELETE /coursestatus/course/:courseid/user/:userid| ??? |
|addCourseMember|User|CourseStatus|POST /coursestatus| ??? |
|getMemberRight|-|CourseStatus|GET /coursestatus/course/:courseid/user/:userid| ??? |
|getMemberRights|-|CourseStatus|GET /coursestatus/user/:userid| ??? |
|getCourseRights|-|CourseStatus|GET /coursestatus/course/:courseid| ??? |
|addPlatform|Platform|Platform|POST<br>/platform|installiert dies zugehörige Tabelle und die Prozeduren für diese Plattform|
|deletePlatform|-|Platform|DELETE<br>/platform|entfernt die Tabelle und Prozeduren aus der Plattform|
|getExistsPlatform|-|Platform|GET<br>/link/exists/platform| prüft, ob die Tabelle und die Prozeduren existieren |
|getSamplesInfo|-|-|GET<br>/samples| ??? |

#### Ausgänge
courseid = eine Veranstaltungs ID (`Course`)
userid = die ID eines Nutzers (`User`)

| Bezeichnung  | Ziel  | Verwendung | Beschreibung |
| :----------- |:----- | :--------- | :----------- |
|out2|DBQuery2|POST<br>/query| wird für EDIT, DELETE und POST SQL-Templates verwendet |
|out|DBQuery|POST<br>/query| wird für EDIT, DELETE und POST SQL-Templates verwendet |
|getMemberRight|DBQuery2|GET /query/procedure/DBCourseStatusGetMemberRight/:courseid/:userid| ??? |
|getMemberRights|DBQuery2|GET /query/procedure/DBCourseStatusGetMemberRights/:userid| ??? |
|getCourseRights|DBQuery2|GET /query/procedure/DBCourseStatusGetCourseRights/:courseid| ??? |
|getExistsPlatform|DBQuery2|GET /query/procedure/DBFileGetExistsPlatform| Prozeduraufruf |
|getSamplesInfo|DBQuery2|GET /query/procedure/DBFileGetExistsPlatform| Prozeduraufruf |

#### Anbindungen
| Bezeichnung  | Ziel  | Priorität | Beschreibung |
| :----------- |:----- | :--------:| :------------|
|request|CLocalObjectRequest|-| damit DBCourseStatus als lokales Objekt aufgerufen werden kann |

Stand 13.06.2015