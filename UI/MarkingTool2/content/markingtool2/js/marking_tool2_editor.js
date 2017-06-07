//require helper_events.js
//require helper_queue.js
//require helper_html.js
//require helper_updateindicator.js
//require helper_updateproperty.js
//require helper_updateobject.js
//require helper_updatefactory.js

//Lege Klassenvariable an
var MarkingTool = MarkingTool || {};
MarkingTool.Editor = MarkingTool.Editor || {};

//Stellt die Oberfläche und ihre Funktionen bereit.
MarkingTool.Editor.View = new function() {
	var thisref = this;
	//Dies löscht die Eingabe in einem html input file Element
	//thx to: <http://stackoverflow.com/a/24608023>
	var clearFileInput = function(f) {
		if (f.value) {
			try { f.value = ""; }
			catch(err) {
				if (f.value) {
					var form = document.createElement("form");
					var parentNode = f.parentNode;
					var ref = f.nextSibling;
					form.appendChild(f);
					form.reset();
					parentNode.insertBefore(f, ref);
				}
			}
		}
	};
	//verpackt das Element in ein <div/> Element
	var createWrapper = function(element) {
		return Helper.HTML.CreateElementRaw({children: [element]});
	};
	//erzeugt die Optionsleiste ganz oben
	var createCommandBar = function() {
		var hc = Helper.HTML;
		var counter;
		var optionsBar = hc.CreateElementRaw({
			css: ["ui-commandbar"],
			children: [
				hc.CreateButton(Lang("menu","backBtn"), function() {
					document.location.href = MarkingTool.Editor.Settings.BackUrl;
				}),
				hc.CreateElementRaw({
					css: ["ui-commandbar-container"],
					children: [
						hc.CreateButtonMenu(hc.CreateButton(Lang("menu","viewBtn")), [
							hc.CreateButton(Lang("filter","filterHeader"), function() {
								$(this).toggleClass("active");
								if ($(this).hasClass("active")) $(".ui-layout-left").addClass("ui-open");
								else $(".ui-layout-left").removeClass("ui-open");
								$(".ui-ref-view-button").removeClass("ui-open");
							}, {css:["active"]} ),
							hc.CreateButton(Lang("changes","changeHeader"), function() {
								$(this).toggleClass("active");
								if ($(this).hasClass("active")) $(".ui-layout-right").addClass("ui-open");
								else $(".ui-layout-right").removeClass("ui-open");
								$(".ui-ref-view-button").removeClass("ui-open");
							}, {css:["active"]} )
						], { css: ["ui-ref-view-button"] }),
						hc.CreateButton(Lang("menu","updateBtn"), function() {
							Helper.UpdateIndicator.ShowBox();
							document.location.reload();
						}),
						hc.CreateButton(Lang("menu","saveBtn"), function() {
							MarkingTool.Editor.Logic.CheckForUploadableTasks(true);
						}, {
							children: [
								counter = hc.CreateElement("div", "0", {css:["ui-change-counter"]})
							]
						}),
						hc.CreateButton(Lang("menu","optionBtn"), function() {
							MarkingTool.Editor.View.CreateOptionsMenu()
								.appendTo($(document.body));
						})
					]
				})
			]
		});
		createWrapper(optionsBar).appendTo($(".content-box"));
		var upd = function() {
			counter.html(Helper.UpdateFactory.UpdateList.length);
		};
		Helper.UpdateFactory.AddedEvent.add(upd);
		Helper.UpdateFactory.RemovedEvent.add(upd);
	};
	//erzeugt die Inhalte für die Filterbox auf der linken seite
	var createFilterBox = function() {
		var hc = Helper.HTML;
		return [
			hc.CreateElementRaw({
				css: ["warning", "devmode"],
				children: [
					hc.CreateElement("div", Lang("filter","warningHeader"), {css: ["warning-header"]}),
					hc.CreateElement("div", Lang("filter","devModeWarning"))
				] 
			}),
			hc.CreateElementRaw({
				css: ["warning", "many-items", "ui-hide"],
				children: [
					hc.CreateElement("div", "Warnung", {css: ["warning-header"]}),
					hc.CreateElement("div", Lang("filter","largeSeries"))
				] 
			}),
			hc.CreateFoldingGroup(Lang("filter","filterHeader"), [
				hc.CreateElement("div", Lang("filter","seriesSetting"), { css: ["ui-filter-title"] }),
				hc.CreateSelect(MarkingTool.Editor.View.SheetCodes, undefined, undefined, { css: ["ui-select"] }),
				hc.CreateElement("div", Lang("filter","lectureSetting"), { css: ["ui-filter-title"] }),
				hc.CreateSelect(MarkingTool.Editor.View.TutorCodes, undefined, function() {
					MarkingTool.Editor.Logic.Filter.lecture = $(this).val();
					MarkingTool.Editor.Logic.ApplyFilter();
				}, { css: ["ui-select"] }),
				hc.CreateElement("div", Lang("filter","stateSetting"), { css: ["ui-filter-title"] }),
				hc.CreateSelect(MarkingTool.Editor.View.StateCodes, undefined, function() {
					MarkingTool.Editor.Logic.Filter.state = $(this).val();
					MarkingTool.Editor.Logic.ApplyFilter();
				}, { css: ["ui-select"] }),
				MarkingTool.Editor.Settings.RestrictedMode ? null :
					hc.CreateElement("div", Lang("filter","withoutSubmissionSetting"), { css: ["ui-filter-title"] }),
				MarkingTool.Editor.Settings.RestrictedMode ? null :
					hc.CreateInput("checkbox", function() {
						MarkingTool.Editor.Logic.Filter.showTaskWithoutUserFiles = $(this).is(":checked");
						MarkingTool.Editor.Logic.ApplyFilter();
					}),
				MarkingTool.Editor.Settings.RestrictedMode ? null :
					hc.CreateElement("label", Lang("filter","wosCheckbox"), {
						style: "font-size: 0.8em"
					})
			], { css: ["ui-open"] }),
			MarkingTool.Editor.Settings.RestrictedMode ? null :
				hc.CreateFoldingGroup(Lang("filter","sortHeader"), [
					hc.CreateElement("div", Lang("filter","sortSetting"), { css: ["ui-filter-title"] }),
					hc.CreateSelect({
						"name": Lang("filter","sortForName"),
						"task": Lang("filter","sortForTask")
					}, "name", function() {
						var useTaskNum = $(this).val() == "task";
						Helper.UpdateIndicator.ShowBox();
						MarkingTool.Editor.View.createFunctions.clear();
						MarkingTool.Editor.View.Loader.lazyLoadList = [];
						MarkingTool.Editor.View.createCompleteTaskView(useTaskNum);
						MarkingTool.Editor.Logic.ApplyFilter();
						MarkingTool.Editor.Logic.UpdateTaskCounter();
						Helper.UpdateIndicator.HideBox();
					}, { css: ["ui-select"] })
				], { css: ["ui-open"] }),
			hc.CreateFoldingGroup(Lang("filter","searchHeader"), [
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
	var createLayoutWindow = function(name, content, viewIndex) {
		var hc = Helper.HTML;
		var window = hc.CreateElementRaw({
			css: ["ui-layout-window-outer"],
			children: [
				hc.CreateElementRaw({
					css: ["ui-layout-window-inner"],
					children: [
						createWrapper(hc.CreateElementRaw({
							css: ["ui-layout-window-title"],
							children: [
								hc.CreateElement("span", name),
								hc.CreateButton("X", function() {
									window.parent().removeClass("ui-open");
									$(".ui-ref-view-button .ui-foldable-marker .ui-button")
										.eq(viewIndex).removeClass("active");
								}, {
									element: "span",
									css: ["ui-close"]
								})
							]
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
		var hc = Helper.HTML;
		var container = hc.CreateElementRaw({
			css: ["ui-layout-container"],
			children: [
				hc.CreateElementRaw({
					css: ["ui-layout-left", "ui-layout-dock", "ui-open"],
					children: [
						createLayoutWindow(Lang("filter","filterHeader"), createFilterBox(), 0)
					]
				}),
				createWrapper(hc.CreateElementRaw({
					css: ["ui-layout-main"],
					children: []
				})),
				hc.CreateElementRaw({
					css: ["ui-layout-right", "ui-layout-dock", "ui-open"],
					children: [
						createLayoutWindow(Lang("changes","changeHeader"), [], 1)
					]
				})
			]
		});
		createWrapper(container).appendTo($(".content-box"));
	};
	//erzeugt den dünnen Streifen mit den schnellen Optionseinstellungen für die 
	//ganzen Einträge
	var createTaskSingleBar = function(task, useTaskNum) {
		var hc = Helper.HTML;
		var changeState = 0;
		var inpPoints, inpState;
		var bar = hc.CreateElementRaw({
			css: ["ui-task-bar"],
			children: [
				(useTaskNum ? hc.CreateElement("div", task.path[1], { css: ["ui-task-num"] }) :
					hc.CreateElement("div", "#"+task.markingId, { css: ["ui-task-sub"], title: Lang("task","submissionId") })),
				hc.CreateElementRaw({
					children: [
						inpPoints = hc.CreateInput("text", function(){
							changeState++;
							if (changeState == 1) {
								var val = String($(this).val()).replace(/,/g, ".");
								try { task.points = val == "" || val == undefined ? undefined : val * 1.0; }
								catch (e) {
									if ($(this).val() == "" || $(this).val() == undefined) task.points = undefined;
									else $(this).focus();
								}
							}
							changeState--;
						}, {
							css: ["ui-task-points small"], 
							value: String(task.points == undefined ? "": task.points).replace(/\./g, ","), 
							placeholder: Lang("task","emptyInput")
						} ),
						hc.CreateElement("span", "/" + task.maxPoints +
							(task.isBonus ? "<span title="+
							Lang("task","bonusPoints")+"> "+
							Lang("task","bonusPointsShort")+
							"</span>" : ""
						), {
							title: Lang("task","points")
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
					hc.CreateSimpleImage("../../../../../../CContent/content/common/img/Text.png"),
					hc.CreateSimpleImage("../../../../../../CContent/content/common/img/Error.png", 
						task.studentComment == null || task.studentComment == "" ? 
						{ css: [ "ui-show" ] } : undefined),
					hc.CreateElement("div", Lang("task", "studentShort")),
					undefined,
					{ title: Lang("task","studentComment") }
				),
				hc.CreateComplexButton(
					hc.CreateSimpleImage("../../../../../../CContent/content/common/img/Text.png"),
					hc.CreateSimpleImage("../../../../../../CContent/content/common/img/Error.png", 
						task.tutorComment == null || task.tutorComment == "" ? 
						{ css: [ "ui-show" ] } : undefined),
					hc.CreateElement("div", Lang("task", "lectureShort")),
					undefined,
					{ title: Lang("task","lectureComment") }
				),
				hc.CreateComplexButton(
					hc.CreateSimpleImage("../../../../../../CContent/content/common/img/Download.png"),
					hc.CreateSimpleImage("../../../../../../CContent/content/common/img/Error.png", 
						task.userFile == null ? { css: [ "ui-show" ] } : undefined),
					hc.CreateElement("div", Lang("task", "studentShort")),
					undefined,
					{ title: Lang("task","studentSubmission") }
				),
				hc.CreateComplexButton(
					hc.CreateSimpleImage("../../../../../../CContent/content/common/img/Download.png"),
					hc.CreateSimpleImage("../../../../../../CContent/content/common/img/Error.png", 
						task.tutorFile == null ? { css: [ "ui-show" ] } : undefined),
					hc.CreateElement("div", Lang("task", "lectureShort")),
					undefined,
					{ title: Lang("task","lectureSubmission") }
				),
				!useTaskNum ? null :
					hc.CreateElement("div", "#"+task.markingId, { css: ["ui-task-sub"], title: Lang("task","submissionId") })
			]
		});
		task.UpdatedEvent.add(function() {
			changeState++;
			if (changeState == 1) {
				inpPoints.val(String(task.points == undefined ? "": task.points).replace(/\./g, ","));
				inpState.val(task.status);
			}
			changeState--;
		});
		return bar;
	};
	//erzeugt das Rohgerüst für die Box in der alle Einsendungen aufgelistet sind.
	var createSimpleTaskBox = function(key, isTaskNum) {
		var hc = Helper.HTML;
		var content;
		var header;
		if (isTaskNum) header = hc.CreateElement("div", Lang("task","task")+" "+key);
		else {
			var user = MarkingTool.Editor.Logic.bName[key].user;
			var names = [];
			for (var i = 0; i<user.length; ++i)
				names.push(user[i].name);
			header = hc.CreateElement("div", names.join(Lang("localisation","nameListSeperator")));
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
		var hc = Helper.HTML;
		var box = hc.CreateElementRaw({
			css: show ? ["ui-task-big-box", "empty"] : ["ui-task-big-box", "empty", "ui-hide"],
			content: Lang("task","noElementsToShow")
		});
		return box;
	};
	//Erzeugt den Inhalt für die erweiterten Funktionen
	var createTaskDetailContent = function(task) {
		var hc = Helper.HTML;
		task.changeState_detailContent = 0;
		var slider, pointInput, stategroup, tutorComment, studFileBut, studFileInput,
			tutorFileBut, tutorFileInput, acceptedInput;
		var states = [];
		var stateobj = {};
		for (var i = 0; i<MarkingTool.Editor.View.SimpleStateCodes.length; ++i) {
			var id = "state-" + task.id + "-" + task.groupIndex + "-" +
				MarkingTool.Editor.View.SimpleStateCodes[i].key;
			states.push(stateobj[MarkingTool.Editor.View.SimpleStateCodes[i].key] = 
				hc.CreateInput("radio", function() {
					task.changeState_detailContent++;
					if (task.changeState_detailContent == 1) {
						task.status = stategroup.find(":checked").val();
					}
					task.changeState_detailContent--;
				}, {
					value: MarkingTool.Editor.View.SimpleStateCodes[i].key,
					name: "state-"+task.id+"-"+task.groupIndex ,
					id: id
				}));
			if (MarkingTool.Editor.View.SimpleStateCodes[i].key == task.status ||
				(task.status == null && i == 0)) {
				//Leider muss das zeitverzögert gemacht werden, da die Browser
				//keiner Änderung der checked Eigenschaft erlauben solange es 
				//nicht zum DOM gehört. :(
				setTimeout(function(){ 
					//alert ("now "+id+"\n"+$("#"+id).length);
					task.changeState_detailContent++;
					var e = stateobj[task.status];
					//e.addClass("hi");
					//e.attr("checked", "checked");
					//e.prop("checked", true);
					e[0].checked = true;
					task.changeState_detailContent--;
				}, 1000);
			}
			states.push(hc.CreateElement("label", 
				MarkingTool.Editor.View.SimpleStateCodes[i].value, {
					"for": id
				}));
			states.push(hc.CreateElement("div", "")); //<br/>
		}
		var cont = [
			//Punkteauswahl
			hc.CreateElementRaw({
				css: ["ui-task-points"],
				children: [
					hc.CreateElement("div", Lang("task","pointsHeader")),
					createWrapper(slider = hc.CreateTrackBar(task.points == null ? 0 : task.points, 
						task.maxPoints, function(value) {
							task.changeState_detailContent++;
							if (task.changeState_detailContent == 1) {
								task.points = value;
								pointInput.val(String(task.points == null ? "" : task.points).replace(/\./g, ","));
							}
							task.changeState_detailContent--;
						})),
					hc.CreateElementRaw({
						children: [
							pointInput = hc.CreateInput("text", function() {
								task.changeState_detailContent++;
								if (task.changeState_detailContent == 1) {
									var val = String($(this).val()).replace(/,/g, ".");
									try {
										task.points = val == "" || val == undefined ? undefined : val * 1.0; 
									}
									catch (e) {
										if ($(this).val() == "" || $(this).val() == undefined) task.points = undefined;
										else $(this).focus();
									}
									slider.slider("value", task.points == null ? 0 : task.points);
								}
								task.changeState_detailContent--;
							}, {
								value: String(task.points == null ? "" : task.points).replace(/\./g, ","),
								placeholder: Lang("task","emptyInput")
							}),
							hc.CreateElement("span", "/" + task.maxPoints + (task.isBonus ? "<span title=\"Bonus\"> (B)</span>" : ""), {
								title: Lang("task","points")
							})
						]
					})
				]
			}),
			//Statusauswahl
			hc.CreateElementRaw({
				children: [
					hc.CreateElement("div", Lang("task","stateHeader")),
					createWrapper(stategroup = hc.CreateElementRaw({
						children: states,
						element: "fieldset"
					}))
				]
			}),
			//Akzeptiert
			hc.CreateElementRaw({
				children: [
					hc.CreateElement("div", Lang("task","acceptedHeader")),
					hc.CreateElementRaw({
						children: [
							acceptedInput = hc.CreateInput("checkbox", function(){
								task.changeState_detailContent++;
								if (task.changeState_detailContent == 1) {
									task.accepted = $(this).is(":checked");
								}
								task.changeState_detailContent--;
							}, task.accepted ? { checked: "checked" } : {}),
							hc.CreateElement("label", Lang("task","acceptSubmission"), {
								style: "font-size: 0.9em"
							})
						]
					})
				]
			}),
			//Bemerkung
			hc.CreateElementRaw({
				css: ["ui-task-comment"],
				children: [
					hc.CreateElement("div", Lang("task","commentHeader")),
					hc.CreateElementRaw({
						children: [
							hc.CreateElement("div", Lang("task","studentHeader")),
							task.studentComment == null ?
							hc.CreateElement("div", Lang("task","noComment"), {
								style: "font-style: italic; font-weight: normal;"
							}) :
							hc.CreateElementRaw({
								element: "textarea",
								text: task.studentComment,
								readonly: "readonly"
							}),
							hc.CreateElement("div", Lang("task","lectureHeader")),
							tutorComment = hc.CreateElementRaw({
								element: "textarea",
								text: task.tutorComment
							})
						]
					})
				]
			}),
			//Einsendungen
			hc.CreateElementRaw({
				children: [
					hc.CreateElement("div", Lang("task","filesHeader")),
					hc.CreateElementRaw({
						css: ["ui-task-files"],
						children: [
							hc.CreateElementRaw({
								"data-has-file": task.userFile != null,
								children: [
									hc.CreateElement("div", Lang("task","studentHeader"), {css: ["ui-task-files-header"]}),
									hc.CreateElement("div", Lang("task","noSubmissionExists")),
									studFileBut = hc.CreateButton(
										Lang("task","openFile"), undefined, {
											element: "a",
											target: "_blank",
											href: task.userFile==null ? "" : task.userFile.url,
											title: task.userFile==null ? "" : task.userFile.name
										}
									),
									studFileInput = hc.CreateInput("file", function(evt) {
										task.changeState_detailContent++;
										if (task.changeState_detailContent == 1) {
											if (evt.target.files.length == 0)
												task.getPropertys()["userFile"].resetValue();
											else {
												var obj = { file: evt.target.files[0] };
												var reader = new FileReader();
												reader.onload = function(e) {
													obj.blob = e.target.result;
												};
												reader.readAsDataURL(obj.file);
												task.userFile = obj;
											}
											var info = cont[0].parent().parent().children().eq(0)
												.find(".ui-complex-button-info").eq(2);
											if (task.userFile != null)
												info.removeClass("ui-show");
											else info.addClass("ui-show");
										}
										task.changeState_detailContent--;
									})
								]
							}),
							hc.CreateElementRaw({
								"data-has-file": task.tutorFile != null,
								children: [
									hc.CreateElement("div", Lang("task","lectureHeader"), {css: ["ui-task-files-header"]}),
									hc.CreateElement("div", Lang("task","noSubmissionExists")),
									tutorFileBut = hc.CreateButton(
										Lang("task","openFile"), undefined, {
											element: "a",
											target: "_blank",
											href: task.tutorFile==null ? "" : task.tutorFile.url,
											title: task.tutorFile==null ? "" : task.tutorFile.name
										}
									),
									tutorFileInput = hc.CreateInput("file", function(evt) {
										task.changeState_detailContent++;
										if (task.changeState_detailContent == 1) {
											if (evt.target.files.length == 0)
												task.getPropertys()["tutorFile"].resetValue();
											else {
												var obj = { file: evt.target.files[0] };
												var reader = new FileReader();
												reader.onload = function(e) {
													obj.blob = e.target.result;
												};
												reader.readAsDataURL(obj.file);
												task.tutorFile = obj;
											}
											var info = cont[0].parent().parent().children().eq(0)
												.find(".ui-complex-button-info").eq(3);
											if (task.tutorFile != null)
												info.removeClass("ui-show");
											else info.addClass("ui-show");
										}
										task.changeState_detailContent--;
									})
								]
							})
						]
					})
				]
			})
		];
		tutorComment.change(function() {
			task.changeState_detailContent++;
			if (task.changeState_detailContent == 1) {
				var val = $(this).val() == "" ? null : $(this).val();
				task.tutorComment = val;
				var info = cont[0].parent().parent().children().eq(0)
					.find(".ui-complex-button-info").eq(1);
				if (val == null) info.addClass("ui-show");
				else info.removeClass("ui-show");
			}
			task.changeState_detailContent--;
		});
		task.UpdatedEvent.add(function() {
			task.changeState_detailContent++;
			if (task.changeState_detailContent == 1) {
				//Points
				try { slider.slider("value", task.points == null ? 0 : task.points); }
				catch (e) {} //ignore this shit
				pointInput.val(String(task.points == null ? "" : task.points).replace(/\./g, ","));
				//Status
				stateobj[task.status][0].checked = true;
				//Accepted
				acceptedInput.prop("checked", task.accepted);
				//Comment
				tutorComment.val(task.tutorComment);
				var info = cont[0].parent().parent().children().eq(0)
					.find(".ui-complex-button-info").eq(1);
				if (task.tutorComment == null) info.addClass("ui-show");
				else info.removeClass("ui-show");
				//StudentFile
				info = cont[0].parent().parent().children().eq(0)
					.find(".ui-complex-button-info").eq(2);
				if (task.userFile == null)
					info.addClass("ui-show");
				else {
					info.removeClass("ui-show");
					studFileBut.attr("href", task.userFile.url);
					studFileBut.attr("title", task.userFile.name);
				}
				clearFileInput(studFileInput[0]);
				studFileBut.parent().attr("data-has-file", task.userFile != null);
				//TutorFile
				info = cont[0].parent().parent().children().eq(0)
					.find(".ui-complex-button-info").eq(3);
				if (task.tutorFile == null)
					info.addClass("ui-show");
				else {
					info.removeClass("ui-show");
					tutorFileBut.attr("href", task.tutorFile.url);
					tutorFileBut.attr("title", task.tutorFile.name);
				}
				clearFileInput(tutorFileInput[0]);
				tutorFileBut.parent().attr("data-has-file", task.tutorFile != null);
			}
			task.changeState_detailContent--;
		});
		return cont;
	};
	//Erzeugt die Info für die Auspaltung der Zustände
	var createForkInfo = function(baseState, serverState, localState, changed) {
		var hc = Helper.HTML;
		var server = hc.CreateElementRaw({
			css: ["ui-fork-button", "large", "server"],
			children: [
				hc.CreateElementRaw({
					children: [
						createWrapper(hc.CreateElementRaw({
							element: "img",
							src: "../../../../../../CContent/content/markingtool2/img/server-icon.png"
						})),
						createWrapper(serverState),
						createWrapper(hc.CreateElementRaw({
							css: ["ui-fork-arrow"],
							element: "img",
							src: "../../../../../../CContent/content/markingtool2/img/ok-icon.png"
						}))
					]
				})
			],
			title: Lang("fork","currentServerState")
		});
		var local = hc.CreateElementRaw({
			css: ["ui-fork-button", "large", "local"],
			children: [
				hc.CreateElementRaw({
					children: [
						createWrapper(hc.CreateElementRaw({
							element: "img",
							src: "../../../../../../CContent/content/markingtool2/img/computer-icon.png"
						})),
						createWrapper(localState),
						createWrapper(hc.CreateElementRaw({
							css: ["ui-fork-arrow"],
							element: "img",
							src: "../../../../../../CContent/content/markingtool2/img/ok-icon.png"
						}))
					]
				})
			],
			title: Lang("fork","stateToSave")
		});
		var state = null; //undefined
		var handler = function() {
			if ($(this).hasClass("server")) state = false;
			if ($(this).hasClass("local")) state = true;
			var sa = server.find(".ui-fork-arrow");
			var la = local.find(".ui-fork-arrow");
			if (state === false) sa.addClass("checked");
			else sa.removeClass("checked");
			if (state === true) la.addClass("checked");
			else la.removeClass("checked");
			if (changed != undefined) changed(state);
		};
		server.click(handler);
		local.click(handler);
		var fork = hc.CreateElementRaw({
			css: [ "ui-fork-element" ],
			children: [
				createWrapper(hc.CreateElementRaw({
					css: ["ui-fork-button"],
					children: [ baseState ],
					title: Lang("fork","oldState")
				})),
				createWrapper(hc.CreateElementRaw({
					children: [
						hc.CreateElementRaw({ css: [ "ui-fork-util-line", "vert", "up" ] }),
						hc.CreateElementRaw({ css: [ "ui-fork-util-line", "vert", "down" ] }),
						hc.CreateElementRaw({ css: [ "ui-fork-util-line", "horz", "top" ] }),
						hc.CreateElementRaw({ css: [ "ui-fork-util-line", "horz", "bottom" ] }),
						hc.CreateElementRaw({ css: [ "ui-fork-util-line", "horz", "middle" ] }),
						hc.CreateElementRaw({ css: [ "ui-fork-util-arrow", "up" ] }),
						hc.CreateElementRaw({ css: [ "ui-fork-util-arrow", "down" ] })
					]
				})),
				hc.CreateElementRaw({
					children: [ server, local ]
				})
			]
		});
		return fork;
	};
	
	var createForkTaskInfo = function(task, fullsetted) {
		var hc = Helper.HTML;
		if (task.error != null) {
		}
		else {
			task.use = {};
			var content = [];
			var setted;
			var createFork = function(param, createView) {
				var fork = createForkInfo(
					createView(task.task[param+"_old"]), 
					createView(task.newData[param]),
					createView(task.task[param+"_new"]), 
					function(state) {
						if (task.use[param] == undefined) {
							setted--;
							if (setted == 0 && fullsetted != undefined) fullsetted();
						}
						task.use[param] = state;
					});
				return fork;
			};
			if (task.newData.points != undefined)
				content.push(createFork("points", function(value) {
					return hc.CreateElementRaw({ text: Lang("task","pointsHeader")+" " + value });
				}));
			if (task.newData.accepted != undefined)
				content.push(createFork("accepted", function(value) {
					return hc.CreateElementRaw({ text: Lang("task", value ? "accepted" : "notAccepted") });
				}));
			if (task.newData.status != undefined)
				content.push(createFork("status", function(value) {
					for (var i = 0; i<MarkingTool.Editor.View.StateCodes.length; ++i)
						if (MarkingTool.Editor.View.StateCodes[i].key == value)
							return hc.CreateElementRaw({ text: Lang("task","stateHeader")+" " + MarkingTool.Editor.View.StateCodes[i].value });
					return hc.CreateElementRaw({ text: Lang("fork","stateUnknown") });
				}));
			if (task.newData.tutorComment != undefined)
				content.push(createFork("tutorComment", function(value) {
					return hc.CreateElementRaw({
						css: ["comment-box"],
						children: [
							hc.CreateElementRaw({ title: Lang("task","lectureCommentHeader") }),
							hc.CreateElementRaw({ element: "textarea", text: value })
						]
					});
				}));
			if (task.newData.studentComment != undefined)
				content.push(createFork("studentComment", function(value) {
					return hc.CreateElementRaw({
						css: ["comment-box"],
						children: [
							hc.CreateElementRaw({ title: Lang("task","studentCommentHeader") }),
							hc.CreateElementRaw({ element: "textarea", text: value })
						]
					});
				}));
			
			//Dateien
			
			setted = content.length;
			if (setted == 0) {
				fullsetted();
				return null;
			}
			var name = "";
			if (!MarkingTool.Editor.Settings.RestrictedMode)
				for (var i = 0; i<MarkingTool.Editor.Logic.bName.length; ++i)
					if (MarkingTool.Editor.Logic.bName[i].user[0].id == task.task.leaderId) {
						var group = MarkingTool.Editor.Logic.bName[i].user;
						for (i = 0; i<group.length; ++i)
							name += group[i].name + Lang("localisation","nameListSeperator");
						break;
					}
			name += "#"+task.task.markingId;
			return hc.CreateElementRaw({
				css: ["ui-fork-task-box"],
				children: [
					hc.CreateElementRaw({
						css: [ "ui-fork-task-header" ],
						text: name
					}),
					hc.CreateElementRaw({
						children: content
					})
				]
			});
		}
	};
	
	//Erzeugt das Optionsmenu
	this.CreateOptionsMenu = function() {
		var hc = Helper.HTML;
		var updating = 0;
		var autoSaveSlide, autoSaveInput;
		var win = Helper.HTML.CreateWindow(
			Lang("menu","optionBtn"), "large", [
				hc.CreateFoldingGroup(Lang("settings","saveHeader"), [
					hc.CreateElement("div", Lang("settings","saveIntervallDesc"),
						{ css: ["ui-filter-title"] }),
					hc.CreateElementRaw({
						children: [
							createWrapper(autoSaveSlide = hc.CreateTrackBar(
								MarkingTool.Editor.Settings.IntervallTime, 
								120, function(val) {
									if ((++updating) == 1) {
										val = Math.ceil(val < 1 ? 1 : val > 120 ? 120 : val);
										autoSaveInput.val(String(val));
										autoSaveSlide.slider("value", val);
										MarkingTool.Editor.Settings.IntervallTime = val;
										MarkingTool.Editor.Settings.SaveCookies();
									}
									updating--;
								})),
							autoSaveInput = hc.CreateInput("number", function() {
								if ((++updating) == 1) {
									var val = $(this).val();
									try { val = val * 1; }
									catch (e) { val = 5; }
									val = Math.ceil(val < 1 ? 1 : val > 120 ? 120 : val);
									autoSaveInput.val(val);
									autoSaveSlide.slider("value", val);
									MarkingTool.Editor.Settings.IntervallTime = val;
									MarkingTool.Editor.Settings.SaveCookies();
								}
								updating--;
							}, {
								value: MarkingTool.Editor.Settings.IntervallTime,
								placeholder: Lang("settings","missingValue")
							})
						],
						css: ["opt-inline-content"]
					})
				], { css: ["ui-open"] }),
				hc.CreateFoldingGroup(Lang("settings","editorHeader"), [
					hc.CreateElement("div", Lang("settings","autoOpenEditorBoxes"),
						{ css: ["ui-filter-title"] }),
					hc.CreateElementRaw({
						children: [
							hc.CreateInput("checkbox", function() {
								if ((++updating) == 1) {
									MarkingTool.Editor.Settings.AutoOpenTaskBoxes =
										$(this).is(":checked");
									MarkingTool.Editor.Settings.SaveCookies();
								}
								updating--;
							}, MarkingTool.Editor.Settings.AutoOpenTaskBoxes ? { checked: "checked" } : {}),
							hc.CreateElement("span", Lang("settings", "autoOpenEditorBoxDesc"))
						]
					})
				], { css: ["ui-open"] })
			], function() {
				win.remove();
			});
		return win;
	};
	//Erzeugt eine Übersicht zu allen Änderungen
	this.createForkInfo = function(tasks, fullsetted) {
		var left = tasks.length;
		var submitted = false;
		var content = [];
		for (var i = 0; i<tasks.length; ++i)
			content.push(createForkTaskInfo(tasks[i], function() {
				left--;
				if (left == 0) {
					submitted = true;
					fullsetted();
				}
			}));
		if (left == 0) {
			if (!submitted) fullsetted();
			return;
		}
		return Helper.HTML.CreateElementRaw({
			children: content
		});
	};
	//erzeugt den Eintrag für die Änderungsliste in der genau aufgelistet ist, 
	//was geändert wurde.
	this.createChangeInfo = function(task, closeMethod) {
		var hc = Helper.HTML;
		var user = task.path[0];
		var names = [];
		for (var i = 0; i<user.length; ++i)
			names.push(user[i].name);
		var oldP, newP, newState, newAccepted, newStudentFile, newTutorFile;
		var lineP, lineS, lineA, lineC, lineSF, lineTF;
		var update = function() {
			var def = task.getPropertys()["points"].getDefaultValue();
			oldP.html(def == undefined ? 0 : def);
			newP.html(task.points);
			newAccepted.html(task.accepted ? Lang("changes","submissionAcceptedNow") :
				Lang("changes","submissionNotAcceptedNow"));
			newStudentFile.html(task.userFile != null && task.userFile.file != null ?
				Math.round(task.userFile.file.size/1024) + " KB" : "0 KB");
			newTutorFile.html(task.tutorFile != null && task.tutorFile.file != null ?
				Math.round(task.tutorFile.file.size/1024) + " KB" : "0 KB");
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
			if (task.getPropertys()["accepted"].isValueChanged()) lineA.addClass("ui-open"); else lineA.removeClass("ui-open");
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
					hc.CreateElement("span", Lang("task","points")),
					hc.CreateElement("span", "&#10142;"),
					newP = hc.CreateElement("span", 0),
					hc.CreateElement("span", Lang("task","points"))
				]
			}),
			lineS = hc.CreateElementRaw({
				css: ["ui-upd-line", "ui-upd-state"],
				children: [
					hc.CreateElement("span", Lang("changes","newState")),
					newState = hc.CreateElement("span", Lang("changes","voidState"))
				]
			}),
			lineA = hc.CreateElementRaw({
				css: ["ui-upd-line", "ui-upd-accepted"],
				children: [
					newAccepted = hc.CreateElement("span", Lang("task","accepted"))
				]
			}),
			lineC = hc.CreateElementRaw({
				css: ["ui-upd-line", "ui-upd-comment"],
				children: [
					hc.CreateElement("span", Lang("changes","newComment"))
				]
			}),
			lineSF = hc.CreateElementRaw({
				css: ["ui-upd-line", "ui-upd-student-file"],
				children: [
					hc.CreateElement("span", Lang("changes","newSubmission")),
					newStudentFile = hc.CreateElement("span", "0 KB")
				]
			}),
			lineTF = hc.CreateElementRaw({
				css: ["ui-upd-line", "ui-upd-tutor-file"],
				children: [
					hc.CreateElement("span", Lang("changes","newCorrectedFile")),
					newTutorFile = hc.CreateElement("span", "0 KB")
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
						}, { title: Lang("changes","resetChanges")}))
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
		var hc = Helper.HTML;
		var result = {
			box: null,
			show: false,
			create: function() {
				result.box = hc.CreateElementRaw({
					css: MarkingTool.Editor.Settings.AutoOpenTaskBoxes ?
						["ui-task-box", "ui-open"] : ["ui-task-box"],
					children: [
						hc.CreateElementRaw({
							css: ["ui-task-header"],
							children: [
								createWrapper(createWrapper(createTaskSingleBar(task, useTaskNum))),
								createWrapper(hc.CreateButton("", function(){
									$(this).parent().parent().parent().toggleClass("ui-open");
								}, {
									css: ["ui-task-header-switch"],
									title: Lang("task","detailView")
								}))
							]
						}),
						hc.CreateElementRaw({
							css: ["ui-task-detail-container"],
							children: createTaskDetailContent(task)
						})
					]
				});
				if (!result.show) result.box.addClass("ui-hide");
			},
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
					//show |= includes(task.maxPoints, filter.text);
					show |= includes(task.points, filter.text);
					for (var i = 0; i<MarkingTool.Editor.View.StateCodes.length; ++i)
						if (MarkingTool.Editor.View.StateCodes[i].key == task.status)
							show |= includes(MarkingTool.Editor.View.StateCodes[i].value, filter.text);
					show |= includes(task.tutorComment, filter.text);
					show |= includes(task.studentComment, filter.text);
					show |= includes(task.date, filter.text);
					show |= includes(task.path[1], filter.text);
				}
				if (result.box != null) {
					if (show) result.box.removeClass("ui-hide");
					else result.box.addClass("ui-hide");
				}
				return result.show = show;
			}
		};
		return result;
	};
	//Erzeugt einen Container für alle Einsendungen einer Rubrik. Alle Elemente
	//sind schon eingetragen.
	this.createTaskBox = function(key, useTaskNum) {
		var items = [];
		if (useTaskNum)
			items = MarkingTool.Editor.Logic.bTask[key];
		else items = MarkingTool.Editor.Logic.bName[key].tasks;
		var b = createSimpleTaskBox(key, useTaskNum);
		var list = [];
		var box = {
			control: b.box,
			show: false,
			filterlist: [],
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
			createTasks: function(lazy) {
				if (list.length == 0) return;
				if ((lazy || false) && !thisref.Loader.checkLazy(b.box)) return;
				for (var i = 0; i<list.length; ++i) {
					list[i].create();
					list[i].box.appendTo(b.content);
					box.tasks.push(list[i]);
				}
				list = [];
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
					//box.createTasks(true);
					for (var i = 0; i<box.tasks.length; ++i)
						box.tasks[i].removeClass("ui-hide");
					b.box.removeClass("ui-hide");
				}
				else {
					for (var i = 0; i<box.filterlist.length; ++i)
						show |= box.filterlist[i](filter);
					if (show) {
						//box.createTasks(true);
						b.box.removeClass("ui-hide");
					}
					else b.box.addClass("ui-hide");
				}
				return box.show = show;
			}
		};
		for (var i = 0; i<items.length; ++i) {
			var _task = thisref.createTasksView(items[i].task, !useTaskNum);
			list.push(_task);
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
		thisref.Loader.addLazy(b.box, function() {
			if (box.show) box.createTasks();
			return box.show;
		});
		return box;
	};
	//Erzeugt alle Container für Einsendungen.
	this.createCompleteTaskView = function(useTaskNum) {
		var boxes = [], box;
		var container = $(".ui-layout-main");
		container.children().filter(":not(.loader):not(.empty)").remove();
		container.find(".ui-trackbar").slider("destroy");
		//container.html("");
		thisref.createFunctions.clear();
		//var show = false;
		if (useTaskNum) {
			for (var task in MarkingTool.Editor.Logic.bTask)
				if (MarkingTool.Editor.Logic.bTask.hasOwnProperty(task)) {
					thisref.createFunctions.push((function(task, boxes, loader) {
						return function() {
							var box;
							boxes.push(box = thisref.createTaskBox(task, useTaskNum));
							box.control.insertBefore(loader);
							return box.filter(MarkingTool.Editor.Logic.Filter);
						};
					})(task, boxes, thisref.Loader.loaderBox));
					//boxes.push(box = thisref.createTaskBox(task, useTaskNum));
					//box.control.appendTo(container);
					//show |= box.filter(MarkingTool.Editor.Logic.Filter);
				}
		}
		else {
			for (var i = 0; i<MarkingTool.Editor.Logic.bName.length; ++i) {
				thisref.createFunctions.push((function(i, boxes, loader) {
					return function() {
						var box;
						boxes.push(box = thisref.createTaskBox(i, useTaskNum));
						box.control.insertBefore(loader);
						return box.filter(MarkingTool.Editor.Logic.Filter);
					};
				})(i, boxes, thisref.Loader.loaderBox));
				//boxes.push(box = thisref.createTaskBox(i, useTaskNum));
				//box.control.appendTo(container);
				//show |= box.filter(MarkingTool.Editor.Logic.Filter);
			}
		}
		if ($(".ui-task-big-box.empty").length == 0)
			container.append(createEmptyTaskBox(false));
		//container.append(createEmptyTaskBox(!show));
		thisref.Boxes = boxes;
		thisref.Loader.check();
	}
	//Eine Schlange an Funktionen, die neue Boxen erzeugen können.
	this.createFunctions = new Helper.Queue();
	//Ein paar Methoden, die das Nachladen von Boxen unterstützen.
	this.Loader = {
		loaderBox: undefined,
		lazyLoadList: [],
		createLoaderContainer: function() {
			var hc = Helper.HTML;
			var content = "";
			for (var i = 0; i<12; ++i) content+="<div/>";
			var box = hc.CreateElementRaw({
				css: ["ui-task-big-box", "loader"],
				children: [
					hc.CreateElementRaw({
						css: ["loading-rotator"],
						content: content
					})
				]
			});
			box.appendTo($(".ui-layout-main"));
			$(".ui-layout-main").scroll(thisref.Loader.check);
			thisref.Loader.loaderBox = box;
		},
		loadNext: function() {
			if (thisref.createFunctions.isEmpty()) {
				$(".ui-task-big-box.loader").addClass("ui-hide");
				//MarkingTool.Editor.Logic.ApplyFilter();
			}
			else {
				$(".ui-task-big-box.loader").removeClass("ui-hide");
				if (!thisref.createFunctions.pop()())
					$(".ui-task-big-box.empty").addClass("ui-hide");
				thisref.Loader.checkNext();
			}
		},
		checkNext: function() {
			if (thisref.Loader.canLoad(thisref.Loader.loaderBox))
				thisref.Loader.loadNext();
		},
		check: function() {
			if (thisref.Loader.lazyLoadList.length != 0) {
				for (var i = 0; i<thisref.Loader.lazyLoadList.length; ++i)
					if (thisref.Loader.canLoad(thisref.Loader.lazyLoadList[i][0]) &&
						thisref.Loader.lazyLoadList[i][1]()) {
							thisref.Loader.lazyLoadList.splice(i, 1);
							i--;
						}
			}
			thisref.Loader.checkNext();
			MarkingTool.Editor.Logic.UpdateTaskCounter();
		},
		canLoad: function(element) {
			var cont = element.parent();
			var scrollPos = cont[0].scrollTop;
			var offset = element[0].offsetTop - cont[0].offsetTop;
			var height = cont.outerHeight(true);
			return scrollPos + height >= offset;
		},
		addLazy: function(element, create) {
			thisref.Loader.lazyLoadList.push([element, create]);
		},
		checkLazy: function(element) {
			var ind = 0;
			for (; ind < thisref.Loader.lazyLoadList.length; ++ind)
				if (thisref.Loader.lazyLoadList[ind][0] == element)
					break;
			if (ind >= thisref.Loader.lazyLoadList.length) return true;
			if (!thisref.Loader.canLoad(element)) return false;
			if (thisref.Loader.lazyLoadList[ind][1]())
				thisref.Loader.lazyLoadList.splice(ind, 1);
			else return false;
			return true;
		}
	};
	
	//private Init()
	var _init = function(){
		createCommandBar();
		createLayoutContainer();
		thisref.Loader.createLoaderContainer();
	};
	//Initialisiert die Oberfläche
	this.Init = function() {
		_init.call(thisref); 
	};
};

//Stellt die Programmlogik bereit
MarkingTool.Editor.Logic = new function() {
	var thisref = this;
	var checking = 0;
	var bName = []; //Sortiert nach Name
	var bTask = {}; //Sortiert nach Aufgabennummer
	//erzeugt ein neues überwachtes Objekt aus den Rohdaten der Aufgabe.
	var createTaskObject = function(raw, path) {
		var data = new Helper.UpdateFactory.AddObject(raw, path); //Erzeugt das Objekt über die Verwaltung
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
		if (data.accepted == null) data.accepted = false;
		data.changeTime = Number.MAX_SAFE_INTEGER;
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
				if (MarkingTool.Editor.Settings.RestrictedMode && task.submissionId == null)
					continue; //Performance Optimierung
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
	var filterChangedEvent = new Helper.Event();
	//Dieses Event wird ausgelöst, sobald ApplyFilter() aufgerufen wurde.
	Object.defineProperty(this, "FilterChanged", {get: function(){ return filterChangedEvent; } });
	//Wendet den ausgewählten Filter auf alle angezeigten Boxen an.
	//[filter]: Objekt - Der Filter. (default: this.Filter)
	this.ApplyFilter = function(filter) {
		Helper.UpdateIndicator.ShowBox();
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
		MarkingTool.Editor.View.Loader.check();
		Helper.UpdateIndicator.HideBox();
	};
	//Die Anzahl der noch zu erstellenden Tasks
	this.TaskLeftCounter = 0;
	
	this.UpdateTaskCounter = function() {
		var c = MarkingTool.Editor.View.createFunctions.size();
		c += MarkingTool.Editor.View.Loader.lazyLoadList.length;
		thisref.TaskLeftCounter = c;
		if (c > 50) {
			$(".warning.many-items").removeClass("ui-hide");
		}
		else $(".warning.many-items").addClass("ui-hide");
	};
	//Überprüft ob Tasks nun hochgeladen werden können und führt diesen Upload durch.
	this.CheckForUploadableTasks = function(force) {
		checking++;
		if (checking == 1) {
			//Phase 1 - Suche nach Uploadbarem und packe es zusammen
			var list = [];
			var time = Date.now() - MarkingTool.Editor.Settings.IntervallTime * 60000;
			for (var i = 0; i<Helper.UpdateFactory.UpdateList.length; ++i)
				if (force || Helper.UpdateFactory.UpdateList[i].changeTime <= time) {
					var task = Helper.UpdateFactory.UpdateList[i];
					var changeObj = { 
						data: {
							leaderId: task.leaderId,
							exerciseId: task.id,
							submissionId: task.submissionId,
							markingId: task.markingId
						},
						count: 3
					};
					var props = task.getPropertys();
					var setProp = function(name) {
						if (props[name].isValueChanged()) {
							changeObj.data[name+"_old"] = props[name].getDefaultValue();
							changeObj.data[name+"_new"] = task[name];
							changeObj.count += 2;
						}
					};
					var setPropFile = function(name) {
						if (props[name].isValueChanged()) {
							if (task[name].blob == undefined) return false;
							changeObj.data[name+"_old"] = props[name].getDefaultValue().id;
							changeObj.data[name+"_new_name"] = task[name].file.name;
							changeObj.data[name+"_new_blob"] = task[name].blob;
							changeObj.count += 3;
						}
						return true;
					};
					setProp("points");
					setProp("accepted");
					setProp("status");
					setProp("tutorComment");
					setProp("studentComment");
					if (!setPropFile("userFile")) continue; //Datei wurde noch nicht geladen
					if (!setPropFile("tutorFile")) continue;
					task.setAllValuesAsDefault();
					list.push(changeObj);
				}
			//Phase 2 - Verpacke die kleinen Datenpakete zu großen
			var upl = [], cur = [];
			var left = MarkingTool.Editor.Settings.MaxUploadVariablesCount;
			for (var i = 0; i<list.length; ++i) {
				if (list[i].count > left) {
					upl.push(cur);
					cur = [];
					left = MarkingTool.Editor.Settings.MaxUploadVariablesCount;
				}
				cur.push(JSON.stringify(list[i].data));
				left -= list[i].count;
			}
			if (cur.length > 0) upl.push(cur);
			//Phase 3 - Lade die Änderungen hoch
			for (var i = 0; i<upl.length; ++i) {
				$.post({
					url: "../../../../../api/upload/course/" + MarkingTool.Editor.Settings.Get.cid +
						"/exercisesheet/" + MarkingTool.Editor.Settings.Get.sid,
					cache: false,
					data: { "tasks[]": upl[i] },
					success: function(data) {
						console.log(data);
						//data = JSON.parse(data);
						if (data.success) return;
						if (data.error != "outdatetData") {
							var message = Lang("error","errorHeader")+data.error;
							if (data.hint) message += "<br/>"+Lang("error","hint")+data.hint;
							message += "<br/>"+Lang("error","courseId")+MarkingTool.Editor.Settings.Get.cid;
							message += "<br/>"+Lang("error","seriesId")+MarkingTool.Editor.Settings.Get.sid;
							message += "<br/>"+Lang("error","report");
							var frame = Helper.HTML.CreateWindow(
								Lang("error","duringSubmission"), "small", [
									Helper.HTML.CreateElementRaw({
										content: message
									})
								], function() {
									frame.remove();
								});
							frame.appendTo($(document.body));
						}
						else {
							var full = false;
							var info = MarkingTool.Editor.View.createForkInfo(data.smalStates, function() {
								full = true;
							});
							var btn = info.find(".ui-fork-button.large");
							var serv = btn.filter(".server");
							var locl = btn.filter(".local");
							var frame = Helper.HTML.CreateWindow(
								Lang("fork","newStateOnServer"), "large", [
									Helper.HTML.CreateElementRaw({
										//content: JSON.stringify(data.smalStates)
										content: [Helper.HTML.CreateButtonFrame([
											Helper.HTML.CreateButton(Lang("fork","selectServer"), function() {
												serv.click();
											}),
											Helper.HTML.CreateButton(Lang("fork","selectLocal"), function() {
												locl.click();
											}),
											Helper.HTML.CreateButton(Lang("fork","takeChanges"), function() {
												if (!full) {
													alert(Lang("fork","someLeftMsg"));
													return;
												}
												else {
													var tasks = data.smalStates;
													for (var i = 0; i<tasks.length; ++i) {
														var use = tasks[i].use;
														console.log(tasks[i]);
														var task;
														for (var n = 0; n<bName.length; ++n)
															if (bName[n].user[0].id == tasks[i].task.leaderId) {
																for (var t = 0; t<bName[n].tasks.length; ++t)
																	if (bName[n].tasks[t].task.id == tasks[i].task.exerciseId) {
																		task = bName[n].tasks[t].task;
																		break;
																	}
																break;
															}
														console.log(task);
														if (task == undefined) continue;
														var props = task.getPropertys();
														for (var p in tasks[i].use)
															if (tasks[i].use.hasOwnProperty(p)) {
																if (tasks[i].use[p]) {
																	props[p].setDefaultValue(tasks[i].newData[p]);
																}
																else {
																	props[p].setDefaultValue(tasks[i].newData[p]);
																	props[p].changeValue(tasks[i].newData[p]);
																}
															}
														task.changeTime = 0;
													}
													frame.remove();
													MarkingTool.Editor.Logic.CheckForUploadableTasks(false);
												}
											})
										], [ info ]										
										)]
									})
								], function() {
									frame.remove();
								}
							);
							frame.appendTo($(document.body));
						}
					}
				});
			}
		}
		checking--;
	};
	
	//private Init()
	var _init = function() {
		getAllTasks();
		thisref.bName = bName;
		thisref.bTask = bTask;
		Helper.UpdateFactory.AddedEvent.add(updAddHandler);
		Helper.UpdateFactory.RemovedEvent.add(updRemoveHandler);
		var loop = function() {
			thisref.CheckForUploadableTasks(false);
			setTimeout(loop, 60000); //1 Minute
		};
		setTimeout(loop, 60000);
	};
	//Initialisiert die Logik
	this.Init = function() {
		_init.call(thisref);
	};
};

//Stellt alle Einstellungsfunktionen bereit
MarkingTool.Editor.Settings = new function() {
	var thisref = this;
	//Bool - Bestimmt, ob der Nutzer nur eingeschränkte Rechte hat.
	this.RestrictedMode = false;
	//Int - Bestimmt den Nutzerlevel, der diese Seite betrachtet.
	//0 = Student, 1 = Tutor, 2 = Dozent, 3 = Admin, 4 = Super-Admin
	this.UserLevel = 0;
	//String - Gibt eine Rücksprung-URL an, wo die Serienübersicht ist.
	this.BackUrl = "";
	//Int - Die Zeit in Minuten bis ein Task hochgeladen werden kann.
	this.IntervallTime = 5;
	//Int - die maximale Anzahl an Variablen die per HTTP-POST gesendet werden können.
	this.MaxUploadVariablesCount = 1000;
	//Bool - Gibt an ob alle Boxen standartmäßig geöffnet sind
	this.AutoOpenTaskBoxes = false;
	//Die Variablen, die über HTTP-GET in der URL definiert wurden
	this.Get = {};
	//Der Cookiezugriff
	this.Cookie = new Helper.Cookie();
	
	this.SaveCookies = function() {
		var cookie = {
			IntervallTime: thisref.IntervallTime,
			AutoOpenTaskBoxes: thisref.AutoOpenTaskBoxes
		};
		thisref.Cookie.SetCookie("MarkingTool", cookie, thisref.Cookie.OneDay * 365);
	};
	
	//private Init()
	var _init = function() {
		// var query = window.location.search.substring(1);
		// var vars = query.split('&');
		// for (var i = 0; i<vars.length; ++i) {
			// var pair = vars[i].split('=');
			// this.Get[decodeURIComponent(pair[0])] = decodeURIComponent(pair[1]);
		// }
		// console.log("Get Parameter: "+JSON.stringify(this.Get));
		var cookies = thisref.Cookie.GetCookie("MarkingTool");
		if (cookies) {
			if (cookies.IntervallTime != undefined) 
				thisref.IntervallTime = cookies.IntervallTime;
			if (cookies.AutoOpenTaskBoxes != undefined)
				thisref.AutoOpenTaskBoxes = cookies.AutoOpenTaskBoxes;
		}
	};
	//Initialisiert die Einstellungen
	this.Init = function() {
		_init.call(thisref);
	};
};


$(function() {
	//Diese Funktion wird aufgerufen, wenn die Webseite bereit ist. Nun kann alles geladen 
	//und aufgebaut werden.
	MarkingTool.Editor.Settings.Init();
	MarkingTool.Editor.Logic.UpdateTaskCounter();
	MarkingTool.Editor.View.Init();
	MarkingTool.Editor.Logic.Init();
	MarkingTool.Editor.View.createCompleteTaskView(MarkingTool.Editor.Settings.RestrictedMode);
	MarkingTool.Editor.Logic.ApplyFilter();
	MarkingTool.Editor.Logic.UpdateTaskCounter();
	Helper.UpdateIndicator.HideBox();
});
