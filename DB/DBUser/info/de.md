<!--
 * @file de.md
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015
-->

#### Datenbank
Die DBUser ermöglicht den Zugriff auf die `User` Tabelle der Datenbank, dabei sollen
Nutzerdaten verwaltet werden.
Dazu wird bei einem `POST /platform` Aufruf die nachstehende Tabelle erzeugt.

| Spalte        | Struktur  | Beschreibung | Besonderheit |
| :------       |:---------:| :------------| -----------: |
|U_id           |INT NOT NULL| die ID des Nutzerkontos |AUTO_INCREMENT,<br>UNIQUE|
|U_username     |VARCHAR(120) NOT NULL| der Nutzername (wird für den Login benötigt) |UNIQUE|
|U_email        |VARCHAR(120) NULL| eine eventuelle E-Mail Adresse (möglicherweise nicht immer gesetzt), weil sie meist optional ist)|-|
|U_lastName     |VARCHAR(120) NULL| der Nachname |-|
|U_firstName    |VARCHAR(120) NULL| der Vorname |-|
|U_title        |CHAR(10) NULL| ein eventueller Titel (wird nicht verwendet) |-|
|U_password     |CHAR(64) NOT NULL| das Darstellungsveränderte Passwort (SHA1) |-|
|U_flag         |TINYINT NULL DEFAULT 1| der Status des Kontos ( 0 = gesperrt, 1 = aktiv) |-|
|U_salt         |CHAR(40) NULL| wird als zusätzlicher Schlüssel für die Passwortkodierung verwendet (md5) |-|
|U_failed_logins|INT NULL DEFAULT 0| speichert den letzten fehlgeschlagenen Anmeldeversuch (als Zeitstempel), für eine Sekundensperre |-|
|U_externalId   |VARCHAR(255) NULL| eine externe ID für dieses Nutzerkonto (Bsp.: die StudIP-ID, um dieses Konto dem dortigen zuzuordnen) |-|
|U_studentNumber|VARCHAR(120) NULL| die Matrikelnummer des Studenten oder ähnliches (wird derzeit nicht aus dem StudIP abgefragt) |-|
|U_isSuperAdmin |TINYINT NULL DEFAULT 0| ob es sich dabei um einen Super-Admin Konto handelt (1 = Ja, 0 = Nein) |-|
|U_comment      |VARCHAR(255) NULL| ein Kommentar zu diesem Nutzerkonto (wird nicht verwendet) |-|
|U_lang         |CHAR(2) NOT NULL DEFAULT 'de'| die bevorzugte Sprache des Nutzers als Kürzel (Bsp.: de, en) |-|

#### Datenstruktur
Zu dieser Tabelle gehört die `User` Datenstruktur.

#### Eingänge
- courseid = eine Veranstaltungs ID (`Course`)
- userid = die ID eines Nutzers oder ein Nuzername (`User`)
- statusid = die ID eines Veranstaltungsstatus (siehe `DBCourseStatus::getStatusDefinition()`)
- esid = die ID einer Übungsserie (`ExerciseSheet`)

