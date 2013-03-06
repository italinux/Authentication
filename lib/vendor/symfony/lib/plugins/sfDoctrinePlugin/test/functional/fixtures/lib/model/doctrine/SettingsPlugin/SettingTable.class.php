<?php


class SettingTable extends PluginSettingTable
{
    
    public static function getInstance()
    {
        return Propel_Core::getTable('Setting');
    }
}