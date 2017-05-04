define("cool/rundeck",
	[
        "dojo/_base/lang",
        "cool/cool",
        "cool/dialog/manager"
], function(lang, cool, dialogManager) {
 
    var rundeck = {
        logExecution: function(executionId, onProgress, onFinish) {

            var self = this;

            if(!lang.isFunction(onProgress))
                onProgress = function(){};

            if(!lang.isFunction(onFinish))
                onFinish = function(){};

            var pendingRequest = false;

            var handle = setInterval(function(){

                if(!pendingRequest) {
                    pendingRequest = true;

                    cool.callCommand('RDKgetExecution', function(data) {

                        for(var serverExecutionId in data) {
                            var exec = data[serverExecutionId];
                            var progress = exec.percentCompletedOnOutputAnalysis || 0;

                            onProgress(progress);

                            if(exec['date-ended']) {
                                clearInterval(handle);
                                if(lang.exists('exec.failedNodes')) {
                                    dialogManager.showXhrError("Error in rundeck job execution", '', exec['tail']);
                                } else onFinish(exec);
                            }
                        }

                        pendingRequest = false;
                    }, {executionId: executionId});
                }

            }, 1000);

        }
    };

    return rundeck;

});