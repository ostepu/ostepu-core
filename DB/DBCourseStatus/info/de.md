<!--
 * @file de.md
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015
-->

#### Datenbank
Die DBCourseStatus ermöglicht den Zugriff auf die `CourseStatus` Tabelle der Datenbank, dabei soll
der Status eines Nutzers in einer Veranstaltung verwaltet werden.
Dazu wird bei einem `POST /platform` Aufruf die nachstehende Tabelle erzeugt.

| Spalte        | Struktur  | Beschreibung | Besonderheit |
| :------       |:---------:| :------------| -----------: |
|C_id|INT NOT NULL| ein Verweis auf eine Veranstaltung |-|
|U_id|INT NOT NULL| ein Verweis auf ein Nutzerkonto |-|
|CS_status|INT NOT NULL DEFAULT 0| die Statuskennung (siehe CourseStatus::getStatusDefinition()) |-|

Ein Nutzer kann in jeder Veranstaltung einen eigenen Status erhalten, jedoch nur maximal einen pro Veranstaltung.

#### Datenstruktur
Zu dieser Tabelle gehört die `CourseStatus` Datenstruktur.
Teile dieser Datenstruktur werden in der `User` Datenstruktur verwaltet.

#### Eingänge
- courseid = eine Veranstaltungs ID (`Course`)
- userid = die ID eines Nutzers (`User`)

| Bezeichnung  | Eingabetyp  | Ausgabetyp | Befehl | Beschreibung |
| :----------- |:-----------:| :---------:| :----- | :----------- |
|editMemberRight|User|CourseStatus|PUT<br>/coursestatus/course/:courseid<br>/user/:userid| verändert einen Kursstatus |
|removeCourseMember|-|CourseStatus|DELETE<br>/coursestatus/course/:courseid<br>/user/:userid| entfernt einen Nutzer aus der Veranstaltung (nur Status wird entfernt) |
|addCourseMember|User|CourseStatus|POST<br>/coursestatus| fügt einen Nutzer einer Veranstaltung hinzu |
|getMemberRight|-|CourseStatus|GET<br>/coursestatus/course/:courseid<br>/user/:userid| gibt den Status eine Nutzers in einer Veranstaltung aus |
|getMemberRights|-|CourseStatus|GET<br>/coursestatus/user/:userid| gibt alle Kurszugehörigkeiten eines Nutzers aus |
|getCourseRights|-|CourseStatus|GET<br>/coursestatus/course/:courseid| gibt die Kursstatuse einer Veranstaltung zurück (also alle Nutzer aus der dieser Veranstaltung) |
|addPlatform|Platform|Platform|POST<br>/platform|installiert dies zugehörige Tabelle und die Prozeduren für diese Plattform|
|deletePlatform|-|Platform|DELETE<br>/platform|entfernt die Tabelle und Prozeduren aus der Plattform|
|getExistsPlatform|-|Platform|GET<br>/link/exists/platform| prüft, ob die Tabelle und die Prozeduren existieren |
|getSamplesInfo|-|-|GET<br>/samples| ??? |

#### Ausgänge
- courseid = eine Veranstaltungs ID (`Course`)
- userid = die ID eines Nutzers (`User`)

| Bezeichnung  | Ziel  | Verwendung | Beschreibung |
| :----------- |:----- | :--------- | :----------- |
|out2|DBQuery2|POST<br>/query| wird für EDIT, DELETE<br>und POST<br>SQL-Templates verwendet |
|out|DBQuery|POST<br>/query| wird für EDIT, DELETE<br>und POST<br>SQL-Templates verwendet |
|getMemberRight|DBQuery2|GET<br>/query/procedure<br>/DBCourseStatusGetMemberRight/:courseid/:userid| Prozeduraufruf |
|getMemberRights|DBQuery2|GET<br>/query/procedure<br>/DBCourseStatusGetMemberRights/:userid| Prozeduraufruf |
|getCourseRights|DBQuery2|GET<br>/query/procedure<br>/DBCourseStatusGetCourseRights/:courseid| Prozeduraufruf |
|getExistsPlatform|DBQuery2|GET<br>/query/procedure<br>/DBCourseStatusGetExistsPlatform| Prozeduraufruf |
|getSamplesInfo|DBQuery2|GET<br>/query/procedure<br>/DBCourseStatusGetExistsPlatform| Prozeduraufruf |

#### Anbindungen
| Bezeichnung  | Ziel  | Priorität | Beschreibung |
| :----------- |:----- | :--------:| :------------|
|request|CLocalObjectRequest|-| damit DBCourseStatus als lokales Objekt aufgerufen werden kann |

Stand 13.06.2015