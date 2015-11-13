//ready functions always lower case
function loop_ready() {
    // please unbind event before binding a new one because of jquery bug
    $('.add-test').unbind('click').on('click',createTestcases);
    $('select.parameter-count').unbind('change').on('change',updateCols);
    $('a.update-test').unbind('click').on('click',updateRows);
    $('input.testcount').unbind('keydown').on('keydown',keyenterevent);
    $('a.delete-test').unbind('click').on('click',deleteTestcases);
    $('td.input-parameter-choice').find('select').unbind('change').on('change',switchInputTypes);
    $('td.output-parameter-choice').find('select').unbind('change').on('change',switchInputTypes);
    $('a.deleteRow').unbind('click').on('click',deleteRow);
    $('a.deleteCol').unbind('click').on('click',deleteCol);

    renameTestcases();
    //console.log($('.add-test'));
}

jQuery.fn.outerHTML = function(s) {
    return (s)
      ? this.before(s).remove()
      : jQuery("<p>").append(this.eq(0).clone()).html();
}

function isNumber (o) {
  return ! isNaN (o-0) && o !== null && o !== "" && o !== false;
}

function range(start, count) {
    if(arguments.length == 1) {
        count = start;
        start = 0;
    }

    var foo = [];
    for (var i = 0; i < count; i++) {
        foo.push(start + i);
    }
    return foo;
}

function deleteRow(event) {
    var trig = $(this);
    var testcase_table = trig.closest("table.testcase-table");
    var testcases = testcase_table.find('tr').slice(2).length;

    if(testcases > 1)
    {
        var newtrig = $(this).closest("table").children().first();

        trig.closest("tr").fadeOut("fast", function(){ 
            $(this).remove();

            var testcountInput = testcase_table.parent().closest("table").find(".testcount");
            var newValue = testcases - 1;
            testcountInput.val(newValue.toString());

            renameTestcases();
            checkIfUnused(newtrig);
        });;
    }
    else
    {
        testcase_table.parent().find("a.delete-test").trigger("click");
    }
}

function deleteCol(event) {
    var trig = $(this);
    var newtrig = $(this).closest("table").children().first();

    var testcase_table = trig.closest("table.testcase-table");
    var testcases = testcase_table.find('tr').slice(1);

    var j = trig.closest("tr").children().index(trig.closest("td"));

    var colcount = $(testcases[0]).children().length - 2;

    if (colcount > 1)
    {
        var parameterCountInput = testcase_table.parent().closest("table").find(".parameter-count");
        parameterCountInput.val(colcount - 1);

        var elements;

        // add tds to elements for one single remove command
        for (var i = 0; i < testcases.length; i++) {
            elements = $(elements).add($(testcases[i]).find('td').eq(j));
        }

        elements.fadeOut("fast", function(){ 
            $(this).remove();
            testcase_table.find("td").eq(1).attr('colspan', colcount - 1);
            checkIfUnused(newtrig);
        });;
    }
    else
    {
        testcase_table.parent().find("a.delete-test").trigger("click");
    }
}

function createTestcases(event) {

    var trig = $(this);

    $.get("include/CreateSheet/Processor/LOOPparamcount.template.php", function (data) {

        trig.closest("tr").before(data);
        trig.closest("table").find(".parameter-count").hide().fadeIn('fast');

        $.get("include/CreateSheet/Processor/LOOPtestcount.template.php", function (data2) {

            trig.closest("tr").before(data2);
            trig.closest("table").find(".testcount").hide().fadeIn('fast');

            

            $.get("include/CreateSheet/Processor/LOOPtable.template.php", function (data3) {
                trig.before(data3);
                trig.closest("table").find(".testcase-table").hide().fadeIn('fast');
                trig.closest("table").find("a.delete-test").hide().fadeIn('fast');

                trig.hide();
                $('select.parameter-count').on("change",updateCols);
                $('a.update-test').on("click",updateRows);
                $('input.testcount').unbind('keydown').on('keydown',keyenterevent);
                $('a.delete-test').on("click",deleteTestcases);
                $('td.input-parameter-choice').find('select').unbind('change').on('change',switchInputTypes);
                $('td.output-parameter-choice').find('select').unbind('change').on('change',switchInputTypes);
                $('a.deleteRow').unbind('click').on('click',deleteRow);
                $('a.deleteCol').unbind('click').on('click',deleteCol);

                renameProcessor();
                renameTestcases();

                //trig.closest("table").find(".testcase-table").hide().fadeIn('fast');
            });
        });
    });
}

