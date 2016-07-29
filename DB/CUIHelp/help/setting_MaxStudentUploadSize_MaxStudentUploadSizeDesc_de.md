<!--
  - @file setting_MaxStudentUploadSize_MaxStudentUploadSizeDesc_de.md
  -
  - @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
  -
  - @package OSTEPU (https://github.com/ostepu/system)
  - @since 0.4.0
  -
  - @author Till Uhlig <till.uhlig@student.uni-halle.de>
  - @date 2015
 -->

Über diese Einstellung kann die maximale Einsendungsgröße einer Datei, für Studenten, reguliert werden.

> Wurde kein Wert angegeben, wird die Größe über die Einstellungen des Systems geregelt. (php.ini)

> Die maximale Dateigröße wird in **Byte** angegeben.
> <br/> Bereich: 0 bis ...

Beispieleingaben:
512000 => **500KB**,
2097152 => **2MB**,
5242880 => **5MB**

Studentenanssicht beim Hochladen einer Einsendung:
![](maxStudentUploadSizeA.png)

Studentenansicht, wobei die Einsendung abgelehnt wurde:
![](maxStudentUploadSizeB.png)