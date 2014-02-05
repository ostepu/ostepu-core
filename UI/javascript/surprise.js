$(function () {
    var combination = '';

    key('left', function(){
        combination += 'left';
        checkCombination();
    });

    key('right', function(){
        combination += 'right';
        checkCombination();
    });

    key('up', function(){
        combination += 'up';
        checkCombination();
    });

    key('down', function(){
        combination += 'down';
        checkCombination();
    });

    key('b', function(){
        combination += 'B';
        checkCombination();
    });

    key('a', function(){
        combination += 'A';
        checkCombination();
    });

    key(!'down' && !'left' && !'right' && !'up' && !'B' && !'A',function() {
        combination = '';
    });

    function checkCombination() {
        console.log(combination);
        if (combination === 'upupdowndownleftrightleftrightBA') {
            $.ajax({
                url: "http://api.icndb.com/jokes/random"
            }).then(function(data) {
                console.log(data);
                //alert(data.value.joke);
                var newElement = document.createElement("img");
                newElement.src = "Images/RaptorOnABike.jpg";
                document.body.insertBefore(newElement, document.body.firstChild);
                console.log(newElement);
            });
            combination = '';
        }
    }
});