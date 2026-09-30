<?php
class JsonParse{
    public function parsear(){
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        return $data;
    }
}