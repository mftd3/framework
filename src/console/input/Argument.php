<?php

namespace mftd\console\input;

use InvalidArgumentException;
use LogicException;

class Argument
{
    // 必传参数
    public const IS_ARRAY = 4;

    // 可选参数
    public const OPTIONAL = 2;

    // 数组参数
    public const REQUIRED = 1;
    /**
     * 参数默认值
     * @var mixed
     */
    private $default;
    /**
     * 参数描述
     * @var string
     */
    private $description;
    /**
     * 参数类型
     * @var int
     */
    private $mode;
    /**
     * 参数名
     * @var string
     */
    private $name;

    /**
     * 构造方法
     * @param string $name 参数名
     * @param int $mode 参数类型: self::REQUIRED 或者 self::OPTIONAL
     * @param string $description 描述
     * @param mixed $default 默认值 (仅 self::OPTIONAL 类型有效)
     * @throws InvalidArgumentException
     */
    public function __construct(string $name, int $mode = null, string $description = '', $default = null)
    {
        if (null === $mode) {
            $mode = self::OPTIONAL;
        } elseif (!is_int($mode) || $mode > 7 || $mode < 1) {
            throw new InvalidArgumentException(sprintf('Argument mode "%s" is not valid.', $mode));
        }

        $this->name = $name;
        $this->mode = $mode;
        $this->description = $description;

        $this->setDefault($default);
    }

    /**
     * 获取默认值
     * @return mixed
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * 获取描述
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * 获取参数名
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * 该参数是否接受数组
     * @return bool
     */
    public function isArray(): bool
    {
        return self::IS_ARRAY === (self::IS_ARRAY & $this->mode);
    }

    /**
     * 是否必须
     * @return bool
     */
    public function isRequired(): bool
    {
        return self::REQUIRED === (self::REQUIRED & $this->mode);
    }

    /**
     * 设置默认值
     * @param mixed $default 默认值
     * @throws LogicException
     */
    public function setDefault($default = null): void
    {
        if (self::REQUIRED === $this->mode && null !== $default) {
            throw new LogicException('Cannot set a default value except for InputArgument::OPTIONAL mode.');
        }

        if ($this->isArray()) {
            if (null === $default) {
                $default = [];
            } elseif (!is_array($default)) {
                throw new LogicException('A default value for an array argument must be an array.');
            }
        }

        $this->default = $default;
    }
}
