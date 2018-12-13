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

Die DBExerciseType ermöglicht den Zugriff auf die `ExerciseType` Tabelle der Datenbank, dabei sollen Typen von Aufgaben (Punktearten) verwaltet werden. Dazu wird bei einem `POST /platform` Aufruf die nachstehende Tabelle erzeugt. Zu dieser Tabelle gehört die `ExerciseType` Datenstruktur.

| Spalte        | Struktur  | Beschreibung | Besonderheit |
| :------       |:---------:| :------------| -----------: |
|ET_id|INT NOT NULL| die ID des Bewertungstyps |AUTO_INCREMENT,<br>UNIQUE|
|ET_name|VARCHAR(45) NOT NULL| ein Bezeichner, Bsp.: Theorie, Praxis |-|

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
|Befehl| POST /samples/:amount|
|Eingabetyp| -|
|Ausgabetyp| -|

||deleteExerciseType|
| :----------- |:----- |
|Beschreibung| entfernt einen Eintrag anhand seiner ID|
|Befehl| DELETE /exercisetype/exercisetype/:etid|
|Eingabetyp| -|
|Ausgabetyp| ExerciseType|
|||
||Patzhalter|
|Name|etid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|eine Aufgabetyp ID (`ExerciseType`)|

||getAllExerciseTypes|
| :----------- |:----- |
|Beschreibung| liefert alle Einträge|
|Befehl| GET /exercisetype|
|Eingabetyp| -|
|Ausgabetyp| ExerciseType|

||getExistsPlatform|
| :----------- |:----- |
|Beschreibung| prüft, ob die Tabelle und die Prozeduren existieren und die Komponente generell vollständig installiert ist|
|Befehl| GET /link/exists/platform|
|Eingabetyp| -|
|Ausgabetyp| Platform|

||getExerciseType|
| :----------- |:----- |
|Beschreibung| ermittelt einen einzelnen Eintrag anhand seiner ID|
|Befehl| GET /exercisetype/exercisetype/:etid|
|Eingabetyp| -|
|Ausgabetyp| ExerciseType|
|||
||Patzhalter|
|Name|etid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|eine Aufgabetyp ID (`ExerciseType`)|

||editExerciseType|
| :----------- |:----- |
|Beschreibung| editiert einen Eintrag|
|Befehl| PUT /exercisetype/exercisetype/:etid|
|Eingabetyp| ExerciseType|
|Ausgabetyp| ExerciseType|
|||
||Patzhalter|
|Name|etid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|eine Aufgabetyp ID (`ExerciseType`)|

||deletePlatform|
| :----------- |:----- |
|Beschreibung| entfernt die Komponente und ihre installierten Bestandteile aus der Plattform|
|Befehl| DELETE /platform|
|Eingabetyp| -|
|Ausgabetyp| Platform|

||addPlatform|
| :----------- |:----- |
|Beschreibung| installiert die zugehörige Tabelle und die Prozeduren für diese Plattform|
|Befehl| POST /platform|
|Eingabetyp| Platform|
|Ausgabetyp| Platform|

||getSamplesInfo|
| :----------- |:----- |
|Beschreibung| liefert die Bezeichner der betroffenen Tabellen|
|Befehl| GET /samples|
|Eingabetyp| -|
|Ausgabetyp| -|

||addExerciseType|
| :----------- |:----- |
|Beschreibung| fügt einen neuen Eintrag ein|
|Befehl| POST /exercisetype|
|Eingabetyp| ExerciseType|
|Ausgabetyp| ExerciseType|

||getApiProfiles|
| :----------- |:----- |
|Beschreibung| liefert `GateProfile`-Objekte, welche unsere Befehle in die Standardprofile von CGate einsortieren|
|Befehl| GET /api/profiles|
|Eingabetyp| -|
|Ausgabetyp| GateProfile|


## <a name='ausgaenge'></a>Ausgänge (Component.json => Links)
Wenn eine Komponente selbst noch Unteranfragen an andere Komponenten stellen möchte, dann werden diese über die `Ausgänge` bearbeitet.
Dabei kann ein Ausgang bereits auf eine Komponente gerichtet sein (`Ziel`) oder durch die Zielkomponente selbst angebunden werden (`Connector`)

||editExerciseType|
| :----------- |:----- |
|Ziel| DBQueryWrite|
|Befehl| POST /query|
|Beschreibung| für den Befehl editExerciseType|

||deleteExerciseType|
| :----------- |:----- |
|Ziel| DBQueryWrite|
|Befehl| POST /query|
|Beschreibung| für den Befehl deleteExerciseType|

||addExerciseType|
| :----------- |:----- |
|Ziel| DBQueryWrite|
|Befehl| POST /query|
|Beschreibung| für den Befehl addExerciseType|

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

||getExerciseType|
| :----------- |:----- |
|Ziel| DBQueryRead|
|Befehl| GET /query/procedure/DBExerciseTypeGetExerciseType/:etid|
|Beschreibung| für den Befehl getExerciseType|

||getAllExerciseTypes|
| :----------- |:----- |
|Ziel| DBQueryRead|
|Befehl| GET /query/procedure/DBExerciseTypeGetAllExerciseTypes|
|Beschreibung| für den Befehl getAllExerciseTypes|

||getExistsPlatform|
| :----------- |:----- |
|Ziel| DBQueryRead|
|Befehl| GET /query/procedure/DBExerciseTypeGetExistsPlatform|
|Beschreibung| für den Befehl getExistsPlatform|

||getSamplesInfo|
| :----------- |:----- |
|Ziel| DBQueryRead|
|Befehl| GET /query/procedure/DBExerciseTypeGetExistsPlatform|
|Beschreibung| für den Befehl getSamplesInfo|


## <a name='anbindungen'></a>Anbindungen (Component.json => Connector)
Eine Anbindung verlangt von einer anderen Komponente (`Ziel`) die Anbindung/Verbindung zu dieser Komponente.
Wenn eine Anbindung den aufzurufenden Befehl vorgibt, dann ist die Notation: METHODE URL (PRIORITÄT).

|Ausgang|request|
| :----------- |:----- |
|Ziel| CLocalObjectRequest|
|Beschreibung| damit DBExerciseType als lokales Objekt aufgerufen werden kann|

|Ausgang|postPlatform|
| :----------- |:----- |
|Ziel| CInstall|
|Beschreibung| der Installationsassistent soll uns bei der Plattforminstallation aufrufen|

|Ausgang|getDescFiles|
| :----------- |:----- |
|Ziel| TDocuView|
|Beschreibung| die Entwicklerdokumentation soll unsere Beschreibungsdatei nutzen|

|Ausgang|getComponentProfiles|
| :----------- |:----- |
|Ziel| TApiConfiguration|
|Beschreibung| damit unsere Aufrufe in die Standardprofile der CGate einsortiert werden|


Stand 25.07.2017
