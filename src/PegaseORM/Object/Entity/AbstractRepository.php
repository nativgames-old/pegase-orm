<?php

namespace PegaseORM\Object\Entity;

class AbstractRepository {

  protected $db;
  protected $table;

  public function __construct($db, $table) {
    $this->db = $db;
    $this->table = $table;
  }

  public function convert_to_entity($to_convert) {
    $name = $this->table->get_classname();
    $entity = new $name();
      
    foreach($this->table->get_columns() as $col) {
      $method = "set_";
      $method .= $col[0]; // var name

      $value = call_user_func(array($entity, $method), $to_convert[$col[0]]);
    }

    return $entity;
  }
}

