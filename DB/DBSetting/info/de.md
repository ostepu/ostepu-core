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

Die DBSetting ermöglicht den Zugriff auf die `SETTING_X` Tabellen der Datenbank, dabei sollen Veranstaltungseinstellungen verwaltet werden. Dazu wird bei einem `POST /course` Aufruf die nachstehende Tabelle erzeugt (X = ID der Veranstaltung). Zu dieser Tabelle gehört die `Session` Datenstruktur.

| Spalte  | Struktur  | Beschreibung | Besonderheit |
| :------ |:---------:| :------------| -----------: |
|SET_id   |INT NOT NULL| die ID der Einstellung |AUTO_INCREMENT,<br>UNIQUE|
|SET_name |VARCHAR(255) NOT NULL| ein Bezeichner, wird exakt so auch zur Bestimmung des Wertes benötigt |UNIQUE|
|SET_state|VARCHAR(255) NOT NULL DEFAULT ''| der Zustand/Wert der Einstellung (Bsp.: 1) ' |-|
|SET_type |VARCHAR(255) NOT NULL DEFAULT 'TEXT'| der erwartete Typ (wird in der Oberfläche benötigt, damit der Nutzer den Wert entsprechend eingeben kann). Erlaubt sind: TEXT (Text), INT (Zahl/Integer), BOOL (Wahrheitswert als 0/1), TIMESTAMP (Unix-Zeitstempel)  |-|
|SET_category|VARCHAR(255) NOT NULL DEFAULT ''| Ein Bezeichner für die Kategorie (submissions, markings, userManagement...) ' |-|

