<!--
  - @file page_admin_createSheet_exercise_de.md
  -
  - @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
  -
  - @package OSTEPU (https://github.com/ostepu/system)
  - @since 0.4.0
  -
  - @author Till Uhlig <till.uhlig@student.uni-halle.de>
  - @date 2015
 -->

#### Aufgabennummer #####

![](createExerciseA.png)

Hier kann nichts eingestellt werden. Diese Aufgabennummer (bzw. Nummer der Teilaufgabe) wird dem Studenten so angezeigt.

---
#### Maximalpunktzahl festlegen #####

![](createExerciseB.png)

Wenn Sie dieses Feld leer lassen, wird die Maximalpunktzahl **0** betragen.
> Die Maximalpunktzahl muss zwischen 0 und 65535 liegen.

---
#### Punkteart festlegen #####

![](createExerciseC.png)

Die gewählte Punkteart wird für die Zulassungsbedingungen benötigt. Sie können nur eine Punkteart pro Aufgabe wählen.


---
#### Einsendungstyp festlegen

![](createExerciseD.png)

Sie können die Einsendungsdateien auf bestimmte Dateitypen (mime-Type) und Dateiendungen begrenzen.

Dabei können Sie mehrere Muster, durch `,` (Komma) getrennt, angeben.

Beispiel:

![](createExerciseG.png)

Erklärung: Es sind PDFs (mime-type: pdf, Dateiendung: pdf) oder zip-Archive (mime-Type: zip, Dateiendung: zip) erlaubt.

Aufbau:
**mimeVertreter.Endung**

> Es ist möglich den mimeVertreter einzeln anzugeben (ohne Dateiendung und ohne Punkt).
> <br/>Beispiel:
>
>![](createExerciseH.png)
>
>Erklärung: Es sind PDFs (mimeType: pdf, Dateiendung: beliebig) und zip-Archive (mime-Type: zip, Dateiendung: beliebig) erlaubt.

| mimeVertreter | mime-type |
| :--- | :----
| gz   | application/gzip
| xls  | application/msexcel
| ppt  | application/mspowerpoint
| doc  | application/msword
| pdf  | application/pdf
| ai *oder*<br/>eps *oder*<br/>ps | application/postscript
| htm *oder*<br/>html *oder*<br/>shtml *oder*<br/>xhtml   | text/html<br/>application/xhtml+xml
| xml  | application/xml<br/>text/xml<br/>text/xml-external-parsed-entity
| gtar | application/x-gtar
| php  | application/x-httpd-php
| tar  | application/x-tar
| zip  | application/zip
| jpg  | image/jpeg
| png  | image/png
| gif  | image/gif
| csv  | text/comma-separated-values
| css  | text/css
| js   | text/javascript<br/>application/x-javascript
| txt  | text/*
| img  | image/*

> Man kann auch nur eine Dateiendung angeben, indem man schreibt .Dateiendung
> <br/>Beispiel: .zip
>
>Erklärung: Es sind alle Dateien mit der Endung zip erlaubt

---
#### Anhang festlegen #####

![](createExerciseE.png)

Sie können hier eine einzelne Datei pro Aufgabe anhängen. Wenn Sie mehr als eine Datei benötigen, können Sie diese in einem zip-Archiv sammeln und anhängen.

---
#### ohne Einsendungsmöglichkeit #####

![](createExerciseF.png)

Sie können diese Option wählen, wenn Sie eine Aufgabe ohne Einsendungsmöglichkeit benötigen.

Beispiel: Präsenzübungen oder Ankündigungen für Aufgaben