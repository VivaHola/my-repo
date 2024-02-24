<?php
class SimpleController extends BaseController
{
    private $mapper;

    public function __construct($table)
    {
        global $f3;						// needed for $f3->get()
        $this->mapper = new DB\SQL\Mapper($f3->get('DB'), $table);	// create DB query mapper object
    }

    public function putIntoDatabase($data)
    {
        $this->mapper->name   = $data["name"];
        $this->mapper->colour = $data["colour"];
        $this->mapper->save();					 // save new record with these fields
    }

    public function getData()
    {
        $mapper = $this->mapper->find();
        return $this->castMapperToArray($mapper);
    }

}