<?php


namespace mftd;

use InvalidArgumentException;

/**
 * 响应输出基础类
 * @package mftd
 */
abstract class Response
{
    /**
     * 是否允许请求缓存
     * @var bool
     */
    protected $allowCache = true;
    /**
     * 字符集
     * @var string
     */
    protected $charset = 'utf-8';
    /**
     * 状态码
     * @var integer
     */
    protected $code = 200;
    /**
     * 输出内容
     * @var string
     */
    protected $content = null;
    /**
     * 当前contentType
     * @var string
     */
    protected $contentType = 'text/html';
    /**
     * Cookie对象
     * @var Cookie
     */
    protected $cookie;
    /**
     * 原始数据
     * @var mixed
     */
    protected $data;
    /**
     * header参数
     * @var array
     */
    protected $header = [];
    /**
     * 输出参数
     * @var array
     */
    protected $options = [];
    /**
     * Session对象
     * @var Session
     */
    protected $session;

    /**
     * 是否允许请求缓存
     * @access public
     * @param bool $cache 允许请求缓存
     * @return $this
     */
    public function allowCache(bool $cache)
    {
        $this->allowCache = $cache;

        return $this;
    }

    /**
     * 页面缓存控制
     * @access public
     * @param string $cache 状态码
     * @return $this
     */
    public function cacheControl(string $cache)
    {
        $this->header['Cache-control'] = $cache;

        return $this;
    }

    /**
     * 发送HTTP状态
     * @access public
     * @param integer $code 状态码
     * @return $this
     */
    public function code(int $code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * 设置页面输出内容
     * @access public
     * @param mixed $content
     * @return $this
     */
    public function content($content)
    {
        if (
            null !== $content && !is_string($content) && !is_numeric($content) && !is_callable([
                $content,
                '__toString',
            ])
        ) {
            throw new InvalidArgumentException(sprintf('variable type error： %s', gettype($content)));
        }

        $this->content = (string)$content;

        return $this;
    }

    /**
     * 页面输出类型
     * @access public
     * @param string $contentType 输出类型
     * @param string $charset 输出编码
     * @return $this
     */
    public function contentType(string $contentType, string $charset = 'utf-8')
    {
        $this->header['Content-Type'] = $contentType . '; charset=' . $charset;

        return $this;
    }

    /**
     * 设置Cookie
     * @access public
     * @param string $name cookie名称
     * @param string $value cookie值
     * @param mixed $option 可选参数
     * @return $this
     */
    public function cookie(string $name, string $value, $option = null)
    {
        $this->cookie->set($name, $value, $option);

        return $this;
    }

    /**
     * 创建Response对象
     * @access public
     * @param mixed $data 输出数据
     * @param string $type 输出类型
     * @param int $code 状态码
     * @return Response
     */
    public static function create($data = '', string $type = 'html', int $code = 200): Response
    {
        $class = false !== strpos($type, '\\') ? $type : '\\mftd\\response\\' . ucfirst(strtolower($type));

        return Container::getInstance()->invokeClass($class, [$data, $code]);
    }

    /**
     * 输出数据设置
     * @access public
     * @param mixed $data 输出数据
     * @return $this
     */
    public function data($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * ETag
     * @access public
     * @param string $eTag
     * @return $this
     */
    public function eTag(string $eTag)
    {
        $this->header['ETag'] = $eTag;

        return $this;
    }

    /**
     * Expires
     * @access public
     * @param string $time
     * @return $this
     */
    public function expires(string $time)
    {
        $this->header['Expires'] = $time;

        return $this;
    }

    /**
     * 获取状态码
     * @access public
     * @return integer
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * 获取输出数据
     * @access public
     * @return string
     */
    public function getContent(): string
    {
        if (null == $this->content) {
            $content = $this->output($this->data);

            if (
                null !== $content && !is_string($content) && !is_numeric($content) && !is_callable([
                    $content,
                    '__toString',
                ])
            ) {
                throw new InvalidArgumentException(sprintf('variable type error： %s', gettype($content)));
            }

            $this->content = (string)$content;
        }

        return $this->content;
    }

    /**
     * 获取原始数据
     * @access public
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * 获取头部信息
     * @access public
     * @param string $name 头部名称
     * @return mixed
     */
    public function getHeader(string $name = '')
    {
        if (!empty($name)) {
            return $this->header[$name] ?? null;
        }

        return $this->header;
    }

    /**
     * 设置响应头
     * @access public
     * @param array $header 参数
     * @return $this
     */
    public function header(array $header = [])
    {
        $this->header = array_merge($this->header, $header);

        return $this;
    }

    /**
     * 是否允许请求缓存
     * @access public
     * @return bool
     */
    public function isAllowCache()
    {
        return $this->allowCache;
    }

    /**
     * LastModified
     * @access public
     * @param string $time
     * @return $this
     */
    public function lastModified(string $time)
    {
        $this->header['Last-Modified'] = $time;

        return $this;
    }

    /**
     * 输出的参数
     * @access public
     * @param mixed $options 输出参数
     * @return $this
     */
    public function options(array $options = [])
    {
        $this->options = array_merge($this->options, $options);

        return $this;
    }

    /**
     * 发送数据到客户端
     * @access public
     * @return void
     * @throws InvalidArgumentException
     */
    public function send(): void
    {
        // 处理输出数据
        $data = $this->getContent();

        if (!headers_sent()) {
            if (!empty($this->header)) {
                // 发送状态码
                http_response_code($this->code);
                // 发送头部信息
                foreach ($this->header as $name => $val) {
                    header($name . (!is_null($val) ? ':' . $val : ''));
                }
            }

            if ($this->cookie) {
                $this->cookie->save();
            }
        }

        $this->sendData($data);

        if (function_exists('fastcgi_finish_request')) {
            // 提高页面响应
            fastcgi_finish_request();
        }
    }

    /**
     * 设置Session对象
     * @access public
     * @param Session $session Session对象
     * @return $this
     */
    public function setSession(Session $session)
    {
        $this->session = $session;
        return $this;
    }

    /**
     * 初始化
     * @access protected
     * @param mixed $data 输出数据
     * @param int $code 状态码
     */
    protected function init($data = '', int $code = 200)
    {
        $this->data($data);
        $this->code = $code;

        $this->contentType($this->contentType, $this->charset);
    }

    /**
     * 处理数据
     * @access protected
     * @param mixed $data 要处理的数据
     * @return mixed
     */
    protected function output($data)
    {
        return $data;
    }

    /**
     * 输出数据
     * @access protected
     * @param string $data 要处理的数据
     * @return void
     */
    protected function sendData(string $data): void
    {
        echo $data;
    }
}
