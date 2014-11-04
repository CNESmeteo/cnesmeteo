<?php namespace CnesMeteo\Output;

use System\Classes\PluginBase;

/**
 * Output Plugin Information File
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
            'name'        => 'Output',
            'description' => 'No description provided yet...',
            'author'      => 'CnesMeteo',
            'icon'        => 'icon-leaf'
        ];
    }

}
