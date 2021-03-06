<!--
  - @file page_admin_markingTool_createArchive_de.md
  -
  - @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
  -
  - @package OSTEPU (https://github.com/ostepu/system)
  - @since 0.4.0
  -
  - @author Till Uhlig <till.uhlig@student.uni-halle.de>
  - @date 2015-2016
 -->

#### Korrekturaufträge herunterladen ####

> ##### Alle Korrekturaufträge #####

![](pathA.png)

Sie können alle Ihnen zugewiesenen Einsendungen herunterladen

> ##### Korrekturaufträge nach Status #####

![](pathB.png)

Es ist möglich, direkt einen bestimmten Korrekturstatus auszuwählen, um das Korrekturarchiv anschließend im Korrekturassistenten herunterzuladen.

![](pathC.png)


> ##### gefilterte Korrekturaufträge #####

![](pathD.png)

Sie können eine zusätzliche Auswahl im Korrekturassistenten treffen.

![](pathE.png)


#### Korrigieren #### {#korrigieren}

**Jedes Korrekturarchiv ist ab dessen Erstellung 30 Tage gültig, danach kann es nicht mehr hochgeladen werden.**

> ##### 1. Entpacken #####

Entpacken Sie das heruntergeladene zip-Archiv.
Sie finden dort die Liste.csv (hier müssen die Bewertungen eingetragen werden) und einige Unterordner (nach den Aufgaben der Übungsserie bezeichnet. Dazu enthalten diese Aufgabenordner weitere Unterordner (mit den internen Korrekturnummern bezeichnet), welche die jeweils zur Korrektur vorgesehene Datei enthalten.

Eventuell enthält der jeweilige Ordner mit der Einsendung noch eine .pdf Datei, welche generiert wurde, weil die Einsendung als Text erkannt wurde. Diese .pdf kann direkt als Korrekturhilfe genutzt werden.

Beispiel: Wenn der Student eine Hallo.java einsendet, wird zudem eine Hallo.pdf erzeugt.

Wenn der Name der Einsendung (des Studenten) und der Korekturdatei (vom Kontrolleur erstellt) übereinstimmen, wird der Korrekturdatei ein K_ vorangestellt. Dieser Fall tritt ebenfalls auf, wenn dem Studenten seine eigene Einsendung als Korrekturdatei (Namensgleich) überlassen wird.

![](sampleB.png)

> ##### 2. Bewerten #####

Sie können die **Bewertung**, die **Korrekturdatei**, den **Korrekturstatus** und einen **Kommentar** in die Liste.csv eintragen. Dazu müssen Sie diese mit einem passenden Editor öffnen.

![](libreA.png "LibreOffice")

![](libreB.png)

1. Korrekturnummer (der Ordnername im zip-Archiv)
2. die erhaltenen Punkte (**dürfen MAXPOINTS überschreiten**)
3. die maximale Punktzahl
4. die Einsendung war "besonders gut" (0=nein, 1=ja)(wird nicht verwendet)
5. der Status der Korrektur (0=nicht eingesendet, 1=unkorrigiert, 2=vorläufig, 3=korrigiert)(wird dem Studenten so angezeigt)
6. ein Kommentar zur Einsendung (wird dem Studenten und im Korrekturassistenten angezeigt)(kein HTML verwenden, **maximal 255 Zeichen**)
7. der Kommentar des Studenten zu seiner Einsendung (Sie können diesen Kommentar nicht verändern)
8. diese Datei (Pfad bezieht sich auf das zip-Archiv) wird als Korrektur dem Studenten angezeigt (Sie können das Feld auch leer lassen, dann wird ihm keine Datei angezeigt)
9. die Nummer des Korrekturarchivs, diese darf nicht entfernt werden und muss in der ersten Zeile stehen

> ##### Anmerkungen #####

Sie können Kommentarzeilen in die csv-Datei eintragen, indem Sie diese mit **--** beginnen. Zudem sind leere Zeilen erlaubt.

Wenn Sie eine Einsendung nicht bewerten möchten bzw. nur eine bestimmte Menge der Einsendungen bearbeiten wollen, können Sie die übrigen Zeilen aus der Liste.csv entfernen und nur die notwendigen Dateien und Zeilen hochladen.

#### Einsendungen bewerten
*Dieses Video wird von einem externen Anbieter bereitgestellt, sodass wir keinen Einfluss auf zusätzliche Inhalte (u.a. Werbung) haben und dieser eventuell Daten, im Rahmen der Nutzung, erheben könnte.*
<iframe width="640" height="360" src="https://www.youtube-nocookie.com/embed/rt3k1m5etZM?list=PLfnTtQX6vUn2lHxmo2WqLsPaEZihOEczh&amp;showinfo=0&amp;modestbranding=1&amp;loop=1&amp;listType=playlist" frameborder="0" allowfullscreen></iframe>
*Quelle: https://www.youtube-nocookie.com/embed/rt3k1m5etZM*