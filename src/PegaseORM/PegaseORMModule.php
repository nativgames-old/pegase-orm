<?php

namespace PegaseORM;

use \Pegase\Core\Module\AbstractModule;

class PegaseORMModule extends AbstractModule {

  public function get_name() {
    return "PegaseORM";
  }

  public function get_path() {
    return __DIR__;
  }
}

