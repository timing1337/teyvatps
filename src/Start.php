<?php

namespace TeyvatPS {

    use TeyvatPS\data\ExcelManager;
    use TeyvatPS\https\DispatchServer;
    use TeyvatPS\network\NetworkServer;
    use TeyvatPS\network\protocol\ProtocolDictonary;
    use TeyvatPS\utils\Crypto;
    use TeyvatPS\utils\Logger;

    require_once dirname(__FILE__, 2) . '/vendor/autoload.php';

    function run(): void
    {
        Config::init();
        ProtocolDictonary::init();
        Crypto::init();
        DispatchServer::init();
        NetworkServer::init();
        ExcelManager::init();

        Logger::send(
            Logger::LIGHT_PURPLE .
            "
       　　　   " . Logger::BOLD . Logger::GREEN . "TeyvatPS " . Logger::RED
            . "(◣_◢) " . Logger::RESET . " 
            " . Logger::RED . "─────────────────────
            " . Logger::GOLD . "✦ " . Logger::BLUE . "Author: " . Logger::WHITE
            . "timing#1337
            " . Logger::GOLD . "✦ " . Logger::BLUE . "Language: "
            . Logger::WHITE . "PHP

            " . Logger::RESET,
            ''
        );
    }

    run();
}
