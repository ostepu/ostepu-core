

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
		if (method == undefined) throw new Error("no method given");
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
		obj.html(content);
		for (var i = 0; i<children.length; ++i)
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
	}
};

//Stellt die Oberfläche und ihre Funktionen bereit.
MarkingTool.Editor.View = new function() {
	var thisref = this;
	var createWrapper = function(element) {
		return MarkingTool.Editor.HTML.CreateElementRaw({children: [element]});
	};
	var createCommandBar = function() {
		var hc = MarkingTool.Editor.HTML;
		var optionsBar = hc.CreateElementRaw({
			css: ["ui-commandbar"],
			children: [
				hc.CreateButton("Optionen"),
				hc.CreateElementRaw({
					css: ["ui-commandbar-container"],
					children: [
						hc.CreateButtonMenu(hc.CreateButton("Ansicht"), [
							hc.CreateButton("Filter", function() {
								$(this).toggleClass("active");
								if ($(this).hasClass("active")) $(".ui-layout-left").addClass("ui-open");
								else $(".ui-layout-left").removeClass("ui-open");
								$(".ui-ref-view-button").removeClass("ui-open");
							}, {css:["active"]} ),
							hc.CreateButton("Änderungen", function() {
								$(this).toggleClass("active");
								if ($(this).hasClass("active")) $(".ui-layout-right").addClass("ui-open");
								else $(".ui-layout-right").removeClass("ui-open");
								$(".ui-ref-view-button").removeClass("ui-open");
							}, {css:["active"]} )
						], { css: ["ui-ref-view-button"] }),
						hc.CreateButton("Aktualisieren", function() {
							MarkingTool.Editor.UpdateIndicator.ShowBox();
							document.location.reload();
						}),
						hc.CreateButton("Speichern")
					]
				})
			]
		});
		createWrapper(optionsBar).appendTo($(".content-box"));
	};
	var createFilterBox = function() {
		var hc = MarkingTool.Editor.HTML;
		return [
			hc.CreateFoldingGroup("Filter", [
				hc.CreateElement("div", "Serie:", { css: ["ui-filter-title"] }),
				hc.CreateSelect(MarkingTool.Editor.View.SheetCodes, undefined, undefined, { css: ["ui-select"] }),
				hc.CreateElement("div", "Kontrolleur:", { css: ["ui-filter-title"] }),
				hc.CreateSelect(MarkingTool.Editor.View.TutorCodes, undefined, undefined, { css: ["ui-select"] }),
				hc.CreateElement("div", "Status:", { css: ["ui-filter-title"] }),
				hc.CreateSelect(MarkingTool.Editor.View.StateCodes, undefined, undefined, { css: ["ui-select"] })
			], { css: ["ui-open"] }),
			hc.CreateFoldingGroup("Sortierung", [
				hc.CreateElement("div", "Sortiere nach:", { css: ["ui-filter-title"] }),
				hc.CreateSelect({
					"name": "Nach Namen",
					"task": "Nach Aufgaben"
				}, "name", undefined, { css: ["ui-select"] })
			], { css: ["ui-open"] }),
			hc.CreateFoldingGroup("Suche", [
				hc.CreateInput(),
				hc.CreateInput("button", undefined, { value: "Suche" })
			], { css: ["ui-open"] })
		];
	};
	var createLayoutWindow = function(name, content) {
		var hc = MarkingTool.Editor.HTML;
		var window = hc.CreateElementRaw({
			css: ["ui-layout-window-outer"],
			children: [
				hc.CreateElementRaw({
					css: ["ui-layout-window-inner"],
					children: [
						createWrapper(hc.CreateElementRaw({
							content: name,
							css: ["ui-layout-window-title"]
						})),
						createWrapper(hc.CreateElementRaw({
							children: [
								hc.CreateElementRaw({ 
									css: ["ui-layout-window-content"],
									children: content 
								})
							]
						}))
					]
				})
			]
		});
		return window;
	};
	var createLayoutContainer = function() {
		var hc = MarkingTool.Editor.HTML;
		var container = hc.CreateElementRaw({
			css: ["ui-layout-container"],
			children: [
				hc.CreateElementRaw({
					css: ["ui-layout-left", "ui-layout-dock", "ui-open"],
					children: [
						createLayoutWindow("Filter", createFilterBox())
					]
				}),
				createWrapper(hc.CreateElementRaw({
					css: ["ui-layout-main"],
					children: []
				})),
				hc.CreateElementRaw({
					css: ["ui-layout-right", "ui-layout-dock", "ui-open"],
					children: [
						createLayoutWindow("Änderungen", [])
					]
				})
			]
		});
		createWrapper(container).appendTo($(".content-box"));
	};
	var createTaskSingleBar = function(task, useTaskNum) {
		var hc = MarkingTool.Editor.HTML;
		var changeState = 0;
		var inpPoints, inpState;
		var bar = hc.CreateElementRaw({
			css: ["ui-task-bar"],
			children: [
				hc.CreateElement("div", task.path[1], { css: ["ui-task-num"] }),
				hc.CreateElementRaw({
					children: [
						inpPoints = hc.CreateInput("text", function(){
							changeState++;
							if (changeState == 1) {
								var val = $(this).val();
								try { task.points = val == "" || val == undefined ? undefined : val * 1.0; }
								catch (e) {
									if ($(this).val() == "" || $(this).val() == undefined) task.points = undefined;
									else $(this).focus();
								}
							}
							changeState--;
						}, {css: ["ui-task-points small"], value: (task.points == undefined ? "": task.points), placeholder: "leer" } ),
						hc.CreateElement("span", "/" + task.maxPoints + (task.isBonus ? "<span title=\"Bonus\"> (B)</span>" : ""), {
							title: "Punkte"
						})
					]
				}),
				hc.CreateElementRaw({
					children: [
						inpState = hc.CreateSelect(MarkingTool.Editor.View.SimpleStateCodes , task.status, function() {
							changeState++;
							if (changeState == 1) {
								var val = $(this).val();
								task.status = val == -1 ? undefined : val;
							}
							changeState--;
						}, {css: ["ui-task-status small"]})
					]
				}),
				hc.CreateElement("div", "Bem."),
				hc.CreateElement("div", "file1"),
				hc.CreateElement("div", "file2")
			]
		});
		task.UpdatedEvent.add(function() {
			changeState++;
			if (changeState == 1) {
				inpPoints.val(task.points == undefined ? "": task.points);
				inpState.val(task.status);
			}
			changeState--;
		});
		return bar;
	};
	var createSimpleTaskBox = function(key, isTaskNum) {
		var hc = MarkingTool.Editor.HTML;
		var content;
		var header;
		if (isTaskNum) header = hc.CreateElement("div", "Aufgabe "+key);
		else {
			var user = MarkingTool.Editor.Logic.bName[key].user;
			var names = [];
			for (var i = 0; i<user.length; ++i)
				names.push(user[i].name);
			header = hc.CreateElement("div", names.join(", "));
		}
		var box = hc.CreateElementRaw({
			css: ["ui-task-big-box"],
			children: [
				header,
				content = hc.CreateElementRaw({ css: ["ui-task-content"] })
			],
			"data-status": "normal"
		});
		return {
			box: box,
			content: content
		};
	};
	
	
	this.createTasksView = function(task, useTaskNum) {
		var hc = MarkingTool.Editor.HTML;
		var box = hc.CreateElementRaw({
			css: ["ui-task-box"],
			children: [
				hc.CreateElementRaw({
					css: ["ui-task-header"],
					children: [
						createWrapper(createWrapper(createTaskSingleBar(task, useTaskNum))),
						createWrapper(hc.CreateButton("open", function(){
							$(this).parent().parent().parent().parent().toggleClass("ui-open");
						}))
					]
				})
			]
		});
		return box;
	};
	this.createTaskBox = function(key, useTaskNum) {
		var items = [];
		if (useTaskNum)
			items = MarkingTool.Editor.Logic.bTask[key];
		else items = MarkingTool.Editor.Logic.bName[key].tasks;
		var b = createSimpleTaskBox(key, useTaskNum);
		var box = {
			control: b.box,
			show: function() {
				b.box.show();
			},
			hide: function() {
				b.box.hide();
			},
			tasks: [],
			setChanged: function() {
				b.box.attr("data-status", "changed");
			},
			setUploading: function() {
				b.box.attr("data-status", "uploading");
			},
			setError: function() {
				b.box.attr("data-status", "error");
			},
			setNormal: function() {
				b.box.attr("data-status", "normal");
			}
		};
		for (var i = 0; i<items.length; ++i) {
			var task = thisref.createTasksView(items[i].task, !useTaskNum);
			task.appendTo(b.content);
			box.tasks.push(task);
			items[i].task.UpdatedEvent.add((function(task, box) {
				return function() {
					if (task.isValueChanged()) {
						if (box.control.attr("data-status") == "normal")
							box.setChanged();
					}
					else {
						if (box.control.attr("data-status") != "uploading")
							box.setNormal();
					}
				};
			})(items[i].task, box));
		}
		return box;
	};
	this.createCompleteTaskView = function(useTaskNum) {
		var boxes = [], box;
		var container = $(".ui-layout-main");
		container.html("");
		if (useTaskNum) {
			for (var task in MarkingTool.Editor.Logic.bTask)
				if (MarkingTool.Editor.Logic.bTask.hasOwnProperty(task)) {
					boxes.push(box = thisref.createTaskBox(task, useTaskNum));
					box.control.appendTo(container);
				}
		}
		else {
			for (var i = 0; i<MarkingTool.Editor.Logic.bName.length; ++i) {
				boxes.push(box = thisref.createTaskBox(i, useTaskNum));
				box.control.appendTo(container);
			}
		}
		thisref.Boxes = boxes;
	}
	var _init = function(){
		createCommandBar();
		createLayoutContainer();
	};
	//Initialisiert die Oberfläche
	this.Init = function() {
		_init.call(thisref); 
	};
};

