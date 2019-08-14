<?php
/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 27.02.2019
 * Time: 12:50
 */

class PAGE
{
    protected $head;
    protected $title;
    protected $body;

    public function __construct($head = '', $body = '')
    {
        $this->head = $head;
        $this->body = $body;
    }

    protected function get_head(){
        return  $this->head;
    }

    protected function get_title(){
        return '<title>'.$this->title.'</title>';
    }

    protected function get_body(){
        return $this->body;
    }

    public function render(){
        echo '<html>';
        echo $this->get_head();
        echo $this->get_body();
       /* echo htmlspecialchars($this->get_head());
        echo htmlspecialchars($this->get_title());
        echo htmlspecialchars($this->get_body());
       */
        echo '</html>';
    }

}
?>