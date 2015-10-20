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


#### Korrigieren ####

> Jedes Korrekturarchiv ist ab dessen Erstellung 30 Tage gültig, danach kann es nicht mehr hochgeladen werden.

> ##### 1. Entpacken #####

Entpacken Sie das heruntergeladene zip-Archiv.
Sie finden dort die Liste.csv (hier müssen die Bewertungen eingetragen werden) und einige Unterordner (nach den Aufgaben der Übungsserie bezeichnet. Dazu enthalten diese Aufgabenordner weitere Unterordner (mit den internen Korrekturnummern bezeichnet), welche die jeweils zur Korrektur vorgesehene Datei enthalten.

Eventuell enthält der jeweilige Ordner mit der Einsendung noch eine .pdf Datei, welche generiert wurde, weil die Einsendung als Text erkannt wurde. Diese .pdf kann direkt als Korrekturhilfe genutzt werden.

Beispiel: Wenn der Student eine Hallo.java einsendet, wird zudem eine Hallo.pdf erzeugt. 

![](sampleB.png)

> ##### 2. Bewerten #####

Sie können die **Bewertung**, die **Korrekturdatei**, den **Korrekturstatus** und einen **Kommentar** in die Liste.csv eintragen. Dazu müssen Sie diese mit einem passenden Editor öffnen. 

![](libreA.png "LibreOffice")

![](libreB.png)

1. Korrekturnummer (der Ordnername im zip-Archiv)
2. die erhaltenen Punkte (dürfen **MAXPOINTS** nicht überschreiten)
3. die maximale Punktzahl
4. die Einsendung war "besonders gut" (0=nein, 1=ja)(wird nicht verwendet)
5. der Status der Korrektur (0=nicht eingesendet, 1=unkorrigiert, 2=vorläufig, 3=korrigiert)(wird dem Studenten so angezeigt)
6. ein Kommentar zur Einsendung (wird dem Studenten und im Korrekturassistenten angezeigt)(kein HTML verwenden)
7. der Kommentar des Studenten zu seiner Einsendung (Sie können diesen Kommentar nicht verändern)
8. diese Datei (Pfad bezieht sich auf das zip-Archiv) wird als Korrektur dem Studenten angezeigt (Sie können das Feld auch leer lassen, dann wird ihm keine Datei angezeigt)
9. die Nummer des Korrekturarchivs, diese darf nicht entfernt werden und muss in der ersten Zeile stehen

> ##### Anmerkungen #####

Sie können Kommentarzeilen in die csv-Datei eintragen, indem Sie diese mit **--** beginnen. Zudem sind leere Zeilen erlaubt.

Wenn Sie eine Einsendung nicht bewerten möchten bzw. nur eine bestimmte Menge der Einsendungen bearbeiten wollen, können Sie die übrigen Zeilen aus der Liste.csv entfernen und nur die notwendigen Dateien und Zeilen hochladen.