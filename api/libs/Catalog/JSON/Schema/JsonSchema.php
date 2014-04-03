<?php
namespace Catalog\JSON\Schema;

class JsonSchema extends JsonSchemaProp {
    const $schema = "http://json-schema.org/draft-04/schema#";
    
    protected $id = "http://catalog.darkholme.net/entry-schema#";
    protected $title = "";
    
    public function __construct($type) {
        parent::__construct($type);
    }
    
    public function setTitle($title) {
        if(!is_string($title)) {
            throw new Exception("title must be a string");
        }
        $this->title = $title;
    }
    
    public function generateJson(array $output = array()) {
        if($this->id!=null) {
            $output['id'] = $this->id;
        }
        $output['$schema'] = self::$schema;
        
        if($this->title!=null) {
            $output['title'] = $this->title;
        }
        return parent::generateJson($output);
    }
}