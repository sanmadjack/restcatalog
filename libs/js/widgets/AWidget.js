function AWidget() {
    this.element = null;
    
    this.addToElement = function(element_name) {
        $('.' + element_name).append(this.element);
    };
}