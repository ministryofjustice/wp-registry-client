<?php

namespace MOJDigital\WP_Registry\Client\Traits;

use MOJDigital\WP_Registry\Client\WordPressGlobalFunctionsInvoker;

trait UsesWordPressGlobalFunctionsInvoker {
    /**
     * Holds the WordPressGlobalFunctionsInvoker object
     * @var WordPressGlobalFunctionsInvoker
     */
    private $WordPressGlobalFunctionsInvoker = null;

    /**
     * @return WordPressGlobalFunctionsInvoker
     */
    public function getWordPressGlobalFunctionsInvoker()
    {
        if (!isset($this->WordPressGlobalFunctionsInvoker)) {
            $this->WordPressGlobalFunctionsInvoker = new WordPressGlobalFunctionsInvoker();
        }
        return $this->WordPressGlobalFunctionsInvoker;
    }

    /**
     * Convenience method for getWordPressGlobalFunctionsInvoker()
     * @return WordPressGlobalFunctionsInvoker
     */
    public function wp()
    {
        return $this->getWordPressGlobalFunctionsInvoker();
    }

    /**
     * @param WordPressGlobalFunctionsInvoker $WordPressGlobalFunctionsInvoker
     */
    public function setWordPressGlobalFunctionsInvoker($WordPressGlobalFunctionsInvoker)
    {
        $this->WordPressGlobalFunctionsInvoker = $WordPressGlobalFunctionsInvoker;
    }
}
