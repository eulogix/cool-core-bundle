define("cool/store/xhrstore",
	["dojo/_base/xhr",
		"dojo/_base/lang",
        "dojo/Deferred",
		"dojo/json",
		"dojo/_base/declare",
		"dojo/store/util/QueryResults",
		"cool/cool",

		"cool/store/errors/DsError"
	/*=====, "./api/Store" =====*/
	], 

function(xhr, lang, Deferred, JSON, declare, QueryResults, cool, DsError /*=====, Store =====*/){

// No base class, but for purposes of documentation, the base class is dojo/store/api/Store
var base = null;
/*===== base = Store; =====*/

/*=====
var __HeaderOptions = {
		// headers: Object?
		//		Additional headers to send along with the request.
	},
	__PutDirectives = declare(Store.PutDirectives, __HeaderOptions),
	__QueryOptions = declare(Store.QueryOptions, __HeaderOptions);
=====*/

return declare("cool.store.xhrstore", base, {
	// summary:
	//		This is a basic store for RESTful communicating with a server through JSON
	//		formatted data. It implements dojo/store/api/Store.

	constructor: function(options){
		// summary:
		//		This is a basic store for RESTful communicating with a server through JSON
		//		formatted data.
		// options: dojo/store/JsonRest
		//		This provides any configuration information that will be mixed into the store
		this.headers = {};
		this.postVars = {};
        this.lastRequest = {};

		lang.mixin(xhr.contentHandlers, {

			DsJsonHandler: function(response){
				var ret = dojo.contentHandlers.json(response);
				if(ret.hasOwnProperty('_success') && ret._success === false)
					throw new DsError('Ds transaction failed', ret);
				return ret;
			}

		});

		declare.safeMixin(this, options);
	},

    lastRequest: {},

	// headers: Object
	//		Additional headers to pass in all requests to the server. These can be overridden
	//		by passing additional headers to calls to the store.
	headers: {},

	//same as above, but mixes POST variables to the requests
	postVars: {},

	// target: String
	//		The target base URL to use for all requests to the server. This string will be
	//		prepended to the id to generate the URL (relative or absolute) for requests
	//		sent to the server
	target: "",

	// idProperty: String
	//		Indicates the property to use as the identity property. The values of this
	//		property should be unique.
	idProperty: "id",

	// sortParam: String
	//		The query parameter to used for holding sort information.
    sortParam: "_sort",

	// ascendingPrefix: String
	//		The prefix to apply to sort attribute names that are ascending
	ascendingPrefix: "A",

	// descendingPrefix: String
	//		The prefix to apply to sort attribute names that are ascending
	descendingPrefix: "D",

	//used to bypass queries for empty fields
    nilToken : '[{NIL}]',

    lastErrors : {},

    lastStatus : true,

    getLastRequest: function() {
        return this.lastRequest;
    },

	get: function(id, options){
		// summary:
		//		Retrieves an object by its identity. This will trigger a GET request to the server using
		//		the url `this.target + id`.
		// id: Number
		//		The identity to use to lookup the object
		// options: Object?
		//		HTTP headers. For consistency with other methods, if a `headers` key exists on this object, it will be
		//		used to provide HTTP headers instead.
		// returns: Object
		//		The object in the store that matches the given id.
		if(id === this.nilToken) {
			var d = new Deferred();
			d.resolve({label:'-', value:this.nilToken});
			return d;
		} else {
            var query = {};
            query[this.idProperty] = id;
            return this.query(query, options);
		}
	},

	// accepts: String
	//		Defines the Accept header to use on HTTP requests
	accepts: "application/javascript, application/json",

	getIdentity: function(object){
		// summary:
		//		Returns an object's identity
		// object: Object
		//		The object to get the identity from
		// returns: Number
		return object[this.idProperty];
	},

    /**
     * hook that gets called on the data received from the server after a successful PUT operation
     */
    _onPutSuccess: function(data) {

    },

	put: function(object, options){
		// summary:
		//		Stores an object. This will trigger a PUT request to the server
		//		if the object has an id, otherwise it will trigger a POST request.
		// object: Object
		//		The object to store.
		// options: __PutDirectives?
		//		Additional metadata for storing the data.  Includes an "id"
		//		property if a specific id is to be used.
		// returns: dojo/_base/Deferred
		options = options || {};

		var url = this.addParams(this.target, {_storeOp:'put'});

		var headers = lang.mixin({ Accept: this.accepts }, this.headers, options.headers);

 		var results = xhr("POST",
 						{
                            url: url,
                            handleAs: "DsJsonHandler",
                            content: lang.mixin({_postedObject:JSON.stringify(object)}, this.postVars),
                            headers: headers
                          });


 		var store = this;

		results.then(function(data){
			store._onPutSuccess(data);
		});

		return results;
	},

	add: function(object, options){
		// summary:
		//		Adds an object. This will trigger a PUT request to the server
		//		if the object has an id, otherwise it will trigger a POST request.
		// object: Object
		//		The object to store.
		// options: __PutDirectives?
		//		Additional metadata for storing the data.  Includes an "id"
		//		property if a specific id is to be used.
		options = options || {};
		options.overwrite = false;
		return this.put(object, options);
	},

    // dummy
    _onRemoveSuccess: function(data) {

    },

	remove: function(id, options){
		// summary:
		//		Deletes an object by its identity. This will trigger a DELETE request to the server.
		// id: Number
		//		The identity to use to delete the object
		// options: __HeaderOptions?
		//		HTTP headers.
		//		
		//		DISABLED, as removal is managed by the lister to intercept server side errors
		//		
	/*	options = options || {};
		var ret = xhr("GET", {
			url: this.addParams(this.target, {_storeOp: 'remove', _opId: id}),
			headers: lang.mixin({}, this.headers, options.headers),
		});
		return ret; */

        options = options || {};

        var query = {idProperty:this.idProperty};
        query[this.idProperty] = id;

        var url = this.addParams(this.target, {_storeOp:'remove'});

        var headers = lang.mixin({ Accept: this.accepts }, this.headers, options.headers);

        var results = xhr("POST",
            {
                url: url,
                handleAs: "DsJsonHandler",
                content: lang.mixin(query, this.postVars),
                headers: headers
            });


        var store = this;

        results.then(function(data){
			store._onRemoveSuccess(data);
		});

        return results;
	},

    // dummy
    _onQuerySuccess: function(data) {

    },

	query: function(query, options){
		// summary:
		//		Queries the store for objects. This will trigger a GET request to the server, with the
		//		query added as a query string.
		// query: Object
		//		The query to use for retrieving objects from the store.
		// options: __QueryOptions?
		//		The optional arguments to apply to the resultset.
		// returns: dojo/store/api/Store.QueryResults
		//		The results of the query, extended with iterative methods.
		options = options || {};

		var headers = lang.mixin({ Accept: this.accepts }, this.headers, options.headers);
        var getData = { _storeOp:'query' };

		if(options && options.sort){
            var sortValue = "";
			for(var i = 0; i<options.sort.length; i++){
				var sort = options.sort[i];
                sortValue += (i > 0 ? "," : "") + (sort.descending ? this.descendingPrefix : this.ascendingPrefix) + encodeURIComponent(sort.attribute);
			}
            getData[ this.sortParam ] = sortValue;
		}

		var url = this.addParams(this.target, getData);

		var postData = lang.mixin({}, this.postVars, query);

		if(options.start >= 0 || options.count >= 0){
			postData._range_from = (options.start || '0');
			postData._range_to = ("count" in options && options.count != Infinity) ? (options.count + (options.start || 0) - 1) : '';
		}

 		var results = xhr("POST", {
            url: url,
            handleAs: "DsJsonHandler",
            content: postData,
            headers: headers
        });

        this.lastRequest = {
            postData : postData,
            getData : getData
        };

        var store = this;

		results.total = results.then(function(data){
			store._onQuerySuccess(data);

			//return range as number
			var range = results.ioArgs.xhr.getResponseHeader("Content-Range");
			if(range != null) {
				range = range.match(/\/(.*)/);
				return range == null ? 0 : range[1];
			} else return 0;
		});

		return QueryResults(results);
	},

	addParams: function(query, params){
		for(var i in params) {
			var hasQuestionMark = query.indexOf("?") > -1;
			query = query + (hasQuestionMark ? "&" : "?") + i + "=" + params[i];	
		}
		return query;
	},

    /**
     * TREE stuff
     * @see https://github.com/oria/gridx/wiki/How-to-show-tree-structure%3F
     */
    hasChildren : function(id, item) {
        //it may happen that an item changes its _recordid (say it passes from 1§* to 1§3), thus getting undefined here
        if(item==undefined){
            return false;
        }
        return item._has_children;
    },

    getChildren : function(item){
        var id = this.getIdentity(item);
        return this.query({_parent_id:id},{start:0, count:Infinity});
    }

});

});