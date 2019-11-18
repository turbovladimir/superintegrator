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
    
    /**
     * @param      $header
     * @param      $body
     * @param null $alertType
     */
    public function addAlert($header, $body, $alertType = null)
    {
        $this->messageList[$alertType ?? self::ALERT_TYPE_INFO][] = "$header: $body\n";
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