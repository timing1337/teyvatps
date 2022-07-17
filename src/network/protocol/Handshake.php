<?php

namespace TeyvatPS\network\protocol;

use labalityowo\Bytebuffer\Buffer;

class Handshake
{
    public const PACKET_LENGTH = 20;

    public const CONNECT_START = 0x000000ff;

    public const CONNECT_END = 0xffffffff;

    public const CONNECT_DATA = 1234567890;

    public const ESTABLISH_START = 0x00000145;

    public const ESTABLISH_END = 0x14514545;

    public const DISCONNECT_START = 0x00000194;

    public const DISCONNECT_END = 0x19419494;

    public int $start = 0;
    public int $param1 = 0;
    public int $param2 = 0;
    public int $data = 0;
    public int $end = 0;

    public static function create(
        int $start,
        int $param1,
        int $param2,
        int $data,
        int $end
    ): self {
        $handshake = new self();
        $handshake->start = $start;
        $handshake->param1 = $param1;
        $handshake->param2 = $param2;
        $handshake->data = $data;
        $handshake->end = $end;

        return $handshake;
    }

    public function decode(Buffer $buffer): bool
    {
        if ($buffer->getLength() !== self::PACKET_LENGTH) {
            return false;
        }
        $this->start = $buffer->readUInt32BE();
        $this->param1 = $buffer->readUInt32BE(4);
        $this->param2 = $buffer->readUInt32BE(8);
        $this->data = $buffer->readUInt32BE(12);
        $this->end = $buffer->readUInt32BE(16);

        return true;
    }

    public function encode(): Buffer
    {
        $buffer = Buffer::allocate(20);
        $buffer->writeUInt32BE($this->start);
        $buffer->writeUInt32BE($this->param1, 4);
        $buffer->writeUInt32BE($this->param2, 8);
        $buffer->writeUInt32BE($this->data, 12);
        $buffer->writeUInt32BE($this->end, 16);

        return $buffer;
    }
}
