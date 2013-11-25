<?php

namespace PegaseORM\Service\Schema;

use Pegase\Core\Service\Service\ServiceInterface;
use Pegase\Core\Exception\Objects\PegaseException;

use PegaseORM\Object\Schema\Schema;
use PegaseORM\Object\Schema\Table;

class SchemaManager implements ServiceInterface {

  private $sm;
  private $schema;
  private $db;

  public function __construct($sm, $params = array()) {

    // initialization
    $this->sm = $sm;
    $this->schema = null;

    $yaml = $this->sm->get('pegase.component.yaml.spyc');
    $mm = $this->sm->get('pegase.core.module_manager');

    if($params != null) {

      if(!is_array($params)) {
        throw new PegaseException("SchemaManager params must be an array or null.");
      }
      else;

      $this->schema = new Schema();

      foreach($params as $name => $class) {

        if(key_exists('import', $class)) {
          $tmp = $yaml->parse($mm->get_path($class['module'], $class['import']));
          // we add the elements of tmp in the class
          foreach($tmp as $n => $t) {
            $class[$n] = $t;
          }
        }

        $table = new Table($name,
                           $class['module'], 
                           $class['class'], 
                           $class['repo_class'],
                           key_exists('constraints', $class) ? $class['constraints'] : array());

        foreach($class['columns'] as $n => $col) {
          $table->add_column($n, $col['type'], $col['size'], $col['constraints']);
        }

        $this->schema->add_table($table);
      }
    }
    else;
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

  public function create() {

    $ret = true;

    foreach($this->schema->get_tables() as $t) {
      $r = $this->create_table($t);
      $ret = $r && $ret;
      
      if($r)
        echo "OK\n";
      else
        echo "NOT OK\n";
    }

    return $ret;
  }

  public function update() {

    $ret = true;

    foreach($this->schema->get_tables() as $t) {
      $ret = $this->update_table($t) && $ret;
    }

    return $ret;
  }

  public function drop() {

    $ret = false;

    do {
      $continue = false;

      // si une table au moins est supprimée, alors ça veut dire qu'il faut continuer
      // car si on essaie de supprimer une table référencée par une autre cela ne marchera pas.

      foreach($this->schema->get_tables() as $t) {
        $continue = $continue || $this->drop_table($t);
      }

      if($continue == true)
        $ret = true;

    } while($continue == true);

    return $ret;
  }

  public function create_table($table) {
    // création de la requête
    $sql = "CREATE TABLE ";
    $sql .= $table->get_name();
    $sql .= " (";

    foreach($table->get_columns() as $col) {
      $sql .= $col[0]; // var name
      $sql .= " ";
      $sql .= $col[1]; // var type

      if($col[2] != null) // var size
        $sql .= "(" . $col[2] . ")";

      if($col[3] != null) {
        foreach($col[3] as $constraint) {
          $sql .= " " . $constraint;
        }
      }

      $sql .= ", ";
    }

    if(count($table->get_constraints()) > 0) {
      foreach($table->get_constraints() as $c) {
        $sql .= $c . ", ";
      }
    }

    $sql = substr($sql, 0, strlen($sql) - 2);

    $sql .= ");";

    echo $sql, "\n";

    // exécution de la requête

    return $this->db->query($sql);
  }

  public function update_table($table) {
    
  }

  public function drop_table($table) {
    // création de la requête
    $sql = "DROP TABLE ";
    $sql .= $table->get_name();
    $sql .= ";";

    // exécution de la requête

    return $this->db->query($sql);
  }

  public function truncate_table($table) {
    // on tronque la table
    $sql = "TRUNCATE ";
    $sql .= $table->get_name();
    $sql .= ";";

    // exécution de la requête

    return $this->db->query($sql);
  }
}

