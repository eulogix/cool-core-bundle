define("cool/functions/popup", [], function() {
  
    var obj = {

        open: function(url, name, windowFeatures, w, h, alignment) {
            
            if (navigator.appVersion.indexOf('Chrome')>0) h=h+1; //Chrome includes the url bar in the height calculation
                
            if(alignment == "CENTER")
            {
                temp_wnd=window.open(url,name,windowFeatures+',height='+h+',width='+w+',top='+(screen.availHeight-h)/2+',left='+(screen.availWidth-w)/2);
            } else
            {
                temp_wnd=window.open(url,name,windowFeatures+',height='+h+',width='+w+',top='+0+',left='+0);
                if(alignment == "LEFT")
                {
                        temp_wnd.moveTo(0,0);
                        temp_wnd.resizeTo(w,screen.availHeight);
                        top.moveTo(w,0);
                        top.resizeTo(screen.availWidth-w,screen.availHeight);
                } else
                if(alignment == "RIGHT")
                {
                        temp_wnd.moveTo(screen.availWidth-w,0);
                        temp_wnd.resizeTo(w,screen.availHeight);
                        top.moveTo(0,0);
                        top.resizeTo(screen.availWidth-w,screen.availHeight);
                } else
                if(alignment == "TOP")
                {
                        temp_wnd.moveTo(0,0);
                        temp_wnd.resizeTo(screen.availWidth,h);
                        top.moveTo(0,h);
                        top.resizeTo(screen.availWidth,screen.availHeight-h);
                } else
                if(alignment == "BOTTOM")
                {
                        temp_wnd.moveTo(0,screen.availHeight-h);
                        temp_wnd.resizeTo(screen.availWidth,h);
                        top.moveTo(0,0);
                        top.resizeTo(screen.availWidth,screen.availHeight-h);
                }
            }
            
            temp_wnd.focus();
        }   
        
    };

    return obj;
  
});
    