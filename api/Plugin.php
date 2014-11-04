<?php namespace CnesMeteo\Api;

use System\Classes\PluginBase;

/**
 * Api Plugin Information File
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
            'name'        => 'Api',
            'description' => 'No description provided yet...',
            'author'      => 'CnesMeteo',
            'icon'        => 'icon-leaf'
        ];
    }

}
