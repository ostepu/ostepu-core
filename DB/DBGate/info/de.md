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

Die DBGate ermöglicht den Zugriff auf die `GateAuth`, `GateProfile` und `GateRule` Tabellen der Datenbank. Dazu wird bei einem `POST /platform` Aufruf die nachstehende Tabelle erzeugt.

| Themen |
| :- |
| [Befehle/Eingänge (Commands.json)](#eingaenge) |
| [Ausgänge (Component.json => Links)](#ausgaenge) |
| [Anbindungen (Component.json => Connector)](#anbindungen) |

## <a name='eingaenge'></a>Befehle/Eingänge (Commands.json)
Diese Befehle bietet diese Komponente als Aufruf an.

||getProfilesByAuth|
| :----------- |:----- |
|Beschreibung| ermittelt alle Profile anhand der Authentifizierungsmethode (noAuth, httpAuth, tokenAuth)|
|Befehl| GET /gateprofile/auth/:authType|
|Eingabetyp| -|
|Ausgabetyp| GateProfile|
|||
||Patzhalter|
|Name|authType|
|Regex|%^([a-zA-Z0-9_]+)$%|
|Beschreibung|ein Authentifizierungstyp (`noAuth`, `httpAuth`, `tokenAuth`|

||editGateAuth|
| :----------- |:----- |
|Beschreibung| editiert eine Authentifizierung|
|Befehl| PUT /gateauth/gateauth/:gaid|
|Eingabetyp| GateAuth|
|Ausgabetyp| GateAuth|
|||
||Patzhalter|
|Name|gaid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID einer Authentifizierung (`GateAuth`)|

||getProfileByName|
| :----------- |:----- |
|Beschreibung| ermittelt ein Profil anhand seines Namens|
|Befehl| GET /gateprofile/name/:gpname|
|Eingabetyp| -|
|Ausgabetyp| GateProfile|
|||
||Patzhalter|
|Name|gpname|
|Regex|%^([a-zA-Z0-9_]+)$%|
|Beschreibung|der Name eines Gate-Profils|

||getComponentProfileWithAuth|
| :----------- |:----- |
|Beschreibung| ermittelt einen Eintrag anhand des Namens und der Komponente (+Authentifizierungsmethode)|
|Befehl| GET /gateprofile/gateprofile/:gpname/auth/:authType/component/:component|
|Eingabetyp| -|
|Ausgabetyp| GateProfile|
|||
||Patzhalter|
|Name|gpname|
|Regex|%^([a-zA-Z0-9_]+)$%|
|Beschreibung|der Name eines Gate-Profils|
|Name|authType|
|Regex|%^([a-zA-Z0-9_]+)$%|
|Beschreibung|ein Authentifizierungstyp (`noAuth`, `httpAuth`, `tokenAuth`|
|Name|component|
|Regex|%^([a-zA-Z0-9_]+)$%|
|Beschreibung|der Name einer Komponente|

||editGateProfile|
| :----------- |:----- |
|Beschreibung| editiert ein Profil|
|Befehl| PUT /gateprofile/gateprofile/:gpid|
|Eingabetyp| GateProfile|
|Ausgabetyp| GateProfile|
|||
||Patzhalter|
|Name|gpid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID eines Gate-Profile (`GateProfile`)|

||addGateProfile|
| :----------- |:----- |
|Beschreibung| fügt ein neues Profil ein|
|Befehl| POST /gateprofile|
|Eingabetyp| GateProfile|
|Ausgabetyp| GateProfile|

||addGateAuth|
| :----------- |:----- |
|Beschreibung| fügt eine neue Authentifizierung ein|
|Befehl| POST /gateauth|
|Eingabetyp| GateAuth|
|Ausgabetyp| GateAuth|

||editGateRule|
| :----------- |:----- |
|Beschreibung| editiert eine Regel|
|Befehl| PUT /gaterule/gaterule/:grid|
|Eingabetyp| GateRule|
|Ausgabetyp| GateRule|
|||
||Patzhalter|
|Name|grid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID einer Gate-Regel (`GateRule`)|

||deleteGateProfile|
| :----------- |:----- |
|Beschreibung| entfernt ein Profil|
|Befehl| DELETE /gateprofile/gateprofile/:gpid|
|Eingabetyp| -|
|Ausgabetyp| GateProfile|
|||
||Patzhalter|
|Name|gpid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID eines Gate-Profile (`GateProfile`)|

||deleteGateRule|
| :----------- |:----- |
|Beschreibung| entfernt eine Regel|
|Befehl| DELETE /gaterule/gaterule/:grid|
|Eingabetyp| -|
|Ausgabetyp| GateRule|
|||
||Patzhalter|
|Name|grid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID einer Gate-Regel (`GateRule`)|

||addGateRule|
| :----------- |:----- |
|Beschreibung| fügt eine neue Regel ein|
|Befehl| POST /gaterule|
|Eingabetyp| GateRule|
|Ausgabetyp| GateRule|

||deletePlatform|
| :----------- |:----- |
|Beschreibung| entfernt die Komponente und ihre installierten Bestandteile aus der Plattform|
|Befehl| DELETE /platform|
|Eingabetyp| -|
|Ausgabetyp| Platform|

||getProfileWithAuth|
| :----------- |:----- |
|Beschreibung| ermittelt ein Profil anhand seiner ID und des Authentifizierungstyps|
|Befehl| GET /gateprofile/gateprofile/:gpid/auth/:authType|
|Eingabetyp| -|
|Ausgabetyp| GateProfile|
|||
||Patzhalter|
|Name|gpid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID eines Gate-Profile (`GateProfile`)|
|Name|authType|
|Regex|%^([a-zA-Z0-9_]+)$%|
|Beschreibung|ein Authentifizierungstyp (`noAuth`, `httpAuth`, `tokenAuth`|

||getProfile|
| :----------- |:----- |
|Beschreibung| ermittelt ein Profil anhand seiner ID|
|Befehl| GET /gateprofile/gateprofile/:gpid|
|Eingabetyp| -|
|Ausgabetyp| GateProfile|
|||
||Patzhalter|
|Name|gpid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID eines Gate-Profile (`GateProfile`)|

||deleteGateAuth|
| :----------- |:----- |
|Beschreibung| entfernt eine Authentifizierung|
|Befehl| DELETE /gateauth/gateauth/:gaid|
|Eingabetyp| -|
|Ausgabetyp| GateAuth|
|||
||Patzhalter|
|Name|gaid|
|Regex|%^([0-9_]+)$%|
|Beschreibung|die ID einer Authentifizierung (`GateAuth`)|

||getAllProfiles|
| :----------- |:----- |
|Beschreibung| liefert alle Profile|
|Befehl| GET /gateprofile|
|Eingabetyp| -|
|Ausgabetyp| GateProfile|

||deleteGateProfileByName|
| :----------- |:----- |
|Beschreibung| löscht ein Profil über seinen Namen|
|Befehl| DELETE /gateprofile/name/:gpname|
|Eingabetyp| -|
|Ausgabetyp| GateProfile|
|||
||Patzhalter|
|Name|gpname|
|Regex|%^([a-zA-Z0-9_]+)$%|
|Beschreibung|der Name eines Gate-Profils|

||getProfilesByComponent|
| :----------- |:----- |
|Beschreibung| ermittelt die Profile zu einer Komponente|
|Befehl| GET /gateprofile/component/:component|
|Eingabetyp| -|
|Ausgabetyp| GateProfile|
|||
||Patzhalter|
|Name|component|
|Regex|%^([a-zA-Z0-9_]+)$%|
|Beschreibung|der Name einer Komponente|

||addPlatform|
| :----------- |:----- |
|Beschreibung| installiert die zugehörige Tabelle und die Prozeduren für diese Plattform|
|Befehl| POST /platform|
|Eingabetyp| Platform|
|Ausgabetyp| Platform|

||getComponentProfileWithAuthLogin|
| :----------- |:----- |
|Beschreibung| ermittelt Profile anhand des Profils, der Methode, einer Komponente und dem Login-Namen|
|Befehl| GET /gateprofile/gateprofile/:gpname/auth/:authType/component/:component/login/:login|
|Eingabetyp| -|
|Ausgabetyp| GateProfile|
|||
||Patzhalter|
|Name|gpname|
|Regex|%^([a-zA-Z0-9_]+)$%|
|Beschreibung|der Name eines Gate-Profils|
|Name|authType|
|Regex|%^([a-zA-Z0-9_]+)$%|
|Beschreibung|ein Authentifizierungstyp (`noAuth`, `httpAuth`, `tokenAuth`|
|Name|component|
|Regex|%^([a-zA-Z0-9_]+)$%|
|Beschreibung|der Name einer Komponente|
|Name|login|
|Regex|%^([a-zA-Z0-9_]+)$%|
|Beschreibung|der Login (`GA_login`)|

||getExistsPlatform|
| :----------- |:----- |
|Beschreibung| prüft, ob die Tabelle und die Prozeduren existieren und die Komponente generell vollständig installiert ist|
|Befehl| GET /link/exists/platform|
|Eingabetyp| -|
|Ausgabetyp| Platform|

||getApiProfiles|
| :----------- |:----- |
|Beschreibung| liefert `GateProfile`-Objekte, welche unsere Befehle in die Standardprofile von CGate einsortieren|
|Befehl| GET /api/profiles|
|Eingabetyp| -|
|Ausgabetyp| GateProfile|


## <a name='ausgaenge'></a>Ausgänge (Component.json => Links)
Wenn eine Komponente selbst noch Unteranfragen an anderen Komponenten stellen möchte, dann werden diese über die `Ausgänge` bearbeitet.
Dabei kann ein Ausgang bereits auf eine Komponente gerichtet sein (`Ziel`) oder durch die Zielkomponente selbst angebunden werden (`Connector`)

||editGateProfile|
| :----------- |:----- |
|Ziel| DBQueryWrite|
|Befehl| POST /query|
|Beschreibung| für den Befehl editGateProfile|

||editGateAuth|
| :----------- |:----- |
|Ziel| DBQueryWrite|
|Befehl| POST /query|
|Beschreibung| für den Befehl editGateAuth|

||editGateRule|
| :----------- |:----- |
|Ziel| DBQueryWrite|
|Befehl| POST /query|
|Beschreibung| für den Befehl editGateRule|

||deleteGateProfile|
| :----------- |:----- |
|Ziel| DBQueryWrite|
|Befehl| POST /query|
|Beschreibung| für den Befehl deleteGateProfile|

||deleteGateProfileByName|
| :----------- |:----- |
|Ziel| DBQueryWrite|
|Befehl| POST /query|
|Beschreibung| für den Befehl deleteGateProfileByName|

||deleteGateRule|
| :----------- |:----- |
|Ziel| DBQueryWrite|
|Befehl| POST /query|
|Beschreibung| für den Befehl deleteGateRule|

||deleteGateAuth|
| :----------- |:----- |
|Ziel| DBQueryWrite|
|Befehl| POST /query|
|Beschreibung| für den Befehl deleteGateAuth|

||addGateProfile|
| :----------- |:----- |
|Ziel| DBQueryWrite|
|Befehl| POST /query|
|Beschreibung| für den Befehl addGateProfile|

||addGateRule|
| :----------- |:----- |
|Ziel| DBQueryWrite|
|Befehl| POST /query|
|Beschreibung| für den Befehl addGateRule|

||addGateAuth|
| :----------- |:----- |
|Ziel| DBQueryWrite|
|Befehl| POST /query|
|Beschreibung| für den Befehl addGateAuth|

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

||getAllProfiles|
| :----------- |:----- |
|Ziel| DBQueryRead|
|Befehl| GET /query/procedure/DBGateGetAllGateProfiles/:profile/:authProfile/:ruleProfile|
|Beschreibung| für den Befehl getAllProfiles|

||getProfile|
| :----------- |:----- |
|Ziel| DBQueryRead|
|Befehl| GET /query/procedure/DBGateGetGateProfile/:profile/:authProfile/:ruleProfile/:gpid|
|Beschreibung| für den Befehl getProfile|

||getProfileByName|
| :----------- |:----- |
|Ziel| DBQueryRead|
|Befehl| GET /query/procedure/DBGateGetGateProfileByName/:profile/:authProfile/:ruleProfile/:name|
|Beschreibung| für den Befehl getProfileByName|

||getProfilesByAuth|
| :----------- |:----- |
|Ziel| DBQueryRead|
|Befehl| GET /query/procedure/DBGateGetGateProfilesByAuth/:profile/:authProfile/:ruleProfile/:authType|
|Beschreibung| für den Befehl getProfilesByAuth|

||getProfilesByComponent|
| :----------- |:----- |
|Ziel| DBQueryRead|
|Befehl| GET /query/procedure/DBGateGetGateProfilesByComponent/:profile/:authProfile/:ruleProfile/:component|
|Beschreibung| für den Befehl getProfilesByComponent|

||getProfileWithAuth|
| :----------- |:----- |
|Ziel| DBQueryRead|
|Befehl| GET /query/procedure/DBGateGetGateProfileWithAuth/:profile/:authProfile/:ruleProfile/:gpid/:authType|
|Beschreibung| für den Befehl getProfileWithAuth|

||getComponentProfileWithAuth|
| :----------- |:----- |
|Ziel| DBQueryRead|
|Befehl| GET /query/procedure/DBGateGetComponentGateProfileWithAuth/:profile/:authProfile/:ruleProfile/:profName/:authType/:component|
|Beschreibung| für den Befehl getComponentProfileWithAuth|

||getComponentProfileWithAuthLogin|
| :----------- |:----- |
|Ziel| DBQueryRead|
|Befehl| GET /query/procedure/DBGateGetComponentGateProfileWithAuthLogin/:profile/:authProfile/:ruleProfile/:profName/:authType/:component/:login|
|Beschreibung| für den Befehl getComponentProfileWithAuthLogin|

||getExistsPlatform|
| :----------- |:----- |
|Ziel| DBQueryRead|
|Befehl| GET /query/procedure/DBGateGetExistsPlatform/:profile|
|Beschreibung| für den Befehl getExistsPlatform|


## <a name='anbindungen'></a>Anbindungen (Component.json => Connector)
Eine Anbindung verlangt von einer anderen Komponente (`Ziel`) die Anbindung/Verbindung zu dieser Komponente.
Wenn eine Anbindung den aufzurufenden Befehl vorgibt, dann ist die Notation: METHODE URL (PRIORITÄT).

|Ausgang|request|
| :----------- |:----- |
|Ziel| CLocalObjectRequest|
|Beschreibung| damit DBGate als lokales Objekt aufgerufen werden kann|

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
