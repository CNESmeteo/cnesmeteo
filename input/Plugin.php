<?php namespace CnesMeteo\Input;

use System\Classes\PluginBase;

/**
 * Input Plugin Information File
 */
class Plugin extends PluginBase
{

    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'Input',
            'description' => 'No description provided yet...',
            'author'      => 'CnesMeteo',
            'icon'        => 'icon-leaf'
        ];
    }

}
