<?php

namespace PegaseORM\Object\Schema;

use Pegase\Core\Exception\Objects\PegaseException;
use Pegase\Core\Service\Service\ServiceInterface;

class Schema {

  private $tables;

  public function __construct() {
    $this->tables = array();
  }

  public function add_table($table) {
    $this->tables[] = $table;
  }

  public function get_tables() {
    return $this->tables;
  }

  public function get_table_with_name() {
    $table = null;

    foreach($this->tables as $t) {
      if($t->get_name() == $classname) {
        $table = $t;
        break;
      }
      else;
    }

    return $table;
  }

  public function get_table_with_classname($classname) {
    $table = null;

    foreach($this->tables as $t) {
      if($t->get_classname() == $classname) {
        $table = $t;
        break;
      }
      else;
    }

    return $table;
  }
}

