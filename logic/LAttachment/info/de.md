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

## Eingänge
---------------

|||
| :----------- |:-----: |
|Befehl| POST<br>/attachment|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Befehl| GET<br>'/attachment/attachment/:attachmentid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Befehl| DELETE<br>/attachment/attachment/:attachmentid|
|Eingabetyp| -|
|Ausgabetyp| -|

|||
| :----------- |:-----: |
|Befehl| PUT<br>/attachment/attachment/:attachmentid|
|Eingabetyp| -|
|Ausgabetyp| -|


## Ausgänge
---------------

||controller|
| :----------- |:-----: |
|Ziel| LController|
|Befehl| POST<br>/DB/attachment|
|Beschreibung| für den Befehl controller|

||controller|
| :----------- |:-----: |
|Ziel| LController|
|Befehl| GET<br>/DB/attachment/attachment/:attachmentid|
|Beschreibung| für den Befehl controller|

||controller|
| :----------- |:-----: |
|Ziel| LController|
|Befehl| DELETE<br>/DB/attachment/:attachmentid|
|Beschreibung| für den Befehl controller|

||controller|
| :----------- |:-----: |
|Ziel| LController|
|Befehl| PUT<br>/DB/attachment/:attachmentid|
|Beschreibung| für den Befehl controller|

||postFile|
| :----------- |:-----: |
|Ziel| LFile|
|Befehl| POST<br>/file|
|Beschreibung| für den Befehl postFile|

||postAttachment|
| :----------- |:-----: |
|Ziel| DBAttachment|
|Befehl| POST<br>/attachment|
|Beschreibung| für den Befehl postAttachment|


Stand 30.06.2017
