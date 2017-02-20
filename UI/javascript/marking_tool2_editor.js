

//Lege Klassenvariable an
var MarkingTool = MarkingTool || {};
MarkingTool.Editor = MarkingTool.Editor || {};

//=== HTML Bibliothek um einen HTML Körper zu erstellen ===
MarkingTool.Editor.HTML = (function(){
	//private static
	
	//public static
	return {
		//Erstellt aus Rohdaten ein neues HTML-Element
		//data: Objekt - Ein Objekt mit den Attributen, den Namen (element) und den Inhalt (content) des neuen Elements
		CreateElementRaw : function(data) {
			var content = data.content || "";
			var element = data.element || "div";
			data.content = undefined;
			data.element = undefined;
			var obj = $("<"+element+"/>");
			foreach (var key in data)
				if (data.hasOwnProperty(key)) {
					obj.attr(key, data[key]);
				}
			obj.innerHTML(content);
			return obj;
		},
		//Erstellt ein neues HTML-Element
		//element: String - der Typ des Elements
		//content: String - der Inhalt des Elements
		//data:    Objekt - Schlüssel-Werte-Paare mit den Attributwerten
		CreateElement : function(element, content, data) {
			data.element = element;
			data.content = content;
			return MarkingTool.Editor.HTML.CreateElementRaw(data);
		}
		
		//TODO: Implementierung des Visuellen Inhalts
		
	};
})();

//=== Update Bibliothek um immer den aktuellen Zustand von Objekten verfolgen zu können. ===
//Verfolgt den Zustand einer Property eines Objektes
//owner: Objekt - Das Objekt das eine Property enthält, die Überwacht werden soll.
//key:   String - Der Name der Property
//path:  Objekt - Eindeutige Informationen, die das Objekt identifizieren
Marking.Editor.UpdateProperty = function(owner, key, path) {
	var defaultValue = undefined;
	var raiseList = [];
	var thisref = this; //Der Verweis auf dieses Objekt für spätere Eventaufrufe
	//String - Der Name der Property
	this.key = key;
	//Objekt - Der Pfad, der das Besitzerobjekt identifiziert.
	this.path = path;
	//Fügt eine neue Methode hinzu die aufgerufen wird, sobald der Wert der Variable sich ändert.
	//method: function(sender) - Die Methode
	//        sender: Objekt   - Das Objekt welches diesen Event ausgelöst hat (meist dieses)
	this.addUpdateHandler = function(method) {
		for (var i = 0; i<raiseList.length; ++i)
			if (raiseList[i] == method) return;
		raiseList.push(method);
	};
	//Enfernt eine Methode , die durch addUpdateHandler hinzugefügt wurde.
	//method: function - Die ehemals hinzugefügt Methode
	this.removeUpdateHandler = function(method) {
		for (var i = 0; i<raiseList.length; ++i)
			if (raiseList[i] == method) {
				raiseList.splice(i, 1);
				return;
			}
	};
	//Setzt den Standartwert für die Property
	//value: Wert - Der Standartwert
	this.setDefaultValue = function(value) {
		defaultValue = value;
	};
	//Überprüft ob der aktuelle Wert der Property mit dem Standartwert übereinstimmt. Es erfolgt keine
	//Typprüfung.
	//return: Boolean - Ergebnis der Überprüfung
	this.isValueChanged = function() {
		var value = owner[key];
		return value == defaultValue;
	};
	//Verändert den Wert der Property
	//newValue: Wert - Der neue Wert der Property
	this.changeValue = function(newValue) {
		owner[key] = newValue;
		for (var i = 0; i<raiseList.length; ++i)
			raiseList[i](thisref);
	};
	//Setzt den Wert auf den Standart zurück
	this.resetValue = function() {
		owner[key] = defaultValue;
		for (var i = 0; i<raiseList.length; ++i)
			raiseList[i](thisref);
	};
	
};
//Ein Objekt welches selbst verfolgen kann, ob sich sein Zustand ändert.
//data: Objekt - Ein Objekt welches durch Schlüssel- und Wertepaare alle Daten enthält. Alle Daten aus 
//               diesem Objekt werden auch hier wieder verfügbar gemacht. Obwohl dieses Objekt als Daten-
//               speicher dient, sollten alle Zugriffe nur über das UpdateObject erfolgen
//path: Objekt - Eindeutige Informationen, die das Objekt identifizieren
Marking.Editor.UpdateObject = function (data, path) {
	var propertys = {};
	var raiseList = [];
	var thisref = this;
	//Objekt - Die eindeitige Information, die dieses Objekt identifiziert.
	this.path = path;
	var raiseHandler = function(sender) {
		for (var i = 0; i<raiseList.length; ++i)
			raiseList[i](thisref);
	};
	for (var key in data) {
		if (data.hasOwnProperty(key)) {
			propertys[key] = new Marking.Editor.UpdateProperty(data, key, path);
			Object.defineProperty(this, key, {
				get: function() { return data[key]; },
				set: function(value) { thisref.propertys[key].changeValue(value); }
			});
			propertys[key].addUpdateHandler(raiseHandler);
		}
		else this[key] = data[key];
	}
	//Fügt eine neue Methode hinzu die aufgerufen wird, sobald der Wert einer Variable sich ändert.
	//method: function(sender) - Die Methode
	//        sender: Objekt   - Das Objekt welches diesen Event ausgelöst hat (meist dieses)
	this.addUpdateHandler = function(method) {
		for (var i = 0; i<raiseList.length; ++i)
			if (raiseList[i] == method) return;
		raiseList.push(method);
	};
	//Enfernt eine Methode , die durch addUpdateHandler hinzugefügt wurde.
	//method: function - Die ehemals hinzugefügt Methode
	this.removeUpdateHandler = function(method) {
		for (var i = 0; i<raiseList.length; ++i)
			if (raiseList[i] == method) {
				raiseList.splice(i, 1);
				return;
			}
	};
	//Setzt alle Propertys auf ihren Standartwert
	this.resetValue = function() {
		for (var key in propertys)
			if (propertys.hasOwnProperty(key))
				propertys[key].resetValue();
	};
	//Überprüft ob die aktuellen Werte aller Propertys mit dem Standartwerten übereinstimmen. Es erfolgt keine
	//Typprüfung.
	//return: Boolean - Ergebnis der Überprüfung
	this.isValueChanged = function() {
		for (var key in propertys)
			if (propertys.hasOwnProperty(key))
				if (propertys[key].IsValueChanged())
					return true;
		return false;
	};
	//Setzt den Standartwert für eine Property
	//key:   String - Der Name der Property
	//value: Wert   - Der neue Standartwert der Property
	this.setDefaultValue = function(key, value) {
		propertys[key].setDefaultValue(value);
	};
	//Setzt die aktuellen Werte aller Propertys als Standartwerte
	this.setAllValuesAsDefault = function() {
		for (var key in propertys)
			if (propertys.hasOwnProperty(key))
				propertys[key].setDefaultValue(data[key]);
	};
};




