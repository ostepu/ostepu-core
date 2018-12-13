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

Die DBApprovalCondition ermöglicht den Zugriff auf die `ApprovalCondition` Tabelle der Datenbank, dabei sollen Zulassungsbedingungen verwaltet werden. Dazu wird bei einem `POST /platform` Aufruf die nachstehende Tabelle erzeugt. Zu dieser Tabelle gehört die `ApprovalCondition` Datenstruktur.

| Spalte        | Struktur  | Beschreibung | Besonderheit |
| :------       |:---------:| :------------| -----------: |
|AC_id|INT NOT NULL| die ID der Zulassungsbedingung |AUTO_INCREMENT,<br>UNIQUE|
|C_id|INT NOT NULL| ein Verweis auf die zugehörige Veranstaltung (`Course`) |-|
|ET_id|INT NOT NULL| ein Verweis auf einen Aufgabentyp (`ExerciseType`) |-|
|AC_percentage|FLOAT NOT NULL DEFAULT 0| die zu erreichenden Punkte in Prozent. Bsp.: 0.5 für 50% |-|

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

||editApprovalCondition|
| :----------- |:----- |
|Beschreibung| editiert eine Zulassungsbedingung|
|Befehl| PUT /approvalcondition/approvalcondition/:apid|
|Eingabetyp| ApprovalCondition|
|Ausgabetyp| ApprovalCondition|
|||
||Patzhalter|
|Name|apid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID einer Zulassungsbedingung (`ApprovalCondition`)|

||getCourseApprovalConditions|
| :----------- |:----- |
|Beschreibung| ermittelt alle Zulassungsbedingungen einer Veranstaltung|
|Befehl| GET /approvalcondition/course/:courseid|
|Eingabetyp| -|
|Ausgabetyp| ApprovalCondition|
|||
||Patzhalter|
|Name|courseid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|eine Veranstaltungs ID (`Course`)|

||addApprovalCondition|
| :----------- |:----- |
|Beschreibung| fügt eine neue Zulassungsbedingung ein|
|Befehl| POST /approvalcondition|
|Eingabetyp| ApprovalCondition|
|Ausgabetyp| ApprovalCondition|

||postSamples|
| :----------- |:----- |
|Beschreibung| erzeugt Zufallsdaten (courseAmount = Anzahl der Veranstaltungen, userAmount = Anzahl der Nutzer), anhand der Vorgabe|
|Befehl| POST /samples/:amount|
|Eingabetyp| -|
|Ausgabetyp| -|

||getApprovalCondition|
| :----------- |:----- |
|Beschreibung| liefert eine einzelne Zulassungsbedingung|
|Befehl| GET /approvalcondition/approvalcondition/:apid|
|Eingabetyp| -|
|Ausgabetyp| ApprovalCondition|
|||
||Patzhalter|
|Name|apid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID einer Zulassungsbedingung (`ApprovalCondition`)|

||getAllApprovalConditions|
| :----------- |:----- |
|Beschreibung| ermittelt alle Zulassungsbedingungen|
|Befehl| GET /approvalcondition/approvalcondition|
|Eingabetyp| -|
|Ausgabetyp| ApprovalCondition|

||deletePlatform|
| :----------- |:----- |
|Beschreibung| entfernt die Komponente und ihre installierten Bestandteile aus der Plattform|
|Befehl| DELETE /platform|
|Eingabetyp| -|
|Ausgabetyp| Platform|

||deleteApprovalCondition|
| :----------- |:----- |
|Beschreibung| entfernt eine Zulassungsbedingung|
|Befehl| DELETE /approvalcondition/approvalcondition/:apid|
|Eingabetyp| -|
|Ausgabetyp| ApprovalCondition|
|||
||Patzhalter|
|Name|apid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID einer Zulassungsbedingung (`ApprovalCondition`)|

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

||editApprovalCondition|
| :----------- |:----- |
|Ziel| DBQueryWrite|
|Befehl| POST /query|
|Beschreibung| für den Befehl editApprovalCondition|

||deleteApprovalCondition|
| :----------- |:----- |
|Ziel| DBQueryWrite|
|Befehl| POST /query|
|Beschreibung| für den Befehl deleteApprovalCondition|

||addApprovalCondition|
| :----------- |:----- |
|Ziel| DBQueryWrite|
|Befehl| POST /query|
|Beschreibung| für den Befehl addApprovalCondition|

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

||getApprovalCondition|
| :----------- |:----- |
|Ziel| DBQueryRead|
|Befehl| GET /query/procedure/DBApprovalConditionGetApprovalCondition/:apid|
|Beschreibung| für den Befehl getApprovalCondition|

||getAllApprovalConditions|
| :----------- |:----- |
|Ziel| DBQueryRead|
|Befehl| GET /query/procedure/DBApprovalConditionGetAllApprovalConditions|
|Beschreibung| für den Befehl getAllApprovalConditions|

||getCourseApprovalConditions|
| :----------- |:----- |
|Ziel| DBQueryRead|
|Befehl| GET /query/procedure/DBApprovalConditionGetCourseApprovalConditions/:courseid|
|Beschreibung| für den Befehl getCourseApprovalConditions|

||getExistsPlatform|
| :----------- |:----- |
|Ziel| DBQueryRead|
|Befehl| GET /query/procedure/DBApprovalConditionGetExistsPlatform|
|Beschreibung| für den Befehl getExistsPlatform|

||getSamplesInfo|
| :----------- |:----- |
|Ziel| DBQueryRead|
|Befehl| GET /query/procedure/DBApprovalConditionGetExistsPlatform|
|Beschreibung| für den Befehl getSamplesInfo|


## <a name='anbindungen'></a>Anbindungen (Component.json => Connector)
Eine Anbindung verlangt von einer anderen Komponente (`Ziel`) die Anbindung/Verbindung zu dieser Komponente.
Wenn eine Anbindung den aufzurufenden Befehl vorgibt, dann ist die Notation: METHODE URL (PRIORITÄT).

|Ausgang|request|
| :----------- |:----- |
|Ziel| CLocalObjectRequest|
|Beschreibung| damit DBApprovalCondition als lokales Objekt aufgerufen werden kann|

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
