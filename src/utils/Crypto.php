<?php

namespace TeyvatPS\utils;

use labalityowo\Bytebuffer\Buffer;
use OpenSSLAsymmetricKey;
use TeyvatPS\FolderConstants;

class Crypto
{
    public static Buffer $dispatchKey;
    public static Buffer $dispatchSeed;
    public static Buffer $secretKey;

    public static OpenSSLAsymmetricKey $publicKey;
    public static OpenSSLAsymmetricKey $privateKey;

    public static OpenSSLAsymmetricKey $publicSigningKey;
    public static OpenSSLAsymmetricKey $privateSigningKey;

    public static function init(): void
    {
        self::$publicKey = openssl_pkey_get_public(
            file_get_contents(FolderConstants::DATA_FOLDER . 'rsa/public.key')
        );
        self::$privateKey = openssl_get_privatekey(
            file_get_contents(FolderConstants::DATA_FOLDER . 'rsa/private.key')
        );
        self::$publicSigningKey = openssl_get_publickey(
            file_get_contents(FolderConstants::DATA_FOLDER . 'rsa/signing_public.key')
        );
        self::$privateSigningKey = openssl_get_privatekey(
            file_get_contents(FolderConstants::DATA_FOLDER . 'rsa/signing_private.key')
        );
        self::$dispatchKey = Buffer::new(
            file_get_contents(FolderConstants::DATA_FOLDER . 'ec2b/dispatchKey.bin')
        );
        self::$dispatchSeed = Buffer::new(
            file_get_contents(FolderConstants::DATA_FOLDER . 'ec2b/dispatchSeed.bin')
        );
        self::$secretKey = Buffer::new(
            file_get_contents(FolderConstants::DATA_FOLDER . 'ec2b/secretKey.bin')
        );
    }

    public static function xorBuffer(Buffer &$buffer, Buffer $key): void
    {
        $rawBuffer = $buffer->toString();
        for ($i = 0; $i < $buffer->getLength(); $i++) {
            $rawBuffer[$i] = $rawBuffer[$i] ^ $key->toString()[$i
                % $key->getLength()];
        }
        $buffer = Buffer::new($rawBuffer);
    }
}
