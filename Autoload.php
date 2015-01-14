<?php
if (!class_exists('SNF_SNFeed_Autoload')) {
    class SNF_SNFeed_Autoload
    {

        protected static $_registered = false;
        protected static $_namespace = 'SNF_SNFeed';

        public static function register()
        {
            if (!self::$_registered) {
                spl_autoload_register('SNF_SNFeed_Autoload::autoload');
                self::$_registered = true;
            }
        }

        public static function autoload($className)
        {
            $prefixLen = strlen(self::$_namespace);

            if (substr($className, 0, $prefixLen) != self::$_namespace) {
                return false;
            }

            $fileName = __DIR__ . str_replace('_', DIRECTORY_SEPARATOR, substr($className, $prefixLen)) . '.php';

            if (is_file($fileName)) {
                include $fileName;
            }
        }
    }
}