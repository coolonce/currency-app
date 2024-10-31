<?php
declare(strict_types=1);

require_once '../app/bootstrap.php';
require_once '../app/routes.php';

use Slim\Factory\AppFactory;
use Slim\Factory\ServerRequestCreatorFactory;

AppFactory::setSlimHttpDecoratorsAutomaticDetection(false);
ServerRequestCreatorFactory::setSlimHttpDecoratorsAutomaticDetection(false);

$app = AppFactory::create();

registerRoutes($app);

$app->run();