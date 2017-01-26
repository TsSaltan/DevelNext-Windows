<?php
namespace bundle\windows;

use bundle\windows\result\startupItem;
use php\lib\arr;
use php\lib\str;
use php\util\Regex;
use bundle\windows\WindowsScriptHost as WSH;


class Startup 
{    
    /**
     * --RU--
     * Получить список программ, находящихся в автозагрузке
     * @return array[startupItem]
     */
    public static function getList(){
        $items = WSH::WMIC('startup get');
        $return = [];
        foreach($items as $k=>$item){
            $return[] = new startupItem($item['Caption'], $item['Command'], $item['Location']);

        }

        return $return;
    }

    /**
     * --RU--
     * Добавляет программу в автозугрузку
     * @var string $file Команда для запуска
     * @var string $description=null Описание
     * @return startupItem
     */
    public static function add($file, $description = null){
        $dir = self::getUserStartupDirectory();
        $basename = basename($file);
        Windows::createShortcut($dir . '\\' . $basename . '.lnk', $file, $description);
        return self::find($file);
        return new startupItem($basename, $basename . '.lnk', 'Startup');
    }

    /**
     * --RU--
     * Найти запись в автозапуске по исполняемому файлу
     * @var string $file Путь к исполняемому файлу
     * @return startupItem|false
     */
    public static function find($file){
        $list = self::getList();
        foreach($list as $item){
            if($item->file() == $file){
                return $item;
            }
        }

        return false;
    }

    /**
     * --RU--
     * Находится ли данный файл в автозапуске
     * @var string $file Путь к исполняемому файлу
     * @return bool
     */
    public static function isExists($file){
        return self::find($file) !== false;
    }

    public static function getUserStartupDirectory(){
        return realpath(Windows::expandEnv('%APPDATA%\Microsoft\Windows\Start Menu\Programs\Startup'));
    }
    
    public static function getCommonStartupDirectory(){
        return realpath(Windows::expandEnv('%PROGRAMDATA%\Microsoft\Windows\Start Menu\Programs\Startup'));
    }
    
}