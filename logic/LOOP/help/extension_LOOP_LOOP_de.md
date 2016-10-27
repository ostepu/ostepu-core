<!--
  - @file extension_LOOP_de.md
  -
  - @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
  -
  - @package OSTEPU (https://github.com/ostepu/system)
  - @since 0.3.6
  -
  - @author Ralf Busch <ralfbusch92@gmail.com>
  - @date 2016
  - @author Till Uhlig <till.uhlig@student.uni-halle.de>
  - @date 2015
 -->

## Die LOOP Verarbeitung ##

Diese Verarbeitung dient der automatischen Kontrolle von Programmieraufgaben. Es folgt eine Erklärung, wie die Teilbereiche des Moduls anzuwenden sind.



---
#### Die Anwendung/Programmiersprache #####

![](LOOPA.png)

Hier wählen Sie die Art der Anwendung aus. Es werden C0-C4, Java, sowie benutzerdefinierte Programmiersprachen unterstützt.
> Die benutzerdefinierte Anwendung erfordert Erfahrung in Bash Scripting. Mehr dazu lesen Sie weiter unten.

---
#### Rückmeldungen #####

![](LOOPF.png)

Wenn diese Option ausgewählt wurde, werden den Studenten die Rückmeldung des Kompiliervorganges direkt angezeigt.

---
#### fehlerhafte Einsendungen #####

![](LOOPF.png)

Wenn diese Option ausgewählt wurde, wird eine Einsendung, welche nicht durch den Kompilierprozess läuft abgelehnt, ansonsten wird
die eine Einsendung immer akzeptiert (aber die Meldungen angezeigt).

---
#### Die Kompilierungsparameter #####

![](LOOPB.png)

Intern wird der String, der als Kompilierungsparameter angegeben ist, an den Compileraufruf wie z.B. javac angehängt.

**$file** : wird durch den Pfad zur Einsendungsdatei des Studenten ersetzt.

**Beispiel**
> die Parameter "$file -abc" wird somit zu "javac '/pfad/zur/Einsendungdatei' -abc", beim Beispiel Java

---
#### Die Parameteranzahl und die Testdurchgänge #####

![](LOOPC.png)

Mit der Parameteranzahl können sie festlegen, wie viele Eingabeparameter für die Ausführung eines Testdurchgangs übergeben werden sollen.
Die Anzahl der einzelnen Testdurchgänge, kann ebenfalls bestimmt werden.

---
#### Die Testfälle #####

![](LOOPD.png)

Die Testfälle werden in Tabellenform dargestellt und bearbeitet. Jede Zeile entspricht einem Testfall. Jede Spalte entspricht einem Eingabeparameter, wobei die letzte Spalte die gewünschte Ausgabe definiert.

**Beschriftung**

1 - Zeilen und Spalten können durch die Mülleimer Schaltfläche gelöscht werden

2 - Hier wird der Datentyp für eine Spalte definiert

3 - eine Zeile enstpricht einem Testfall

---
#### Die Datentypen #####

**Text** - Kombination aus Text und Zahlen (Bsp.: Test123)

**Datei** - hochgeladene Dateien werden als Dateipfad dem Programm übergeben
> Hochgeladen Dateien werden für alle unterliegenden Zeilen einer Spalte übernommen, um bestimmte Zellen der Testfalltabelle mit der Datei zu beschreiben, müssen die gewünschten Zellen mit der Checkbox markiert werden.

**regulärer Ausdruck** - regulärer Ausdruck nach PCRE regex syntax
> siehe [diese Seite](http://www.php-einfach.de/php-tutorial/regulaere-ausdruecke/) um reguläre Ausdrücke zu verstehen

---
### Benutzerdefinierte Programmiersprache ###

Wählt man die benutzerdefinierte Anwendung aus, so erscheinen folgende Eingabefelder:

![](LOOPE.png)

Beide Eingabefelder definieren den Compiler- bzw. den Testausruf. Man kann hierfür folgende Textbausteine verwenden:

**$script** - ist das Compiler- bzw. Ausführskript, das mit dem jeweiligen Dateifeld hochgeladen werden kann

**$file** - Dateipfad zur Einsendung des Studenten

**$parameters** - Alle Eingabeparameter einer Zeile der Testfalltabelle mit Leerzeichen getrennt


**Beispiel:**
> "$script $file" wird zu "./javacompiler '/pfad/zur/Einsendung.java'"

Sie entsprechen somit dem Aufruf von Befehlen, wie sie in einem Terminal bzw. einer Konsole geschrieben werden würden.

---
#### Die Bash Skripte #####

Bei dem Kompilierskript muss bei Erfolg der Status 0, bei Misserfolg der Status 1 ausgegeben werden. Fehler sollen grundsätzlich auf stderr ausgegeben werden.

Bei den Ausfürungsskripten sind lediglich die Ausgaben mit denen in der Testfalltabelle definierten Ausgaben in Übereinstimmung zu bringen. Ansonsten kommt es logischerweise zu keinem Treffer während eines Testdurchgangs.