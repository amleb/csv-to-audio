<?php

namespace App\Classes\Translators;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Class Google
 * @package App\Classes\Downloaders
 */
class GoogleTranslator extends TranslatorAbstract
{
    public const URL = 'https://translate.google.com';

    /**
     * @var Client
     */
    protected $httpClient;

    /**
     * @var array
     */
    protected $options = [
        'ie'     => 'UTF-8',
        'client' => 'tw-ob',
        'total'  => 1,
        'idx'    => 0,
    ];

    /**
     * TranslatorAbstract constructor.
     */
    public function __construct()
    {
        $this->httpClient = new Client(['base_uri' => self::URL]);
    }

    /**
     * @return string
     */
    public function getFileExtension(): string
    {
        return 'mp3';
    }

    /**
     * @param string $word
     * @param string $destination
     * @return bool
     */
    public function grab(string $word, string $destination): bool
    {
        try {
            $this->httpClient->request('GET', '/translate_tts', [
                'query' => $this->getParameters($word),
                'sink' => $destination
            ]);

            $result = true;
        } catch (GuzzleException $e) {
            if ($e->getCode() === 404) {
                $this->lastError = sprintf('404 - Can\'t find audio file for "%s".', $word);
            } else {
                $this->lastError = $e->getMessage();
            }

            $result = false;
        }

        return $result;
    }

    /**
     * @param string $word
     * @return array
     */
    protected function getParameters(string $word): array
    {
        return array_merge($this->options, [
            'q'       => $word,
            'textlen' => mb_strlen($word),
            'tl'      => $this->language,
        ]);
    }
}
