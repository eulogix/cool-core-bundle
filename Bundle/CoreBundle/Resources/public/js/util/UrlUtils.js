define("cool/util/UrlUtils", [

], function() {
  
    var obj = {

        addParams: function(query, params){
            var wkQuery = query;
            for(var i in params) {
                var hasQuestionMark = wkQuery.indexOf("?") > -1;
                wkQuery = wkQuery + (hasQuestionMark ? "&" : "?") + i + "=" + params[i];
            }
            return wkQuery;
        }
        
    };

    return obj;
  
});
    