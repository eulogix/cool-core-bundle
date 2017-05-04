define("cool/functions/waiter", [], function() {
  
    var obj = {

        waiter: function(startFunction, waitUntilFunction, thenFunction) {
            startFunction();
            if(waitUntilFunction())
                thenFunction();
            else {
                var maxCount = 100;
                var count = 0;
                var interval = setInterval(function() {
                    if(waitUntilFunction() || count>maxCount) {
                        clearInterval(interval);
                        thenFunction();
                    }
                }, 100);
            }
        }
        
    };

    return obj;
  
});
    