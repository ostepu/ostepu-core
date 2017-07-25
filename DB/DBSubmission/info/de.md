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

Die DBSubmission ermöglicht den Zugriff auf die `Submission` Tabelle der Datenbank, dabei sollen studentische Einsendungen verwaltet werden. Dazu wird bei einem `POST /platform` Aufruf die nachstehende Tabelle erzeugt. Zu dieser Tabelle gehört die `Submission` Datenstruktur.

| Spalte        | Struktur  | Beschreibung | Besonderheit |
| :------       |:---------:| :------------| -----------: |
|U_id|INT NOT NULL| ein Verweis auf den Nutzer (`User`), dem die Einsendung gehört (hat sie eingeschickt) |-|
|S_id|INT NOT NULL| die ID der Einsendung |AUTO_INCREMENT,<br>UNIQUE|
|F_id_file|INT NULL| ein Verweis auf den Eintrag der Datei (`File`) |-|
|S_comment|VARCHAR(255) NULL| der Kommentar des Einsenders |-|
|S_date|INT UNSIGNED NOT NULL DEFAULT 0| der Einsendezeitpunkt als Unix-Zeitstempel |-|
|S_accepted|TINYINT(1) NOT NULL DEFAULT false| eine Einsendung kann als nicht-akzeptiert markiert werden (wenn sie beispielsweise verspätet eingesendet wurde). 1 = akzeptiert, Student erhält die Punkte, 0 = nicht akzeptiert |-|
|E_id|INT NOT NULL| ein Verweis auf die zugehörige Aufgabe (`Exercise`) |-|
|ES_id|INT NULL| ein Verweis auf die Übungsserie (`ExerciseSheet`) |-|
|S_flag|TINYINT NOT NULL DEFAULT 1| hier kann der Status der Einsendung vermerkt werden (1 = normal, 0 = gelöscht (für den Studenten nichtmehr sichtbar, aber für Admins)) |-|
|S_leaderId|INT NULL| ein Verweis auf das Nutzerkonto des Gruppenführers |-|
|S_hideFile|TINYINT NOT NULL DEFAULT 0| ein Einsendung kann ausgeblendet werden, wenn beispielweise ein manuelle Nachkorrektur vorgenommen wurde (1 = ausgeblendet, 0 = sichtbar) |-|

