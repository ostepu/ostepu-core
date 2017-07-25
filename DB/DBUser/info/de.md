<!--
  - @file de.md
  -
  - @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
  -
  - @package OSTEPU (https://github.com/ostepu/ostepu-core)
  - @since 0.3.4
  -
  - @author Till Uhlig <till.uhlig@student.uni-halle.de>
  - @date 2015,2017
  -
 -->

Die DBUser ermöglicht den Zugriff auf die `User` Tabelle der Datenbank, dabei sollen Nutzerdaten verwaltet werden. Dazu wird bei einem `POST /platform` Aufruf die nachstehende Tabelle erzeugt.

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

| Themen |
| :- |
| [Befehle/Eingänge (Commands.json)](#eingaenge) |
| [Ausgänge (Component.json => Links)](#ausgaenge) |
| [Anbindungen (Component.json => Connector)](#anbindungen) |

## <a name='eingaenge'></a>Befehle/Eingänge (Commands.json)
Diese Befehle bietet diese Komponente als Aufruf an.

||postSamples|
| :----------- |:----- |
|Beschreibung| erzeugt Zufallsdaten (courseAmount = Anzahl der Veranstaltungen, userAmount = Anzahl der Nutzer), anhand der Vorgabe|
|Befehl| POST /samples/course/:courseAmount/user/:userAmount|
|Eingabetyp| -|
|Ausgabetyp| Query|

||removeUser|
| :----------- |:----- |
|Beschreibung| setzt U_flag = 0 und löst damit das Entfernen der persönlichen Nutzerdaten aus (entfernt das Nutzerkonto nicht), zusätzlich wird eine eventuell aktive Session entfernt|
|Befehl| DELETE /user/user/:userid|
|Eingabetyp| -|
|Ausgabetyp| User|
|||
||Patzhalter|
|Name|userid|
|Regex|%^([a-zA-Z0-9äöüÄÖÜß]+)$%|
|Beschreibung|die ID eines Nutzers oder ein Nuzername (`User`)|

||getCourseUserByStatus|
| :----------- |:----- |
|Beschreibung| liefert Nutzerdaten, mit einem bestimmten Status (siehe CourseStatus::getStatusDefinition()) in dieser Veranstaltung|
|Befehl| GET /user/course/:courseid/status/:statusid|
|Eingabetyp| -|
|Ausgabetyp| User|
|||
||Patzhalter|
|Name|courseid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|eine Veranstaltungs ID (`Course`)|
|Name|statusid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID eines Veranstaltungsstatus (siehe `DBCourseStatus::getStatusDefinition()`)|

||getExistsPlatform|
| :----------- |:----- |
|Beschreibung| prüft, ob die Tabelle und die Prozeduren existieren|
|Befehl| GET /link/exists/platform|
|Eingabetyp| -|
|Ausgabetyp| Platform|

||removeUserPermanent|
| :----------- |:----- |
|Beschreibung| entfernt das Nutzerkonto entgültig mit allen Konsequenzen (eventuell sind Einsendungen und damit auch Gruppenmitglieder betroffen)|
|Befehl| DELETE /user/user/:userid/permanent|
|Eingabetyp| -|
|Ausgabetyp| User|
|||
||Patzhalter|
|Name|userid|
|Regex|%^([a-zA-Z0-9äöüÄÖÜß]+)$%|
|Beschreibung|die ID eines Nutzers oder ein Nuzername (`User`)|

||getUser|
| :----------- |:----- |
|Beschreibung| liefert einen einzelnen Nutzer (anhand des Nutzernamens oder der ID)|
|Befehl| GET /user/user/:userid|
|Eingabetyp| -|
|Ausgabetyp| User|
|||
||Patzhalter|
|Name|userid|
|Regex|%^([a-zA-Z0-9äöüÄÖÜß]+)$%|
|Beschreibung|die ID eines Nutzers oder ein Nuzername (`User`)|

||getGroupMember|
| :----------- |:----- |
|Beschreibung| liefert alle Gruppenangehörigen eines Nutzers in einer bestimmten Übungsserie (dabei ist es egal, ob dieser Nutzer Gruppenführer oder Mitglied ist)|
|Befehl| GET /user/group/user/:userid/exercisesheet/:esid|
|Eingabetyp| -|
|Ausgabetyp| User|
|||
||Patzhalter|
|Name|esid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID einer Übungsserie (`ExerciseSheet`)|

||addUser|
| :----------- |:----- |
|Beschreibung| fügt eine neues Nutzerkonto ein|
|Befehl| POST /user|
|Eingabetyp| User|
|Ausgabetyp| User|

||editUser|
| :----------- |:----- |
|Beschreibung| editiert ein vorhandenes Nutzerkonto|
|Befehl| PUT /user/user/:userid|
|Eingabetyp| User|
|Ausgabetyp| User|
|||
||Patzhalter|
|Name|userid|
|Regex|%^([a-zA-Z0-9äöüÄÖÜß]+)$%|
|Beschreibung|die ID eines Nutzers oder ein Nuzername (`User`)|

||addPlatform|
| :----------- |:----- |
|Beschreibung| installiert die zugehörige Tabelle und die Prozeduren für diese Plattform|
|Befehl| POST /platform|
|Eingabetyp| Platform|
|Ausgabetyp| Platform|

||getUserByStatus|
| :----------- |:----- |
|Beschreibung| liefert Nutzerdaten, mit einem bestimmten Status (siehe CourseStatus::getStatusDefinition()), für alle Veranstaltungen|
|Befehl| GET /user/status/:statusid|
|Eingabetyp| -|
|Ausgabetyp| User|
|||
||Patzhalter|
|Name|statusid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID eines Veranstaltungsstatus (siehe `DBCourseStatus::getStatusDefinition()`)|

||getUsers|
| :----------- |:----- |
|Beschreibung| liefert alle existierenden Nutzerkonten (gesperrte und aktive)|
|Befehl| GET /user|
|Eingabetyp| -|
|Ausgabetyp| User|

||deletePlatform|
| :----------- |:----- |
|Beschreibung| entfernt die Tabelle und Prozeduren aus der Plattform|
|Befehl| DELETE /platform|
|Eingabetyp| -|
|Ausgabetyp| Platform|

||getIncreaseUserFailedLogin|
| :----------- |:----- |
|Beschreibung| setzt `U_failed_logins` auf den aktuellen Zeitstempel (damit klar ist, wann der letzte fehlerhafte Loginversuch war)|
|Befehl| GET /user/user/:userid/IncFailedLogin|
|Eingabetyp| -|
|Ausgabetyp| User|
|||
||Patzhalter|
|Name|userid|
|Regex|%^([a-zA-Z0-9äöüÄÖÜß]+)$%|
|Beschreibung|die ID eines Nutzers oder ein Nuzername (`User`)|

||getSamplesInfo|
| :----------- |:----- |
|Beschreibung| liefert die Bezeichner der betroffenen Tabellen|
|Befehl| GET /samples|
|Eingabetyp| -|
|Ausgabetyp| -|

||getCourseMember|
| :----------- |:----- |
|Beschreibung| liefert alle Nutzer zu einer Veranstaltung|
|Befehl| GET /user/course/:courseid|
|Eingabetyp| -|
|Ausgabetyp| User|
|||
||Patzhalter|
|Name|courseid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|eine Veranstaltungs ID (`Course`)|

||getApiProfiles|
| :----------- |:----- |
|Beschreibung| liefert `GateProfile`-Objekte, welche unsere Befehle in die Standardprofile von CGate einsortieren|
|Befehl| GET /api/profiles|
|Eingabetyp| -|
|Ausgabetyp| GateProfile|


## <a name='ausgaenge'></a>Ausgänge (Component.json => Links)
Wenn eine Komponente selbst noch Unteranfragen an anderen Komponenten stellen möchte, dann werden diese über die `Ausgänge` bearbeitet.
Dabei kann ein Ausgang bereits auf eine Komponente gerichtet sein (`Ziel`) oder durch die Zielkomponente selbst angebunden werden (`Connector`)

||editUser|
| :----------- |:----- |
|Ziel| DBQueryWrite|
|Befehl| POST /query|
|Beschreibung| für den Befehl editUser|

||removeUser|
| :----------- |:----- |
|Ziel| DBQueryWrite|
|Befehl| POST /query|
|Beschreibung| für den Befehl removeUser|

||removeUserPermanent|
| :----------- |:----- |
|Ziel| DBQueryWrite|
|Befehl| POST /query|
|Beschreibung| für den Befehl removeUserPermanent|

||addUser|
| :----------- |:----- |
|Ziel| DBQueryWrite|
|Befehl| POST /query|
|Beschreibung| für den Befehl addUser|

||deletePlatform|
| :----------- |:----- |
|Ziel| DBQuerySetup|
|Befehl| POST /query|
|Beschreibung| für den Befehl deletePatform|

||addPlatform|
| :----------- |:----- |
|Ziel| DBQuerySetup|
|Befehl| POST /query|
|Beschreibung| für den Befehl addPlatform|

||postSamples|
| :----------- |:----- |
|Ziel| DBQueryWrite|
|Befehl| POST /query|
|Beschreibung| für den Befehl postSamples|

||getUser|
| :----------- |:----- |
|Ziel| DBQueryRead|
|Befehl| GET /query/procedure/DBUserGetUser/:profile/:userid|
|Beschreibung| für den Befehl getUser|

||getUsers|
| :----------- |:----- |
|Ziel| DBQueryRead|
|Befehl| GET /query/procedure/DBUserGetUsers/:profile|
|Beschreibung| für den Befehl getUsers|

||getCourseMember|
| :----------- |:----- |
|Ziel| DBQueryRead|
|Befehl| GET /query/procedure/DBUserGetCourseMember/:profile/:courseid|
|Beschreibung| für den Befehl getCourseMember|

||getGroupMember|
| :----------- |:----- |
|Ziel| DBQueryRead|
|Befehl| GET /query/procedure/DBUserGetGroupMember/:profile/:esid/:userid|
|Beschreibung| für den Befehl getGroupMember|

||getUserByStatus|
| :----------- |:----- |
|Ziel| DBQueryRead|
|Befehl| GET /query/procedure/DBUserGetUserByStatus/:profile/:statusid|
|Beschreibung| für den Befehl getUserByStatus|

||getCourseUserByStatus|
| :----------- |:----- |
|Ziel| DBQueryRead|
|Befehl| GET /query/procedure/DBUserGetCourseUserByStatus/:profile/:courseid/:statusid|
|Beschreibung| für den Befehl getCoursUserByStatus|

||getIncreaseUserFailedLogin|
| :----------- |:----- |
|Ziel| DBQueryWrite|
|Befehl| GET /query/procedure/DBUserGetIncreaseUserFailedLogin/:profile/:userid|
|Beschreibung| für den Befehl getIncreaseUserFailedLogin|

||getExistsPlatform|
| :----------- |:----- |
|Ziel| DBQueryRead|
|Befehl| GET /query/procedure/DBUserGetExistsPlatform/:profile|
|Beschreibung| für den Befehl getExsistsPlatform|

||getSamplesInfo|
| :----------- |:----- |
|Ziel| DBQueryRead|
|Befehl| GET /query/procedure/DBUserGetExistsPlatform/:profile|
|Beschreibung| für den Befehl getSamplesInfo|


## <a name='anbindungen'></a>Anbindungen (Component.json => Connector)
Eine Anbindung verlangt von einer anderen Komponente (`Ziel`) die Anbindung/Verbindung zu dieser Komponente.
Wenn eine Anbindung den aufzurufenden Befehl vorgibt, dann ist die Notation: METHODE URL (PRIORITÄT).

|Ausgang|request|
| :----------- |:----- |
|Ziel| CLocalObjectRequest|
|Beschreibung| damit DBUser als lokales Objekt aufgerufen werden kann|

|Ausgang|postPlatform|
| :----------- |:----- |
|Ziel| CInstall|
|Beschreibung| der Installationsassistent soll uns bei der Plattforminstallation aufrufen|

|Ausgang|postSamples|
| :----------- |:----- |
|Ziel| CInstall|
|Beschreibung| wir wollen bei Bedarf Beispieldaten erzeugen|

|Ausgang|getAlive|
| :----------- |:----- |
|Ziel| CHelp|
|Beschreibung| soll CHelp mitteilen, ob die Datenbank erreichbar ist, indem die Existenz der User-Tabelle geprüft wird|
|| GET /link/exists/platform|

|Ausgang|getDescFiles|
| :----------- |:----- |
|Ziel| TDocuView|
|Beschreibung| die Entwicklerdokumentation soll unsere Beschreibungsdatei nutzen|

|Ausgang|getComponentProfiles|
| :----------- |:----- |
|Ziel| TApiConfiguration|
|Beschreibung| damit unsere Aufrufe in die Standardprofile der CGate einsortiert werden|


Stand 25.07.2017