function switchInputTypes(event) {
    var trig = $(this);

    // get col number of the select input
    var i = trig.closest("tr").children().index(trig.closest("td"));

    //console.log(i);
    
    //get all testcases
    var testcases = trig.closest("table.testcase-table").find('tr').slice(2);

    if (trig.val() == "Data") 
    {
        var filepath = "";

        var spalte = trig.closest("td");

        if(spalte.hasClass("input-parameter-choice"))
        {
            filepath = "include/CreateSheet/Processor/LOOPfileinput.template.php";
        }
        else
        {
            filepath = "include/CreateSheet/Processor/LOOPfileoutput.template.php";
        }

        $.get(filepath, function (fileinputdata) {
            for (var j = 0; j < testcases.length; j++) {
                var elem = $(testcases[j]);
                
                //var inputdata = $(fileinputdata);
                elem.find('td').eq(i).html(increaseRowname(fileinputdata, j, 0));
    
                elem.find('td').eq(i).find('.fileButton').unbind('change').on('change',selectFile);
                elem.find('td').eq(i).find('select').unbind('change').on('change',changeFile);
            }
            /*if (Files.length > 0 && empty.length == 0)
            {
                uptodate = Files.find("select").first();
            }*/
            var rowCount = trig.closest("div.content-body-wrapper").find("input.testcount").val();

            // all rows
            var rowRange = range(0 , rowCount);
            updateDropdowns(trig, false, rowRange, [i], false, rowRange, []);
            checkIfUnused(trig);  
        });
    }
    else
    {

        var spalte = trig.closest("td");

        if(spalte.hasClass("input-parameter-choice"))
        {
            filepath = "include/CreateSheet/Processor/LOOPinput.template.php";
        }
        else
        {
            filepath = "include/CreateSheet/Processor/LOOPoutput.template.php";
        }

        $.get(filepath, function (inputdata) {
            for (var j = 0; j < testcases.length; j++) {
                var elem = $(testcases[j]);
                elem.find('td').eq(i).html(increaseRowname(inputdata, j, 0));
            }
            checkIfUnused(trig); 
        });
    }
}

function changeFile(event) {
    var trig = $(this);
    var FileArea = trig.closest('.fileArea');

    if (trig.val() == "Add")
    {
        FileArea.find('.fileButton').prop('disabled', false).show();
        FileArea.find('.fileButton').unbind('change').on('change',selectFile);

        FileArea.find('select').hide();
        FileArea.find("input[type='checkbox']").prop('disabled', true).hide();
        FileArea.find('a').hide();
    }
    else
    {
        // get col and row numbers of the select input
        var closestr = trig.closest('tr');
        var col = closestr.children().index(trig.closest('td'));
        var row = trig.closest('tbody').children().index(closestr) - 2;

        // update the dropdowns
        var rowCount = trig.closest("div.content-body-wrapper").find("input.testcount").val();

        // all rows under the select in col
        var selectedRowRange = range(row , rowCount - row);

        updateDropdowns(trig, false, [], [], true, selectedRowRange, [col]);

        checkIfUnused(trig);
    }
}

function getFileLastId(trigger) {
    var lastFile = trigger.closest("div.content-body-wrapper").find('.hiddenFiles').find('.hiddenFile').last();

    if (lastFile.length == 0)
    {
        return -1;
    }

    var name = lastFile.prop("name");
    var regex = /Parameter\]\[([0-9]+)\]\[([0-9]+)\]/gm;
    var newname = name.match(regex);
    var replaceString = "$2";

    if (newname === null)
    {
        return -1;
    }

    var lastId = newname[0].replace(regex, replaceString);

    return parseInt(lastId);
}

