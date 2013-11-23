<?php

namespace PegaseORM\Service;

use Pegase\Core\Exception\Objects\PegaseException;
use Pegase\Core\Service\Service\ServiceInterface;

use PegaseORM\Service\Schema\SchemaManager;
use PegaseORM\Service\Entity\EntityManager;

class PegaseORM implements ServiceInterface {

  private $sm;
  private $db;
  private $params;

  private $schema_manager;
  private $entity_manager;

  public function __construct($sm, $params = array()) {
    $this->sm = $sm;
    $this->params = $params;

    if(key_exists('connection', $params)) {
      if($params['connection'] == "mysql") {
        try {
          $this->db = new \PDO(
            "mysql:host=" . $params['host'] . 
            ";port=" . $params['port'] . ";".
            "dbname=" . $params['dbname'] . ";",
            $params['username'],
            $params['password']
          );
        }
        catch(\Exception $e) {
          throw new PegaseException("PDO: Erreur " . $e->getMessage() . "N° ", $e->getCode());
        }
      }
      else
        throw new PegaseException("PegaseORM: \$params doesn't contain a valid connection: only mysql supported.");
    }
    else throw new PegaseException("PegaseORM: \$params doesn't contain any connection");

    $this->schema_manager = new SchemaManager(
      $this->sm,
      (key_exists('classes', $params)) ? $params['classes'] : null
    );

    $this->schema_manager->set_db($this->db);

    $this->entity_manager = new EntityManager($this->sm);
    $this->entity_manager->set_schema($this->schema_manager->get_schema());
    $this->entity_manager->set_db($this->db);

    $repos = array();

    foreach($this->entity_manager->get_schema()->get_tables() as $table) {
      $name = $table->get_repository_classname();
      $repos[$table->get_name()] = new $name($this->db, $table);
    }

    $this->entity_manager->set_repositories($repos);
  }

  public function set_schema_manager($sm) {
    $this->schema_manager = $sm;
  }

  public function get_schema_manager() {
    return $this->schema_manager;
  }

  public function set_entity_manager($em) {
    $this->entity_manager = $em;
  }

  public function get_entity_manager() {
    return $this->entity_manager;
  }
}

