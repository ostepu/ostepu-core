var Helper = Helper || {};
//Implementiert eine einfache Queue
Helper.Queue = function() {
	var first = null, last = null, count = 0;
	//Legt ein Element an das Ende der Queue
	//element: Wert - das neue Element
	this.push = function(element) {
		var entry = {
			element: element,
			prev: last
		};
		if (last == null) first = last = entry;
		else {
			last.next = entry;
			last = entry;
		}
		count++;
	};
	//Ruft das erste Element von der Queue ab und entfernt dieses
	//return: Wert - der erste Eintrag
	this.pop = function() {
		if (first == null) return null;
		var element = first.element;
		if (first.next) first = first.next;
		else first = last = null;
		count--;
		return element;
	};
	//Fragt ab, ob diese Queue leer ist.
	//return: Bool - true wenn Queue leer
	this.isEmpty = function() {
		return first == null;
	};
	//Ruft das erste Element von der Queue ab ohne es zu entfernen
	//return: Wert - der erste Eintrag
	this.pick = function() {
		if (first == null) return null;
		else return first.element;
	};
	//Löscht alle Elemente in der Queue
	this.clear = function() {
		first = last = null;
		count = 0;
	};
	//Ermittelt die Anzahl in der Queue
	this.size = function() {
		return count;
	};
};
