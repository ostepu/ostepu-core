

//Lege Klassenvariable an
var MarkingTool = MarkingTool || {};
MarkingTool.Editor = MarkingTool.Editor || {};

//=== Hilfsbibliotheken

//Ein Eventhandler, welcher ein Event verwalten kann.
MarkingTool.Event = function() {
	var list = [];
	//Fügt eine neue Eventmethode zu diesem Event hinzu.
	//method: function - die Methode, die beim Auslösen des Events aufgerufen wird.
	this.add = function(method) {
		for (var i = 0; i<list.length; ++i)
			if (list[i] == method) return;
		list.push(method);
	};
	//Entfernt eine Eventmethode wieder
	//method: function - die Methode, die nicht mehr aufgerufen werden soll.
	this.remove = function(method) {
		for (var i = 0; i<list.length; ++i)
			if (list[i] == method) {
				list.splice(i, 1);
				return;
			}
	};
	//Ruft alle Methoden mit aktuellen Kontext und verschiedenen Argumenten auf.
	//args: Werte... - Verschiedene Argumente
	this.invoke = function(args) {
		for (var i = 0; i<list.length; ++i)
			list[i].apply(this, arguments);
	};
	//Ruft alle Methoden in einem bestimmten Kontext und verschiedenen Argumenten auf.
	//thisArg: Wert     - der Zielkontext für die Methoden
	//args:    Werte... - Verschiedene Argumente
	this.call = function(thisArg, args) {
		arguments.splice(0, 1);
		for (var i = 0; i<list.length; ++i)
			list[i].apply(thisArg, arguments);
	};
};


//=== HTML Bibliothek um einen HTML Körper zu erstellen ===
MarkingTool.Editor.HTML = new function(){
	var thisref = this;
	//Erstellt aus Rohdaten ein neues HTML-Element
	//data:      Objekt - Ein Objekt mit den Attributen, den Namen (element) und den Inhalt (content) des neuen Elements
	//-content:  String - Der HTML Inhalt des Elements
	//-element:  String - Der Name des neuen Elements
	//-css:      Array  - Eine Liste aus CSS Klassen, die diesem Element hinzugefügt werden sollen
	//-children: Array  - Eine Liste aus HTML Elementen, die diesem Element als Kinder hinzugefügt werden sollen.
	//-...:		 Werte  - zusätzliche Attribute für das neue Element als Schlüssel-Werte-Paare
	//return:	 jQuery - Das neue erzeugte HTML Element
	this.CreateElementRaw = function(data) {
		var content = data.content || "";
		var element = data.element || "div";
		var css = data.css || [];
		var children = data.children || [];
		data.content = undefined;
		data.element = undefined;
		data.css = undefined;
		data.children = undefined;
		var obj = $("<"+element+"/>");
		for (var key in data)
			if (data.hasOwnProperty(key)) {
				obj.attr(key, data[key]);
			}
		for (var i = 0; i<css.length; ++i)
			obj.addClass(css[i]);
		for (var i = 0; i<children.length; ++i)
			obj.append(children[i]);
		obj.innerHTML(content);
		return obj;
	};
	//Erstellt ein neues HTML-Element
	//element: String - der Typ des Elements
	//content: String - der Inhalt des Elements
	//[data]:  Objekt - Zusätzliche Daten für CreateElementRaw
	//return:  jQuery - Das neue erzeugte HTML Element
	this.CreateElement = function(element, content, data) {
		data = data || {};
		data.element = element;
		data.content = content;
		return MarkingTool.Editor.HTML.CreateElementRaw(data);
	};
	//Erstellt einen neuen klickbaren Button
	//content:  String  - Der Text der angezeigt wird
	//[method]: Fuktion - Die Methode die beim Klicken ausgelöst wird.
	//[data]:   Objekt  - Zusätzliche Daten für CreateElementRaw
	//return:   jQuery  - Das neu erzeugte Element
	this.CreateButton = function(content, method, data) {
		data = data || {};
		data.css = data.css || [];
		data.css.push("ui-button");
		var element = thisref.CreateElement("div", content, data);
		if (method != undefined) element.click(method);
		return element;
	};
	//Erstellt ein Aufklappmenü
	//header:   jQuery        - Der Button der das Aufklappmenü aufklappt
	//elements: Array<jQuery> - Die Elemente die angezeigt werden sollen, wenn das Aufklappmenü offen ist.
	//[data]:   Objekt        - Zusätzliche Daten für das Objekt welches das Aufklappmenü beherbergt
	//return:   jQuery        - Das neu erzeugte Element
	this.CreateButtonMenu = function(header, elements, data) {
		data = data || {};
		data.css = data.css || [];
		data.css.push("ui-foldable");
		data.children = data.children || [];
		data.children.push(header);
		data.children.push(thisref.CreateElementRaw({
			css: ["ui-foldable-box"],
			children: elements
		}));
		header.click(function() {
			$(this).parent().toggleClass("ui-open");
		});
		return thisref.CreateElementRaw(data);
	};
	//Erstellt ein neues Eingabeelement
	//[type]:   String   - der Typ des Eingabeelements (Standart: "text")
	//[method]: Funktion - Die Methode die aufgerufen wird, wenn sich das Objekt ändert
	//[data]:   Objekt   - Zusätzliche Daten für das Eingabeelement
	//return:   jQuery   - Das neu erzeugte Element
	this.CreateInput = function(type, method, data) {
		type = type || "text";
		data = data || {};
		data.element = "input";
		data.type = type;
		data.css = data.css || [];
		data.css.push("ui-input");
		var element = thisref.CreateElementRaw(data);
		if (method != undefined) element.change(method);
		return element;
	}
	
	//TODO: Implementierung des Visuellen Inhalts
	
};

