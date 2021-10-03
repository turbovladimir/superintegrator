<?php

namespace App\Entity\Telebot;

class Button
{
    /**
     * @var string
     */
    private $title;
    /**
     * @var string|null
     */
    private $url;

    public function __construct(string $title, ?string $url)
    {

        $this->title = $title;
        $this->url = $url;
    }

    public function get()
    {
        return $this->url ?
            ['text' => $this->title, 'callback_data' => $this->title, 'url' => $this->url] :
            ['text' => $this->title, 'callback_data' => $this->title];
    }
}