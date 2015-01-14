<?php
set_time_limit(0);

function get_option($name, $default = null) {
	return $default;
}

require_once 'Autoload.php';
SNF_SNFeed_Autoload::register();

$readerFactory = new SNF_SNFeed_Reader_Factory;

$reader = $readerFactory->factory(SNF_SNFeed_Type::TWITTER);

#$reader->readPage();

$ids = $reader->getAllIds();

var_dump(count($ids));
var_dump($ids);