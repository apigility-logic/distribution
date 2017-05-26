<?php
/**
 * Created by PhpStorm.
 * User: figo-007
 * Date: 2016/11/18
 * Time: 16:56
 */
use Zend\Stdlib\ArrayUtils;
use ZF\Apigility\Application;

date_default_timezone_set('Asia/Hong_Kong');

include 'vendor/autoload.php';

$appConfig = include 'config/application.config.php';

if (file_exists('config/development.config.php')) {
    $appConfig = ArrayUtils::merge(
        $appConfig,
        include 'config/development.config.php'
    );
}

// Run the application!
$application = Application::init($appConfig);

$entityManager = $application->getServiceManager()->get('Doctrine\ORM\EntityManager');
return $entityManager;