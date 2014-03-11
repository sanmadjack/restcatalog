function Model() {
    this.getOne = null;
    this.getAll = null;
    this.create = null;
    this.update = null;
    this.destroy = null;
}

function ModelData(parent_model) {
    var model = parent_model;
    
    this.delete = function() {
        
    };
    
}