function selectFile(event) {
    var trig = $(this);

    // get col and row numbers of the select input
    var closestr = trig.closest('tr');
    var col = closestr.children().index(trig.closest('td'));
    var row = trig.closest('tbody').children().index(closestr) - 2;

    //console.log(col);
    //console.log(row);

    // get filename
    var filepath = trig.val();
    var filename = filepath.replace(/^.*?([^\\\/]*)$/, '$1');

    // add new option
    var lastoption = trig.parent().find("select option[value='Add']");

    var fileLastId = getFileLastId(trig) + 1;

    lastoption.before('<option value="' + fileLastId.toString() + '">' + filename + '</option>');

    //lastoption.prop('disabled', true);
    trig.parent().find("select").val(fileLastId.toString());
    //console.log(lastoption);

    // save selected file in hiddenFile
    trig.before(trig.clone().val(""));

    var newtrigger = trig.parent();

    trig.addClass('hiddenFile').css({"visibility":"hidden"});
    
    // add new name
    var oldName = trig.prop('name');
    var regex = /Parameter\]\[([0-9]+)\]\[[0-9]+\]\[\]/gm;
    var nameString = "Parameter][$1]["+ (fileLastId.toString()) +"]";

    var newName = oldName.replace(regex, nameString);

    trig.prop('name', newName);


    trig.closest("div.content-body-wrapper").find("div.hiddenFiles").append(trig);

    // update the dropdowns
    var rowCount = trig.closest("div.content-body-wrapper").find("input.testcount").val();
    //var colCount = trig.closest("div.content-body-wrapper").find("select.parameter-count").val();

    // all rows
    var rowRange = range(0 , rowCount);

    var selectedRowRange = range(row , rowCount - row);

    // all cols with Datafield Data
    var colRange = [];

    newtrigger.closest('.testcase-table').find("td.input-parameter-choice, td.output-parameter-choice").each(function() {
        var currSelectValue = $(this).find('select').val();
        var tr = $(this).parent();

        if (currSelectValue == "Data")
        {
            var datafieldPos = tr.children().index($(this));
            if (datafieldPos != -1)
            {
                colRange.push(datafieldPos);
            }
        }
    });

    updateDropdowns(newtrigger, false, rowRange, colRange, false, selectedRowRange, [col]);

    checkIfUnused(newtrigger);
    //getFileLastId(trig);
}

function checkIfUnused(trigger)
{
    var table = trigger.closest("table.testcase-table");
    var testcases = trigger.closest("table.testcase-table").find('tr').slice(2);

    var files = trigger.closest("div.content-body-wrapper").find('.hiddenFiles').find('.hiddenFile');

    //console.log(files);

    files.each(function() {

        // get Id
        var name = $(this).prop("name");
        var regex = /Parameter\]\[([0-9]+)\]\[([0-9]+)\]/gm;
        var newname = name.match(regex);
        var replaceString = "$2";
        var Id = newname[0].replace(regex, replaceString);

        var SelectorForOption = "select option:selected[value=" + Id + "]";
        var selectedOptions = testcases.find(SelectorForOption);


        if (selectedOptions.length == 0) {
            var SelectorForOption = "select option[value=" + Id + "]";
            var Options = testcases.find(SelectorForOption);

            Options.remove();
            $(this).remove();
        }
    });
}

