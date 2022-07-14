# teyvatps

SLOW DEVELOPMENT

## Requirements
1. [PHP 8.x](https://windows.php.net/download)
2. [Composer](https://getcomposer.org/)
3. [Protos](https://github.com/teyvatps/protos). You will need to generate it yourself, check out the repo for more information
4. packetIds.csv o packetIds.json. It should look like [this](https://github.com/Xiaobin0860/GenshinDebug/blob/master/util/packetIds.json)

## NOTICE
You'll need to redirect the host yourself, suggest using hosts file for simplicity, make sure to trust the credential

## KNOWN ISSUE
Movement bugs (the server can't keep up with the game but still maintaining at stable 0ms (?))
Wouldn't work with CN client > 2.7.50

## TODO LIST

Implement metadata patching

## Installation
1. Clone the repo ``git clone https://github.com/teyvatps/teyvatps``. 

- If you want to use the GenshinData repo, then execute this (RECOMMENDED)

- ``git clone https://github.com/teyvatps/teyvatps --recursive`` 

2. Extract the PHP files and put them in a folder called "bin" in the teyvatps folder

3. Install dependencies

* ``cd teyvatps``

* ``composer install``

4. Put your generated protos files in 'protos' folder, packetIds in 'data'

4. Run the start.cmd and have fun
