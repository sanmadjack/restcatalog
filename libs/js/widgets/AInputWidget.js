AInputWidget.prototype = new AWidget();

function AInputWidget(type) {
    this.element = document.createElement("input");
    this.element.setAttribute('type', type);
    
    this.setValue = function(value) {
        this.element.value = value;
        this.element.setAttribute('value',value);
    }

    this.getValue = function() {
        return this.element.value;
    }
}