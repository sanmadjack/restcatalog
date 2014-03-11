function Debug() {
    this.element = document.createElement("div");
    this.element.setAttribute("id","debug");
    document.body.appendChild(this.element);
    
    this.log = function(level,message) {
        var record = document.createElement("div");
        record.setAttribute("class",level);
        record.innerHTML = this.createTimeStamp() + ": " + message;
        this.element.appendChild(record);
        this.scrollToBottom();
    };
    
    this.warning = function(message) {
        this.log("warning",message);
    };
    this.success = function(message) {
        this.log("success",message);
    };
    this.info = function(message) {
        this.log("info",message);
    };
    this.error = function(message) {
        this.log("error",message);
    };
    
    this.data = function(message,dump_me) {
        this.log("data",message + "<br/>" + this.var_dump(dump_me));
    };
    
    
    this.createTimeStamp = function() {
        var currentdate = new Date();
        return currentdate.getDate() + "/"
                + (currentdate.getMonth()+1)  + "/"
                + currentdate.getFullYear() + " @ "
                + currentdate.getHours() + ":"
                + currentdate.getMinutes() + ":"
                + currentdate.getSeconds() + ":"
                + currentdate.getMilliseconds();
    };
    
    this.scrollToBottom = function() {
        //var objDiv = document.getElementById("debug_console");
        //if(objDiv!=null) {
        this.element.scrollTop = this.element.scrollHeight;
    };
    
    this.var_dump = function(obj) {
        var out = '<ul><li>';
    
        out += this.var_dump_helper(obj);
    
        return out + '</li></ul>';
    };
    
    this.var_dump_helper = function(obj) {
        var out = '';
        switch(typeof(obj)) {
            case "string":
            case "boolean":
            case "number":
                out += obj + ' (' + typeof(obj) + ')';
                break;
            case "object":
            case "array":
                out += ' (' + typeof(obj) + ')';
                out += '<ul>';
                for (var key in obj) {
                    out += "<li>";
                    var child = obj[key];
                    out += key + ": ";
                    out += this.var_dump_helper(child);
    
                    out += "</li>";
                }
                out += '</ul>';
                break;
            case "undefined":
                out += "(undefined)";
                break;
            case "function":
                out += "(function)";
                break;
            default:
                throw Error("var_dump data type not recognized: " + typeof(obj));
        }
        return out;
    };

    window.onerror = function(errorMsg, url, lineNumber) {
        this.error(url + " (" + lineNumber + "): " + errorMsg);
        return false;
    };


}