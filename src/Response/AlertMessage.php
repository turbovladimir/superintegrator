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
class AlertMessage
{
    public const TYPE_SUCCESS = 'alert alert-success';
    public const TYPE_DANGER = 'alert alert-danger';
    public const TYPE_INFO = 'alert alert-info';
    
    private const HTML_BLOCK = '<div class="elem"><div id="%s" class="%s">%s</div></div>';
    private const MAX_CHARS_ALERT = 120;
    
    /**
     * @var array
     */
    private $messageList;
    
    public function __construct($header = null, $body = null, $type = null)
    {
        if ($header) {
            $this->addAlert($header, $body, $type);
        }
    }
    
    /**
     * @param      $header
     * @param null $body
     * @param null $type
     *
     * @return $this
     */
    public function addAlert($header, $body = null, $type = null)
    {
        $this->messageList[] = [
            'type' => $type ?? self::TYPE_INFO,
            'header' => '<h4 class="alert-heading">' . $header . '</h4>',
            'body' =>  $body,
        ];
        
        return $this;
    }
    
    /**
     * @return array
     */
    public function get() : array
    {
        $formattedResponse = [];
        
        foreach ($this->messageList as $message) {
            $id = 'alert_message';
            
            if (strlen($message['body']) > self::MAX_CHARS_ALERT) {
                $id = 'alert_message_lg';
            }
            
            $content = str_replace("\r\n", "</br>", implode('<hr>',[$message['header'], $message['body']]));
            $formattedResponse[] = sprintf(self::HTML_BLOCK, $id, $message['type'], $content);
        }

        return $formattedResponse;
    }
}