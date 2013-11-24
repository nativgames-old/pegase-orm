<?php

namespace PegaseORM\Service;

use Pegase\Core\Exception\Objects\PegaseException;
use Pegase\Core\Service\Service\ServiceInterface;

use PegaseORM\Service\Schema\SchemaManager;
use PegaseORM\Service\Entity\EntityManager;
use PegaseORM\Service\Entity\EntityClassGenerator;

class PegaseORM implements ServiceInterface {

  private $sm;
  private $db;
  private $params;

  private $schema_manager;
  private $entity_manager;
  private $entity_class_generator;

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
      $name = $table->get_module() . '\\' . $table->get_repository_classname();
      $repos[$table->get_name()] = new $name($this->db, $table);
    }

    $this->entity_manager->set_repositories($repos);

    $this->entity_class_generator = new EntityClassGenerator($this->sm);
    $this->entity_class_generator->set_schema($this->schema_manager->get_schema());
  }

  public function get_schema_manager() {
    return $this->schema_manager;
  }

  public function get_entity_manager() {
    return $this->entity_manager;
  }

  public function get_entity_class_generator() {
    return $this->entity_class_generator;
  }
}

