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

Die DBMarking ermöglicht den Zugriff auf die `Marking` Tabelle der Datenbank, dabei sollen Korrekturen, zu Einsendungen, verwaltet werden. Dazu wird bei einem `POST /platform` Aufruf die nachstehende Tabelle erzeugt. Zu dieser Tabelle gehört die `Marking` Datenstruktur.

| Spalte        | Struktur  | Beschreibung | Besonderheit |
| :------       |:---------:| :------------| -----------: |
|M_id|INT NOT NULL| die ID der Korrektur |AUTO_INCREMENT,<br>UNIQUE|
|U_id_tutor|INT NOT NULL| ein Verweis auf das Nutzerkonto des Kontrolleurs (bei automatisch kontrollierten steht hier der Einsender) |-|
|F_id_file|INT NULL| ein Verweis auf den Korrektureintrag |-|
|S_id|INT NOT NULL| ein Verweis auf die Einsendung |-|
|M_tutorComment|VARCHAR(255) NULL| ein Kommentar des Kontrolleurs |-|
|M_outstanding|TINYINT(1) NULL DEFAULT 0| hier kann die Einsendung als "besonders" markiert werden (1 = besonders, 0 = normal) |-|
|M_status|TINYINT NOT NULL DEFAULT 0| der Korrekturstatus (siehe Marking::getStatusDefinition()) |-|
|M_points|FLOAT NULL DEFAULT 0| die durch den Kontrolleur vergebenen Punkte |-|
|M_date|INT UNSIGNED NULL DEFAULT 0| das Datum der Korrektur (als Unix-Zeitstempel) |-|
|E_id|INT NULL| ein Verweis auf die zugehörige Aufgabe |-|
|ES_id|INT NULL| ein Verweis auf die Übungsserie |-|
|M_hideFile|TINYINT NOT NULL DEFAULT 0| ein Korrektur kann ausgeblendet werden, wenn beispielweise ein manuelle Nachkorrektur vorgenommen wurde (1 = 
ausgeblendet, 0 = sichtbar) |-|

