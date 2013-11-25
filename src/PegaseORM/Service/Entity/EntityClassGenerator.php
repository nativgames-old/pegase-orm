<?php

namespace PegaseORM\Service\Entity;

use Pegase\Core\Service\Service\ServiceInterface;
use Pegase\Core\Exception\Objects\PegaseException;

use PegaseORM\Object\Schema\Schema;
use PegaseORM\Object\Schema\Table;

class EntityClassGenerator implements ServiceInterface {

  private $sm;
  private $schema;

  public function __construct($sm, $params = array()) {

    // initialization
    $this->sm = $sm;
    $this->schema = null;
  }
  
  public function set_schema($schema) {
    $this->schema = $schema;
  }

  public function get_schema() {
    return $this->schema;
  }

  public function generate() {

    $ms = $this->sm->get('pegase.core.module_manager'); // module service

    foreach($this->schema->get_tables() as $t) {
      $content = $this->generate_entity_class($t);

      $filename = $ms->get_path($t->get_module(), str_replace('\\', '/', $t->get_classnamerel()));
      $filename .= "-generated.php";

      $f = fopen($filename, "w");
      fwrite($f, $content);
      fclose($f);
    }
    return $this;
  }

  public function generate_entity_class($table) {
    
    $fullclassname = $table->get_classname();
    $tmp = explode('\\', $fullclassname);

    $n = count($tmp);
    $n --;
    $m = $n - 1;

    $namespace = "";

    if($n > 0) {

      for($i = 0; $i < $m; $i++)
        $namespace .= $tmp[$i] . '\\';

      $namespace .= $tmp[$m];
    }

    $classname = $tmp[$n];

    $content = 
'<?php 

namespace ' . $namespace . ';

class ' . $classname . ' {

';

    $content .= "  /* Columns declaration */\n";

    foreach($table->get_columns() as $col) {
      $content .= '  private $' . $col[0] . ";\n";
    }

    $content .= "\n";

    $content .= "  /* Columns getters and setters */\n";

    foreach($table->get_columns() as $col) {
      $content .= '  public function set_' . $col[0] . '($' . $col[0] .") {\n";
      $content .= '    $this->' . $col[0] . ' = $' . $col[0] . ";\n";
      $content .= '    return $this;' . "\n";
      $content .= '  }' . "\n";

      $content .= '  public function get_' . $col[0] . "() {\n";
      $content .= '    return $this->' . $col[0] . ";\n";
      $content .= '  }' . "\n\n";
    }

    $content .= 
'} 
';

    return $content;
  }
}

