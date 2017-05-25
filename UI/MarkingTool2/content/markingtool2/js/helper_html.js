var Helper = Helper || {};
//=== HTML Bibliothek um einen HTML Körper zu erstellen ===
Helper.HTML = new function(){
	var thisref = this;
	//Erstellt aus Rohdaten ein neues HTML-Element
	//data:        Objekt - Ein Objekt mit den Attributen, den Namen (element) und den Inhalt (content) des neuen Elements
	//-[content]:  String - Der HTML Inhalt des Elements
	//-[element]:  String - Der Name des neuen Elements
	//-[css]:      Array  - Eine Liste aus CSS Klassen, die diesem Element hinzugefügt werden sollen
	//-[children]: Array  - Eine Liste aus HTML Elementen, die diesem Element als Kinder hinzugefügt werden sollen.
	//-[...]:      Werte  - zusätzliche Attribute für das neue Element als Schlüssel-Werte-Paare
	//return:	   jQuery - Das neue erzeugte HTML Element
	this.CreateElementRaw = function(data) {
		var content = data.content || "";
		var element = data.element || "div";
		var css = data.css || [];
		var children = data.children || [];
		var text = data.text;
		data.content = undefined;
		data.element = undefined;
		data.css = undefined;
		data.children = undefined;
		data.text = undefined;
		var obj = $("<"+element+"/>");
		for (var key in data)
			if (data.hasOwnProperty(key)) {
				obj.attr(key, data[key]);
			}
		for (var i = 0; i<css.length; ++i)
			obj.addClass(css[i]);
		obj.html(content);
		if (text) obj.text(text);
		for (var i = 0; i<children.length; ++i)
			if (children[i] != null)
				obj.append(children[i]);
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
		return thisref.CreateElementRaw(data);
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
		var element = thisref.CreateElement(data.element || "div", content, data);
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
			css: ["ui-foldable-marker"],
			children: [
				thisref.CreateElementRaw({
					css: ["ui-foldable-box"],
					children: elements
				})
			]
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
	};
	//Erstellt ein Element, welches andere Elemente gruppiert und sie zusammenfalten (verstecken) kann.
	//title:      String        - Der Name der Gruppe
	//[elements]: Array<jQuery> - Die Elemente, die zu dieser Gruppe gehören
	//[data]:     Objekt        - Zusätzliche Daten für dieses Gruppenelement
	//return:     jQuery        - Das neu erzeugte Element
	this.CreateFoldingGroup = function(title, elements, data) {
		data = data || {};
		title = title || "";
		elements = elements || [];
		var button = thisref.CreateElementRaw({
			content: title,
			css: ["ui-foldable-group-header"]
		});
		button.click(function() {
			$(this).parent().toggleClass("ui-open");
		});
		data.css = data.css || [];
		data.css.push("ui-foldable-group");
		data.children = data.children || [];
		data.children.push(button);
		data.children.push(thisref.CreateElementRaw({
			css: ["ui-foldable-group-content"],
			children: elements
		}));
		return thisref.CreateElementRaw(data);
	};	
	//Erstellt ein neues HTML Select Element
	//[values]:  Objekt/Array - Ein Objekt oder eine Array mit den Werten für das neue Element.
	//                          Der values-Schlüssel wird als Optionswert und der values-Wert 
	//                          wird als Darstellungstext verwendet.
	//[current]: Wert         - Der aktuelle Wert (Schlüssel in values) der ausgewählt ist.
	//[method]:  Funktion     - Diese Methode wird aufgerufen, wenn sich der Wert ändert.
	//[data]:    Objekt       - Zusätzliche Daten für das neue Element
	//return:    jQuery       - Das neu erzeugte Element
	this.CreateSelect = function(values, current, method, data) {
		values = values || [];
		data = data || {};
		data.element = "select";
		data.children = data.children || [];
		for (var key in values)
			if (values.hasOwnProperty(key)) {
				var value = values[key];
				if (value.key != undefined && value.value != undefined) {
					key = value.key;
					value = value.value;
				}
				data.children.push(thisref.CreateElement("option", value, { value: key }));
			}
		var sel = thisref.CreateElementRaw(data);
		if (current != undefined) sel.val(current);
		if (method != undefined) sel.change(method);
		return sel;
	};
	//Erstellt ein neue Anzeige für ein Bild
	//src:    String - die relative URL zum Bild
	//[data]: Objekt - Zusätzliche Daten für das neue Element
	//return: jQuery - Das neu erzeugte Element
	this.CreateSimpleImage = function(src, data) {
		data = data || {};
		data.element = "img";
		data.src = src;
		return thisref.CreateElementRaw(data);
	};
	//Erstellt einen erweiterten Button, der unten rechts eine Info und oben links 
	//einen Modus anzeigen kann.
	//background: jQuery   - Das Objekt, welches den Hintergrund darstellt.
	//                       Gleichzeitig bestimmt es die Größe des Buttons
	//info:       jQuery   - Das Objekt, welches unten rechts angezeigt wird.
	//mode:       jQuery   - Das Objekt, welches oben links angezeigt wird.
	//[method]:   Funktion - Die Methode die aufgerufen wird, wenn auf diesem Button geklickt wird.
	//[data]:     Objekt   - Zusätzliche Daten für das neue Element
	//return:     jQuery   - Das neu erzeugte Element
	this.CreateComplexButton = function(background, info, mode, method, data) {
		data = data || {};
		data.children = data.children || [];
		data.css = data.css || [];
		data.css.push("ui-complex-button");
		background.addClass("ui-complex-button-background");
		info.addClass("ui-complex-button-info");
		mode.addClass("ui-complex-button-mode");
		data.children.push(background);
		data.children.push(info);
		data.children.push(mode);
		var button = thisref.CreateElementRaw(data);
		if (method != undefined) button.click(method);
		return button;
	};
	//Erstellt eine Leiste mit einem Balken wo man einen Wert einstellen kann. Er 
	//geht von 0 bis max mit einer Schritteweite von 0,1
	//value:    Zahl           - Der aktuelle Wert
	//max:      Zahl           - Der maximale Wert der Leiste (value darf größer sein)
	//[method]: Funktion(Zahl) - Die Methode die aufgerufen wird, wenn sich der Wert ändert.
	//                           Zusätzlich wird der aktuelle Wert übergeben.
	//[data]:   Objekt         - Zusätzliche Daten für das neue Element
	//return:   jQuery         - Das neu erzeugte Element
	this.CreateTrackBar = function(value, max, method, data) {
		data = data || {};
		data.css = data.css || [];
		data.css.push("ui-trackbar");
		var slider = thisref.CreateElementRaw(data);
		slider.slider({
			range: "min",
			value: value,
			min: 0,
			max: max,
			step: 0.1,
			slide: function(event, ui) {
				if (method != undefined) method(ui.value);
			},
			create: function(event, ui) {
				slider[0].initTest = 1;
				try { slider.slider("value", value); }
				catch (e) { console.log(e); }
			}
		});
		return slider;
	};
	//Erzeugt ein neues Fenster, welches sich dann über alles andere legen kann.
	//title:         String   - Der Titel dieses Fensters
	//[sizeClass]:   String   - "large" um ein großes Fenster zu erzeugen
	//                          "small" (default) um ein kleineres Dialogfenster zu erzeugen
	//[content]:     Array    - Die Liste an Elementen, die als Content hinzugefügt wird
	//[closeMethod]: Funktion - Die Methode, die aufgerufen wird, wenn dieses Fenster 
	//                          geschlossen wurde
	//[data]:        Objekt   - Zusätzliche Daten für den Fensterrahmen
	//return:        jQuery   - Das neu erzeugte Element
	this.CreateWindow = function(title, sizeClass, content, closeMethod, data) {
		var closeButton;
		data = data || {};
		data.css = data.css || [];
		data.css.push("ui-window-frame");
		data.css.push(sizeClass || "small");
		data.children = data.children || [];
		data.children.push(thisref.CreateElementRaw({
			css: ["ui-window-header"],
			children: [
				thisref.CreateElementRaw({
					css: ["ui-window-title"],
					text: title
				}),
				closeButton = thisref.CreateElementRaw({
					css: ["ui-window-close"],
					text: "x"
				})
			]
		}));
		data.children.push(thisref.CreateElementRaw({
			css: ["ui-window-content"],
			children: content
		}));
		var frame = thisref.CreateElementRaw(data);
		if (closeMethod != undefined) closeButton.click(closeMethod);
		return thisref.CreateElementRaw({
			css: ["ui-window-outer"],
			children: [ frame ]
		});
	}
	//Erzeugt einen Rahmen, in dem in den unteren Bereich Buttons nebeneinander angeordnet werden
	//können. Im großen Bereich erfolgt dann der Inhalt.
	//[buttons]: Array<jQuery> - Die Buttons für den unteren Bereich
	//[content]: Array<jQuery> - Der Inhalt für den oberen Bereich
	//[data]:    Object        - Zusätzliche Daten für den Rahmen
	//return:    jQuery        - Das neu erzeugte Element
	this.CreateButtonFrame = function(buttons, content, data) {
		data = data || [];
		data.css = data.css || [];
		data.css.push("ui-button-frame");
		data.children = data.children || [];
		data.children.push(thisref.CreateElementRaw({
			css: ["ui-button-frame-content"],
			children: content
		}));
		data.children.push(thisref.CreateElementRaw({
			css: ["ui-button-frame-buttons"],
			children: buttons
		}));
		return thisref.CreateElementRaw(data);
	};
};
