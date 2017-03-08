

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
};

//Stellt die Oberfläche und ihre Funktionen bereit.
MarkingTool.Editor.View = new function() {
	var thisref = this;
	//verpackt das Element in ein <div/> Element
	var createWrapper = function(element) {
		return MarkingTool.Editor.HTML.CreateElementRaw({children: [element]});
	};
	//erzeugt die Optionsleiste ganz oben
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
	//erzeugt die Inhalte für die Filterbox auf der linken seite
	var createFilterBox = function() {
		var hc = MarkingTool.Editor.HTML;
		return [
			hc.CreateFoldingGroup("Filter", [
				hc.CreateElement("div", "Serie:", { css: ["ui-filter-title"] }),
				hc.CreateSelect(MarkingTool.Editor.View.SheetCodes, undefined, undefined, { css: ["ui-select"] }),
				hc.CreateElement("div", "Kontrolleur:", { css: ["ui-filter-title"] }),
				hc.CreateSelect(MarkingTool.Editor.View.TutorCodes, undefined, function() {
					MarkingTool.Editor.Logic.Filter.lecture = $(this).val();
					MarkingTool.Editor.Logic.ApplyFilter();
				}, { css: ["ui-select"] }),
				hc.CreateElement("div", "Status:", { css: ["ui-filter-title"] }),
				hc.CreateSelect(MarkingTool.Editor.View.StateCodes, undefined, function() {
					MarkingTool.Editor.Logic.Filter.state = $(this).val();
					MarkingTool.Editor.Logic.ApplyFilter();
				}, { css: ["ui-select"] }),
				hc.CreateElement("div", "Ohne Einsendung", { css: ["ui-filter-title"] }),
				hc.CreateInput("checkbox", function() {
					MarkingTool.Editor.Logic.Filter.showTaskWithoutUserFiles = $(this).is(":checked");
					MarkingTool.Editor.Logic.ApplyFilter();
				}),
				hc.CreateElement("label", "Einträge ohne Einsendungen anzeigen", {
					style: "font-size: 0.8em"
				})
			], { css: ["ui-open"] }),
			hc.CreateFoldingGroup("Sortierung", [
				hc.CreateElement("div", "Sortiere nach:", { css: ["ui-filter-title"] }),
				hc.CreateSelect({
					"name": "Nach Namen",
					"task": "Nach Aufgaben"
				}, "name", undefined, { css: ["ui-select"] })
			], { css: ["ui-open"] }),
			hc.CreateFoldingGroup("Suche", [
				hc.CreateInput("text", function() {
					MarkingTool.Editor.Logic.Filter.text = $(this).val();
					MarkingTool.Editor.Logic.ApplyFilter();
				})/*,
				hc.CreateInput("button", function() {
					MarkingTool.Editor.Logic.ApplyFilter();
				}, { value: "Suche" })*/
			], { css: ["ui-open"] })
		];
	};
	//erzeugt eine Sidebar, die auch als Fenster geöffnet werden kann. Sie wird für 
	//den Filter genutzt.
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
	//erzeugt den Hauptcontainer in dem sich dann der Filter, die Änderungen und die
	//Hauptelemente befinden.
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
	//erzeugt den dünnen Streifen mit den schnellen Optionseinstellungen für die 
	//ganzen Einträge
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
				hc.CreateComplexButton(
					hc.CreateSimpleImage("Images/Text.png"),
					hc.CreateSimpleImage("Images/Error.png", 
						task.studentComment == null || task.studentComment == "" ? 
						{ css: [ "ui-show" ] } : undefined),
					hc.CreateElement("div", "S"),
					undefined,
					{ title: "Studentenkommentar" }
				),
				hc.CreateComplexButton(
					hc.CreateSimpleImage("Images/Text.png"),
					hc.CreateSimpleImage("Images/Error.png", 
						task.tutorComment == null || task.tutorComment == "" ? 
						{ css: [ "ui-show" ] } : undefined),
					hc.CreateElement("div", "K"),
					undefined,
					{ title: "Kontrolleurkommentar" }
				),
				hc.CreateComplexButton(
					hc.CreateSimpleImage("Images/Download.png"),
					hc.CreateSimpleImage("Images/Error.png", 
						task.userFile == null ? { css: [ "ui-show" ] } : undefined),
					hc.CreateElement("div", "S"),
					undefined,
					{ title: "Studenteneinsendung" }
				),
				hc.CreateComplexButton(
					hc.CreateSimpleImage("Images/Download.png"),
					hc.CreateSimpleImage("Images/Error.png", 
						task.tutorFile == null ? { css: [ "ui-show" ] } : undefined),
					hc.CreateElement("div", "K"),
					undefined,
					{ title: "Kontrolleureinsendung" }
				)
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
	//erzeugt das Rohgerüst für die Box in der alle Einsendungen aufgelistet sind.
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
	//erzeugt eine Box zur Signalisation, dass keine Einträge sichtbar sind.
	var createEmptyTaskBox = function(show) {
		var hc = MarkingTool.Editor.HTML;
		var box = hc.CreateElementRaw({
			css: show ? ["ui-task-big-box", "empty"] : ["ui-task-big-box", "empty", "ui-hide"],
			content: "Keine Elemente zur Anzeige vorhanden"
		});
		return box;
	};
	
	//erzeugt den Eintrag für die Änderungsliste in der genau aufgelistet ist, 
	//was geändert wurde.
	this.createChangeInfo = function(task, closeMethod) {
		var hc = MarkingTool.Editor.HTML;
		var user = task.path[0];
		var names = [];
		for (var i = 0; i<user.length; ++i)
			names.push(user[i].name);
		var oldP, newP, newState;
		var lineP, lineS, lineC, lineSF, lineTF;
		var update = function() {
			var def = task.getPropertys()["points"].getDefaultValue();
			oldP.html(def == undefined ? 0 : def);
			newP.html(task.points);
			var found = false;
			for (var i = 0; i<MarkingTool.Editor.View.SimpleStateCodes.length; ++i)
				if (MarkingTool.Editor.View.SimpleStateCodes[i].key == task.status) {
					newState.html(MarkingTool.Editor.View.SimpleStateCodes[i].value);
					found = true;
					break;
				}
			if (!found) newState.html("-");
			if (task.getPropertys()["points"].isValueChanged()) lineP.addClass("ui-open"); else lineP.removeClass("ui-open");
			if (task.getPropertys()["status"].isValueChanged()) lineS.addClass("ui-open"); else lineS.removeClass("ui-open");
			if (task.getPropertys()["tutorComment"].isValueChanged()) lineC.addClass("ui-open"); else lineC.removeClass("ui-open");
			if (task.getPropertys()["userFile"].isValueChanged()) lineSF.addClass("ui-open"); else lineSF.removeClass("ui-open");
			if (task.getPropertys()["tutorFile"].isValueChanged()) lineTF.addClass("ui-open"); else lineTF.removeClass("ui-open");
		};
		var close = function() {
			content.remove();
			task.UpdatedEvent.remove(update);
			closeMethod();
		};
		var children = [
			lineP = hc.CreateElementRaw({
				css: ["ui-upd-line", "ui-upd-points"],
				children: [
					oldP = hc.CreateElement("span", 0),
					hc.CreateElement("span", "Punkte"),
					hc.CreateElement("span", "&#10142;"),
					newP = hc.CreateElement("span", 0),
					hc.CreateElement("span", "Punkte")
				]
			}),
			lineS = hc.CreateElementRaw({
				css: ["ui-upd-line", "ui-upd-state"],
				children: [
					hc.CreateElement("span", "Neuer Status: "),
					newState = hc.CreateElement("span", "-")
				]
			}),
			lineC = hc.CreateElementRaw({
				css: ["ui-upd-line", "ui-upd-comment"],
				children: [
					hc.CreateElement("span", "Neue Bemerkung")
				]
			}),
			lineSF = hc.CreateElementRaw({
				css: ["ui-upd-line", "ui-upd-student-file"],
				children: [
					hc.CreateElement("span", "Neue Einsendung")
				]
			}),
			lineTF = hc.CreateElementRaw({
				css: ["ui-upd-line", "ui-upd-tutor-file"],
				children: [
					hc.CreateElement("span", "Neue Korrektur")
				]
			})
		];
		var content = hc.CreateElementRaw({
			css: ["ui-upd-box"],
			children: [
				hc.CreateElementRaw({
					css: ["ui-upd-header"],
					children: [
						hc.CreateElement("div", names.join(", ")),
						createWrapper(hc.CreateButton("X", function() {
							task.resetValues();
							close();
						}, { title: "Alle Werte aus diesem Eintrag zurücksetzen"}))
					]
				}),
				hc.CreateElementRaw({
					css: ["ui-upd-body"],
					children: [
						hc.CreateElement("div", task.path[1]),
						hc.CreateElementRaw({
							css: ["ui-upd-content"],
							children: children
						})
					]
				})
			]
		});
		task.UpdatedEvent.add(update);
		update();
		return {
			content: content,
			close: close
		};
	};
	//Erzeugt den Container und Ansicht für eine Einsendung. Hier wird alles zu
	//dieser bearbeitet und angezeigt.
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
		return {
			box: box,
			filter: function(filter) {
				var show = true;
				if (filter.lecture != "all") {
					show &= task.tutor != undefined && task.tutor.id == filter.lecture;
				}
				if (filter.state != "all") {
					if (filter.state == "notAccepted")
						show &= task.accepted != true; // false | null
					else show &= task.status == filter.state;
				}
				if (!filter.showTaskWithoutUserFiles) {
					show &= task.userFile != null;
				}
				if (show && filter.text != "") {
					show = false;
					var includes = function(value, text) {
						return value == null ? false : String(value).toLowerCase().includes(text);
					}
					show |= includes(task.maxPoints, filter.text);
					show |= includes(task.points, filter.text);
					show |= includes(MarkingTool.Editor.View.StateCodes[task.status], filter.text);
					show |= includes(task.tutorComment, filter.text);
					show |= includes(task.studentComment, filter.text);
					show |= includes(task.date, filter.text);
				}
				if (show) box.removeClass("ui-hide");
				else box.addClass("ui-hide");
				return show;
			}
		};
	};
	//Erzeugt einen Container für alle Einsendungen einer Rubrik. Alle Elemente
	//sind schon eingetragen.
	this.createTaskBox = function(key, useTaskNum) {
		var items = [];
		if (useTaskNum)
			items = MarkingTool.Editor.Logic.bTask[key];
		else items = MarkingTool.Editor.Logic.bName[key].tasks;
		var b = createSimpleTaskBox(key, useTaskNum);
		var box = {
			control: b.box,
			filterlist: [],
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
			},
			filter: function(filter) {
				var groupfilter = function(group) {
					if (filter.text == "") return false;
					for (var i = 0; i<group.length; ++i) {
						if (String(group[i].name).toLowerCase().includes(filter.text))
							return true;
						if (String(group[i].user).toLowerCase().includes(filter.text))
							return true;
					}
					return false;
				};
				var taskfilter = function(tasknum) {
					return filter.text != "" && String(tasknum).toLowerCase().includes(filter.text);
				};
				var show;
				if (useTaskNum) show = taskfilter(key);
				else show = groupfilter(MarkingTool.Editor.Logic.bName[key].user);
				if (show) {
					for (var i = 0; i<box.tasks.length; ++i)
						box.tasks[i].removeClass("ui-hide");
					b.box.removeClass("ui-hide");
				}
				else {
					for (var i = 0; i<box.filterlist.length; ++i)
						show |= box.filterlist[i](filter);
					if (show) b.box.removeClass("ui-hide");
					else b.box.addClass("ui-hide");
				}
				return show;
			}
		};
		for (var i = 0; i<items.length; ++i) {
			var _task = thisref.createTasksView(items[i].task, !useTaskNum);
			var task = _task.box;
			task.appendTo(b.content);
			box.tasks.push(task);
			box.filterlist.push(_task.filter);
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
	//Erzeugt alle Container für Einsendungen.
	this.createCompleteTaskView = function(useTaskNum) {
		var boxes = [], box;
		var container = $(".ui-layout-main");
		container.html("");
		var show = false;
		if (useTaskNum) {
			for (var task in MarkingTool.Editor.Logic.bTask)
				if (MarkingTool.Editor.Logic.bTask.hasOwnProperty(task)) {
					boxes.push(box = thisref.createTaskBox(task, useTaskNum));
					box.control.appendTo(container);
					show |= box.filter(MarkingTool.Editor.Logic.Filter);
				}
		}
		else {
			for (var i = 0; i<MarkingTool.Editor.Logic.bName.length; ++i) {
				boxes.push(box = thisref.createTaskBox(i, useTaskNum));
				box.control.appendTo(container);
				show |= box.filter(MarkingTool.Editor.Logic.Filter);
			}
		}
		container.append(createEmptyTaskBox(!show));
		thisref.Boxes = boxes;
	}
	
	//private Init()
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
	//erzeugt ein neues überwachtes Objekt aus den Rohdaten der Aufgabe.
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
		if (data.status == null) data.status = -1;
		data.setAllValuesAsDefault();
		//Das Objekt ist jetzt fertig und zur Überwachung hinzugefügt
		return data;
	};
	//Verarbeitet alle Rohdaten und befüllt bName und bTask.
	var getAllTasks = function() {
		var data = MarkingTool.Editor.Data;
		for (var i1 = 0; i1<data.length; ++i1) {
			var user = data[i1].group;
			var tasklist = [];
			for (var i2 = 0; i2<data[i1].tasks.length; ++i2) {
				var task = data[i1].tasks[i2];
				var num = task.tasknum;
				task = createTaskObject(task, [user, num]);
				if (bTask[num] == undefined) bTask[num] = [];
				bTask[num].push({user: user, task: task});
				tasklist.push({num: num, task: task});
			}
			bName.push({user : user, tasks: tasklist});
		}
	}
	//Eine Liste mit allen Referenzen für Objekte die unter Änderungen angezeigt werden.
	var updObjectList = {};
	//Ein Handler wenn ein Objekt den Zustand geändert erhält.
	var updAddHandler = function(task) {
		var path = JSON.stringify(task.path);
		if (updObjectList[path] != undefined) return;
		var vo = MarkingTool.Editor.View.createChangeInfo(task, function() {
			updObjectList[path] = undefined;
		});
		updObjectList[path] = vo;
		$(".ui-layout-right").find(".ui-layout-window-content").append(vo.content);
	};
	//Ein Handler wenn ein Objekt der Zustand geändert entfernt wird.
	var updRemoveHandler = function(task) {
		var path = JSON.stringify(task.path);
		if (updObjectList[path] == undefined) return;
		updObjectList[path].close();
		updObjectList[path] = undefined;
	};
	//Bestimmt den Filter, der auf alle angezeigten Aufgaben angewandt wird.
	this.Filter = {
		//Der ausgewählte zugewiesene Kontrolleur. 'all' für alle Kontrolleure.
		lecture: "all",
		//Der Status. 'all' für alle Statusmodi
		state: "all",
		//Bestimmt ob Aufgaben ohne Dateien von Nutzern angezeigt werden sollen.
		showTaskWithoutUserFiles: false,
		//Der Text der zusätzlich irgendwo enthalten sein soll.
		text: ""
	};
	//Ein Schlüssel zur Erkennung neuerer Filtermethoden
	var filterChangedEventKey = 0;
	//private Event für FilterChanged
	var filterChangedEvent = new MarkingTool.Event();
	//Dieses Event wird ausgelöst, sobald ApplyFilter() aufgerufen wurde.
	Object.defineProperty(this, "FilterChanged", {get: function(){ return filterChangedEvent; } });
	//Wendet den ausgewählten Filter auf alle angezeigten Boxen an.
	//[filter]: Objekt - Der Filter. (default: this.Filter)
	this.ApplyFilter = function(filter) {
		MarkingTool.Editor.UpdateIndicator.ShowBox();
		if (filter == undefined) filter = thisref.Filter;
		else thisref.Filter = filter;
		thisref.FilterChanged.invoke(thisref);
		var key = ++filterChangedEventKey;
		var show = false;
		for (var i = 0; i<MarkingTool.Editor.View.Boxes.length; ++i) {
			if (filterChangedEventKey != key) return; //Es gibt einen neueren Prozess
			show |= MarkingTool.Editor.View.Boxes[i].filter(filter);
		}
		if (show) $(".ui-task-big-box.empty").addClass("ui-hide");
		else $(".ui-task-big-box.empty").removeClass("ui-hide");
		MarkingTool.Editor.UpdateIndicator.HideBox();
	};
	
	//private Init()
	var _init = function() {
		getAllTasks();
		thisref.bName = bName;
		thisref.bTask = bTask;
		MarkingTool.Editor.UpdateFactory.AddedEvent.add(updAddHandler);
		MarkingTool.Editor.UpdateFactory.RemovedEvent.add(updRemoveHandler);
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
	//Ruft den Standartwert ab
	//return: Wert - der Standartwert dieser Property
	this.getDefaultValue = function() {
		return defaultValue;
	}
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
			for (var i = 0; i<thisref.UpdateList.length; ++i)
				if (thisref.UpdateList[i] == sender)
					return;
			thisref.UpdateList.push(sender);
			addedEvent.invoke(sender);
		}
		else {
			for (var i = 0; i<thisref.UpdateList.length; ++i)
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
	Object.defineProperty(this, "RemovedEvent", {get: function() { return removedEvent; } });
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
