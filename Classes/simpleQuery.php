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
    protected $errorConnect = 0;

    public function __construct($connectParams)
    {
        $error = 'Отсутствуют параметры: ';
        $missingParameters =[];

        //пробегаемся по параметрам подключения
            foreach ($connectParams as $param => $value) {
                if (empty($value)) {
                    $missingParameters[]= $param;

                }
            }
            // если хоть один пустой, выбрасываем ошибку
            if (!empty($missingParameters)){
                throw new dataBaseException($error.implode(', ', $missingParameters));
            }


           $this->Db = new SafeMySQL($connectParams);
    }

    /** @return int   возвращает количество строк в таблице
     *  @param string $table возвращает количество строк в таблице*/
    public function CountRowsOfTable(string $table):int
    {
        $getCount = 'SELECT COUNT(*) FROM ?n;';
        $array = $this->Db->getCol($getCount, $table);
        return $array[0];
    }

    /**@return array   возвращает массив значений колонки таблицы с возможностью выставить ограничение
     *@param string $table название таблицы
     * @param string $columnName название колонки
     * @param int $limit ограничение на количество возвращаемых значений
     */
    public function selectColumnFromTable(string $table, string $columnName, int $limit):array
    {
        $querySelect = 'SELECT ?n FROM ?n LIMIT ?i;';
        return $this->Db->getCol($querySelect,$columnName, $table, $limit);
    }

    /**@return array   возвращает массив значений со всех колонок таблицы с возможностью выставить ограничение
     *@param string $table название таблицы
     * @param int $limit ограничение на количество возвращаемых значений
     */
    public function selectAllFromTable(string $table, int $limit):array
    {
        $querySelect = 'SELECT * FROM ?n LIMIT ?i;';
        return $this->Db->getAll($querySelect, $table, $limit);
    }

    /** обновляет выбранную ячейку по id в таблице
     *@param string $table название таблицы
     * @param string $columnName название колонки
     * @param string $value новое значение ячейки
     * @param int $id айди строки в таблице
     */
    public function updateCellInTable(string $table, string $columnName,string $value, int $id)
    {
        $queryUpdate = 'UPDATE ?n SET ?n= ?s where `id`= ?i;';
        $this->Db->query($queryUpdate, $table, $columnName, $value, $id);
    }

    /** добавляет данные в конец таблицы если таблица в виде списка со столбцом id autoinc, primary key
     *@param string $table название таблицы
     * @param string $columnName название колонки
     * @param string $value новое значение ячейки
     */
    public function insertToTable(string $table, string $columnName, string $value)
    {
        $queryInsert = 'INSERT INTO ?n (`id`, ?n) VALUES (NULL, ?s);';
        $this->Db->query($queryInsert, $table, $columnName, $value);
    }

    public function rowQuery(string $query)
    {
        $this->Db->query($query);
    }

    /** удаляет данные из таблицы
     *@param string $table название таблицы
     * @param int $limit ограничение на количество удаляемых строк от начала таблицы
     */
    public function deleteRowsFromTable(string $table, int $limit)
    {
        $queryDelete = 'DELETE  FROM ?n LIMIT ?i;';
        $this->Db->query($queryDelete, $table, $limit);
    }

    /** очищает таблицу
     *@param string $table название таблицы
     */
    public function clearTable(string $table)
    {
        $queryClean = 'TRUNCATE TABLE ?n;';
        $this->Db->query($queryClean, $table);
    }

}
// Some tests...
/*
$test = new DatabaseExec($host, $database, $user, $pass);
$tablePostbacks = 'postbacktable_test';
$columnName = 'url';
$tableLog = 'table_log_test';
$columnNameTestLog = 'url_amount';

$test->updateCellInTable($tableLog, 'url_amount', 110, 1);
echo $test->CountRowsOfTable($tableLog);
*/
//$test->insertToTable($tableLog,$columnNameTestLog, '0');

/*
for ($i = 0; $i < 201; $i++){
    $test->insertToTable($tablePostbacks,$columnName, 'test_url');
}
try{
    $x = new simpleQuery();
    $count = $x->CountRowsOfTable($tableLog);
    echo $count;
} catch (dataBaseException $ex){
    //Выводим сообщение об исключении.
    echo $ex->getMessage();
}
*/

//$test->clearTable($tablePostbacks);
//$tablePostbacks = 'postbacktable_test';
//$test->deleteRowsFromTable($tablePostbacks, 100);
?>