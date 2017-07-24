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

| Themen |
| :- |
| [Befehle/Eingänge (Commands.json)](#Eingaenge) |
| [Ausgänge (Component.json => Links)](#Ausgaenge) |
| [Anbindungen (Component.json => Connector)](#Anbindungen) |

## Befehle/Eingänge (Commands.json)  {#Eingaenge}
Diese Befehle bietet diese Komponente als Aufruf an.

|||
| :----------- |:----- |
|Befehl| POST /:string+|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Befehl| PUT /:string+|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Befehl| GET /:string+|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:----- |
|Befehl| DELETE /:string+|
|Eingabetyp| -|
|Ausgabetyp| -|


## Ausgänge (Component.json => Links)  {#Ausgaenge}
Wenn eine Komponente selbst noch Unteranfragen an anderen Komponenten stellen möchte, dann werden diese über die `Ausgänge` bearbeitet.
Dabei kann ein Ausgang bereits auf eine Komponente gerichtet sein (`Ziel`) oder durch die Zielkomponente selbst angebunden werden (`Connector`)

||database|
| :----------- |:----- |
|Ziel| DBControl|
|Befehl| POST /path+|
|Beschreibung| für den Befehl database|

||database|
| :----------- |:----- |
|Ziel| DBControl|
|Befehl| GET /path+|
|Beschreibung| für den Befehl database|

||database|
| :----------- |:----- |
|Ziel| DBControl|
|Befehl| PUT /path+|
|Beschreibung| für den Befehl database|

||database|
| :----------- |:----- |
|Ziel| DBControl|
|Befehl| DELETE /path+|
|Beschreibung| für den Befehl database|

||filesystem|
| :----------- |:----- |
|Ziel| FSControl|
|Befehl| POST /path+|
|Beschreibung| für den Befehl filesystem|

||filesystem|
| :----------- |:----- |
|Ziel| FSControl|
|Befehl| GET /path+|
|Beschreibung| für den Befehl filesystem|

||filesystem|
| :----------- |:----- |
|Ziel| FSControl|
|Befehl| PUT /path+|
|Beschreibung| für den Befehl filesystem|

||filesystem|
| :----------- |:----- |
|Ziel| FSControl|
|Befehl| DELETE /path+|
|Beschreibung| für den Befehl filesystem|


## Anbindungen (Component.json => Connector) {#Anbindungen}
Eine Anbindung verlangt von einer anderen Komponente (`Ziel`) die Anbindung/Verbindung zu dieser Komponente.
Wenn eine Anbindung den aufzurufenden Befehl vorgibt, dann ist die Notation: METHODE URL (PRIORITÄT).

|Ausgang|request|
| :----------- |:----- |
|Ziel| CLocalObjectRequest|
|Beschreibung| damit LController als lokales Objekt aufgerufen werden kann|


Stand 30.06.2017
