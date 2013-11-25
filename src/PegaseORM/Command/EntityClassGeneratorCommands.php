<?php

namespace PegaseORM\Command;

use Pegase\Core\Shell\Command\AbstractShellCommand;

class EntityClassGeneratorCommands extends AbstractShellCommand {
  
  public function generate($service) {

    $output = $this->output;
    $formater = $this->formater;

    // 1) on récupère les éléments à afficher

    $orm = $this->sm->get($service);

    $ecg = $orm->get_entity_class_generator();
    $ecg->generate();
    $s = $formater->set_color("Génération effectuée.\n", 'blue');

    $output
      ->write_line(
      $s
    );
  }

  public function generate_parameters() {
    return array(
      array(
        'service',
         AbstractShellCommand::IS_REQUIRED,
         'Which pegase-orm service must be used ?'
      )
    );
  }
}

