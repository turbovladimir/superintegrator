<?php
/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 19.08.2019
 * Time: 18:31
 */

namespace App\Entity;

use DateTimeInterface;

class BaseEntity implements EntityInterface
{
    
    protected $data = [];
    
    
    /**
     * @param array|null $values
     */
    public function __construct(array $values = null)
    {
        if (is_array($values)) {
            $this->data = $values;
        }
    }
    
    /**
     * @param  bool $datetime_to_string
     *
     * @return array
     */
    public function toArray($datetime_to_string = true)
    {
        if ($datetime_to_string === false) {
            return $this->data;
        }
        
        $result = [];
        foreach ($this->data as $key => $val) {
            if ($val instanceof DateTimeInterface) {
                $result[$key] = $val->format('Y-m-d H:i:s');
            }
            else {
                $result[$key] = $val;
            }
        }
        
        return $result;
    }
    
    /**
     * Проверка, установлено ли свойство объекта
     *
     * @param  mixed $offset
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->data);
    }
    
    /**
     * Получение свойства объекта
     *
     * @param  mixed $offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->data[$offset];
    }
    
    /**
     * Установка свойства объекта
     *
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        $this->data[$offset] = $value;
    }
    
    /**
     * Сброс свойства объекта
     *
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }
    
    /**
     * Получение свойства объекта
     *
     * @param  mixed $offset
     *
     * @return mixed
     */
    public function __get($offset)
    {
        return $this->offsetGet($offset);
    }
    
    /**
     * Установка свойства объекта
     *
     * @param mixed $offset
     * @param mixed $value
     */
    public function __set($offset, $value)
    {
        $this->offsetSet($offset, $value);
    }
    
    /**
     * Проверка, установлено ли свойство объекта
     *
     * @param  mixed $offset
     *
     * @return bool
     */
    public function __isset($offset)
    {
        return $this->offsetExists($offset);
    }
    
    /**
     * Сброс свойства объекта
     *
     * @param mixed $offset
     */
    public function __unset($offset)
    {
        $this->offsetUnset($offset);
    }
}