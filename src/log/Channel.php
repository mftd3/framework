<?php


namespace mftd\log;

use mftd\contract\LogHandlerInterface;
use mftd\Event;
use mftd\event\LogRecord;
use mftd\event\LogWrite;
use Psr\Log\LoggerInterface;

class Channel implements LoggerInterface
{
    /**
     * 允许写入类型
     * @var array
     */
    protected $allow = [];
    /**
     * 关闭日志
     * @var array
     */
    protected $close = false;
    protected $event;

    protected $lazy = true;
    /**
     * 日志信息
     * @var array
     */
    protected $log = [];
    protected $logger;
    protected $name;

    public function __construct(string $name, LogHandlerInterface $logger, array $allow, bool $lazy = true, Event $event = null)
    {
        $this->name = $name;
        $this->logger = $logger;
        $this->allow = $allow;
        $this->lazy = $lazy;
        $this->event = $event;
    }

    public function __call($method, $parameters)
    {
        $this->log($method, ...$parameters);
    }

    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function alert($message, array $context = [])
    {
        $this->log(__FUNCTION__, $message, $context);
    }

    /**
     * 清空日志
     */
    public function clear()
    {
        $this->log = [];
    }

    /**
     * 关闭通道
     */
    public function close()
    {
        $this->clear();
        $this->close = true;
    }

    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function critical($message, array $context = [])
    {
        $this->log(__FUNCTION__, $message, $context);
    }

    /**
     * Detailed debug information.
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function debug($message, array $context = [])
    {
        $this->log(__FUNCTION__, $message, $context);
    }

    /**
     * System is unusable.
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function emergency($message, array $context = [])
    {
        $this->log(__FUNCTION__, $message, $context);
    }

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function error($message, array $context = [])
    {
        $this->log(__FUNCTION__, $message, $context);
    }

    /**
     * 获取日志信息
     * @return array
     */
    public function getLog(): array
    {
        return $this->log;
    }

    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function info($message, array $context = [])
    {
        $this->log(__FUNCTION__, $message, $context);
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function log($level, $message, array $context = [])
    {
        $this->record($message, $level, $context);
    }

    /**
     * Normal but significant events.
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function notice($message, array $context = [])
    {
        $this->log(__FUNCTION__, $message, $context);
    }

    /**
     * 记录日志信息
     * @access public
     * @param mixed $msg 日志信息
     * @param string $type 日志级别
     * @param array $context 替换内容
     * @param bool $lazy
     * @return $this
     */
    public function record($msg, string $type = 'info', array $context = [], bool $lazy = true)
    {
        if ($this->close || (!empty($this->allow) && !in_array($type, $this->allow))) {
            return $this;
        }

        if (is_string($msg) && !empty($context)) {
            $replace = [];
            foreach ($context as $key => $val) {
                $replace['{' . $key . '}'] = $val;
            }

            $msg = strtr($msg, $replace);
        }

        if (!empty($msg) || 0 === $msg) {
            $this->log[$type][] = $msg;
            if ($this->event) {
                $this->event->trigger(new LogRecord($type, $msg));
            }
        }

        if (!$this->lazy || !$lazy) {
            $this->save();
        }

        return $this;
    }

    /**
     * 保存日志
     * @return bool
     */
    public function save(): bool
    {
        $log = $this->log;
        if ($this->event) {
            $event = new LogWrite($this->name, $log);
            $this->event->trigger($event);
            $log = $event->log;
        }

        if ($this->logger->save($log)) {
            $this->clear();
            return true;
        }

        return false;
    }

    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function warning($message, array $context = [])
    {
        $this->log(__FUNCTION__, $message, $context);
    }

    /**
     * 实时写入日志信息
     * @access public
     * @param mixed $msg 调试信息
     * @param string $type 日志级别
     * @param array $context 替换内容
     * @return $this
     */
    public function write($msg, string $type = 'info', array $context = [])
    {
        return $this->record($msg, $type, $context, false);
    }
}
