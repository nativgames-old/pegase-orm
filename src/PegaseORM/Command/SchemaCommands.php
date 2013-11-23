<?php

namespace PegaseORM\Command;

use Pegase\Core\Shell\Command\AbstractShellCommand;

class SchemaCommands extends AbstractShellCommand {
  
  public function create() {

    $output = $this->output;
    $formater = $this->formater;

    // 1) on récupère les éléments à afficher

    $orm = $this->sm->get('il_db');

    $schema_manager = $orm->get_schema_manager();
    $tables = $schema_manager->get_schema()->get_tables();

    $lines = array();

    if($schema_manager->create() == true)
      $s = $formater->set_color("Création de toutes les tables: FAITE.\n", 'blue');
    else 
      $s = $formater->set_color("Création de toutes les tables: NON FAITE.\n", 'red');

    $output
      ->write_line(
      $s
    );
  }

  public function drop() {

    $output = $this->output;
    $formater = $this->formater;

    // 1) on récupère les éléments à afficher

    $orm = $this->sm->get('il_db');

    $schema_manager = $orm->get_schema_manager();
    $tables = $schema_manager->get_schema()->get_tables();

    $lines = array();

    if($schema_manager->drop() == true)
      $s = $formater->set_color("Suppression de toutes les tables: FAITE.\n", 'blue');
    else 
      $s = $formater->set_color("Suppression de toutes les tables: NON FAITE.\n", 'red');

    $output
      ->write_line(
      $s
    );
  }
}

