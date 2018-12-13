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

Die CAbstract hat selber keine Funktionalität und ist überwiegend als Basis für Kopien gedacht. Dabei kann sie die Komponentenkonfiguration an einem beliebigen Ort speichern, indem in der Komponentendefinition beispielsweise `"option":"confPath=UI/include/Condition/uicondition_setcondition_cconfig.json"` angegeben wird (der Speicherort der Konfiguration).

| Themen |
| :- |
| [Anbindungen (Component.json => Connector)](#anbindungen) |

## <a name='anbindungen'></a>Anbindungen (Component.json => Connector)
Eine Anbindung verlangt von einer anderen Komponente (`Ziel`) die Anbindung/Verbindung zu dieser Komponente.
Wenn eine Anbindung den aufzurufenden Befehl vorgibt, dann ist die Notation: METHODE URL (PRIORITÄT).

|Ausgang|getDescFiles|
| :----------- |:----- |
|Ziel| TDocuView|
|Beschreibung| die Entwicklerdokumentation soll unsere Beschreibungsdatei nutzen|


Stand 25.07.2017
