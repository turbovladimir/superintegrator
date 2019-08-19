<?php
/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 19.08.2019
 * Time: 18:28
 */

namespace App\Orm;


interface ModelInterface
{
    const FETCH_ARRAY  = 'array';
    const FETCH_ENTITY = 'entity';
    
    /**
     * @return string
     */
    public function getConnectionName();
    
    /**
     * @param string $connectionName
     */
    public function setConnectionName($connectionName);
    
    /**
     * @return \Doctrine\DBAL\Connection
     */
    public function getConnection();
    
    /**
     * @return string
     */
    public function getTableName();
    
    /**
     * @return $this
     */
    public function setFetchArray();
    
    /**
     * @return $this
     */
    public function setFetchEntity();
    
    /**
     * @param  mixed $pk
     *
     * @return \Cityads\Processing\Orm\Entity\EntityInterface|array|null
     */
    public function get($pk);
    
    /**
     * @param  array $filter
     * @param  array $order
     *
     * @return \Cityads\Processing\Orm\Entity\EntityInterface|array|null
     */
    public function getBy(array $filter, array $order = []);
    
    /**
     * @param  \Doctrine\DBAL\Query\QueryBuilder $query
     *
     * @return \Cityads\Processing\Orm\Entity\EntityInterface|array|null
     */
    public function getByQuery(QueryBuilder $query);
    
    /**
     * @param  array $filter
     * @param  array $order
     *
     * @return \Cityads\Processing\Orm\Entity\EntityInterface[]|array
     */
    public function all(array $filter, array $order = []);
    
    /**
     * @param  \Doctrine\DBAL\Query\QueryBuilder $query
     *
     * @return \Cityads\Processing\Orm\Entity\EntityInterface[]|array
     */
    public function allByQuery(QueryBuilder $query);
    
    /**
     * Функция для обхода огромного числа строк безопасно для памяти
     *
     * @param  array $filter
     * @param  array $order
     *
     * @return \Cityads\Processing\Orm\Entity\EntityInterface[]|array
     */
    public function iterate(array $filter = [], array $order = []);
    
    /**
     * Функция для обхода огромного числа строк безопасно для памяти
     *
     * @param  \Doctrine\DBAL\Query\QueryBuilder $query
     *
     * @return \Cityads\Processing\Orm\Entity\EntityInterface[]|array
     */
    public function iterateByQuery(QueryBuilder $query);
    
    /**
     * @param  string $key
     * @param  array  $filter
     * @param  array  $order
     *
     * @return string|null
     */
    public function getOne($key, array $filter, array $order = []);
    
    /**
     * @param  string $key
     * @param  array  $filter
     * @param  array  $order
     *
     * @return array
     */
    public function getColumn($key, array $filter, array $order = []);
    
    /**
     * @param  string $key
     * @param  string $value
     * @param  array  $filter
     * @param  array  $order
     *
     * @return array
     */
    public function getPairs($key, $value, array $filter, array $order = []);
    
    /**
     * @param  array  $filter
     * @param  string $column
     *
     * @return int
     */
    public function count(array $filter, $column = '*');
    
    /**
     * @param  array $data
     * @param  array $types
     *
     * @return int|null
     */
    public function insert(array $data, array $types = []);
    
    /**
     * Множественная вставка, работает только для MySQL
     *
     * @param  array $data_set
     * @param  array $types
     *
     * @return int|null
     */
    public function insertMulti(array $data_set, array $types = []);
    
    /**
     * @param  array $data
     * @param  array $filter
     * @param  array $types
     *
     * @return int
     */
    public function update(array $data, array $filter, array $types = []);
    
    /**
     * @param  array $filter
     *
     * @return int
     */
    public function delete(array $filter);
    
    /**
     * @param  array       $columns
     * @param  string|null $alias
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    public function fetchQuery(array $columns = ['*'], $alias = null);
    
    /**
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    public function insertQuery();
    
    /**
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    public function updateQuery();
    
    /**
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    public function deleteQuery();
    
    /**
     * Преобразование фильтра к DBAL query builder форме
     *
     * @param \Doctrine\DBAL\Query\QueryBuilder $query
     * @param array                             $filter
     */
    public function applyFilter(QueryBuilder $query, array $filter);
    
    /**
     * @param \Doctrine\DBAL\Query\QueryBuilder $query
     * @param array                             $order_by
     */
    public function applyOrderBy(QueryBuilder $query, array $order_by);
    
    /**
     * @param  string   $lockKey
     * @param  int      $timeout
     * @param  callable $callback
     *
     * @return bool
     */
    public function performWithLock($lockKey, $timeout, callable $callback);
}