<?php

declare(strict_types=1);

use Core\Application;
use Core\Config;

Config::load(dirname(__DIR__, 2));

return new Application();