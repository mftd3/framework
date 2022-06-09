<?php


namespace mftd\db\concern;

use mftd\db\exception\DbException;
use mftd\db\Raw;

/**
 * 聚合查询
 */
trait AggregateQuery
{
    /**
     * AVG查询
     * @access public
     * @param string|Raw $field 字段名
     * @return float
     */
    public function avg($field): float
    {
        return $this->aggregate('AVG', $field, true);
    }

    /**
     * COUNT查询
     * @access public
     * @param string|Raw $field 字段名
     * @return int
     */
    public function count(string $field = '*'): int
    {
        if (!empty($this->options['group'])) {
            // 支持GROUP

            if (!preg_match('/^[\w\.\*]+$/', $field)) {
                throw new DbException('not support data:' . $field);
            }

            $options = $this->getOptions();
            $subSql = $this->options($options)
                ->field('count(' . $field . ') AS mftd_count')
                ->bind($this->bind)
                ->buildSql();

            $query = $this->newQuery()->table([$subSql => '_group_count_']);

            $count = $query->aggregate('COUNT', '*');
        } else {
            $count = $this->aggregate('COUNT', $field);
        }

        return (int)$count;
    }

    /**
     * MAX查询
     * @access public
     * @param string|Raw $field 字段名
     * @param bool $force 强制转为数字类型
     * @return mixed
     */
    public function max($field, bool $force = true)
    {
        return $this->aggregate('MAX', $field, $force);
    }

    /**
     * MIN查询
     * @access public
     * @param string|Raw $field 字段名
     * @param bool $force 强制转为数字类型
     * @return mixed
     */
    public function min($field, bool $force = true)
    {
        return $this->aggregate('MIN', $field, $force);
    }

    /**
     * SUM查询
     * @access public
     * @param string|Raw $field 字段名
     * @return float
     */
    public function sum($field): float
    {
        return $this->aggregate('SUM', $field, true);
    }

    /**
     * 聚合查询
     * @access protected
     * @param string $aggregate 聚合方法
     * @param string|Raw $field 字段名
     * @param bool $force 强制转为数字类型
     * @return mixed
     */
    protected function aggregate(string $aggregate, $field, bool $force = false)
    {
        return $this->connection->aggregate($this, $aggregate, $field, $force);
    }
}
