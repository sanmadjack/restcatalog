function RestController(rest_url) {
    this.url = rest_url;
    this.accepts = "application/json";
    this.dataType = "json";
    
    this.contentType = "application/json";

    this.headers = [];

    var code_handlers = [];
    
    
 
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
            window.alert(status);
            window.alert(error);
        };
        
        options.success = function(data,status,ajax) {
            window.alert(data);
            window.alert(status);
        };
        
        var url = this.url + path;
        
        $.ajax(url,options);
    };
 
    this.addCodeHandler = function (code,handler) {
        if(!(code in code_handlers)) {
            code_handlers[code] = [];
        }
        code_handlers[code].push(handler);
    };
}