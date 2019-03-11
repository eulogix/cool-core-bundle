require([
        "dojo/date/locale",
        "dojo/currency"
    ],
function(dateLocale, currencyLocale){

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

    Handlebars.registerHelper('boolIcon', function(boolValue){
        if(boolValue === null || boolValue === undefined) {
            return '-';
        }
        if(boolValue) {
            return '<img src="/bower_components/fugue/icons/tick.png">';
        }
        else {
            return '<img src="/bower_components/fugue/icons/cross.png">';
        } }

    );

    Handlebars.registerHelper('fileSize', function(fileSizeInBytes) {
        var i = -1;
        var byteUnits = [' Kb', ' Mb', ' Gb', ' Tb', 'Pb', 'Eb', 'Zb', 'Yb'];
        fileSizeInBytes = parseInt(fileSizeInBytes);
        do {
            fileSizeInBytes = fileSizeInBytes / 1024;
            i++;
        } while (fileSizeInBytes > 1024);

        return Math.max(fileSizeInBytes, 0).toFixed(1) + byteUnits[i];
    });

    Handlebars.registerHelper('currency', function(decimalNumber) {
        return currencyLocale.format(decimalNumber);
    });

});

