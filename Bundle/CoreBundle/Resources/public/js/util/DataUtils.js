define("cool/util/DataUtils", [
], function() {
  
    var obj = {

        /**
         * returns a simple id, value array from an object's properties
         * @param obj
         * @returns {Array}
         */
        kvFromObj: function(obj) {
            var ret = [];
            var i = 0;
            for(var key in obj)
                (function(name, value) {
                    ret.push({id: name, value: value});
                })(key, obj[key]);
            return ret;
        }

    };

    return obj;
  
});
    