| Themen |
| :- |
| [Befehle/Eingänge (Commands.json)](#eingaenge) |
| [Ausgänge (Component.json => Links)](#ausgaenge) |
| [Anbindungen (Component.json => Connector)](#anbindungen) |

## <a name='eingaenge'></a>Befehle/Eingänge (Commands.json)
Diese Befehle bietet diese Komponente als Aufruf an.

||getExistsPlatform|
| :----------- |:----- |
|Beschreibung| prüft, ob die Tabelle und die Prozeduren existieren und die Komponente generell vollständig installiert ist|
|Befehl| GET /link/exists/platform|
|Eingabetyp| -|
|Ausgabetyp| Platform|

||getMarking|
| :----------- |:----- |
|Beschreibung| liefert eine einzelne Korrektur|
|Befehl| GET /marking/marking/:mid(/:sub)|
|Eingabetyp| -|
|Ausgabetyp| Marking|
|||
||Patzhalter|
|Name|mid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID einer Korrektur (`Marking`)|

||getExerciseMarkings|
| :----------- |:----- |
|Beschreibung| liefert alle Korrekturen einer Aufgabe|
|Befehl| GET /marking/exercise/:eid(/:sub)|
|Eingabetyp| -|
|Ausgabetyp| Marking|
|||
||Patzhalter|
|Name|eid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID einer Aufgabe (`Exercise`)|

||editMarking|
| :----------- |:----- |
|Beschreibung| editiert eine Korrektur|
|Befehl| PUT /marking/marking/:mid|
|Eingabetyp| Marking|
|Ausgabetyp| Marking|
|||
||Patzhalter|
|Name|mid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID einer Korrektur (`Marking`)|

||getTutorSheetMarkings|
| :----------- |:----- |
|Beschreibung| liefert Korrekturen eines Kontrolleurs|
|Befehl| GET /marking/exercisesheet/:esid/tutor/:userid(/:sub)|
|Eingabetyp| -|
|Ausgabetyp| Marking|
|||
||Patzhalter|
|Name|esid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID einer Übungsserie (`ExerciseSheet`)|
|Name|userid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID eines Nutzers oder ein Nuzername (`User`)|

||addMarking|
| :----------- |:----- |
|Beschreibung| fügt eine neue Korrektur ein|
|Befehl| POST /marking|
|Eingabetyp| Marking|
|Ausgabetyp| Marking|

||getCourseUserGroupMarkings|
| :----------- |:----- |
|Beschreibung| gibt alle Korrekturen eines Nutzers und seiner zugehörigen Gruppe aus|
|Befehl| GET /marking/course/:courseid/user/:userid(/:sub)|
|Eingabetyp| -|
|Ausgabetyp| Marking|
|||
||Patzhalter|
|Name|courseid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|eine Veranstaltungs ID (`Course`)|
|Name|userid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID eines Nutzers oder ein Nuzername (`User`)|

||getSubmissionMarking|
| :----------- |:----- |
|Beschreibung| liefert alle Korrekturen zu einer Einsendung|
|Befehl| GET /marking/submission/:suid(/:sub)|
|Eingabetyp| -|
|Ausgabetyp| Marking|
|||
||Patzhalter|
|Name|suid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID einer Einsendung (`Submission`)|

||deleteMarking|
| :----------- |:----- |
|Beschreibung| entfernt eine Korrektur|
|Befehl| DELETE /marking/marking/:mid|
|Eingabetyp| -|
|Ausgabetyp| Marking|
|||
||Patzhalter|
|Name|mid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID einer Korrektur (`Marking`)|

||getCourseMarkings|
| :----------- |:----- |
|Beschreibung| ermittelt alle Korrekturen einer Veranstaltung|
|Befehl| GET /marking/course/:courseid(/:sub)|
|Eingabetyp| -|
|Ausgabetyp| Marking|
|||
||Patzhalter|
|Name|courseid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|eine Veranstaltungs ID (`Course`)|

||getSheetMarkings|
| :----------- |:----- |
|Beschreibung| ermittelt alle Korrekturen einer Übungsserie|
|Befehl| GET /marking/exercisesheet/:esid(/:sub)|
|Eingabetyp| -|
|Ausgabetyp| Marking|
|||
||Patzhalter|
|Name|esid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID einer Übungsserie (`ExerciseSheet`)|

||getTutorExerciseMarkings|
| :----------- |:----- |
|Beschreibung| ermittelt alle Korrekturen eines Tutors anhand einer Aufgaben-ID|
|Befehl| GET /marking/exercise/:eid/tutor/:userid(/:sub)|
|Eingabetyp| -|
|Ausgabetyp| Marking|
|||
||Patzhalter|
|Name|eid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID einer Aufgabe (`Exercise`)|
|Name|userid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID eines Nutzers oder ein Nuzername (`User`)|

||postSamples|
| :----------- |:----- |
|Beschreibung| erzeugt Zufallsdaten (courseAmount = Anzahl der Veranstaltungen, userAmount = Anzahl der Nutzer), anhand der Vorgabe|
|Befehl| POST /samples/course/:courseAmount/user/:userAmount|
|Eingabetyp| -|
|Ausgabetyp| Query|

||getAllMarkings|
| :----------- |:----- |
|Beschreibung| ermittelt alle Korrekturen|
|Befehl| GET /marking(/:sub)|
|Eingabetyp| -|
|Ausgabetyp| Marking|

||getUserGroupMarkings|
| :----------- |:----- |
|Beschreibung| gibt alle Korrekturen eines Nutzers und seiner zugehörigen Gruppe aus|
|Befehl| GET /marking/exercisesheet/:esid/user/:userid(/:sub)|
|Eingabetyp| -|
|Ausgabetyp| Marking|
|||
||Patzhalter|
|Name|esid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID einer Übungsserie (`ExerciseSheet`)|
|Name|userid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID eines Nutzers oder ein Nuzername (`User`)|

||deletePlatform|
| :----------- |:----- |
|Beschreibung| entfernt die Komponente und ihre installierten Bestandteile aus der Plattform|
|Befehl| DELETE /platform|
|Eingabetyp| -|
|Ausgabetyp| Platform|

||deleteSheetMarkings|
| :----------- |:----- |
|Beschreibung| entfernt alle Korrekturen einer Übungsserie|
|Befehl| DELETE /marking/marking/exercisesheet/:esid|
|Eingabetyp| -|
|Ausgabetyp| Marking|
|||
||Patzhalter|
|Name|esid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID einer Übungsserie (`ExerciseSheet`)|

||getSamplesInfo|
| :----------- |:----- |
|Beschreibung| liefert die Bezeichner der betroffenen Tabellen|
|Befehl| GET /samples|
|Eingabetyp| -|
|Ausgabetyp| -|

||addPlatform|
| :----------- |:----- |
|Beschreibung| installiert die zugehörige Tabelle und die Prozeduren für diese Plattform|
|Befehl| POST /platform|
|Eingabetyp| Platform|
|Ausgabetyp| Platform|

||getApiProfiles|
| :----------- |:----- |
|Beschreibung| liefert `GateProfile`-Objekte, welche unsere Befehle in die Standardprofile von CGate einsortieren|
|Befehl| GET /api/profiles|
|Eingabetyp| -|
|Ausgabetyp| GateProfile|


## <a name='ausgaenge'></a>Ausgänge (Component.json => Links)
Wenn eine Komponente selbst noch Unteranfragen an andere Komponenten stellen möchte, dann werden diese über die `Ausgänge` bearbeitet.
Dabei kann ein Ausgang bereits auf eine Komponente gerichtet sein (`Ziel`) oder durch die Zielkomponente selbst angebunden werden (`Connector`)

||editMarking|
| :----------- |:----- |
|Ziel| DBQueryWrite|
|Befehl| POST /query|
|Beschreibung| für den Befehl editMarking|

||deleteMarking|
| :----------- |:----- |
|Ziel| DBQueryWrite|
|Befehl| POST /query|
|Beschreibung| für den Befehl deleteMarking|

||deleteSheetMarkings|
| :----------- |:----- |
|Ziel| DBQueryWrite|
|Befehl| POST /query|
|Beschreibung| für den Befehl deleteSheetMarkings|

||addMarking|
| :----------- |:----- |
|Ziel| DBQueryWrite|
|Befehl| POST /query|
|Beschreibung| für den Befehl addMarking|

||postSamples|
| :----------- |:----- |
|Ziel| DBQueryWrite|
|Befehl| POST /query|
|Beschreibung| für den Befehl postSamples|

||deletePlatform|
| :----------- |:----- |
|Ziel| DBQuerySetup|
|Befehl| POST /query|
|Beschreibung| für den Befehl deletePlatform|

||addPlatform|
| :----------- |:----- |
|Ziel| DBQuerySetup|
|Befehl| POST /query|
|Beschreibung| für den Befehl addPlatform|

||getMarking|
| :----------- |:----- |
|Ziel| DBQueryRead|
|Befehl| GET /query/procedure/DBMarkingGetMarking/:mid/:sub|
|Beschreibung| für den Befehl getMarking|

||getSubmissionMarking|
| :----------- |:----- |
|Ziel| DBQueryRead|
|Befehl| GET /query/procedure/DBMarkingGetSubmissionMarking/:suid/:sub|
|Beschreibung| für den Befehl getSubmissionMarking|

||getAllMarkings|
| :----------- |:----- |
|Ziel| DBQueryRead|
|Befehl| GET /query/procedure/DBExerciseGetAllMarkings/:sub|
|Beschreibung| für den Befehl getAllMarkings|

||getCourseMarkings|
| :----------- |:----- |
|Ziel| DBQueryRead|
|Befehl| GET /query/procedure/DBMarkingGetCourseMarkings/:courseid/:sub|
|Beschreibung| für den Befehl getCourseMarkings|

||getExerciseMarkings|
| :----------- |:----- |
|Ziel| DBQueryRead|
|Befehl| GET /query/procedure/DBMarkingGetExerciseMarkings/:eid/:sub|
|Beschreibung| für den Befehl getExerciseMarkings|

||getSheetMarkings|
| :----------- |:----- |
|Ziel| DBQueryRead|
|Befehl| GET /query/procedure/DBMarkingGetSheetMarkings/:esid/:sub|
|Beschreibung| für den Befehl getSheetMarkings|

||getTutorCourseMarkings|
| :----------- |:----- |
|Ziel| DBQueryRead|
|Befehl| GET /query/procedure/DBMarkingGetTutorCourseMarkings/:courseid/:userid/:sub|
|Beschreibung| für den Befehl getTutorCourseMarkings|

||getTutorExerciseMarkings|
| :----------- |:----- |
|Ziel| DBQueryRead|
|Befehl| GET /query/procedure/DBMarkingGetTutorExerciseMarkings/:eid/:userid/:sub|
|Beschreibung| für den Befehl getTutorExerciseMarkings|

||getTutorSheetMarkings|
| :----------- |:----- |
|Ziel| DBQueryRead|
|Befehl| GET /query/procedure/DBMarkingGetTutorSheetMarkings/:esid/:userid/:sub|
|Beschreibung| für den Befehl getTutorSheetMarkings|

||getUserGroupMarkings|
| :----------- |:----- |
|Ziel| DBQueryRead|
|Befehl| GET /query/procedure/DBMarkingGetUserGroupMarkings/:esid/:userid/:sub|
|Beschreibung| für den Befehl getUserGroupMarkings|

||getCourseUserGroupMarkings|
| :----------- |:----- |
|Ziel| DBQueryRead|
|Befehl| GET /query/procedure/DBMarkingGetCourseUserGroupMarkings/:courseid/:userid/:sub|
|Beschreibung| für den Befehl getCourseUserGroupMarkings|

||getExistsPlatform|
| :----------- |:----- |
|Ziel| DBQueryRead|
|Befehl| GET /query/procedure/DBMarkingGetExistsPlatform|
|Beschreibung| für den Befehl getExistsPlatform|


## <a name='anbindungen'></a>Anbindungen (Component.json => Connector)
Eine Anbindung verlangt von einer anderen Komponente (`Ziel`) die Anbindung/Verbindung zu dieser Komponente.
Wenn eine Anbindung den aufzurufenden Befehl vorgibt, dann ist die Notation: METHODE URL (PRIORITÄT).

|Ausgang|request|
| :----------- |:----- |
|Ziel| CLocalObjectRequest|
|Beschreibung| damit DBMarking als lokales Objekt aufgerufen werden kann|

|Ausgang|postPlatform|
| :----------- |:----- |
|Ziel| CInstall|
|Beschreibung| der Installationsassistent soll uns bei der Plattforminstallation aufrufen|

|Ausgang|postSamples|
| :----------- |:----- |
|Ziel| CInstall|
|Beschreibung| wir wollen bei Bedarf Beispieldaten erzeugen|

|Ausgang|getDescFiles|
| :----------- |:----- |
|Ziel| TDocuView|
|Beschreibung| die Entwicklerdokumentation soll unsere Beschreibungsdatei nutzen|

|Ausgang|getComponentProfiles|
| :----------- |:----- |
|Ziel| TApiConfiguration|
|Beschreibung| damit unsere Aufrufe in die Standardprofile der CGate einsortiert werden|


Stand 25.07.2017
