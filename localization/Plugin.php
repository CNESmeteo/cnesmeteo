<?php namespace CnesMeteo\Localization;

use App;
use Backend;
use System\Classes\PluginBase;
use Illuminate\Foundation\AliasLoader;

/**
 * Localization Plugin Information File
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
            'name'        => 'Localization',
            'description' => 'Translations. Languages & Places definitions.',
            'author'      => 'CnesMeteo',
            'icon'        => 'icon-globe'
        ];
    }



    public function registerSettings()
    {
        return [
            'location' => [
                'label'       => 'Provinces',
                'description' => 'Manage provinces (locations).',
                'category'    => 'Users',
                'icon'        => 'icon-globe',
                'url'         => Backend::url('cnesmeteo/localization/provinces'),
                'sort'        => 100
            ],
            'settings' => [
                'label'       => 'Language Settings',
                'description' => 'Manage languages.',
                'category'    => 'Users',
                'icon'        => 'icon-language',
                'url'         => Backend::url('cnesmeteo/localization/languages'),
                'sort'        => 100
            ],
        ];
    }

    /**
     * Register new Twig variables
     * @return array
     */
    public function registerMarkupTags()
    {
        return [
            'functions' => [
                'form_select_country' => ['CnesMeteo\Localization\Models\Country', 'formSelect'],
                'form_select_state' => ['CnesMeteo\Localization\Models\State', 'formSelect'],
                'form_select_province' => ['CnesMeteo\Localization\Models\Province', 'formSelect'],
            ]
        ];
    }

}
