<?php

namespace PegaseORM\Object\Entity;

class AbstractRepository {

  protected $db;
  protected $table;

  public function __construct($db, $table) {
    $this->db = $db;
    $this->table = $table;
  }
}

