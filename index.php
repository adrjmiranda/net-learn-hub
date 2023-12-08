<?php
use app\classes\GlobalValues;

$dependencies = require_once __DIR__ . '/bootstrap.php';

$app = $dependencies['slim_app'];

$gg = $dependencies[GlobalValues::G_CSRF_TOKEN];
var_dump($gg);

require_once __DIR__ . '/app/routes/staticFilesRoutes.php';
require_once __DIR__ . '/app/routes/administratorRoutes.php';
require_once __DIR__ . '/app/routes/courseRoutes.php';
require_once __DIR__ . '/app/routes/userRoutes.php';
require_once __DIR__ . '/app/routes/commentRoutes.php';
require_once __DIR__ . '/app/routes/commonRoutes.php';

$app->run();