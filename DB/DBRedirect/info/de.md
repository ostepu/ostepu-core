<!--
  - @file de.md
  -
  - @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
  -
  - @package OSTEPU (https://github.com/ostepu/ostepu-core)
  - @since -
  -
  - @author Till Uhlig <till.uhlig@student.uni-halle.de>
  - @date 2017
  -
 -->

Die DBRedirect ermöglicht den Zugriff auf die `Redirect_X` Tabellen der Datenbank, wobei dese Einträge der Definition von Umleitungslinks dienen. Dazu wird bei einem `POST /platform` Aufruf die nachstehende Tabelle erzeugt. Zu dieser Tabelle gehört die `SelectedSubmission` Datenstruktur.

| Themen |
| :- |
| [Befehle/Eingänge (Commands.json)](#eingaenge) |
| [Ausgänge (Component.json => Links)](#ausgaenge) |
| [Anbindungen (Component.json => Connector)](#anbindungen) |

## <a name='eingaenge'></a>Befehle/Eingänge (Commands.json)
Diese Befehle bietet diese Komponente als Aufruf an.

||addCourse|
| :----------- |:----- |
|Beschreibung| fügt DBRedirect zur Veranstaltung hinzu|
|Befehl| post /course|
|Eingabetyp| Course|
|Ausgabetyp| Course|

||getExistsCourseRedirects|
| :----------- |:----- |
|Beschreibung| prüft, ob die Komponente korrekt für diese Veranstaltung installiert wurde|
|Befehl| get /link/exists/course/:courseid|
|Eingabetyp| -|
|Ausgabetyp| Course|
|||
||Patzhalter|
|Name|courseid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|eine Veranstaltungs ID (`Course`)|

||getRedirectByLocation|
| :----------- |:----- |
|Beschreibung| ermittelt Weiterleitungen anhand ihrer Zeichenorte|
|Befehl| get /redirect/course/:courseid/location/:locname|
|Eingabetyp| -|
|Ausgabetyp| Redirect|
|||
||Patzhalter|
|Name|courseid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|eine Veranstaltungs ID (`Course`)|
|Name|locname|
|Regex|%^([a-zA-Z_]+)$%|
|Beschreibung|der Ortsbezeichner einer Weiterleitung, an welchem diese gezeichnet wird (beispielsweise `sheet`, für alle Übungsserien)|

||addRedirect|
| :----------- |:----- |
|Beschreibung| fügt einen neuen Eintrag ein|
|Befehl| post /redirect/course/:courseid|
|Eingabetyp| Redirect|
|Ausgabetyp| Redirect|
|||
||Patzhalter|
|Name|courseid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|eine Veranstaltungs ID (`Course`)|

||editRedirect|
| :----------- |:----- |
|Beschreibung| editiert eine Weiterleitung|
|Befehl| put /redirect/redirect/:redid|
|Eingabetyp| Redirect|
|Ausgabetyp| Redirect|
|||
||Patzhalter|
|Name|redid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID eines Redirect-Eintrags (`Redirect`)|

||getRedirect|
| :----------- |:----- |
|Beschreibung| liefert den Eintrag einer Weiterleitungs-ID|
|Befehl| get /redirect/redirect/:redid|
|Eingabetyp| -|
|Ausgabetyp| Redirect|
|||
||Patzhalter|
|Name|redid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID eines Redirect-Eintrags (`Redirect`)|

||getCourseRedirects|
| :----------- |:----- |
|Beschreibung| ermittelt alle Weiterleitungen einer Veranstaltung|
|Befehl| get /redirect/course/:courseid|
|Eingabetyp| -|
|Ausgabetyp| Redirect|
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

||deleteRedirect|
| :----------- |:----- |
|Beschreibung| entfernt eine Weiterleitung|
|Befehl| delete /redirect/redirect/:redid|
|Eingabetyp| -|
|Ausgabetyp| Redirect|
|||
||Patzhalter|
|Name|redid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID eines Redirect-Eintrags (`Redirect`)|

||getApiProfiles|
| :----------- |:----- |
|Beschreibung| liefert `GateProfile`-Objekte, welche unsere Befehle in die Standardprofile von CGate einsortieren|
|Befehl| GET /api/profiles|
|Eingabetyp| -|
|Ausgabetyp| GateProfile|


## <a name='ausgaenge'></a>Ausgänge (Component.json => Links)
Wenn eine Komponente selbst noch Unteranfragen an anderen Komponenten stellen möchte, dann werden diese über die `Ausgänge` bearbeitet.
Dabei kann ein Ausgang bereits auf eine Komponente gerichtet sein (`Ziel`) oder durch die Zielkomponente selbst angebunden werden (`Connector`)

||editRedirect|
| :----------- |:----- |
|Ziel| DBQueryWrite|
|Befehl| POST /query|
|Beschreibung| für den Befehl editRedirect|

||deleteRedirect|
| :----------- |:----- |
|Ziel| DBQueryWrite|
|Befehl| POST /query|
|Beschreibung| für den Befehl deleteRedirect|

||addRedirect|
| :----------- |:----- |
|Ziel| DBQueryWrite|
|Befehl| POST /query|
|Beschreibung| für den Befehl addRedirect|

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

||getRedirect|
| :----------- |:----- |
|Ziel| DBQueryRead|
|Befehl| GET /query/procedure/DBRedirectGetRedirect/:profile/:courseid/:redid|
|Beschreibung| für den Befehl getRedirect|

||getRedirectByLocation|
| :----------- |:----- |
|Ziel| DBQueryRead|
|Befehl| GET /query/procedure/DBRedirectGetRedirectByLocation/:profile/:courseid/:locname|
|Beschreibung| für den Befehl getRedirectByLocation|

||getExistsCourseRedirects|
| :----------- |:----- |
|Ziel| DBQueryRead|
|Befehl| GET /query/procedure/DBRedirectGetExistsPlatform/:profile/:courseid|
|Beschreibung| für den Befehl getExistsCourseRedirects|

||getCourseRedirects|
| :----------- |:----- |
|Ziel| DBQueryRead|
|Befehl| GET /query/procedure/DBRedirectGetCourseRedirects/:profile/:courseid|
|Beschreibung| für den Befehl getCourseRedirects|


## <a name='anbindungen'></a>Anbindungen (Component.json => Connector)
Eine Anbindung verlangt von einer anderen Komponente (`Ziel`) die Anbindung/Verbindung zu dieser Komponente.
Wenn eine Anbindung den aufzurufenden Befehl vorgibt, dann ist die Notation: METHODE URL (PRIORITÄT).

|Ausgang|request|
| :----------- |:----- |
|Ziel| CLocalObjectRequest|
|Beschreibung| damit DBRedirect als lokales Objekt aufgerufen werden kann|

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
