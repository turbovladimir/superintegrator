<?php

namespace App\Services\TeleBot\Http;

use App\Services\TeleBot\Entity\TelegramUpdate;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Stream;
use Longman\TelegramBot\Entities\InputMedia\InputMedia;
use Longman\TelegramBot\Exception\TelegramException;

class Driver
{
    private $botToken;

    /**
     * @var ClientInterface
     */
    private $client;
    /**
     * @var string
     */
    private $baseUri;

    public function __construct(
        string $baseUri,
        string $botToken
    ) {
        $this->client = new Client();
        $this->baseUri = $baseUri;
        $this->botToken = $botToken;
    }

    public function makeRequest($action, array $data = []) : TelegramUpdate {
        $params = $this->setUpRequestParams($action, $data);
        $url = str_replace(['{token}', '{action}'], [$this->botToken, $action], $this->baseUri);
        $response = $this->client->post($url, $params);

        return new TelegramUpdate(json_decode((string)$response->getBody(), true));
    }

    private function setUpRequestParams(string $action, array $data)
    {
        $has_resource = false;
        $multipart    = [];

        $input_file_fields = [
        'setWebhook'          => ['certificate'],
        'sendPhoto'           => ['photo'],
        'sendAudio'           => ['audio', 'thumb'],
        'sendDocument'        => ['document', 'thumb'],
        'sendVideo'           => ['video', 'thumb'],
        'sendAnimation'       => ['animation', 'thumb'],
        'sendVoice'           => ['voice', 'thumb'],
        'sendVideoNote'       => ['video_note', 'thumb'],
        'setChatPhoto'        => ['photo'],
        'sendSticker'         => ['sticker'],
        'uploadStickerFile'   => ['png_sticker'],
        'createNewStickerSet' => ['png_sticker', 'tgs_sticker'],
        'addStickerToSet'     => ['png_sticker', 'tgs_sticker'],
        'setStickerSetThumb'  => ['thumb'],
    ];

        foreach ($data as $key => &$item) {
            if ($key === 'media') {
                // Magical media input helper.
                $item = $this->mediaInputHelper($item, $has_resource, $multipart);
            } elseif (array_key_exists($action, $input_file_fields) && in_array($key, $input_file_fields[$action], true)) {
                // Allow absolute paths to local files.
                if (is_string($item) && file_exists($item)) {
                    $item = new Stream($this->encodeFile($item));
                }
            } elseif (is_array($item) || is_object($item)) {
                // Convert any nested arrays or objects into JSON strings.
                $item = json_encode($item);
            }

            // Reformat data array in multipart way if it contains a resource
            $has_resource = $has_resource || is_resource($item) || $item instanceof Stream;
            $multipart[]  = ['name' => $key, 'contents' => $item];
        }
        unset($item);

        if ($has_resource) {
            return ['multipart' => $multipart];
        }

        return ['form_params' => $data];
    }

    private function mediaInputHelper($item, &$has_resource, array &$multipart)
    {
        $was_array = is_array($item);
        $was_array || $item = [$item];

        foreach ($item as $media_item) {
            if (!($media_item instanceof InputMedia)) {
                continue;
            }

            // Make a list of all possible media that can be handled by the helper.
            $possible_medias = array_filter([
                'media' => $media_item->getMedia(),
                'thumb' => $media_item->getThumb(),
            ]);

            foreach ($possible_medias as $type => $media) {
                // Allow absolute paths to local files.
                if (is_string($media) && file_exists($media)) {
                    $media = new Stream($this->encodeFile($media));
                }

                if (is_resource($media) || $media instanceof Stream) {
                    $has_resource = true;
                    $unique_key   = uniqid($type . '_', false);
                    $multipart[]  = ['name' => $unique_key, 'contents' => $media];

                    // We're literally overwriting the passed media type data!
                    $media_item->$type           = 'attach://' . $unique_key;
                    $media_item->raw_data[$type] = 'attach://' . $unique_key;
                }
            }
        }

        $was_array || $item = reset($item);

        return json_encode($item);
    }

    /**
     * Encode file
     *
     * @param string $file
     *
     * @return resource
     * @throws TelegramException
     */
    private function encodeFile($file)
    {
        $fp = fopen($file, 'rb');
        if ($fp === false) {
            throw new TelegramException('Cannot open "' . $file . '" for reading');
        }

        return $fp;
    }
}