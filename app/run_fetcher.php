<?php
require 'bootstrap.php';
use App\Commands\FetchDataCommand;

$fetchDataCommand = $container->get(FetchDataCommand::class);

if ($argc < 2) {
    echo "Usage: php fetch_currency.php <days>\n";
    exit(1);
}

if(!ctype_digit($argv[1])){
    echo "arg don`t int";
    exit(1);
}
$days = (int) $argv[1];
$fetchDataCommand->execute($days);