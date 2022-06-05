<?php

namespace TeyvatPS\network\protocol;

use TeyvatPS\Config;

class ProtocolDictonary
{

    private static array $idToNameProtocol = [];
    private static array $nameToIdProtocol = [];

    public static function init(): void
    {
        /*
         * For this it should be
         */
        $file =  Config::DATA_FOLDER . "packetIds.csv";
        if(file_exists($file)) {
            $file = explode(PHP_EOL, file_get_contents($file));
            foreach ($file as $line) {
                $csv = str_getcsv($line);
                if($csv[0] === "" || !isset($csv[0]) || $csv[1] === "")
                {
                    continue;
                }
                self::$nameToIdProtocol[$csv[0]] = $csv[1];
            }
        }else{
            $file = file_get_contents(Config::DATA_FOLDER . "/packetIds.json");
            self::$nameToIdProtocol = json_decode($file, true);
        }
        self::$idToNameProtocol = array_flip(self::$nameToIdProtocol);
    }

    /**
     * @param int $id
     * @return string
     */
    public static function getProtocolNameFromId(int $id): string
    {
        return self::$idToNameProtocol[$id] ?? "unknown";
    }

    /**
     * @param string $name
     * @return int
     */
    public static function getProtocolIdFromName(string $name): int
    {
        return self::$nameToIdProtocol[$name] ?? -1;
    }
}