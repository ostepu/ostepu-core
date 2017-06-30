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

Die CUIHelp enthält einige Hilfedatei der Benutzerschicht (`UI`)

## Anbindungen
---------------

|Ausgang|request|
| :----------- |:-----: |
|Ziel| CLocalObjectRequest|
|Beschreibung| damit CUIHelp als lokales Objekt aufgerufen werden kann|

|Ausgang|request|
| :----------- |:-----: |
|Ziel| CHelp|
|Beschreibung| hier werden Hilfedateien beim zentralen Hilfesystem angemeldet, sodass sie über ihre globale Adresse abgerufen werden können|


Stand 30.06.2017
