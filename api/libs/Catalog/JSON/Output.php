<?php
namespace Catalog\JSON;
use Catalog\JSON\Schema\JsonSchemaLink;

class Output {
    private $error = null;
    private $links = array();

    public function addLinks(array $links) {
        foreach($links as $link) {
            $this->addLink($link);
        }
    }

    public function addLink(JsonSchemaLink $link) {
        array_push($this->links,$link->generateJson());
    }


    public function generateJson() {
        $output = array();
        if($this->error!=null) {
            $output["error"] = $this->error;
        }
        if(sizeof($this->links)>0) {
            $output["links"] = $this->links;
        }
        return $output;
    }
}

?>