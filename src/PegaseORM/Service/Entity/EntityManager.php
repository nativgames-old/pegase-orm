<?php

namespace PegaseORM\Service\Entity;

use Pegase\Core\Service\Service\ServiceInterface;
use Pegase\Core\Exception\Objects\PegaseException;

use PegaseORM\Object\Schema\Schema;
use PegaseORM\Object\Schema\Table;

class EntityManager implements ServiceInterface {

  private $sm;
  private $schema;
  private $repos; // array('table_name' => repo)
  private $db;

  public function __construct($sm, $params = array()) {

    // initialization
    $this->sm = $sm;
    $this->schema = null;
    $this->repos = null;
  }
  
  public function set_schema($schema) {
    $this->schema = $schema;
  }

  public function get_schema() {
    return $this->schema;
  }

  public function set_db($db) {
    $this->db = $db;
  }

  public function get_db() {
    return $this->db;
  }

  public function set_repositories($repos) {
    $this->repos = $repos;
  }

  public function get_repositories() {
    return $this->repos;
  }

  public function get_repository($name) {
    return (key_exists($name, $this->repos)) ? $this->repos[$name] : null;
  }

  public function insert($entity) {
    
    $table = $this->schema->get_table_with_classname(get_class($entity));

    // création de la requête
    $sql = "INSERT INTO ";
    $sql .= $table->get_name();
    $sql .= " (";

    foreach($table->get_columns() as $col) {
      $sql .= $col[0]; // var name
      $sql .= ", ";
    }

    $sql = substr($sql, 0, strlen($sql) - 2);
    $sql .= ") ";

    $sql .= " VALUES (";

    foreach($table->get_columns() as $col) {
      $method = "get_";
      $method .= $col[0]; // var name
      $value = call_user_func(array($entity, $method));

      if(is_string($value))
        $sql .= "'${value}'";
      else if($value == null)
        $sql .= 'NULL';
      else
       $sql .= $value;
  
      $sql .= ", ";
    }

    $sql = substr($sql, 0, strlen($sql) - 2);
    $sql .= ");";

    echo $sql, "\n";

    // exécution de la requête

    return $this->db->query($sql);
  }

  public function update($entity) {
    // UPDATE entity_table SET column1 = val1, column2 = val2 
    // WHERE id = enity_id

    $table = $this->schema->get_table_with_classname(get_class($entity));

    // création de la requête
    $sql = "UPDATE ";
    $sql .= $table->get_name();
    $sql .= " SET ";

    foreach($table->get_columns() as $col) {
      $sql .= $col[0]; // var name
      $sql .= ' = ';

      $method = "get_";
      $method .= $col[0]; // var name
      $value = call_user_func(array($entity, $method));

      if(is_string($value))
        $sql .= "'${value}'";
      else if($value == null)
        $sql .= 'NULL';
      else
       $sql .= $value;

      $sql .= ", ";
    }

    $sql = substr($sql, 0, strlen($sql) - 2);
    $sql .= " WHERE id = ";

    $sql .= $entity->get_id();

    $sql .= ";";

    // exécution de la requête

    return $this->db->query($sql);
  }

  public function delete($entity) {

    $table = $this->schema->get_table_with_classname(get_class($entity));

    // création de la requête
    $sql = "DELETE FROM ";
    $sql .= $table->get_name();
    $sql .= " WHERE id = ";
    $sql .= $entity->get_id();
    $sql .= ";";

    echo $sql;

    // exécution de la requête

    return $this->db->query($sql);
  }
}

