define("cool/util/formatters", [
    "dojo/date/locale",
    "dojo/number",
    "dojo/currency"
], function(dateLocale, number, currency) {
  
    var obj = {

        currencyFormatter: function(value){
            return currency.format(value, {currency: "EUR"});
        },

        /**
         * Thursday 4/20/2015
         */
        unixTimestampFormatterWithDayOfWeek: function(unixTimestamp){
            return dateLocale.format(new Date(unixTimestamp*1000), { locale: dojoConfig.locale, formatLength: "full", selector: "date", datePattern:"EEEE" })+
                ' '+this.unixTimestampFormatter(unixTimestamp);
        },

        /**
         * 4/20/2015
         */
        unixTimestampFormatter: function(unixTimestamp){
            return dateLocale.format(new Date(unixTimestamp*1000), { locale: dojoConfig.locale, formatLength: "medium", selector: "date" })
        },

        unixTimestampFormatterFull: function(unixTimestamp){
            return dateLocale.format(new Date(unixTimestamp*1000), { locale: dojoConfig.locale, formatLength: "medium" })
        },

        unixTimestampFormatterDayAndMonth: function(unixTimestamp){
            return dateLocale.format(new Date(unixTimestamp*1000), { locale: dojoConfig.locale, selector: "date", datePattern: "dd/MM" })
        }
        
    };

    return obj;
  
});
    