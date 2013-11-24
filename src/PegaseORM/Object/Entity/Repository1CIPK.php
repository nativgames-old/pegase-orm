<?php

namespace PegaseORM\Object\Entity;

use PegaseORM\Object\Entity\Repository;

// Repository 1 Column Integer Primary Key

class Repository1CIPK extends Repository {
 
  public function generate_id() { 
    $res = $this->db->query("SELECT id FROM " . $this->table->get_name()
    . " ORDER BY id DESC LIMIT 0, 1");

    if($r = $res->fetch(\PDO::FETCH_ASSOC)) {
      $id = $r['id'];
    }
    else
      $id = 0;

    return $id + 1;
  }
}

