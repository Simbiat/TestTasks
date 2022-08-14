<?php
declare(strict_types=1);

use Lamia\CLI;

#Register autoloader
#In proper project I would imagine Composer's autoload being in use
require __DIR__. '/Lamia/Autoload.php';

(new CLI)->run();
