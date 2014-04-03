<?php
namespace Catalog\JSON\Schema;

// An implementation for generating JSON Schema links, as defined at
// http://json-schema.org/latest/json-schema-hypermedia.html
class JsonSchemaLink {
    
    //const $hyper_schema ='http://json-schema.org/draft-04/hyper-schema#';


    protected $rel = null;
    protected $href = null;
    
    protected $title = null;
    protected $method = null;
    protected $schema = null;
    
    public function __construct($rel,$href) { 
        $this->rel = $rel;
        $this->href = $href;
    }
    
    public function setTitle($title) {
        $this->title = $title;
    }
    
    public function setSchema(JsonSchemaProp $schema) {
        $this->schema = $schema;
    }
    
    public function setMethod($method) {
        $this->method = $method;
    }
    
    public function generateJson(array $output = array()) {
        if($this->title!=null) {
            $output["title"] = $this->title;
        }
        if($this->rel!=null) {
            $output["rel"] = $this->rel;
        }
        if($this->href!=null) {
            $output["href"] = $this->href;
        }
        if($this->method!=null) {
            $output["method"] = $this->method;
        }
        if($this->schema!=null) {
            $output["schema"] = $this->schema->generateJson();
        }
        return $output;
    }
    
}
?>