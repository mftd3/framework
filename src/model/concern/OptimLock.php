<?php


namespace mftd\model\concern;

use mftd\db\exception\DbException as Exception;

/**
 * 乐观锁
 */
trait OptimLock
{
    public function getWhere()
    {
        $where = parent::getWhere();
        $optimLock = $this->getOptimLockField();

        if ($optimLock && $lockVer = $this->getOrigin($optimLock)) {
            $where[] = [$optimLock, '=', $lockVer];
        }

        return $where;
    }

    /**
     * 数据检查
     * @access protected
     * @return void
     */
    protected function checkData(): void
    {
        $this->isExists() ? $this->updateLockVersion() : $this->recordLockVersion();
    }

    protected function checkResult($result): void
    {
        if (!$result) {
            throw new Exception('record has update');
        }
    }

    protected function getOptimLockField()
    {
        return property_exists($this, 'optimLock') && isset($this->optimLock) ? $this->optimLock : 'lock_version';
    }

    /**
     * 记录乐观锁
     * @access protected
     * @return void
     */
    protected function recordLockVersion(): void
    {
        $optimLock = $this->getOptimLockField();

        if ($optimLock) {
            $this->set($optimLock, 0);
        }
    }

    /**
     * 更新乐观锁
     * @access protected
     * @return void
     */
    protected function updateLockVersion(): void
    {
        $optimLock = $this->getOptimLockField();

        if ($optimLock && $lockVer = $this->getOrigin($optimLock)) {
            // 更新乐观锁
            $this->set($optimLock, $lockVer + 1);
        }
    }
}