function updateDropdowns(trigger, isdelete, row, col, onlySelect, selectRow, selectCol) {
    //var filesCount = trigger.closest("div.content-body-wrapper").find('.hiddenFiles').find('.hiddenFile').length;

    var hasTypeDataEnabled = trigger.closest("table.testcase-table").find("select option:selected[value=Data]").length;

    var table = trigger.closest("table.testcase-table");
    var testcases = trigger.closest("table.testcase-table").find('tr').slice(2).detach();
    var testcasesCheckboxes = testcases.find("input[type='checkbox']:checked");

    var testcasesAreChecked = testcasesCheckboxes.length;

    // values for Dropdown with most entries: pointer, selected value, optioncount
    var maxDropdown;
    var currentValue;
    var max = 0;

    var updateSelectedValues = false;

    if (onlySelect == false)
    {
        
        for (var i = 0; i < row.length; i++)
        {
            for (var j = 0; j < col.length; j++)
            {
                var elem = $(testcases[row[i]]).find('td').eq(col[j]);

                // if no files available 
                /*if (filesCount == 0)
                {
                    // if no visible fileinput button
                    if (testcases.find('input.fileButton:enabled').length == 0)
                    {
                        elem.find('.fileButton').prop('disabled', false).show();
                        elem.find('.fileButton').unbind('change').on('change',selectFile);

                        elem.find('select').hide();
                        elem.find("input[type='checkbox']").prop('disabled', true).hide();
                        elem.find('a').hide();
                    }
                    else
                    {
                        elem.find('.fileButton').prop('disabled', true).hide();
                        elem.find('select').val("").show();
                        elem.find("input[type='checkbox']").prop('disabled', false).show();
                        elem.find('a').hide();
                    }
                }*/
                if (hasTypeDataEnabled > 0)
                {
                    // get uptodate dropdown with most entries only once
                    if (maxDropdown === undefined)
                    {

                        testcases.find('select').each(function() {
                            var currSelectValue = $(this).children().length;

                            if (currSelectValue > max)
                            {
                                maxDropdown = $(this);
                                currentValue = $(this).val();
                                max = currSelectValue;
                            }
                        });
                    }

                    var clone = maxDropdown.outerHTML();
                    var jclone = $(clone);
                    jclone.find("option[value='Add']").prop('disabled', false).show();

                    elem.find('.fileButton').prop('disabled', true).hide();

                    var oldselect = elem.find('select');
                    var oldvalue = oldselect.val();
                    oldselect.before(jclone.outerHTML());

                    var oldname = oldselect.prop("name");

                    oldselect.remove();

                    var newselect = elem.find('select');
                    newselect.show();
                    newselect.prop("name",oldname);

                    var checkbox = elem.find("input[type='checkbox']");

                    // select current Value
                    var isChecked = elem.find("input[type='checkbox']").prop('checked');
                    if (testcasesAreChecked > 0 && isChecked == true || testcasesAreChecked == 0 && (selectRow.indexOf(row[i]) != -1 && selectCol.indexOf(col[j]) != -1 || oldvalue == "Add"))
                    {
                        newselect.val(currentValue);
                        //console.log(currentValue);

                        if (testcasesAreChecked > 0 && isChecked == true)
                        {
                            checkbox.prop('checked', false);
                        }
                    }
                    else
                    {
                        newselect.val(oldvalue);
                    }

                    newselect.unbind('change').on('change',changeFile);

                    checkbox.prop('disabled', false).show();

                    elem.find('a').show();
                }
            }
        }
    }
    else
    {
        var newvalue = trigger.val();
        if (testcasesAreChecked > 0)
        {
            testcasesCheckboxes.each(function() {
                $(this).parent().find("select").val(newvalue);
                $(this).prop('checked', false);
            });
        }
        else
        {
            for (var i = 0; i < selectRow.length; i++)
            {
                for (var j = 0; j < selectCol.length; j++)
                {
                    var elem = $(testcases[selectRow[i]]).find('td').eq(selectCol[j]);

                    elem.find("select").val(newvalue);
                }
            }
        }
    }
    table.append(testcases);
}

function deleteTestcases(event) {
    var trig = $(this);
    var testcases = trig.closest("table").find(".testcase-table");
    var hiddenFiles = trig.closest("table").find(".hiddenFiles");
    var parametercount = trig.closest("table").find("select.parameter-count").closest("tr");
    var testcount = trig.closest("table").find("input.testcount").closest("tr");
    var addtest = trig.closest("table").find("a.add-test");

    hiddenFiles.remove();

    testcases.fadeOut('fast', function() {
        testcases.remove();
    });

    trig.fadeOut('fast', function() {
        trig.remove();
    });

    parametercount.fadeOut('fast', function() {
        parametercount.remove();
    });

    testcount.fadeOut('fast', function() {
        testcount.remove();
        addtest.fadeIn();
    });


}

