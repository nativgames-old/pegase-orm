<?php

namespace PegaseORM\Object\Schema;

use Pegase\Core\Exception\Objects\PegaseException;
use Pegase\Core\Service\Service\ServiceInterface;

class Table {

  private $classname;
  private $name;
  private $repoclassname;

  private $columns;
  private $constraints;

  public function __construct($name, $classname, $repoclassname) {
    $this->name = $name;
    $this->classname = $classname;
    $this->repoclassname = $repoclassname;
    $this->columns = array();
    $this->constraints = array();
  }

  public function add_column($name, $type, $size, $constraints = array()) {
    $this->columns[] = array($name, $type, $size, $constraints);
  }

  public function add_constraint($constraint) {
    $this->constraints[] = $constraint;
  }

  public function get_columns() {
    return $this->columns;
  }

  public function get_constraints() {
    return $this->constraints;
  }

  public function get_classname() {
    return $this->classname;
  }

  public function get_name() {
    return $this->name;
  }

  public function get_repository_classname() {
    return $this->repoclassname;
  }
}

