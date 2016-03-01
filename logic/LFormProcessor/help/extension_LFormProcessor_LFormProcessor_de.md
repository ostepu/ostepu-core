<!--
  - @file extension_LFormProcessor_LFormProcessor_de.md
  -
  - @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
  -
  - @package OSTEPU (https://github.com/ostepu/system)
  - @since 0.3.6
  -
  - @author Till Uhlig <till.uhlig@student.uni-halle.de>
  - @date 2015
 -->

#### LFormProcessor
Dieses Modul nimmt die Bewertung der Nutzerantworten (bei der Verwendung von Formularen) vor. Dazu wird die Musterlösung mit der Eingabe des Nutzers verglichen und entsprechend Punkte vergeben.

Grundsätzlich wird jede Antwort, die des Nutzers und die Musterlösung, vorbehandelt. Dieser Vorgang ist nur für die Verwendung von Eingabezeilen interessant. Dabei wird die Zeichenkette in Kleinbuchstaben umgewandelt und beidseitig Zeichen, wie Leerzeichen und Tabulatorzeichen, entfernt.
Genauere Informationen finden Sie unter [www.php.net/manual/de/function.trim.php](http://php.net/manual/de/function.trim.php).

##### normaler Vergleich

![](LFormProcessorNormal.png)

Wenn Sie diese Einstellung auswählen, gilt die Antwort des Nutzers als korrekt, sofern sie exakt mit der Musterlösung übereinstimmt. Sie müssen keine weiteren Werte in das Eingabefeld eintragen.

##### ähnliche Antworten

![](LFormProcessorAhnlichkeit.png)

Um ähnliche Antworten als Lösung zuzulassen, können Sie diese Vergleichsart verwenden. Wählen Sie dazu den Grad der Übereinstimmung aus, indem Sie ihn in Prozent, in das nebenstehende Eingabefeld eintragen. 

Bsp.: 90

Für Informationen zum verwendeten Algorithmus, siehe [www.php.net/manual/de/function.similar-text.php](http://www.php.net/manual/de/function.similar-text.php).

##### regulärer Ausdruck

![](LFormProcessorRegular.png)

Mit dieser Einstellung können Sie in das nebenstehende Eingabefeld reguläre Ausdrücke eintragen und zum Vergleich mit der Einsendung des Nutzers verwenden. Die Musterlösung des Formulars wird dabei im Falle einer Fehlantwort des Studenten verwendet, um diese in der korrigierten Einsendung als Musterlösung zu verwenden.

weitere Informationen zur Form finden Sie unter
[www.php.net/manual/de/regexp.introduction.php](http://www.php.net/manual/de/regexp.introduction.php).

Bsp.: %^[B|b]erlin$%

#### Voraussetzungen
Dieses Modul benötigt LForm