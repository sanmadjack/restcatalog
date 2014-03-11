function RestController(rest_url) {
    var rest = this;
    this.url = rest_url;
    this.accepts = "application/json";
    this.dataType = "json";
    
    this.contentType = "application/json";

    this.headers = [];

    var code_handlers = [];
    
    this.success = null;
    
    this.error = null;
        
 
    this.sendRequest = function(method,path) {
        var options = {};
        
        // Sets the http method to use
        options.type = method;
        
        // the type of data expected back
        options.accepts = this.accepts;
        // tells jquery how to parse the returning data
        options.dataType = this.dataType;
        
        // Type of data being sent
        options.contentType = this.contentType;
        
        //options.data
        //options.headers //kv pair array
        
        
        options.error = function(ajax,status,error) {
            debug.error(status);
            debug.error(error);
            
            if(rest.error!=null) {
                rest.error(ajax,status,error);
            } else {
                window.alert(error);
            }
        };
        
        options.success = function(data,status,ajax) {
            debug.success("REST request succesful (" + ajax.status + " - " + status + ")");
            if(data!=null) {
                debug.data("Returned data:",data);
            }

            if(rest.success!=null) {
                rest.success(data,status,ajax);
            } else {
                window.alert(status);
                
            }
        };
        
        var url = this.url + path;
        
        debug.info("Sending REST request: " + url);
        debug.data("REST request options:",options);
        $.ajax(url,options);
    };
 
    this.addCodeHandler = function (code,handler) {
        if(!(code in code_handlers)) {
            code_handlers[code] = [];
        }
        code_handlers[code].push(handler);
    };
}