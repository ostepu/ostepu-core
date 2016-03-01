<!--
  - @file de.md
  -
  - @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
  -
  - @package OSTEPU (https://github.com/ostepu/system)
  - @since 0.3.4
  -
  - @author Till Uhlig <till.uhlig@student.uni-halle.de>
  - @date 2015
 -->

#### Datenbank
Die DBSubmission ermöglicht den Zugriff auf die `Submission` Tabelle der Datenbank, dabei sollen
studentische Einsendungen verwaltet werden.
Dazu wird bei einem `POST /platform` Aufruf die nachstehende Tabelle erzeugt.

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

#### Datenstruktur
Zu dieser Tabelle gehört die `Submission` Datenstruktur.

#### Eingänge
- courseid = eine Veranstaltungs ID (`Course`)
- userid = die ID eines Nutzerkontos (`User`)
- esid = die ID einer Übungsserie (`ExerciseSheet`)
- eid = die ID einer Aufgabe (`Exercise`)
- suid = die ID einer Einsendung (`Submission`)
- beginStamp = der Anfangsstempel (Unix-Zeitstempel)
- endStamp = der Endstempel (Unix-Zeitstempel)
- selected = bestimmt, ob nur selektierte (`SelectedSubmission`) zurückgegeben werden sollen ('selected' = Ja, sonst = Nein)

| Bezeichnung  | Eingabetyp  | Ausgabetyp | Befehl | Beschreibung |
| :----------- |:-----------:| :---------:| :----- | :----------- |
|editSubmission|Submission|Submission|PUT<br>/submission(/submission)/:suid| editiert eine existierende Einsendung |
|deleteSubmission|-|Submission|DELETE<br>/submission(/submission)/:suid| entfernt eine Einsendung (damit werden auch Korrekturen entfernt und vergebene Punkte) |
|addSubmission|Submission|Submission|POST<br>/submission| fügt eine neue Einsendung ein |
|getExerciseSubmissions|-|Submission|GET<br>/submission/exercise/:eid| gibt alle Einsendungen zu einer Aufgabe zurück |
|getUserExerciseSubmissions|-|Submission|GET<br>/submission/user/:userid/exercise/:eid| gibt alle Einsendungen eines Nutzers zu einer Aufgabe zurück |
|getUserSheetSubmissions|-|Submission|GET<br>/submission/user/:userid/exercisesheet/:esid| gibt alle Einsendungen eines Nutzers zu einer Übungsserie zurück |
|getGroupSubmissions|-|Submission|GET<br>/submission/group/user/:userid/exercisesheet/:esid| liefert alle Einsendungen einer Gruppe (anhand der NutzerId eines Gruppenmitglieds und der Übungsserie) |
|getGroupSelectedSubmissions|-|Submission|GET<br>/submission/group/user/:userid/exercisesheet/:esid/selected| liefert nur die selektierten Einsendungen einer Gruppe (nur diese gehen in die Bewertung ein) |
|getGroupExerciseSubmissions|-|Submission|GET<br>/submission/group/user/:userid/exercise/:eid| gibt die Einsendungen einer Gruppe zu einer Aufgabe |
|getGroupSelectedExerciseSubmissions|-|Submission|GET<br>/submission/group/user/:userid/exercise/:eid/selected| gibt die selektierten Einsendungen einer Gruppe zu einer Aufgabe |
|getGroupSelectedCourseSubmissions|-|Submission|GET<br>/submission/group/user/:userid/course/:courseid/selected| gibt die selektierten Einsendungen einer Gruppe zu einer Veranstaltung |
|getGroupCourseSubmissions|-|Submission|GET<br>/submission/group/user/:userid/course/:courseid| gibt die Einsendungen einer Gruppe zu einer Veranstaltung |
|getSelectedSheetSubmissions|-|Submission|GET<br>/submission/exercisesheet/:esid/selected| gibt alle selektierten Einsendungen einer Übungsserie |
|getAllSubmissions|-|Submission|GET<br>/submission(/submission)(/:selected)(/date/begin/:beginStamp/end/:endStamp)| liefert alle Einsendungen (für alle Veranstaltungen), es kann aber ein bestimmter Zeitraum eingegrenzt und sich auf selektierte Einsendungen festgelegt werden |
|getSelectedExerciseSubmissions|-|Submission|GET<br>/submission/exercise/:eid/selected| gibt alle selektierten Einsendungen einer Aufgabe |
|getSheetSubmissions|-|Submission|GET<br>/submission/exercisesheet/:esid| gibt alle Einsendungen einer Übungsserie |
|getSubmission|-|Submission|GET<br>/submission(/submission)/:suid| liefert eine einzelne Einsendung |
|getCourseSubmissions|-|Submission|GET<br>/submission/course/:courseid| gibt alle selektierten Einsendungen einer Veranstaltung |
|getCourseUserSubmissions|-|Submission|GET<br>/submission/course/:courseid/user/:userid| gibt alle Einsendungen eines Nutzers in einer Veranstaltung |
|getSelectedCourseUserSubmissions|-|Submission|GET<br>/submission/course/:courseid/user/:userid/selected| gibt alle selektierten Einsendungen eines Nutzers in einer Veranstaltung |
|addPlatform|Platform|Platform|POST<br>/platform|installiert dies zugehörige Tabelle und die Prozeduren für diese Plattform|
|deletePlatform|-|Platform|DELETE<br>/platform|entfernt die Tabelle und Prozeduren aus der Plattform|
|getExistsPlatform|-|Platform|GET<br>/link/exists/platform| prüft, ob die Tabelle und die Prozeduren existieren |
|getSamplesInfo|-|-|GET<br>/samples| ??? |
|postSamples|-|Query|POST<br>/samples/course/:courseAmount<br>/user/:userAmount| erzeugt Zufallsdaten (courseAmount = Anzahl der Veranstaltungen, userAmount = Anzahl der Nutzer), anhand der Vorgabe |

#### Ausgänge
- courseid = eine Veranstaltungs ID (`Course`)
- userid = die ID eines Nutzerkontos (`User`)
- esid = die ID einer Übungsserie (`ExerciseSheet`)
- eid = die ID einer Aufgabe (`Exercise`)
- suid = die ID einer Einsendung (`Submission`)
- beginStamp = der Anfangsstempel (Unix-Zeitstempel)
- endStamp = der Endstempel (Unix-Zeitstempel)
- selected = bestimmt, ob nur selektierte (`SelectedSubmission`) zurückgegeben werden sollen ('selected' = Ja, sonst = Nein)

| Bezeichnung  | Ziel  | Verwendung | Beschreibung |
| :----------- |:----- | :--------- | :----------- |
|out2|DBQuery2|POST<br>/query| wird für EDIT, DELETE<br>und POST<br>SQL-Templates verwendet |
|out|DBQuery|POST<br>/query| wird für EDIT, DELETE<br>und POST<br>SQL-Templates verwendet |
|getAllSubmissions|DBQuery2|GET<br>/query/procedure<br>/DBSubmissionGetAllSubmissions/:selected/:beginStamp/:endStamp| Prozeduraufruf |
|getCourseSubmissions|DBQuery2|GET<br>/query/procedure<br>/DBSubmissionGetCourseSubmissions/:courseid| Prozeduraufruf |
|getCourseUserSubmissions|DBQuery2|GET<br>/query/procedure<br>/DBSubmissionGetCourseUserSubmissions/:courseid/:userid| Prozeduraufruf |
|getExerciseSubmissions|DBQuery2|GET<br>/query/procedure<br>/DBSubmissionGetExerciseSubmissions/:eid| Prozeduraufruf |
|getGroupCourseSubmissions|DBQuery2|GET<br>/query/procedure<br>/DBSubmissionGetGroupCourseSubmissions/:userid/:courseid| Prozeduraufruf |
|getGroupExerciseSubmissions|DBQuery2|GET<br>/query/procedure<br>/DBSubmissionGetGroupExerciseSubmissions/:userid/:eid| Prozeduraufruf |
|getGroupSelectedCourseSubmissions|DBQuery2|GET<br>/query/procedure<br>/DBSubmissionGetGroupSelectedCourseSubmissions/:userid/:courseid| Prozeduraufruf |
|getGroupSelectedExerciseSubmissions|DBQuery2|GET<br>/query/procedure<br>/DBSubmissionGetGroupSelectedExerciseSubmissions/:userid/:eid| Prozeduraufruf |
|getGroupSelectedSubmissions|DBQuery2|GET<br>/query/procedure<br>/DBSubmissionGetGroupSelectedSubmissions/:userid/:esid| Prozeduraufruf |
|getGroupSubmissions|DBQuery2|GET<br>/query/procedure<br>/DBSubmissionGetGroupSubmissions/:userid/:esid| Prozeduraufruf |
|getSelectedCourseUserSubmissions|DBQuery2|GET<br>/query/procedure<br>/DBSubmissionGetSelectedCourseUserSubmissions/:courseid/:userid| Prozeduraufruf |
|getSelectedExerciseSubmissions|DBQuery2|GET<br>/query/procedure<br>/DBSubmissionGetSelectedExerciseSubmissions/:eid| Prozeduraufruf |
|getSelectedSheetSubmissions|DBQuery2|GET<br>/query/procedure<br>/DBSubmissionGetSelectedSheetSubmissions/:esid| Prozeduraufruf |
|getSheetSubmissions|DBQuery2|GET<br>/query/procedure<br>/DBSubmissionGetSheetSubmissions/:esid| Prozeduraufruf |
|getSubmission|DBQuery2|GET<br>/query/procedure<br>/DBSubmissionGetSubmission/:suid| Prozeduraufruf |
|getUserExerciseSubmissions|DBQuery2|GET<br>/query/procedure<br>/DBSubmissionGetUserExerciseSubmissions/:userid/:eid| Prozeduraufruf |
|getUserSheetSubmissions|DBQuery2|GET<br>/query/procedure<br>/DBSubmissionGetUserSheetSubmissions/:userid/:esid| Prozeduraufruf |
|getExistsPlatform|DBQuery2|GET<br>/query/procedure<br>/DBSubmissionGetExistsPlatform| Prozeduraufruf |

#### Anbindungen
| Bezeichnung  | Ziel  | Priorität | Beschreibung |
| :----------- |:----- | :--------:| :------------|
|request|CLocalObjectRequest|-| damit DBSubmission als lokales Objekt aufgerufen werden kann |

Stand 13.06.2015