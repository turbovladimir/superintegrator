<?php

/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 13.03.2019
 * Time: 13:44
 * Класс для упрощения работы с бд mysql использует объект класса SafeMySQL
 * string $Host, string $dbName, string $dbUser, string $dbPass
 */

class simpleQuery
{
    protected $Db;
    public $errorConnect = 0;
    
    public function __construct()
    {
        $connectParams     = ['host' => HOST, 'db' => DATABASE, 'user' => USER, 'pass' => PASSWORD, 'charset' => 'utf8'];
        $error             = 'Отсутствуют параметры: ';
        $missingParameters = [];
        
        //пробегаемся по параметрам подключения
        foreach ($connectParams as $param => $value) {
            if (empty($value)) {
                $missingParameters[] = $param;
                
            }
        }
        // если хоть один пустой, выбрасываем ошибку
        if (!empty($missingParameters)) {
            throw new dataBaseException($error.implode(', ', $missingParameters));
        }
        
        
        $this->Db = new SafeMySQL($connectParams);
    }
    
    /** @return int   возвращает количество строк в таблице
     * @param string $table возвращает количество строк в таблице
     */
    public function CountRowsOfTable($table)
    {
        $getCount = 'SELECT COUNT(*) FROM ?n;';
        $array    = $this->Db->getCol($getCount, $table);
        return $array[0];
    }
    
    /**@return array   возвращает массив значений колонки таблицы с возможностью выставить ограничение
     * @param string $table      название таблицы
     * @param string $columnName название колонки
     * @param int    $limit      ограничение на количество возвращаемых значений
     */
    public function selectColumnFromTable($table, $columnName, $limit)
    {
        $querySelect = 'SELECT ?n FROM ?n LIMIT ?i;';
        return $this->Db->getCol($querySelect, $columnName, $table, $limit);
    }
    
    /**@return array   возвращает массив значений со всех колонок таблицы с возможностью выставить ограничение
     * @param string $table название таблицы
     * @param int    $limit ограничение на количество возвращаемых значений
     */
    public function selectAllFromTable($table, $limit)
    {
        $querySelect = 'SELECT * FROM ?n LIMIT ?i;';
        return $this->Db->getAll($querySelect, $table, $limit);
    }
    
    /** обновляет выбранную ячейку по id в таблице
     *
     * @param string $table      название таблицы
     * @param string $columnName название колонки
     * @param string $value      новое значение ячейки
     * @param int    $id         айди строки в таблице
     */
    public function updateCellInTable($table, $whereColumn, $whereValue, $setColumn, $setValue)
    {
        $queryUpdate = 'UPDATE ?n SET ?n= ?s where ?n= ?s;';
        $this->Db->query($queryUpdate, $table, $setColumn, $setValue, $whereColumn, $whereValue);
    }
    
    /** добавляет данные в конец таблицы если таблица в виде списка со столбцом id autoinc, primary key
     *
     * @param string $table      название таблицы
     * @param string $columnName название колонки
     * @param string $value      новое значение ячейки
     */
    public function insertToTable($table, $columnName, $value)
    {
        $queryInsert = 'INSERT INTO ?n (`id`, ?n) VALUES (NULL, ?s);';
        $this->Db->query($queryInsert, $table, $columnName, $value);
    }
    
    public function rawQuery($query)
    {
        $this->Db->query($query);
    }
    
    /** удаляет данные из таблицы
     *
     * @param string $table название таблицы
     * @param int    $limit ограничение на количество удаляемых строк от начала таблицы
     */
    public function deleteRowsFromTable($table, $limit)
    {
        $queryDelete = 'DELETE  FROM ?n LIMIT ?i;';
        $this->Db->query($queryDelete, $table, $limit);
    }
    
    /** очищает таблицу
     *
     * @param string $table название таблицы
     */
    public function clearTable($table)
    {
        $queryClean = 'TRUNCATE TABLE ?n;';
        $this->Db->query($queryClean, $table);
    }
    
    public function checkConnect()
    {
        $connect = $this->Db->getConn();
        if ($connect->error === 'MySQL server has gone away') {
            return false;
        } else {
            return true;
        }
    }
    
}

?>