| Bezeichnung  | Eingabetyp  | Ausgabetyp | Befehl | Beschreibung |
| :----------- |:-----------:| :---------:| :----- | :----------- |
|editUser|User|User|PUT<br>/user(/user)/:userid| editiert ein vorhandenes Nutzerkonto |
|removeUser|-|User|DELETE<br>/user(/user)/:userid| setzt U_flag = 0 und löst damit das Entfernen der persönlichen Nutzerdaten aus (entfernt das Nutzerkonto nicht), zusätzlich wird eine eventuell aktive Session entfernt |
|removeUserPermanent|-|User|DELETE<br>/user(/user)/:userid/permanent| entfernt das Nutzerkonto entgültig mit allen Konsequenzen (eventuell sind Einsendungen und damit auch Gruppenmitglieder betroffen) |
|addUser|User|User|POST<br>/user| fügt eine neues Nutzerkonto ein |
|getUsers|-|User|GET<br>/user(/user)| liefert alle existierenden Nutzerkonten (gesperrte und aktive)|
|getIncreaseUserFailedLogin|-|User|GET<br>/user(/user)/:userid/IncFailedLogin| setzt `U_failed_logins` auf den aktuellen Zeitstempel |
|getUser|-|User|GET<br>/user(/user)/:userid| liefert einen einzelnen Nutzer |
|getCourseUserByStatus|-|User|GET<br>/user/course/:courseid/<br>status/:statusid| liefert Nutzerdaten, mit einem bestimmten Status (siehe CourseStatus::getStatusDefinition()) in dieser Veranstaltung |
|getCourseMember|-|User|GET<br>/user/course/:courseid| liefert alle Nutzer zu einer Veranstaltung |
|getGroupMember|-|User|GET<br>/user/group/user/:userid<br>/exercisesheet/:esid| liefert alle Gruppenangehörigen eines Nutzers in einer bestimmten Übungsserie (dabei ist es egal, ob dieser Nutzer Gruppenführer oder Mitglied ist) |
|getUserByStatus|-|User|GET<br>/user/status/:statusid| liefert Nutzerdaten, mit einem bestimmten Status (siehe CourseStatus::getStatusDefinition()), für alle Veranstaltungen |
|addPlatform|Platform|Platform|POST<br>/platform|installiert dies zugehörige Tabelle und die Prozeduren für diese Plattform|
|deletePlatform|-|Platform|DELETE<br>/platform|entfernt die Tabelle und Prozeduren aus der Plattform|
|getExistsPlatform|-|Platform|GET<br>/link/exists/platform| prüft, ob die Tabelle und die Prozeduren existieren |
|getSamplesInfo|-|-|GET<br>/samples| ??? |
|postSamples|-|Query|POST<br>/samples/course/:courseAmount<br>/user/:userAmount| erzeugt Zufallsdaten (courseAmount = Anzahl der Veranstaltungen, userAmount = Anzahl der Nutzer), anhand der Vorgabe |

#### Ausgänge
- courseid = eine Veranstaltungs ID (`Course`)
- userid = die ID eines Nutzers oder ein Nuzername (`User`)
- statusid = die ID eines Veranstaltungsstatus (siehe `DBCourseStatus::getStatusDefinition()`)
- esid = die ID einer Übungsserie (`ExerciseSheet`)

| Bezeichnung  | Ziel  | Verwendung | Beschreibung |
| :----------- |:----- | :--------- | :----------- |
|out2|DBQuery2|POST<br>/query| wird für EDIT, DELETE<br>und POST<br>SQL-Templates verwendet |
|out|DBQuery|POST<br>/query| wird für EDIT, DELETE<br>und POST<br>SQL-Templates verwendet |
|getUser|DBQuery2|GET<br>/query/procedure<br>/DBUserGetUser/:userid| Prozeduraufruf |
|getUsers|DBQuery2|GET<br>/query/procedure<br>/DBUserGetUsers| Prozeduraufruf |
|getCourseMember|DBQuery2|GET<br>/query/procedure<br>/DBUserGetCourseMember/:courseid| Prozeduraufruf |
|getGroupMember|DBQuery2|GET<br>/query/procedure<br>/DBUserGetGroupMember/:esid/:userid| Prozeduraufruf |
|getUserByStatus|DBQuery2|GET<br>/query/procedure<br>/DBUserGetUserByStatus/:statusid| Prozeduraufruf |
|getCourseUserByStatus|DBQuery2|GET<br>/query/procedure<br>/DBUserGetCourseUserByStatus/:courseid/:statusid| Prozeduraufruf |
|getIncreaseUserFailedLogin|DBQuery2|GET<br>/query/procedure<br>/DBUserGetIncreaseUserFailedLogin/:userid| Prozeduraufruf |
|getExistsPlatform|DBQuery2|GET<br>/query/procedure<br>/DBUserGetExistsPlatform| Prozeduraufruf |
|getSamplesInfo|DBQuery2|GET<br>/query/procedure<br>/DBUserGetExistsPlatform| Prozeduraufruf |

#### Anbindungen
| Bezeichnung  | Ziel  | Priorität | Beschreibung |
| :----------- |:----- | :--------:| :------------|
|request|CLocalObjectRequest|-| damit DBUser als lokales Objekt aufgerufen werden kann |

Stand 13.06.2015