<?php
class SNF_SNFeed_Log {
	
	public static function log($message, $file = 'default.log') {
		file_put_contents(__DIR__ . '/log/' . $file, $message . PHP_EOL, FILE_APPEND);
	}
	
	public static function exception(Exception $e) {
		self::log($e->__toString(), 'exception.log');
	}
}