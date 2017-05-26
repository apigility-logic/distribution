<?php
/**
 * Doctrine2 命令行配置文件
 * Created by PhpStorm.
 * User: figo-007
 * Date: 2016/11/7
 * Time: 10:54
 */


$entityManager = include 'doctrine-entity-manager.php';

return \Doctrine\ORM\Tools\Console\ConsoleRunner::createHelperSet($entityManager);