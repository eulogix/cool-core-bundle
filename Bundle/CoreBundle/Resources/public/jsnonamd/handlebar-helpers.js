require(["dojo/date/locale"],
function(dateLocale){

    Handlebars.registerHelper ('truncate', function (str, len) {
        if (str && str.length > len && str.length > 0) {
            var new_str = str + " ";
            new_str = str.substr (0, len);
            new_str = str.substr (0, new_str.lastIndexOf(" "));
            new_str = (new_str.length > 0) ? new_str : str.substr (0, len);

            return new Handlebars.SafeString ( new_str +'...' );
        }
        return str;
    });

    Handlebars.registerHelper('onlyDate', function(isoTimestamp) {
        if(!isoTimestamp)
            return '-';
        return dateLocale.format(new Date(isoTimestamp), { locale: dojoConfig.locale, formatLength: "medium", selector: "date" });
    });

    Handlebars.registerHelper('onlyTime', function(isoTimestamp) {
        if(!isoTimestamp)
            return '-';
        return dateLocale.format(new Date(isoTimestamp), { locale: dojoConfig.locale, formatLength: "medium", selector: "time" });
    });

    Handlebars.registerHelper('ratioAsPercentage', function(floatValue) {
        if(!floatValue)
            return '-';
        return Math.trunc(100*floatValue);
    });

    Handlebars.registerHelper('boolIcon', function(boolValue) {
        if(boolValue === null || boolValue === undefined)
            return '-';
        return '<img src="/bower_components/fugue/icons/tick' + (boolValue ? '' : '-red') + '.png">';
    });

});

