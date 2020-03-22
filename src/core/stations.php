<?php namespace Core;

use DB\dbClass;

class stations extends dbClass
{
    public $id;

    protected $title;
    protected $data;
    protected $cluster;

    private $table = 'stations';

    public function __construct($id)
    {
        parent::__construct();
        $this->cluster = new Clusters(2);
        $this->id = $id;
        $this->data = $this->gatherData();
    }


    private function gatherData() {
        $this->data              = $this->find($this->table, $this->id);
        $this->title                = $this->data->title;
    }
}