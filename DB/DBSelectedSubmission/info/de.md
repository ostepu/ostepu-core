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

Die DBSelectedSubmission ermöglicht den Zugriff auf die `SelectedSubmission` Tabelle der Datenbank, dabei sollen für einen Gruppeneintrag ausgewählte Einsendungen (für eine Aufgabe) verwaltet werden. Dazu wird bei einem `POST /platform` Aufruf die nachstehende Tabelle erzeugt. Zu dieser Tabelle gehört die `SelectedSubmission` Datenstruktur.

| Spalte        | Struktur  | Beschreibung | Besonderheit |
| :------       |:---------:| :------------| -----------: |
|U_id_leader|INT NOT NULL| ein Verweis auf ein Nutzerkonto, welches die Gruppe repräsentiert |-|
|S_id_selected|INT NOT NULL| ein Verweis auf die ausgewählte Einsendung |UNIQUE|
|E_id|INT NOT NULL| ein Verweis auf die zugehörige Aufgabe |-|
|ES_id|INT NULL| ein Verweis auf die Übungsserie |-|

| Themen |
| :- |
| [Befehle/Eingänge (Commands.json)](#eingaenge) |
| [Ausgänge (Component.json => Links)](#ausgaenge) |
| [Anbindungen (Component.json => Connector)](#anbindungen) |

## <a name='eingaenge'></a>Befehle/Eingänge (Commands.json)
Diese Befehle bietet diese Komponente als Aufruf an.

||editSelectedSubmission|
| :----------- |:----- |
|Beschreibung| editiert einen Auswahleintrag|
|Befehl| PUT /selectedsubmission/leader/:userid/exercise/:eid|
|Eingabetyp| SelectedSubmission|
|Ausgabetyp| SelectedSubmission|
|||
||Patzhalter|
|Name|userid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID eines Nutzers oder ein Nuzername (`User`)|
|Name|eid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID einer Aufgabe (`Exercise`)|

||deleteSelectedSubmission|
| :----------- |:----- |
|Beschreibung| entfernt eine Auswahl|
|Befehl| DELETE /selectedsubmission/leader/:userid/exercise/:eid|
|Eingabetyp| -|
|Ausgabetyp| SelectedSubmission|
|||
||Patzhalter|
|Name|userid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID eines Nutzers oder ein Nuzername (`User`)|
|Name|eid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID einer Aufgabe (`Exercise`)|

||postSamples|
| :----------- |:----- |
|Beschreibung| erzeugt Zufallsdaten (courseAmount = Anzahl der Veranstaltungen, userAmount = Anzahl der Nutzer), anhand der Vorgabe|
|Befehl| POST /samples/course/:courseAmount/user/:userAmount|
|Eingabetyp| -|
|Ausgabetyp| Query|

||getExerciseSelected|
| :----------- |:----- |
|Beschreibung| liefert alle Auswahleinträge einer Aufgabe|
|Befehl| GET /selectedsubmission/exercise/:eid|
|Eingabetyp| -|
|Ausgabetyp| SelectedSubmission|
|||
||Patzhalter|
|Name|eid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID einer Aufgabe (`Exercise`)|

||deleteSubmissionSelectedSubmission|
| :----------- |:----- |
|Beschreibung| entfernt eine Auswahl anhand der Submission-ID|
|Befehl| DELETE /selectedsubmission/submission/:suid|
|Eingabetyp| -|
|Ausgabetyp| SelectedSubmission|
|||
||Patzhalter|
|Name|suid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID einer Einsendung (`Submission`)|

||getExistsPlatform|
| :----------- |:----- |
|Beschreibung| prüft, ob die Tabelle und die Prozeduren existieren und die Komponente generell vollständig installiert ist|
|Befehl| GET /link/exists/platform|
|Eingabetyp| -|
|Ausgabetyp| Platform|

||deleteUserSheetSelectedSubmission|
| :----------- |:----- |
|Beschreibung| entfernt alle Auswahleinträge der Einsendungen eines Nutzers für eine Veranstaltung|
|Befehl| DELETE /selectedsubmission/user/:userid/exercisesheet/:esid|
|Eingabetyp| -|
|Ausgabetyp| SelectedSubmission|
|||
||Patzhalter|
|Name|userid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID eines Nutzers oder ein Nuzername (`User`)|
|Name|esid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID einer Übungsserie (`ExerciseSheet`)|

||addSelectedSubmission|
| :----------- |:----- |
|Beschreibung| fügt eine neue Auswahl ein|
|Befehl| POST /selectedsubmission|
|Eingabetyp| SelectedSubmission|
|Ausgabetyp| SelectedSubmission|

||getCourseSelected|
| :----------- |:----- |
|Beschreibung| liefert alle Auswahleinträge einer Veranstaltung|
|Befehl| GET /selectedsubmission/course/:courseid|
|Eingabetyp| -|
|Ausgabetyp| SelectedSubmission|
|||
||Patzhalter|
|Name|courseid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|eine Veranstaltungs ID (`Course`)|

||editSubmissionSelectedSubmission|
| :----------- |:----- |
|Beschreibung| editiert einen Auswahleintrag anhand der Submission-ID|
|Befehl| PUT /selectedsubmission/submission/:suid|
|Eingabetyp| SelectedSubmission|
|Ausgabetyp| SelectedSubmission|
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

||getSheetSelected|
| :----------- |:----- |
|Beschreibung| liefert alle Auswahleinträge einer Übungsserie|
|Befehl| GET /selectedsubmission/exercisesheet/:esid|
|Eingabetyp| -|
|Ausgabetyp| SelectedSubmission|
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

||editSelectedSubmission|
| :----------- |:----- |
|Ziel| DBQueryWrite|
|Befehl| POST /query|
|Beschreibung| für den Befehl editSelectedSubmission|

||editSubmissionSelectedSubmission|
| :----------- |:----- |
|Ziel| DBQueryWrite|
|Befehl| POST /query|
|Beschreibung| für den Befehl editSubmissionSelectedSubmission|

||deleteSelectedSubmission|
| :----------- |:----- |
|Ziel| DBQueryWrite|
|Befehl| POST /query|
|Beschreibung| für den Befehl deleteSelectedSubmission|

||deleteUserSheetSelectedSubmission|
| :----------- |:----- |
|Ziel| DBQueryWrite|
|Befehl| POST /query|
|Beschreibung| für den Befehl deleteUserSheetSelectedSubmission|

||deleteSubmissionSelectedSubmission|
| :----------- |:----- |
|Ziel| DBQueryWrite|
|Befehl| POST /query|
|Beschreibung| für den Befehl deleteSubmissionSelectedSubmission|

||addSelectedSubmission|
| :----------- |:----- |
|Ziel| DBQueryWrite|
|Befehl| POST /query|
|Beschreibung| für den Befehl addSelectedSubmission|

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

||getCourseSelected|
| :----------- |:----- |
|Ziel| DBQueryRead|
|Befehl| GET /query/procedure/DBSelectedSubmissionGetCourseSelected/:courseid|
|Beschreibung| für den Befehl getCourseSelected|

||getExerciseSelected|
| :----------- |:----- |
|Ziel| DBQueryRead|
|Befehl| GET /query/procedure/DBSelectedSubmissionGetExerciseSelected/:eid|
|Beschreibung| für den Befehl getExerciseSelected|

||getSheetSelected|
| :----------- |:----- |
|Ziel| DBQueryRead|
|Befehl| GET /query/procedure/DBSelectedSubmissionGetSheetSelected/:esid|
|Beschreibung| für den Befehl getSheetSelected|

||getExistsPlatform|
| :----------- |:----- |
|Ziel| DBQueryRead|
|Befehl| GET /query/procedure/DBSelectedSubmissionGetExistsPlatform|
|Beschreibung| für den Befehl getExistsPlatform|

||getSamplesInfo|
| :----------- |:----- |
|Ziel| DBQueryRead|
|Befehl| GET /query/procedure/DBSelectedSubmissionGetExistsPlatform|
|Beschreibung| für den Befehl getSamplesInfo|


## <a name='anbindungen'></a>Anbindungen (Component.json => Connector)
Eine Anbindung verlangt von einer anderen Komponente (`Ziel`) die Anbindung/Verbindung zu dieser Komponente.
Wenn eine Anbindung den aufzurufenden Befehl vorgibt, dann ist die Notation: METHODE URL (PRIORITÄT).

|Ausgang|request|
| :----------- |:----- |
|Ziel| CLocalObjectRequest|
|Beschreibung| damit DBSelectedSubmission als lokales Objekt aufgerufen werden kann|

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