//=== Bibliothek um die Updates nachzuvollziehen
MarkingTool.Editor.UpdateIndicator = new function() {
	var thisref = this;
	var display = undefined;
	$(function(){ display = $(".loading-box"); });
	//Zeigt die Ladeanzeige an
	this.ShowBox = function() {
		if (display != undefined) display.show();
	};
	//Versteckt die Ladeanzeige
	this.HideBox = function() {
		if (display != undefined) display.hide();
	};
	//Setzt die Beschreibung der Ladeanzeige
	//text: String - Der Text der gesetzt werden soll.
	this.SetText = function(text) {
		if (display != undefined) display.find(".loading-description").html(text);
	};
};

//=== Update Bibliothek um immer den aktuellen Zustand von Objekten verfolgen zu können. ===

//Verfolgt den Zustand einer Property eines Objektes
//owner: Objekt - Das Objekt das eine Property enthält, die Überwacht werden soll.
//key:   String - Der Name der Property
//path:  Objekt - Eindeutige Informationen, die das Objekt identifizieren
MarkingTool.Editor.UpdateProperty = function(owner, key, path) {
	var defaultValue = undefined;
	var updadedEvent = new MarkingTool.Event();
	var thisref = this; //Der Verweis auf dieses Objekt für spätere Eventaufrufe
	//String - Der Name der Property
	this.key = key;
	//Objekt - Der Pfad, der das Besitzerobjekt identifiziert.
	this.path = path;
	//[EVENT] Dieses Event wird ausgelöst, wenn sich der Wert dieser Variable ändert.
	//[function(sender)]
	//    sender: Objekt - dieses Objekt
	Object.defineProperty(this, "UpdatedEvent", {get: function(){ return updadedEvent; } });
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
		updatedEvent.invoke(thisref);
	};
	//Setzt den Wert auf den Standart zurück
	this.resetValue = function() {
		owner[key] = defaultValue;
		updatedEvent.invoke(thisref);
	};
	
};
//Ein Objekt welches selbst verfolgen kann, ob sich sein Zustand ändert.
//data: Objekt - Ein Objekt welches durch Schlüssel- und Wertepaare alle Daten enthält. Alle Daten aus 
//               diesem Objekt werden auch hier wieder verfügbar gemacht. Obwohl dieses Objekt als Daten-
//               speicher dient, sollten alle Zugriffe nur über das UpdateObject erfolgen
//path: Objekt - Eindeutige Informationen, die das Objekt identifizieren
MarkingTool.Editor.UpdateObject = function (data, path) {
	var propertys = {};
	var thisref = this;
	var updadedEvent = new MarkingTool.Event();
	var raiseHandler = function(sender) {
		updatedEvent.invoke(thisref);
	};
	for (var key in data) {
		if (data.hasOwnProperty(key)) {
			propertys[key] = new MarkingTool.Editor.UpdateProperty(data, key, path);
			Object.defineProperty(this, key, {
				get: function() { return data[key]; },
				set: function(value) { thisref.propertys[key].changeValue(value); }
			});
			propertys[key].UpdatedEvent.add(raiseHandler);
		}
		else this[key] = data[key];
	}
	//Objekt - Die eindeitige Information, die dieses Objekt identifiziert.
	this.path = path;
	//[EVENT] Dieses Event wird ausgelöst, wenn sich der Wert einer Variable ändert.
	//[function(sender)]
	//    sender: Objekt - dieses Objekt
	Object.defineProperty(this, "UpdatedEvent", {get: function(){ return updadedEvent; } });
	//Setzt alle Propertys auf ihren Standartwert
	this.resetValues = function() {
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
		updatedEvent.invoke(thisref);
	};
	//Ruft die Liste alle Propertys zu diesen Objekt ab
	//return: Objekt - Eine Liste aus Schlüssel-Werte-Paaren mit UpdateProperty als Werten.
	this.getPropertys = function() {
		return propertys;
	};
};
//Eine Bibliothek die mehrere Objekte auf Änderungen überwachen kann.
MarkingTool.Editor.UpdateFactory = new function() {
	var thisref = this;
	var changedHandler = function(sender) {
		if (sender.isValueChanged()) {
			for (var i = 0; i<thisref.UpdateList; ++i)
				if (thisref.UpdateList[i] == sender)
					return;
			thisref.UpdateList.push(sender);
			addedEvent.invoke(sender);
		}
		else {
			for (var i = 0; i<thisref.UpdateList; ++i)
				if (thisref.UpdateList[i] == sender) {
					thisref.UpdateList.splice(i, 1);
					removedEvent.invoke(sender);
					return;
				}
		}
	};
	var addedEvent = new MarkingTool.Event();
	var removedEvent = new MarkingTool.Event();
	
	//[EVENT] Dieses Event wird ausgelöst, falls eines Objekt sich geändert hat und nun in UpdateList steht.
	//[function(sender)]
	//    sender: Objekt - das veränderte Objekt
	Object.defineProperty(this, "AddedEvent", {get: function() { return addedEvent; } });
	//[EVENT] Dieses Event wird ausgelöst, falls eines Objekt nun nicht mehr als geändert betrachtet wird und 
	//        deshalb nicht mehr in UpdateList steht.
	//[function(sender)]
	//    sender: Objekt - das nun unveränderte Objekt
	Object.defineProperty(this, "RemovedEvent", {get: function() { return addedEvent; } });
	//[UpdateObject] - Eine Liste aller aktuell geänderter Werte
	this.UpdateList = [];
	//[UpdateObject] - Eine Liste aller Objekte die von dieser Facoty überwacht werden.
	this.WatchList = [];
	//Fügt ein neues Objekt der Überwachung hinzu.
	//data:   Objekt       - Die Daten mit den das UpdateObject erstellt wird.
	//path:   Objekt       - Die Daten mit den das Objekt identifiziert wird.
	//return: UpdateObject - Das zur Überwachung hinzugefügte UpdateObject
	this.AddObject = function(data, path) {
		var obj = new MarkingTool.Editor.UpdateObject(data, path);
		obj.addUpdateHandler(changedHandler);
		WatchList.push(obj);
		return obj;
	};
	//Entfernt ein UpdateObject wieder aus der Überwachung.
	//path: Objekt - der Identifizierer für das UpdateObject.
	this.RemoveObject = function(path) {
		for (var i = 0; i<this.WatchList.length; ++i)
			if (this.WatchList[i].path == path) {
				this.WatchList.splice(i,1)[0].removeUpdateHandler(changedHandler);
				return;
			}
	};
	//Setzt ein UpdateObject wieder auf den Standartwert zurück.
	//path: Objekt - der Identifizierer unter der das UpdateObject zu finden ist.
	this.Reset = function(path) {
		for (var i = 0; i<this.UpdateList.length; ++i)
			if (this.UpdateList[i].path == path) {
				this.UpdateList.splice(i, 1)[0].resetValues();
				return;
			}
	};
	
};


