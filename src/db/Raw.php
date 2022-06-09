<?php


namespace mftd\db;

/**
 * SQL Raw
 */
class Raw
{
    /**
     * 参数绑定
     *
     * @var array
     */
    protected $bind = [];
    /**
     * 查询表达式
     *
     * @var string
     */
    protected $value;

    /**
     * 创建一个查询表达式
     *
     * @param string $value
     * @param array $bind
     * @return void
     */
    public function __construct(string $value, array $bind = [])
    {
        $this->value = $value;
        $this->bind = $bind;
    }

    /**
     * 获取参数绑定
     *
     * @return string
     */
    public function getBind(): array
    {
        return $this->bind;
    }

    /**
     * 获取表达式
     *
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }
}
