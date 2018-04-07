<?php

use pixiv\Crawl;

require_once __DIR__ . '/vendor/autoload.php';

$crawl = new Crawl('user', 'pass');

var_dump($crawl->login());
