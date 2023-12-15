<?php
$dependencies = require_once __DIR__ . '/bootstrap.php';

$app = $dependencies['slim_app'];

require_once __DIR__ . '/app/routes/staticFilesRoutes.php';
require_once __DIR__ . '/app/routes/administratorRoutes.php';
require_once __DIR__ . '/app/routes/userRoutes.php';
require_once __DIR__ . '/app/routes/commonRoutes.php';

$app->run();