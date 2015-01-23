<?php

namespace RDE\DNS\Controller;

use RDE\DNS\Bin\CLI;

class CLIHelper
{
    public static function update()
    {
        // run
        ob_start();
        $cli = new CLI;
        $cli->runWithTry(array('cli', 'update'));
        ob_end_clean();
    }
}
