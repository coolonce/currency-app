<?php
require 'bootstrap.php';

use App\Workers\Worker;

$worker = $container->get(Worker::class);
$worker->processMessages();