| Themen |
| :- |
| [Befehle/Eingänge (Commands.json)](#eingaenge) |
| [Ausgänge (Component.json => Links)](#ausgaenge) |
| [Anbindungen (Component.json => Connector)](#anbindungen) |

## <a name='eingaenge'></a>Befehle/Eingänge (Commands.json)
Diese Befehle bietet diese Komponente als Aufruf an.

||getSheetSubmissions|
| :----------- |:----- |
|Beschreibung| gibt alle Einsendungen einer Übungsserie|
|Befehl| GET /submission/exercisesheet/:esid|
|Eingabetyp| -|
|Ausgabetyp| Submission|
|||
||Patzhalter|
|Name|esid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID einer Übungsserie (`ExerciseSheet`)|

||postSamples|
| :----------- |:----- |
|Beschreibung| erzeugt Zufallsdaten (courseAmount = Anzahl der Veranstaltungen, userAmount = Anzahl der Nutzer), anhand der Vorgabe|
|Befehl| POST /samples/course/:courseAmount/user/:userAmount|
|Eingabetyp| -|
|Ausgabetyp| Query|

||getGroupCourseSubmissions|
| :----------- |:----- |
|Beschreibung| gibt die Einsendungen einer Gruppe zu einer Veranstaltung|
|Befehl| GET /submission/group/user/:userid/course/:courseid|
|Eingabetyp| -|
|Ausgabetyp| Submission|
|||
||Patzhalter|
|Name|userid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID eines Nutzers oder ein Nuzername (`User`)|
|Name|courseid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|eine Veranstaltungs ID (`Course`)|

||addPlatform|
| :----------- |:----- |
|Beschreibung| installiert die zugehörige Tabelle und die Prozeduren für diese Plattform|
|Befehl| POST /platform|
|Eingabetyp| Platform|
|Ausgabetyp| Platform|

||getExistsPlatform|
| :----------- |:----- |
|Beschreibung| prüft, ob die Tabelle und die Prozeduren existieren und die Komponente generell vollständig installiert ist|
|Befehl| GET /link/exists/platform|
|Eingabetyp| -|
|Ausgabetyp| Platform|

||getGroupSelectedSubmissions|
| :----------- |:----- |
|Beschreibung| liefert nur die selektierten Einsendungen einer Gruppe (nur diese gehen in die Bewertung ein)|
|Befehl| GET /submission/group/user/:userid/exercisesheet/:esid/selected|
|Eingabetyp| -|
|Ausgabetyp| Submission|
|||
||Patzhalter|
|Name|esid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID einer Übungsserie (`ExerciseSheet`)|

||getGroupSelectedExerciseSubmissions|
| :----------- |:----- |
|Beschreibung| gibt die selektierten Einsendungen einer Gruppe zu einer Aufgabe|
|Befehl| GET /submission/group/user/:userid/exercise/:eid/selected|
|Eingabetyp| -|
|Ausgabetyp| Submission|
|||
||Patzhalter|
|Name|eid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID einer Aufgabe (`Exercise`)|

||getUserExerciseSubmissions|
| :----------- |:----- |
|Beschreibung| gibt alle Einsendungen eines Nutzers zu einer Aufgabe zurück|
|Befehl| GET /submission/user/:userid/exercise/:eid|
|Eingabetyp| -|
|Ausgabetyp| Submission|
|||
||Patzhalter|
|Name|eid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID einer Aufgabe (`Exercise`)|

||deleteSubmission|
| :----------- |:----- |
|Beschreibung| entfernt eine Einsendung (damit werden auch Korrekturen entfernt und vergebene Punkte)|
|Befehl| DELETE /submission/submission/:suid|
|Eingabetyp| -|
|Ausgabetyp| Submission|
|||
||Patzhalter|
|Name|suid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID einer Einsendung (`Submission`)|

||getUserSheetSubmissions|
| :----------- |:----- |
|Beschreibung| gibt alle Einsendungen eines Nutzers zu einer Übungsserie zurück|
|Befehl| GET /submission/user/:userid/exercisesheet/:esid|
|Eingabetyp| -|
|Ausgabetyp| Submission|
|||
||Patzhalter|
|Name|esid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID einer Übungsserie (`ExerciseSheet`)|

||getAllSubmissions|
| :----------- |:----- |
|Beschreibung| liefert alle Einsendungen (für alle Veranstaltungen), es kann aber ein bestimmter Zeitraum eingegrenzt und sich auf selektierte Einsendungen festgelegt werden|
|Befehl| GET /submission(/:selected)(/date/begin/:beginStamp/end/:endStamp)|
|Eingabetyp| -|
|Ausgabetyp| Submission|

||getGroupSubmissions|
| :----------- |:----- |
|Beschreibung| liefert alle Einsendungen einer Gruppe (anhand der NutzerId eines Gruppenmitglieds und der Übungsserie)|
|Befehl| GET /submission/group/user/:userid/exercisesheet/:esid|
|Eingabetyp| -|
|Ausgabetyp| Submission|
|||
||Patzhalter|
|Name|esid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID einer Übungsserie (`ExerciseSheet`)|

||getCourseUserSubmissions|
| :----------- |:----- |
|Beschreibung| gibt alle Einsendungen eines Nutzers in einer Veranstaltung|
|Befehl| GET /submission/course/:courseid/user/:userid|
|Eingabetyp| -|
|Ausgabetyp| Submission|
|||
||Patzhalter|
|Name|courseid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|eine Veranstaltungs ID (`Course`)|
|Name|userid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID eines Nutzers oder ein Nuzername (`User`)|

||getSubmission|
| :----------- |:----- |
|Beschreibung| liefert eine einzelne Einsendung|
|Befehl| GET /submission/submission/:suid|
|Eingabetyp| -|
|Ausgabetyp| Submission|
|||
||Patzhalter|
|Name|suid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID einer Einsendung (`Submission`)|

||getGroupExerciseSubmissions|
| :----------- |:----- |
|Beschreibung| gibt die Einsendungen einer Gruppe zu einer Aufgabe|
|Befehl| GET /submission/group/user/:userid/exercise/:eid|
|Eingabetyp| -|
|Ausgabetyp| Submission|
|||
||Patzhalter|
|Name|eid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID einer Aufgabe (`Exercise`)|

||getSelectedExerciseSubmissions|
| :----------- |:----- |
|Beschreibung| gibt alle selektierten Einsendungen einer Aufgabe|
|Befehl| GET /submission/exercise/:eid/selected|
|Eingabetyp| -|
|Ausgabetyp| Submission|
|||
||Patzhalter|
|Name|eid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID einer Aufgabe (`Exercise`)|

||addSubmission|
| :----------- |:----- |
|Beschreibung| fügt eine neue Einsendung ein|
|Befehl| POST /submission|
|Eingabetyp| Submission|
|Ausgabetyp| Submission|

||getCourseSubmissions|
| :----------- |:----- |
|Beschreibung| gibt alle Einsendungen einer Veranstaltung|
|Befehl| GET /submission/course/:courseid|
|Eingabetyp| -|
|Ausgabetyp| Submission|
|||
||Patzhalter|
|Name|courseid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|eine Veranstaltungs ID (`Course`)|

||getGroupSelectedCourseSubmissions|
| :----------- |:----- |
|Beschreibung| gibt die selektierten Einsendungen einer Gruppe zu einer Veranstaltung|
|Befehl| GET /submission/group/user/:userid/course/:courseid/selected|
|Eingabetyp| -|
|Ausgabetyp| Submission|
|||
||Patzhalter|
|Name|courseid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|eine Veranstaltungs ID (`Course`)|

||getSelectedCourseUserSubmissions|
| :----------- |:----- |
|Beschreibung| gibt alle selektierten Einsendungen eines Nutzers in einer Veranstaltung|
|Befehl| GET /submission/course/:courseid/user/:userid/selected|
|Eingabetyp| -|
|Ausgabetyp| Submission|
|||
||Patzhalter|
|Name|courseid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|eine Veranstaltungs ID (`Course`)|
|Name|userid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID eines Nutzers oder ein Nuzername (`User`)|

||editSubmission|
| :----------- |:----- |
|Beschreibung| editiert eine existierende Einsendung|
|Befehl| PUT /submission/submission/:suid|
|Eingabetyp| Submission|
|Ausgabetyp| Submission|
|||
||Patzhalter|
|Name|suid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID einer Einsendung (`Submission`)|

||deletePlatform|
| :----------- |:----- |
|Beschreibung| entfernt die Komponente und ihre installierten Bestandteile aus der Plattform|
|Befehl| DELETE /platform|
|Eingabetyp| -|
|Ausgabetyp| Platform|

||getSelectedSheetSubmissions|
| :----------- |:----- |
|Beschreibung| gibt alle selektierten Einsendungen einer Übungsserie|
|Befehl| GET /submission/exercisesheet/:esid/selected|
|Eingabetyp| -|
|Ausgabetyp| Submission|
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

||getExerciseSubmissions|
| :----------- |:----- |
|Beschreibung| gibt alle Einsendungen zu einer Aufgabe zurück|
|Befehl| GET /submission/exercise/:eid|
|Eingabetyp| -|
|Ausgabetyp| Submission|
|||
||Patzhalter|
|Name|eid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID einer Aufgabe (`Exercise`)|

||getApiProfiles|
| :----------- |:----- |
|Beschreibung| liefert `GateProfile`-Objekte, welche unsere Befehle in die Standardprofile von CGate einsortieren|
|Befehl| GET /api/profiles|
|Eingabetyp| -|
|Ausgabetyp| GateProfile|


## <a name='ausgaenge'></a>Ausgänge (Component.json => Links)
Wenn eine Komponente selbst noch Unteranfragen an anderen Komponenten stellen möchte, dann werden diese über die `Ausgänge` bearbeitet.
Dabei kann ein Ausgang bereits auf eine Komponente gerichtet sein (`Ziel`) oder durch die Zielkomponente selbst angebunden werden (`Connector`)

||editSubmission|
| :----------- |:----- |
|Ziel| DBQueryWrite|
|Befehl| POST /query|
|Beschreibung| für den Befehl editSubmission|

||deleteSubmission|
| :----------- |:----- |
|Ziel| DBQueryWrite|
|Befehl| POST /query|
|Beschreibung| für den Befehl deleteSubmission|

||addSubmission|
| :----------- |:----- |
|Ziel| DBQueryWrite|
|Befehl| POST /query|
|Beschreibung| für den Befehl addSubmission|

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

||getAllSubmissions|
| :----------- |:----- |
|Ziel| DBQueryRead|
|Befehl| GET /query/procedure/DBSubmissionGetAllSubmissions/:profile/:selectedSubmissionProfile/:fileProfile/:selected/:beginStamp/:endStamp|
|Beschreibung| für den Befehl getAllSubmissions|

||getCourseSubmissions|
| :----------- |:----- |
|Ziel| DBQueryRead|
|Befehl| GET /query/procedure/DBSubmissionGetCourseSubmissions/:profile/:selectedSubmissionProfile/:fileProfile/:exerciseProfile/:courseid|
|Beschreibung| für den Befehl getCourseSubmissions|

||getCourseUserSubmissions|
| :----------- |:----- |
|Ziel| DBQueryRead|
|Befehl| GET /query/procedure/DBSubmissionGetCourseUserSubmissions/:profile/:selectedSubmissionProfile/:fileProfile/:courseid/:userid|
|Beschreibung| für den Befehl getCourseUserSubmissions|

||getExerciseSubmissions|
| :----------- |:----- |
|Ziel| DBQueryRead|
|Befehl| GET /query/procedure/DBSubmissionGetExerciseSubmissions/:profile/:selectedSubmissionProfile/:fileProfile/:eid|
|Beschreibung| für den Befehl getExerciseSubmissions|

||getGroupCourseSubmissions|
| :----------- |:----- |
|Ziel| DBQueryRead|
|Befehl| GET /query/procedure/DBSubmissionGetGroupCourseSubmissions/:profile/:selectedSubmissionProfile/:fileProfile/:exerciseProfile/:groupProfile/:userid/:courseid|
|Beschreibung| für den Befehl getGroupCourseSubmissions|

||getGroupExerciseSubmissions|
| :----------- |:----- |
|Ziel| DBQueryRead|
|Befehl| GET /query/procedure/DBSubmissionGetGroupExerciseSubmissions/:profile/:selectedSubmissionProfile/:fileProfile/:groupProfile/:userid/:eid|
|Beschreibung| für den Befehl getGroupExerciseSubmissions|

||getGroupSelectedCourseSubmissions|
| :----------- |:----- |
|Ziel| DBQueryRead|
|Befehl| GET /query/procedure/DBSubmissionGetGroupSelectedCourseSubmissions/:profile/:selectedSubmissionProfile/:fileProfile/:exerciseProfile/:groupProfile/:userid/:courseid|
|Beschreibung| für den Befehl getGroupSelectedCourseSubmissions|

||getGroupSelectedExerciseSubmissions|
| :----------- |:----- |
|Ziel| DBQueryRead|
|Befehl| GET /query/procedure/DBSubmissionGetGroupSelectedExerciseSubmissions/:profile/:selectedSubmissionProfile/:fileProfile/:groupProfile/:userid/:eid|
|Beschreibung| für den Befehl getGroupSelectedExerciseSubmissions|

||getGroupSelectedSubmissions|
| :----------- |:----- |
|Ziel| DBQueryRead|
|Befehl| GET /query/procedure/DBSubmissionGetGroupSelectedSubmissions/:profile/:selectedSubmissionProfile/:fileProfile/:exerciseProfile/:groupProfile/:userid/:esid|
|Beschreibung| für den Befehl getGroupSelectedSubmissions|

||getGroupSubmissions|
| :----------- |:----- |
|Ziel| DBQueryRead|
|Befehl| GET /query/procedure/DBSubmissionGetGroupSubmissions/:profile/:selectedSubmissionProfile/:fileProfile/:exerciseProfile/:groupProfile/:userid/:esid|
|Beschreibung| für den Befehl getGroupSubmissions|

||getSelectedCourseUserSubmissions|
| :----------- |:----- |
|Ziel| DBQueryRead|
|Befehl| GET /query/procedure/DBSubmissionGetSelectedCourseUserSubmissions/:profile/:selectedSubmissionProfile/:fileProfile/:exerciseProfile/:courseid/:userid|
|Beschreibung| für den Befehl getSelectedCourseUserSubmissions|

||getSelectedExerciseSubmissions|
| :----------- |:----- |
|Ziel| DBQueryRead|
|Befehl| GET /query/procedure/DBSubmissionGetSelectedExerciseSubmissions/:profile/:selectedSubmissionProfile/:fileProfile/:eid|
|Beschreibung| für den Befehl getSelectedExerciseSubmissions|

||getSelectedSheetSubmissions|
| :----------- |:----- |
|Ziel| DBQueryRead|
|Befehl| GET /query/procedure/DBSubmissionGetSelectedSheetSubmissions/:profile/:selectedSubmissionProfile/:fileProfile/:esid|
|Beschreibung| für den Befehl getSelectedSheetSubmissions|

||getSheetSubmissions|
| :----------- |:----- |
|Ziel| DBQueryRead|
|Befehl| GET /query/procedure/DBSubmissionGetSheetSubmissions/:profile/:selectedSubmissionProfile/:fileProfile/:esid|
|Beschreibung| für den Befehl getSheetSubmissions|

||getSubmission|
| :----------- |:----- |
|Ziel| DBQueryRead|
|Befehl| GET /query/procedure/DBSubmissionGetSubmission/:profile/:selectedSubmissionProfile/:fileProfile/:suid|
|Beschreibung| für den Befehl getSubmission|

||getUserExerciseSubmissions|
| :----------- |:----- |
|Ziel| DBQueryRead|
|Befehl| GET /query/procedure/DBSubmissionGetUserExerciseSubmissions/:profile/:selectedSubmissionProfile/:fileProfile/:userid/:eid|
|Beschreibung| für den Befehl getUserExerciseSubmissions|

||getUserSheetSubmissions|
| :----------- |:----- |
|Ziel| DBQueryRead|
|Befehl| GET /query/procedure/DBSubmissionGetUserSheetSubmissions/:profile/:selectedSubmissionProfile/:fileProfile/:userid/:esid|
|Beschreibung| für den Befehl getUserSheetSubmissions|

||getExistsPlatform|
| :----------- |:----- |
|Ziel| DBQueryRead|
|Befehl| GET /query/procedure/DBSubmissionGetExistsPlatform/:profile|
|Beschreibung| für den Befehl getExistsPlatform|


## <a name='anbindungen'></a>Anbindungen (Component.json => Connector)
Eine Anbindung verlangt von einer anderen Komponente (`Ziel`) die Anbindung/Verbindung zu dieser Komponente.
Wenn eine Anbindung den aufzurufenden Befehl vorgibt, dann ist die Notation: METHODE URL (PRIORITÄT).

|Ausgang|request|
| :----------- |:----- |
|Ziel| CLocalObjectRequest|
|Beschreibung| damit DBSubmission als lokales Objekt aufgerufen werden kann|

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
