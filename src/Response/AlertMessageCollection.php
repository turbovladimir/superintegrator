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
class AlertMessageCollection
{
    public const ALERT_TYPE_SUCCESS = 'alert alert-success';
    public const ALERT_TYPE_DANGER = 'alert alert-danger';
    public const ALERT_TYPE_INFO = 'alert alert-info';
    
    /**
     * @var array
     */
    private $messageList;
    
    public function __construct($header = null, $body = null, $alertType = null)
    {
        if ($header) {
            $this->addAlert($header, $body, $alertType);
        }
    }
    
    /**
     * @param      $header
     * @param null $body
     * @param null $alertType
     *
     * @return $this
     */
    public function addAlert($header, $body = null, $alertType = null)
    {
        $message = $body === null ? $header : "$header: $body";
        $this->messageList[$alertType ?? self::ALERT_TYPE_INFO][] = $message . "\n";
        
        return $this;
    }
    
    /**
     *
     * @return array
     */
    public function getMessages() : array
    {
        $formattedResponse = [];
        
        foreach ($this->messageList as $alertType => $messages) {
            $alertBody = '';
            
            foreach ($messages as $message) {
                $alertBody .= $message;
            }
            
            $formattedResponse[$alertType] = $alertBody;
        }
        
        return $formattedResponse;
    }
}