<?php
/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 15.11.2019
 * Time: 11:42
 */

namespace App\Response;

/**
 * Class AlertMessageCollection
 *
 * @package App\Utils\Response
 */
class ResponseMessage extends ResponseData
{
    public const TYPE_WARNING= 'alert alert-warning';
    public const TYPE_DANGER = 'alert alert-danger';
    public const TYPE_INFO = 'alert alert-info';
    
    private const HTML_BLOCK = '<div class="elem"><div id="%s" class="%s">%s</div></div>';
    private const MAX_CHARS_ALERT = 120;
    private const TYPE_MESSAGE = 'message';
    
    public function __construct($header = null, $body = null, $type = null)
    {
        if ($header) {
            $this->add($header, $body, $type);
        }
    }

    /**
     * @param      $header
     * @param null $body
     *
     * @return $this
     */
    public function addError($header, $body = null)
    {
        $this->add($header, $body, self::TYPE_DANGER);

        return $this;
    }

    /**
     * @param      $header
     * @param null $body
     *
     * @return $this
     */
    public function addWarning($header, $body = null)
    {
        $this->add($header, $body, self::TYPE_WARNING);

        return $this;
    }

    /**
     * @param      $header
     * @param null $body
     *
     * @return $this
     */
    public function addInfo($header, $body = null)
    {
        $this->add($header, $body, self::TYPE_INFO);

        return $this;
    }
    

    public function getData() : array
    {
        $formattedResponse = '';
        
        foreach ($this->data as $message) {
            $id = 'alert_message';
            
            if (strlen($message['body']) > self::MAX_CHARS_ALERT) {
                $id = 'alert_message_lg';
            }

            !empty($message['body']) ?
                $content = implode('<hr>',[$message['header'], $message['body']]):
                $content = $message['header'];
            $content = str_replace("\r\n", "</br>", $content);
            $formattedResponse .= sprintf(self::HTML_BLOCK, $id, $message['message_type'], $content);
        }

        $this->data = [self::TYPE_MESSAGE => $formattedResponse];

        return parent::getData();
    }

    /**
     * @param      $header
     * @param null $body
     * @param null $type
     *
     * @return $this
     */
    private function add($header, $body = null, $type = null)
    {
        $this->data[] = [
            'message_type' => $type ?? self::TYPE_INFO,
            'header' => '<h4 class="alert-heading">' . $header . '</h4>',
            'body' =>  $body,
        ];

        return $this;
    }
}