<?php

namespace App\Classes\Translators;

/**
 * Interface DownloaderInterface
 * @package App\Classes\Downloaders
 */
abstract class TranslatorAbstract
{
    /**
     * @var string
     */
    protected $lastError;

    /**
     * @var string
     */
    protected $language;

    /**
     * @return string
     */
    public function getLastError(): string
    {
        return $this->lastError;
    }

    /**
     * @param string $language
     * @return TranslatorAbstract
     */
    public function setLanguage(string $language): TranslatorAbstract
    {
        $this->language = $language;
        return $this;
    }

    /**
     * @return string
     */
    abstract public function getFileExtension(): string;

    /**
     * @param string $word
     * @param string $destination
     * @return bool
     */
    abstract public function grab(string $word, string $destination): bool;
}
