<?php

namespace RDE\DNS\Controller;

use RDE\DNS\Bin\CLI;

class CLIHelper
{
    public static function update()
    {
        // run
        $cli = new CLI;
        $cli->runWithTry(array('cli', 'update'));
    }
}