//Stellt die Programmlogik bereit
MarkingTool.Editor.Logic = new function() {
	var thisref = this;
	var bName = []; //Sortiert nach Name
	var bTask = {}; //Sortiert nach Aufgabennummer
	var createTaskObject = function(raw, path) {
		var data = new MarkingTool.Editor.UpdateFactory.AddObject(raw, path); //Erzeugt das Objekt über die Verwaltung
		//Falls der Wert auf null gesetzt wurde, so wurde seine Property nicht erzeugt.
		//Hier wird gegengeprüft, ob alle Propertys angelegt wurden (nur die die auf null gesetzt werden können).
		data.checkProperty("submissionId", null);
		data.checkProperty("markingId", null);
		data.checkProperty("points", null);
		data.checkProperty("accepted", true);
		data.checkProperty("isBonus", false);
		data.checkProperty("status", null);
		data.checkProperty("tutorComment", "");
		data.checkProperty("userFile", null);
		data.checkProperty("tutorFile", null);
		data.checkProperty("studentComment", "");
		data.checkProperty("date", 0);
		data.checkProperty("tutor", null);
		//Setze alle Werte als Default
		data.setAllValuesAsDefault();
		//Das Objekt ist jetzt fertig und zur Überwachung hinzugefügt
		return data;
	};
	var getTaskNum = function(primary, secondary) {
		if (secondary == 0) return "" + primary;
		else return "" + primary + ("abcdefghijklmnopqrstuvwxyz")[secondary - 1];
	};
	var getAllTasks = function() {
		var data = MarkingTool.Editor.Data;
		for (var i1 = 0; i1<data.length; ++i1) {
			var user = data[i1].group;
			var primary = 0, secondary = 0;
			var tasklist = [];
			for (var i2 = 0; i2<data[i1].tasks.length; ++i2) {
				var task = data[i1].tasks[i2];
				if (task.isMainTask) {
					primary++;
					secondary = 0;
				}
				else secondary++;
				var num = getTaskNum(primary, secondary);
				task = createTaskObject(task, [user, num]);
				if (bTask[num] == undefined) bTask[num] = [];
				bTask[num].push({user: user, task: task});
				tasklist.push({num: num, task: task});
			}
			bName.push({user : user, tasks: tasklist});
		}
	}
	
	var _init = function() {
		getAllTasks();
		thisref.bName = bName;
		thisref.bTask = bTask;
	};
	//Initialisiert die Logik
	this.Init = function() {
		_init.call(thisref);
	};
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
		thisref.UpdatedEvent.invoke(thisref);
	};
	//Überprüft ob der aktuelle Wert der Property mit dem Standartwert übereinstimmt. Es erfolgt keine
	//Typprüfung.
	//return: Boolean - Ergebnis der Überprüfung
	this.isValueChanged = function() {
		var value = owner[thisref.key];
		return value != defaultValue;
	};
	//Verändert den Wert der Property
	//newValue: Wert - Der neue Wert der Property
	this.changeValue = function(newValue) {
		owner[key] = newValue;
		thisref.UpdatedEvent.invoke(thisref);
	};
	//Setzt den Wert auf den Standart zurück
	this.resetValue = function() {
		owner[key] = defaultValue;
		thisref.UpdatedEvent.invoke(thisref);
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
		thisref.UpdatedEvent.invoke(thisref);
	};
	for (var key in data) {
		if (data.hasOwnProperty(key)) {
			propertys[key] = new MarkingTool.Editor.UpdateProperty(data, key, path);
			Object.defineProperty(this, key, {
				get: (function(data, key) { return function() { return data[key]; }; })(data, key),
				set: (function(data, key, propertys) { return function(value) { propertys[key].changeValue(value); }; })(data, key, propertys)
			});
			propertys[key].UpdatedEvent.add(raiseHandler);
		}
		else this[key] = data[key];
	}
	//Überprüft ob eine Property schon exisitiert und fügt sie gegebenfalls hinzu.
	//key:          String - Der Name der Property
	//defaultValue: Wert   - Der Wert den die Property annehmen soll, falls sie noch nicht existierte.
	this.checkProperty = function(key, defaultValue) {
		if (propertys[key] == undefined) {
			propertys[key] = new MarkingTool.Editor.UpdateProperty(data, key, path);
			propertys[key].changeValue(defaultValue);
			propertys[key].setDefaultValue(defaultValue);
			Object.defineProperty(this, key, {
				get: (function(data, key) { return function() { return data[key]; }; })(data, key),
				set: (function(data, key, propertys) { return function(value) { propertys[key].changeValue(value); }; })(data, key, propertys)
			});
			propertys[key].UpdatedEvent.add(raiseHandler);
		}
	};
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
		thisref.UpdatedEvent.invoke(thisref);
	};
	//Überprüft ob die aktuellen Werte aller Propertys mit dem Standartwerten übereinstimmen. Es erfolgt keine
	//Typprüfung.
	//return: Boolean - Ergebnis der Überprüfung
	this.isValueChanged = function() {
		for (var key in propertys)
			if (propertys.hasOwnProperty(key))
				if (propertys[key].isValueChanged())
					return true;
		return false;
	};
	//Setzt den Standartwert für eine Property
	//key:   String - Der Name der Property
	//value: Wert   - Der neue Standartwert der Property
	this.setDefaultValue = function(key, value) {
		propertys[key].setDefaultValue(value);
		thisref.UpdatedEvent.invoke(thisref);
	};
	//Setzt die aktuellen Werte aller Propertys als Standartwerte
	this.setAllValuesAsDefault = function() {
		for (var key in propertys)
			if (propertys.hasOwnProperty(key))
				propertys[key].setDefaultValue(data[key]);
		thisref.UpdatedEvent.invoke(thisref);
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
		obj.UpdatedEvent.add(changedHandler);
		thisref.WatchList.push(obj);
		return obj;
	};
	//Entfernt ein UpdateObject wieder aus der Überwachung.
	//path: Objekt - der Identifizierer für das UpdateObject.
	this.RemoveObject = function(path) {
		for (var i = 0; i<this.WatchList.length; ++i)
			if (this.WatchList[i].path == path) {
				this.WatchList.splice(i,1)[0].UpdatedEvent.remove(changedHandler);
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


$(function() {
	//Diese Funktion wird aufgerufen, wenn die Webseite bereit ist. Nun kann alles geladen 
	//und aufgebaut werden.
	MarkingTool.Editor.View.Init();
	MarkingTool.Editor.Logic.Init();
	MarkingTool.Editor.View.createCompleteTaskView(false);
	MarkingTool.Editor.UpdateIndicator.HideBox();
});
