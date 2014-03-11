function Model(url) {
    this.url = url;
    
    this.getOne = null;
    this.getAll = null;
    this.create = null;
    this.update = null;
    this.destroy = null;
    
    this.nuke = function() {
        var rest = new RestController(this.url);
        rest.success = function() {
            window.alert("They had it coming...");
        
        }
        rest.sendRequest("DELETE","nuke");
    }
}

function ModelData(parent_model) {
    var model = parent_model;
    
    this.delete = function() {
        
    };
    
}