function updateCols(event) {

    var addedrows = [];
    var addedcols = [];

    var colsAreRemoved = false;

    var trig = $(this);
    var table = trig.closest("table").find(".testcase-table");
    var testcases = table.find("tr").slice(1).detach();

    var testcaseLength = testcases.length;

    for (var i = 0; i < testcaseLength; i++) {
        var elem = $(testcases[i]);
        var tds = elem.find("td");
        inputtds = tds.slice(1,tds.length-1);

        //add row in rows array
        if (addedrows.length < testcaseLength - 1)
        {
            addedrows.push(i);
        }

        // check if adding parameters or deleting it
        if (trig.val() > inputtds.length)
        {
            //adding parameters
            var newparamscount = trig.val() - inputtds.length;

            var inputhtml = inputtds.last().outerHTML();

            var newselectedvalue = inputtds.last().find('select').val();

            for (var j = 0; j < newparamscount; j++) {
                tds.last().before(inputhtml);

                // apply old selected values to new inputs
                var newtds = elem.find("td");
                var newinputtd = newtds.slice(0,newtds.length-1).last();

                // apply InputType to new Cols
                if (i == 0) {
                    newinputtd.find('select').val(newselectedvalue);
                }
                else
                {
                    // reset input Forms
                    newinputtd.find('input.parameter-choice-test').val("");
                }
                

                //add col in cols array
                if (addedcols.length < newparamscount)
                {
                    addedcols.push(inputtds.length + j);
                }
            }
        }
        else if (trig.val() < inputtds.length)
        {
            //deleting parameters
            var newparamscount = inputtds.length - trig.val();
            inputtds.slice(-newparamscount).remove();

            if (colsAreRemoved == false)
            {
                colsAreRemoved = true;
            }
        }
    }

    table.append(testcases);

    if (colsAreRemoved == false)
    {
        // have to be inside testcase-table
        var dummytrigger = table.children().first();

        updateDropdowns(dummytrigger, false, addedrows, addedcols, false, [], []);
    }
    else
    {
        checkIfUnused(table.children().first());
    }

    table.find("td").eq(1).attr('colspan', trig.val());
    $('td.input-parameter-choice').find('select').unbind('change').on('change',switchInputTypes);
    $('td.input-parameter').find('select').unbind('change').on('change',changeFile);
    $('a.deleteCol').unbind('click').on('click',deleteCol);
}

function updateRows(event) {
    var trig = $(this);
    var trigParent = trig.parent();
    var table = trig.closest("table").find(".testcase-table");
    var testcases = table.find("tr").slice(2);
    var testcasesLength = testcases.length;

    var addedrows = [];
    var addedcols = [];

    var newTestcaseCount = trigParent.find(".testcount").val();

    if (isNumber(newTestcaseCount))
    {
        // check for 0 oder negative values
        if (newTestcaseCount <= 0)
        {
            newTestcaseCount = 1;
            trigParent.find(".testcount").val(newTestcaseCount);
        }
        if (newTestcaseCount > testcasesLength)
        {
            //adding parameters
            var addedTestcasesCount = newTestcaseCount - testcasesLength;
            var newTestcasesHTML = "";

            var lastTestcase = testcases.last().clone();
            // resetting value
            lastTestcase.find('input.parameter-choice-test').attr("value", "");

            var lastTestcaseHTML = lastTestcase.outerHTML();

            for (var i = 0; i < addedTestcasesCount; i++) {

                newTestcasesHTML += increaseRowname(lastTestcaseHTML, i, testcasesLength);

                //save added rowsindex
                addedrows.push(testcasesLength + i);
            }

            table.append(newTestcasesHTML);

            // save cols with Type Data
            table.find("td.input-parameter-choice").each(function() {
                var currSelectValue = $(this).find('select').val();
                var tr = $(this).parent();

                if (currSelectValue == "Data")
                {
                    var datafieldPos = tr.children().index($(this));
                    if (datafieldPos != -1)
                    {
                        addedcols.push(datafieldPos);
                    }
                }
            });

            $('td.input-parameter').find('select').unbind('change').on('change',changeFile);
            $('td.output-parameter').find('select').unbind('change').on('change',changeFile);
            $('a.deleteRow').unbind('click').on('click',deleteRow);

            // have to be inside testcase-table
            var dummytrigger = table.children().first();

            updateDropdowns(dummytrigger, false, addedrows, addedcols, false, [], []);

        }
        else if (newTestcaseCount < testcasesLength)
        {
            //deleting parameters
            var removedTestcasesCount = testcasesLength - newTestcaseCount;
            testcases.slice(-removedTestcasesCount).remove();

            checkIfUnused(table.children().first());
        }
    }
}

