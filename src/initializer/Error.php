<?php


namespace mftd\initializer;

use mftd\App;
use mftd\console\Output as ConsoleOutput;
use mftd\exception\ErrorException;
use mftd\exception\Handle;
use Throwable;

/**
 * 错误和异常处理
 */
class Error
{
    /** @var App */
    protected $app;

    /**
     * Error Handler
     * @access public
     * @param integer $errno 错误编号
     * @param string $errstr 详细错误信息
     * @param string $errfile 出错的文件
     * @param integer $errline 出错行号
     * @throws ErrorException
     */
    public function appError(int $errno, string $errstr, string $errfile = '', int $errline = 0): void
    {
        $exception = new ErrorException($errno, $errstr, $errfile, $errline);

        if (error_reporting() & $errno) {
            // 将错误信息托管至 mftd\exception\ErrorException
            throw $exception;
        }
    }

    /**
     * Exception Handler
     * @access public
     * @param Throwable $e
     */
    public function appException(Throwable $e): void
    {
        $handler = $this->getExceptionHandler();

        $handler->report($e);

        if ($this->app->runningInConsole()) {
            $handler->renderForConsole(new ConsoleOutput(), $e);
        } else {
            $handler->render($this->app->request, $e)->send();
        }
    }

    /**
     * Shutdown Handler
     * @access public
     */
    public function appShutdown(): void
    {
        if (!is_null($error = error_get_last()) && $this->isFatal($error['type'])) {
            // 将错误信息托管至mftd\ErrorException
            $exception = new ErrorException($error['type'], $error['message'], $error['file'], $error['line']);

            $this->appException($exception);
        }
    }

    /**
     * 注册异常处理
     * @access public
     * @param App $app
     * @return void
     */
    public function init(App $app)
    {
        $this->app = $app;
        error_reporting(E_ALL);
        set_error_handler([$this, 'appError']);
        set_exception_handler([$this, 'appException']);
        register_shutdown_function([$this, 'appShutdown']);
    }

    /**
     * Get an instance of the exception handler.
     *
     * @access protected
     * @return Handle
     */
    protected function getExceptionHandler()
    {
        return $this->app->make(Handle::class);
    }

    /**
     * 确定错误类型是否致命
     *
     * @access protected
     * @param int $type
     * @return bool
     */
    protected function isFatal(int $type): bool
    {
        return in_array($type, [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE]);
    }
}
