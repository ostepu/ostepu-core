#### LFormPredecessor
Dieses Modul wandelt die Eingaben der Nutzer, bei der Verwendung von Formularen, in eine PDF um.

Bei der Verwendung von `Eingabzeilen`, kann die Antwortform für den Nutzer durch die Verwendung von Filtern begrenzt werden. Dieser Punkt ist ebenfalls für den Abgleich mit einer Musterlösung wichtig, da er die Antwortmöglichkeiten für den Nutzer einschränkt. Dabei wäre es auch denkbar, mehrere Filter zu kombinieren, diese werden dabei UND verknüpft angewendet.

##### druckbare Zeichen

Lässt nur druckbare Zeichen zu. Das stellt eine Kombination aus den Filtern `Buchstaben`, `Ziffern` und `[ !"'#&'()*+,-./:;$<=>?@[]\^_$\{|}~]` dar.
 
##### Buchstaben
Erlaubt nur Zeichenfolgen, mit Buchstaben (`[A-Z]` bzw. `[a-z]`).

##### Zahlen
Erlaubt Ziffern (`[0-9]`) und (`[-,]`).

##### Ziffern
Erlaubt Ziffern (`[0-9]`).

##### Buchstaben+Ziffern
Kombiniert die Filter `Ziffern` und `Buchstaben`.

##### Hexadezimalzahlen
Erlaubt Zeichenketten als Eingabe, welche Ziffern (`[0-9]`) oder Buchstaben (`[A-F]` bzw. `[a-f]`) enthalten.

##### regulärer Ausdruck
Sie können auch selbst reguläre Ausdrücke definieren, weitere Informationen zur Form finden Sie unter 
[http://www.php.net/manual/de/regexp.introduction.php](http://www.php.net/manual/de/regexp.introduction.php).

#### Voraussetzungen
Dieses Modul benötigt LForm