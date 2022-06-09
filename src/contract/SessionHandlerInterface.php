<?php


namespace mftd\contract;

/**
 * Session驱动接口
 */
interface SessionHandlerInterface
{
    public function delete(string $sessionId): bool;

    public function read(string $sessionId): string;

    public function write(string $sessionId, string $data): bool;
}
