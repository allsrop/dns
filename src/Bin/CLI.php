<?php

namespace RDE\DNS\Bin;

use CLIFramework\Application;

class CLI extends Application
{
    public function init()
    {
        parent::init();
        $this->command('update', 'RDE\DNS\Bin\UpdateCommand');
    }
}
