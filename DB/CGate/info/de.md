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

Die CGate stellt zugangskontrollierte Zugänge zu internen Komponenten bereit.

| Themen |
| :- |
| [Befehle/Eingänge (Commands.json)](#eingaenge) |
| [Ausgänge (Component.json => Links)](#ausgaenge) |
| [Anbindungen (Component.json => Connector)](#anbindungen) |

## <a name='eingaenge'></a>Befehle/Eingänge (Commands.json)
Diese Befehle bietet diese Komponente als Aufruf an.

||call|
| :----------- |:----- |
|Beschreibung| über diese Anfrage werden alle Interface-Anfragen behandelt|
|Befehl| PUT,GET,HEAD,OPTIONS,POST,DELETE /interface/:profile/:component/:path+|
|Eingabetyp| binary|
|Ausgabetyp| binary|


## <a name='ausgaenge'></a>Ausgänge (Component.json => Links)
Wenn eine Komponente selbst noch Unteranfragen an anderen Komponenten stellen möchte, dann werden diese über die `Ausgänge` bearbeitet.
Dabei kann ein Ausgang bereits auf eine Komponente gerichtet sein (`Ziel`) oder durch die Zielkomponente selbst angebunden werden (`Connector`)

||getComponentProfileWithAuthLogin|
| :----------- |:----- |
|Ziel| DBGate|
|Befehl| GET /gateprofile/gateprofile/:profName/auth/:authType/component/:component/login/:login|
|Beschreibung| für den Befehl getComponentProfileWithAuthLogin|

||getComponentProfileWithAuth|
| :----------- |:----- |
|Ziel| DBGate|
|Befehl| GET /gateprofile/gateprofile/:profName/auth/:authType/component/:component|
|Beschreibung| für den Befehl getComponentProfileWithAuth|


## <a name='anbindungen'></a>Anbindungen (Component.json => Connector)
Eine Anbindung verlangt von einer anderen Komponente (`Ziel`) die Anbindung/Verbindung zu dieser Komponente.
Wenn eine Anbindung den aufzurufenden Befehl vorgibt, dann ist die Notation: METHODE URL (PRIORITÄT).

|Ausgang|request|
| :----------- |:----- |
|Ziel| CLocalObjectRequest|
|Beschreibung| damit CGate als lokales Objekt aufgerufen werden kann|

|Ausgang|getDescFiles|
| :----------- |:----- |
|Ziel| TDocuView|
|Beschreibung| die Entwicklerdokumentation soll unsere Beschreibungsdatei nutzen|


Stand 25.07.2017
