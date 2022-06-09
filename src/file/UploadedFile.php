<?php


namespace mftd\file;

use mftd\exception\FileException;
use mftd\File;

class UploadedFile extends File
{
    private $error;
    private $mimeType;
    private $originalName;
    private $test = false;

    public function __construct(string $path, string $originalName, string $mimeType = null, int $error = null, bool $test = false)
    {
        $this->originalName = $originalName;
        $this->mimeType = $mimeType ?: 'application/octet-stream';
        $this->test = $test;
        $this->error = $error ?: UPLOAD_ERR_OK;

        parent::__construct($path, UPLOAD_ERR_OK === $this->error);
    }

    /**
     * 获取文件扩展名
     * @return string
     */
    public function extension(): string
    {
        return $this->getOriginalExtension();
    }

    /**
     * 获取上传文件扩展名
     * @return string
     */
    public function getOriginalExtension(): string
    {
        return pathinfo($this->originalName, PATHINFO_EXTENSION);
    }

    /**
     * 获取上传文件类型信息
     * @return string
     */
    public function getOriginalMime(): string
    {
        return $this->mimeType;
    }

    /**
     * 上传文件名
     * @return string
     */
    public function getOriginalName(): string
    {
        return $this->originalName;
    }

    public function isValid(): bool
    {
        $isOk = UPLOAD_ERR_OK === $this->error;

        return $this->test ? $isOk : $isOk && is_uploaded_file($this->getPathname());
    }

    /**
     * 上传文件
     * @access public
     * @param string $directory 保存路径
     * @param string|null $name 保存的文件名
     * @return File
     */
    public function move(string $directory, string $name = null): File
    {
        if ($this->isValid()) {
            if ($this->test) {
                return parent::move($directory, $name);
            }

            $target = $this->getTargetFile($directory, $name);

            set_error_handler(function ($type, $msg) use (&$error) {
                $error = $msg;
            });

            $moved = move_uploaded_file($this->getPathname(), (string)$target);
            restore_error_handler();
            if (!$moved) {
                throw new FileException(sprintf('Could not move the file "%s" to "%s" (%s)', $this->getPathname(), $target, strip_tags($error)));
            }

            @chmod((string)$target, 0666 & ~umask());

            return $target;
        }

        throw new FileException($this->getErrorMessage());
    }

    /**
     * 获取错误信息
     * @access public
     * @return string
     */
    protected function getErrorMessage(): string
    {
        switch ($this->error) {
            case 1:
            case 2:
                $message = 'upload File size exceeds the maximum value';
                break;
            case 3:
                $message = 'only the portion of file is uploaded';
                break;
            case 4:
                $message = 'no file to uploaded';
                break;
            case 6:
                $message = 'upload temp dir not found';
                break;
            case 7:
                $message = 'file write error';
                break;
            default:
                $message = 'unknown upload error';
        }

        return $message;
    }
}
