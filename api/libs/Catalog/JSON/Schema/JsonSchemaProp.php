<?php
namespace Catalog\JSON\Schema;

class JsonSchemaProp {
    // Optional
    protected $description = null
    
    
    protected $type = "object";
    const $valid_types = array(
        "object",
        "integer",
        "string",
        "number",
        "array",
        "boolean",
        "null"
        );

    protected $readOnly = false;
    
    protected $definitions = array();
    protected $enum = array();
    
    // Object-related
    protected $properties = array(); // key array of property specs
    protected $patternProperties = array(); // key array of property specs
    protected $required = array(); // array of strings corresponding to property keys
    protected $additionalProperties = true; // Controls whether properties other than those strictly specified can be used
    protected $maxProperties = null; //integer
    protected $minProperties = null; //integer
    
    // Stirng-related
    protected $pattern = null;
    protected $maxLength = null; //integer, <=
    protected $minLength = null; //integer, >=
    
    // Numeric-related
    protected $minimum = null; // integer
    protected $maximum = null; // integer
    protected $multipleOf = null; // integer
    protected $exclusiveMaximum = false; // boolean, controls whether maximum means < (true) or <= (false) (default)
    protected $exclusiveMinimum = false; // boolean, controls whether minimum means > (true) or >= (false) (default)
    
    // Array-related
    protected $items = array();
    protected $minItems = null;
    protected $maxItems = null;
    protected $uniqueItems = false;
    protected $additionalItems = true;
    
    protected $links = array();
    
    public function __construct($type) {
        $this->type = $type;
    }
    
    // NOTICE
    // When adding setters, be sure to reference http://json-schema.org/latest/json-schema-validation.html
    
    public function addProperty($name, JsonSchemaProp $prop, $required = false) {
        if(!is_string($name)) {
            throw new Exception("name must be a string");
        }
        $this->properties[$name] = $prop;
        if($required===true) {
            array_push($this->required,$name);
        }
    }
    public function addPatternProperty($pattern, JsonSchemaProp $prop) {
        if(!is_string($pattern)) {
            throw new Exception("pattern must be a string");
        }
        $this->patternProperties[$pattern] = $prop;
    }
    
    public function generateJson(array $output = array()) {
        if($this->description!=null) {
            $output["description"] = $this->description;
        }
        
        $output["type"] = $this->type;

        if(sizeof($this->enum)>0) {
            $output["enum"] = $this->enum;
        }
        
        switch($this->type) {
            case "object":
                if(sizeof($this->properties) > 0) {
                    $output["properties"] = array();
                    foreach($this->properties as $name=>$prop) {
                        $output["properties"][$name] = $prop->generateJson();
                    }
                    
                }
                if(sizeof($this->patternProperties) > 0) {
                    $output["patternProperties"] = array();
                    foreach($this->patternProperties as $pattern=>$prop) {
                        $output["patternProperties"][$pattern] = $prop->generateJson();
                    }
                    
                }

                if(sizeof($this->required)>0) {
                    $output["required"] = $this->required;
                }
                break;
            case "number":
            case "integer":
                if($this->minimum!=null) {
                    $output["minimum"] = $this->minimum;
                }
                if($this->maximum!=null) {
                    $output["maximum"] = $this->maximum;
                }
                if($this->exclusiveMinimum==true) {
                    $output["exclusiveMinimum"] = true;
                }
                break;
            case "array":
                if(sizeof($this->items) > 0){
                    $output["items"] = $this->items;
                }
                if($this->minItems!=null) {
                    $output["minItems"] = $this->minItems;
                }
                if($this->uniqueItems==true) {
                    $output["uniqueItems"] = true;
                }
                break;
            case "string":
                if($this->pattern!=null) {
                    $output["pattern"] = $this->pattern;
                }
                break;
        }
        
        if(sizeof($this->definitions)>0) {
            $output["definitions"] = array();
            foreach($this->definitions as $name->$def) {
                $output["definitions"][$name] = $def;
            }
        }
        
        if(sizeof($this->links)>0) {
            $output["links"] = array();
            foreach($this->links as $link) {
                array_push($output["links"],$link->generateJson());
            }
        }
        
        if($this->readOnly==true) {
            $output["readOnly"] = true;
        }
        
        
        return $output;
    }
}