function increaseRowname(html, i, n) {
    var regex = /Parameter\]\[([0-9]+)\]\[[0-9]+\]/gm;
    var newrow = n + i;
    var nameString = "Parameter][$1]["+ (newrow) +"]";

    var newNameString = html.replace(regex, nameString);

    return newNameString;
}

function renameTestcases() {
    var all = $('.processor-type');

    for (var i = 0; i < all.length; i++) {
        // add new select names for datatypes
        var regex = /exercises\[(.+?)]\[.+?\]\[(.+?)]\[(.+?)]\[[0-9]+\]\[]/gm;
        var regex2 = /exercises\[(.+?)]\[.+?\]\[(.+?)]\[(.+?)]\[[0-9]+\]/gm;

        var elem = $(all[i]).closest('.content-body-wrapper').find('.input-parameter-choice').find(".parameter-choice-test");

        if(elem.length > 0)
        {
            elem.each(function() {
                var oldName = $(this).prop('name');
                var nameString = "exercises[$1][subexercises][$2][inputDatatype]["+ (i) +"][]";
                var newName = oldName.replace(regex, nameString);

                $(this).prop('name', newName);
            });
        }

        var elem2 = $(all[i]).closest('.content-body-wrapper').find('.output-parameter-choice').find(".parameter-choice-test");

        if(elem2.length > 0)
        {
            elem2.each(function() {
                var oldName = $(this).prop('name');
                var nameString = "exercises[$1][subexercises][$2][outputDatatype]["+ (i) +"]";
                var newName = oldName.replace(regex2, nameString);

                $(this).prop('name', newName);
            });
        }

        //get testcaserows
        var testcases = $(all[i]).closest('.content-body-wrapper').find(".testcase-table").find("tr").slice(2);

        // add new names for testcase input/output parameters
        for (var j = 0; j < testcases.length; j++) {
            var elem3 = $(testcases[j]).find('.input-parameter').find(".parameter-choice-test");
            
            var elem4 = $(testcases[j]).find('.output-parameter').find(".parameter-choice-test");
            

            if (elem3.length > 0 && elem4.length > 0)
            {
                var regex3 = /exercises\[(.+?)]\[.+?\]\[(.+?)]\[(.+?)]\[[0-9]+\]\[[0-9]+\]\[]/gm;
                var nameString3 = "exercises[$1][subexercises][$2][$3]["+ (i) +"]["+ (j) +"][]";
                

                elem3.each(function() {
                    var oldName3 = $(this).prop('name');
                    var newName3 = oldName3.replace(regex3, nameString3);

                    $(this).prop('name', newName3);
                });

                var regex4 = /exercises\[(.+?)]\[.+?\]\[(.+?)]\[(.+?)]\[[0-9]+\]\[[0-9]+\]/gm;
                var nameString4 = "exercises[$1][subexercises][$2][$3]["+ (i) +"]["+ (j) +"]";
                

                elem4.each(function() {
                    var oldName4 = $(this).prop('name');
                    var newName4 = oldName4.replace(regex4, nameString4);

                    $(this).prop('name', newName4);
                });
       
            }
        }
    }
}

function keyenterevent(event) {
    var trig = $(this);

    if (event.keyCode == 10 || event.keyCode == 13) 
    {
        event.preventDefault();
        updateRows.call(trig, event);
        return false;
    }
}