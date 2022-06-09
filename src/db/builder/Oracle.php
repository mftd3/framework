<?php


namespace mftd\db\builder;

use mftd\db\Builder;
use mftd\db\Query;

/**
 * Oracle数据库驱动
 */
class Oracle extends Builder
{
    protected $selectSql = 'SELECT * FROM (SELECT mftdphp.*, rownum AS numrow FROM (SELECT  %DISTINCT% %FIELD% FROM %TABLE%%JOIN%%WHERE%%GROUP%%HAVING%%ORDER%) mftdphp ) %LIMIT%%COMMENT%';

    /**
     * 字段和表名处理
     * @access public
     * @param Query $query 查询对象
     * @param string $key
     * @param string $strict
     * @return string
     */
    public function parseKey(Query $query, $key, bool $strict = false): string
    {
        $key = trim($key);

        if (strpos($key, '->') && false === strpos($key, '(')) {
            // JSON字段支持
            [$field, $name] = explode($key, '->');
            $key = $field . '."' . $name . '"';
        }

        return $key;
    }

    /**
     * limit分析
     * @access protected
     * @param Query $query 查询对象
     * @param mixed $limit
     * @return string
     */
    protected function parseLimit(Query $query, string $limit): string
    {
        $limitStr = '';

        if (!empty($limit)) {
            $limit = explode(',', $limit);

            if (count($limit) > 1) {
                $limitStr = "(numrow>" . $limit[0] . ") AND (numrow<=" . ($limit[0] + $limit[1]) . ")";
            } else {
                $limitStr = "(numrow>0 AND numrow<=" . $limit[0] . ")";
            }
        }

        return $limitStr ? ' WHERE ' . $limitStr : '';
    }

    /**
     * 设置锁机制
     * @access protected
     * @param Query $query 查询对象
     * @param bool|false $lock
     * @return string
     */
    protected function parseLock(Query $query, $lock = false): string
    {
        if (!$lock) {
            return '';
        }

        return ' FOR UPDATE NOWAIT ';
    }

    /**
     * 随机排序
     * @access protected
     * @param Query $query 查询对象
     * @return string
     */
    protected function parseRand(Query $query): string
    {
        return 'DBMS_RANDOM.value';
    }
}