| Themen |
| :- |
| [Befehle/Eingänge (Commands.json)](#eingaenge) |
| [Ausgänge (Component.json => Links)](#ausgaenge) |
| [Anbindungen (Component.json => Connector)](#anbindungen) |

## <a name='eingaenge'></a>Befehle/Eingänge (Commands.json)
Diese Befehle bietet diese Komponente als Aufruf an.

||addCourse|
| :----------- |:----- |
|Beschreibung| fügt DBSetting zur Veranstaltung hinzu|
|Befehl| post /course|
|Eingabetyp| Course|
|Ausgabetyp| Course|

||getExistsCourseSettings|
| :----------- |:----- |
|Beschreibung| prüft, ob DBSettings für diese Veranstaltung installiert wurde|
|Befehl| get /link/exists/course/:courseid|
|Eingabetyp| -|
|Ausgabetyp| Setting|
|||
||Patzhalter|
|Name|courseid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|eine Veranstaltungs ID (`Course`)|

||getSettingByName|
| :----------- |:----- |
|Beschreibung| ermittelt einen Eintrag anhand des Namens (`SET_name`)|
|Befehl| get /setting/course/:courseid/name/:setname|
|Eingabetyp| -|
|Ausgabetyp| Setting|
|||
||Patzhalter|
|Name|courseid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|eine Veranstaltungs ID (`Course`)|
|Name|setname|
|Regex|%^([a-zA-Z0-9_]+)$%|
|Beschreibung|der Name einer Einstellung `SET_name` aus `Setting`|

||addSetting|
| :----------- |:----- |
|Beschreibung| fügt einen neuen Setting-Eintrag ein|
|Befehl| post /setting/course/:courseid|
|Eingabetyp| Setting|
|Ausgabetyp| Setting|
|||
||Patzhalter|
|Name|courseid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|eine Veranstaltungs ID (`Course`)|

||editSetting|
| :----------- |:----- |
|Beschreibung| editiert einen Eintrag|
|Befehl| put /setting/setting/:setid|
|Eingabetyp| Setting|
|Ausgabetyp| Setting|
|||
||Patzhalter|
|Name|setid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID einer Einstellung (`Setting_X`), bestehend aus der ID und der Veranstaltung|

||getSetting|
| :----------- |:----- |
|Beschreibung| liefert ein einzelnes Setting anhand seiner ID|
|Befehl| get /setting/setting/:setid|
|Eingabetyp| -|
|Ausgabetyp| Setting|
|||
||Patzhalter|
|Name|setid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID einer Einstellung (`Setting_X`), bestehend aus der ID und der Veranstaltung|

||getCourseSettings|
| :----------- |:----- |
|Beschreibung| ermittelt alle Settings einer Veranstaltung|
|Befehl| get /setting/course/:courseid|
|Eingabetyp| -|
|Ausgabetyp| Setting|
|||
||Patzhalter|
|Name|courseid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|eine Veranstaltungs ID (`Course`)|

||deleteCourse|
| :----------- |:----- |
|Beschreibung| entfernt die Komponente aus der Veranstaltung|
|Befehl| delete /course/:courseid|
|Eingabetyp| -|
|Ausgabetyp| Course|
|||
||Patzhalter|
|Name|courseid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|eine Veranstaltungs ID (`Course`)|

||deleteSetting|
| :----------- |:----- |
|Beschreibung| entfernt einen Setting-Eintrag|
|Befehl| delete /setting/setting/:setid|
|Eingabetyp| -|
|Ausgabetyp| Setting|
|||
||Patzhalter|
|Name|setid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID einer Einstellung (`Setting_X`), bestehend aus der ID und der Veranstaltung|

||getApiProfiles|
| :----------- |:----- |
|Beschreibung| liefert `GateProfile`-Objekte, welche unsere Befehle in die Standardprofile von CGate einsortieren|
|Befehl| GET /api/profiles|
|Eingabetyp| -|
|Ausgabetyp| GateProfile|


## <a name='ausgaenge'></a>Ausgänge (Component.json => Links)
Wenn eine Komponente selbst noch Unteranfragen an anderen Komponenten stellen möchte, dann werden diese über die `Ausgänge` bearbeitet.
Dabei kann ein Ausgang bereits auf eine Komponente gerichtet sein (`Ziel`) oder durch die Zielkomponente selbst angebunden werden (`Connector`)

||editSetting|
| :----------- |:----- |
|Ziel| DBQueryWrite|
|Befehl| POST /query|
|Beschreibung| für den Befehl editSetting|

||deleteSetting|
| :----------- |:----- |
|Ziel| DBQueryWrite|
|Befehl| POST /query|
|Beschreibung| für den Befehl deleteSetting|

||addSetting|
| :----------- |:----- |
|Ziel| DBQueryWrite|
|Befehl| POST /query|
|Beschreibung| für den Befehl addSetting|

||deleteCourse|
| :----------- |:----- |
|Ziel| DBQuerySetup|
|Befehl| POST /query|
|Beschreibung| für den Befehl deleteCourse|

||addCourse|
| :----------- |:----- |
|Ziel| DBQuerySetup|
|Befehl| POST /query|
|Beschreibung| für den Befehl addCourse|

||getSetting|
| :----------- |:----- |
|Ziel| DBQueryRead|
|Befehl| GET /query/procedure/DBSettingGetSetting/:profile/:courseid/:setid|
|Beschreibung| für den Befehl getSetting|

||getSettingByName|
| :----------- |:----- |
|Ziel| DBQueryRead|
|Befehl| GET /query/procedure/DBSettingGetSettingByName/:profile/:courseid/:setname|
|Beschreibung| für den Befehl getSettingByName|

||getExistsCourseSettings|
| :----------- |:----- |
|Ziel| DBQueryRead|
|Befehl| GET /query/procedure/DBSettingGetExistsPlatform/:profile/:courseid|
|Beschreibung| für den Befehl getExistsCourseSettings|

||getCourseSettings|
| :----------- |:----- |
|Ziel| DBQueryRead|
|Befehl| GET /query/procedure/DBSettingGetCourseSettings/:profile/:courseid|
|Beschreibung| für den Befehl getCourseSettings|


## <a name='anbindungen'></a>Anbindungen (Component.json => Connector)
Eine Anbindung verlangt von einer anderen Komponente (`Ziel`) die Anbindung/Verbindung zu dieser Komponente.
Wenn eine Anbindung den aufzurufenden Befehl vorgibt, dann ist die Notation: METHODE URL (PRIORITÄT).

|Ausgang|request|
| :----------- |:----- |
|Ziel| CLocalObjectRequest|
|Beschreibung| damit DBSetting als lokales Objekt aufgerufen werden kann|

|Ausgang|postCourse|
| :----------- |:----- |
|Ziel| LCourse|
|Beschreibung| wenn eine neue Veranstaltung angelegt wird, dann wollen wir auch aufgerufen werden|

|Ausgang|deleteCourse|
| :----------- |:----- |
|Ziel| LCourse|
|Beschreibung| wenn eine Veranstaltung gelöscht wird, dann müssen auch unsere Tabellen entfernt werden|

|Ausgang|getDescFiles|
| :----------- |:----- |
|Ziel| TDocuView|
|Beschreibung| die Entwicklerdokumentation soll unsere Beschreibungsdatei nutzen|

|Ausgang|getComponentProfiles|
| :----------- |:----- |
|Ziel| TApiConfiguration|
|Beschreibung| damit unsere Aufrufe in die Standardprofile der CGate einsortiert werden|


Stand 25.07.2017
