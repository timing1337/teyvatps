<?php

namespace TeyvatPS\network\protocol;

use Exception;
use Google\Protobuf\Internal\Message;
use labalityowo\Bytebuffer\Buffer;
use PacketHead;
use TeyvatPS\utils\Logger;

class DataPacket
{
    public const HEADER_LENGTH = 12;

    public const MAGIC_START = 0x4567;

    public const MAGIC_END = 0x89ab;

    public int $id;
    public ?PacketHead $header;
    public ?Message $data;

    public function __construct(
        string|int $id = null,
        Message $data = null,
        PacketHead $header = null
    )
    {
        if ($header === null) {
            $this->header = new PacketHead;
        } else {
            $this->header = $header;
        }
        if (is_string($id)) {
            $this->id = ProtocolDictonary::getProtocolIdFromName($id);
            $this->data = new $id;
        } else {
            if (is_int($id)) {
                $this->id = $id;
                $this->data = new ($this->getName());
            }
        }
        if ($data !== null) {
            $this->data = $data;
        }
    }

    public function getName(): string
    {
        return ProtocolDictonary::getProtocolNameFromId($this->pid());
    }

    public function pid(): int
    {
        return $this->id;
    }

    public function decode(Buffer $buffer): bool
    {
        try {
            if ($buffer->getLength() < self::HEADER_LENGTH) {
                return false;
            }

            $start = $buffer->readUInt16BE();
            $this->id = $buffer->readUInt16BE(2);
            $metadataSize = $buffer->readUInt16BE(4);
            $dataSize = $buffer->readUInt32BE(6);
            if ($buffer->getLength() !== self::HEADER_LENGTH + $metadataSize
                + $dataSize
            ) {
                return false;
            }
            $this->header = new PacketHead;
            $this->header->mergeFromString(
                $buffer->slice(10, 10 + $metadataSize)->toString()
            );
            $name = $this->getName();
            if ($name === "unknown") {
                Logger::notice("Unknown packet id: " . $this->id);
                return false;
            }
            if (!class_exists($this->getName())) {
                Logger::notice("Missing proto class: " . $this->getName());

                return false;
            }

            $this->data = new ($this->getName());
            $this->data->mergeFromString(
                $buffer->slice(
                    10 + $metadataSize,
                    10 + $metadataSize + $dataSize
                )->toString()
            );

            $end = $buffer->readUInt16BE(10 + $metadataSize + $dataSize);
            if ($start !== self::MAGIC_START || $end !== self::MAGIC_END) {
                return false;
            }
        } catch (Exception $exception) {
            return false;
        }

        return true;
    }

    /**
     * @throws Exception
     */
    public function encode(): Buffer
    {
        if (!$this->data instanceof Message
            || !$this->header instanceof PacketHead
        ) {
            throw new Exception(
                "Packet " . $this->pid() . " is not properly initialized"
            );
        }

        $header = Buffer::new($this->header->serializeToString());
        $data = Buffer::new($this->data->serializeToString());

        $buffer = Buffer::allocate(
            self::HEADER_LENGTH + $header->getLength() + $data->getLength()
        );
        $buffer->writeUInt16BE(self::MAGIC_START);
        $buffer->writeUInt16BE($this->id, 2);
        $buffer->writeUInt16BE($header->getLength(), 4);
        $buffer->writeUInt32BE($data->getLength(), 6);
        $header->copy($buffer, 10);
        $data->copy($buffer, 10 + $header->getLength());
        $buffer->writeUInt16BE(
            self::MAGIC_END,
            10 + $header->getLength() + $data->getLength()
        );

        return $buffer;
    }
}
