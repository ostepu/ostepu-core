//ready functions always lower case
function loop_ready() {
    // please unbind event before binding a new one because of jquery bug
    $('.add-test').unbind('click').on('click',createTestcases);
    $('select.parameter-count').unbind('change').on('change',updateCols);
    $('a.update-test').unbind('click').on('click',updateRows);
    $('input.testcount').unbind('keydown').on('keydown',keyenterevent);
    $('a.delete-test').unbind('click').on('click',deleteTestcases);

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

function createTestcases(event) {

    var trig = $(this);

    $.get("include/CreateSheet/Processor/LOOPparamcount.template.php", function (data) {

        trig.closest("tr").before(data);
        trig.closest("table").find(".parameter-count").hide().fadeIn('fast');
        
        //trig.closest("table").find("select.parameter-count").on("change",updateCols);
        //trig.closest("table").find(".parameter-count").last().closest("tr").find('label').last().hide().fadeIn('fast');

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

                renameTestcases();
                //trig.closest("table").find(".testcase-table").hide().fadeIn('fast');
            });
        });

          

        // animate new element
        /*trig.parent().find('.processor').last().hide().fadeIn('fast');
        
                
        trig.parent().find('.processor').last().children('.content-header').on("click",collapseProcessor);     
        trig.parent().find('.processor').last().children('.content-header').css('cursor','pointer');   
        trig.parent().find('.processor').last().children('.content-header').find('.delete-processor').on("click",deleteProcessor);
        
        trig.parent().find('.processor-type').last().on("change",loadProcessorTemplate);
        trig.parent().find('.processor-type').last().change();
       
        renameProcessor();*/
    });
    
}

function deleteTestcases(event) {
    var trig = $(this);
    var testcases = trig.closest("table").find(".testcase-table");
    var parametercount = trig.closest("table").find("select.parameter-count").closest("tr");
    var testcount = trig.closest("table").find("input.testcount").closest("tr");
    var addtest = trig.closest("table").find("a.add-test");

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

    var trig = $(this);
    var table = trig.closest("table").find(".testcase-table");
    var testcases = table.find("tr").slice(1);

    for (var i = 0; i < testcases.length; i++) {
        var elem = $(testcases[i]);
        var tds = elem.find("td");
        inputtds = tds.slice(0,tds.length-1);

        // check if adding parameters or deleting it
        if (trig.val() > inputtds.length)
        {
            //adding parameters
            var newparamscount = trig.val() - inputtds.length;
            for (var j = 0; j < newparamscount; j++) {
                tds.last().before(inputtds.last().outerHTML());
            }
        }
        else if (trig.val() < inputtds.length)
        {
            //deleting parameters
            var newparamscount = inputtds.length - trig.val();
            inputtds.slice(-newparamscount).remove();
        }
    }

    table.find("td").first().attr('colspan', trig.val());

    renameTestcases();
}

function updateRows(event) {
    var trig = $(this);
    var table = trig.closest("table").find(".testcase-table");
    var testcases = table.find("tr").slice(2);

    var newTestcaseCount = trig.parent().find(".testcount").val();

    if (isNumber(newTestcaseCount))
    {
        // check for 0 oder negative values
        if (newTestcaseCount <= 0)
        {
            newTestcaseCount = 1;
            trig.parent().find(".testcount").val(newTestcaseCount);
        }
        if (newTestcaseCount > testcases.length)
        {
            //adding parameters
            var addedTestcasesCount = newTestcaseCount - testcases.length;
            for (var i = 0; i < addedTestcasesCount; i++) {
                table.append(testcases.last().outerHTML());
            }
        }
        else if (newTestcaseCount < testcases.length)
        {
            //deleting parameters
            var removedTestcasesCount = testcases.length - newTestcaseCount;
            testcases.slice(-removedTestcasesCount).remove();
        }
    }

    renameTestcases();
}

function renameTestcases() {
    var all = $('.processor-type');

    for (var i = 0; i < all.length; i++) {
        // add new select names for datatypes
        var elem = $(all[i]).closest('.content-body-wrapper').find('.input-parameter-choice').children(".parameter-choice-test");
        var oldName = elem.attr('name');

        var elem2 = $(all[i]).closest('.content-body-wrapper').find('.output-parameter-choice').children(".parameter-choice-test");
        var oldName2 = elem2.attr('name');
        
        if (typeof oldName !== 'undefined')
        {
            var regex = /exercises\[(.+?)]\[.+?\]\[(.+?)]\[(.+?)]\[[0-9]+\]\[]/gm;
            var regex2 = /exercises\[(.+?)]\[.+?\]\[(.+?)]\[(.+?)]\[[0-9]+\]/gm;

            var nameString = "exercises[$1][subexercises][$2][inputDatatype]["+ (i) +"][]";
            var nameString2 = "exercises[$1][subexercises][$2][outputDatatype]["+ (i) +"]";

            // match the regex and replace the numbers
            var newName = oldName.replace(regex, nameString);
            var newName2 = oldName2.replace(regex2, nameString2);

            // set the new name
            elem.attr('name', newName);
            elem2.attr('name', newName2);      
        }

        //get testcaserows
        var testcases = $(all[i]).closest('.content-body-wrapper').find(".testcase-table").find("tr").slice(2);

        // add new names for testcase input/output parameters
        for (var j = 0; j < testcases.length; j++) {
            var elem3 = $(testcases[j]).find('.input-parameter').children(".parameter-choice-test");
            var oldName3 = elem3.attr('name');
            var elem4 = $(testcases[j]).find('.output-parameter').children(".parameter-choice-test");
            var oldName4 = elem4.attr('name');

            if (typeof oldName3 !== 'undefined')
            {
                var regex3 = /exercises\[(.+?)]\[.+?\]\[(.+?)]\[(.+?)]\[[0-9]+\]\[[0-9]+\]\[]/gm;
                var nameString3 = "exercises[$1][subexercises][$2][inputParameter]["+ (i) +"]["+ (j) +"][]";
                var newName3 = oldName3.replace(regex3, nameString3);
                elem3.attr('name', newName3);

                var regex4 = /exercises\[(.+?)]\[.+?\]\[(.+?)]\[(.+?)]\[[0-9]+\]\[[0-9]+\]/gm;
                var nameString4 = "exercises[$1][subexercises][$2][ouputParameter]["+ (i) +"]["+ (j) +"]";
                var newName4 = oldName4.replace(regex4, nameString4);
                elem4.attr('name', newName4);       
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