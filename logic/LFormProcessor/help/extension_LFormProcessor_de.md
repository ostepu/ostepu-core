#### LFormProcessor
Dieses Modul nimmt die Bewertung der Nutzerantworten (bei der Verwendung von Formularen) vor. Dazu wird die Musterlösung mit der Eingabe des Nutzers verglichen und entsprechend Punkte vergeben.

Grundsätzlich wird jede Antwort, die des Nutzers und die Musterlösung, vorbehandelt. Dieser Vorgang ist nur für die Verwendung von Eingabezeilen interessant. Dabei wird die Zeichenkette in Kleinbuchstaben umgewandelt und beidseitig Zeichen, wie Leerzeichen und Tabulatorzeichen, entfernt.
Genauere Informationen finden Sie unter `http://php.net/manual/de/function.trim.php`.

##### normaler Vergleich
Wenn Sie diese Einstellung auswählen, gilt die Antwort des Nutzers als korrekt, sofern sie exakt mit der Musterlösung übereinstimmt. Sie müssen keine weiteren Werte in das Eingabefeld eintragen.

##### ähnliche Antworten

Um ähnliche Antworten als Lösung zuzulassen, können Sie diese Vergleichsart verwenden. Wählen Sie dazu den Grad der Übereinstimmung aus, indem Sie ihn in Prozent, in das nebenstehende Eingabefeld eintragen. 

Bsp.: 90

Für Informationen zum verwendeten Algorithmus, siehe `http://www.php.net/manual/de/function.similar-text.php`.

##### regulärer Ausdruck

Mit dieser Einstellung können Sie in das nebenstehende Eingabefeld reguläre Ausdrücke eintragen und zum Vergleich mit der Einsendung des Nutzers verwenden. Die Musterlösung des Formulars wird dabei im Falle einer Fehlantwort des Studenten verwendet, um diese in der korrigierten Einsendung als Musterlösung zu verwenden.

weitere Informationen zur Form finden Sie unter
`http://www.php.net/manual/de/regexp.introduction.php`.

Bsp.: %$textasciicircum[B|b]erlin$%

#### Voraussetzungen
Dieses Modul benötigt LForm