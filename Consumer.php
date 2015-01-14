<?php
class SNF_SNFeed_Consumer
{

    protected $_readerFactory;
    protected $_converterFactory;

    private function __construct()
    {
        $this->_readerFactory = new SNF_SNFeed_Reader_Factory;
        $this->_converterFactory = new SNF_SNFeed_Converter_Factory;

        set_error_handler(array($this, 'onError'));
    }

    public static function instance()
    {
        static $instance = null;

        if (is_null($instance)) {
            $instance = new self;
        }

        return $instance;
    }

    public function consume($type)
    {
        set_time_limit(0);
        $GLOBALS['wp_rich_edit'] = true;

        kses_remove_filters();

        $reader = $this->_readerFactory->factory($type);
        $converter = $this->_converterFactory->factory($type);
        $postConverter = $this->_converterFactory->factory('post');

        do {
            try {
                $page = $reader->readPage();
            } catch (Exception $e) {
                SNF_SNFeed_Log::exception($e);
                break;
            }

            $elements = array_map(array($converter, 'toElement'), $page);

            if (method_exists($converter, 'postConvert')) {
                $elements = $converter->postConvert($elements);
            }

            $postData = array_map(array($postConverter, 'fromElement'), $elements);

            $postData = array_filter($postData);

            if (!empty($postData)) {
                SNF_SNFeed_Model_Db::instance()->savePosts($postData);
            }
        } while ($reader->nextPage());

        kses_init_filters();

        $snIds = $reader->getAllIds();

        if ($snIds) {
            $snTypes = array_fill(0, count($snIds), $reader->getNamespace());
            $snIds = array_map(array($postConverter, 'getSnId'), $snTypes, $snIds);
            SNF_SNFeed_Model_Db::instance()->removeUnrelatedPosts($reader->getNamespace(), $snIds);
        }
    }

    public function onError($errNo, $errStr, $errFile, $errLine, $errContext)
    {
        throw new Exception(sprintf('Error in file %s on line %s: %s', $errFile, $errLine, $errStr), $errNo);
    }
}