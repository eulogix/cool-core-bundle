define(['dojo/errors/create'], function(create){
	return create("DsError", function(message, response){
		this.response = response;

		this.failed = function() {
			return response._success === false;
		};

		this.getErrors = function() {
			return response._errors;
		};
	});
});
