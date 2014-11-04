<?php namespace CnesMeteo\Data;

use Backend;
use System\Classes\PluginBase;

/**
 * Data Plugin Information File
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
            'name'        => 'Data',
            'description' => 'Meteorological data',
            'author'      => 'CnesMeteo',
            'icon'        => 'icon-database'
        ];
    }

    public function registerComponents()
    {
        return [
            'CnesMeteo\Data\Components\InputData' => 'InputData',
        ];
    }

    public function registerFormWidgets()
    {
        /*
        return [
            'CnesMeteo\User\Modules\Backend\FormWidgets\OrgClassDropdown'   => 'OrgClassDropdown',
            'CnesMeteo\User\Modules\Backend\FormWidgets\OrgUserSiteWidget'   => 'OrgUserSiteWidget'
        ];
        */
    }

    public function registerNavigation()
    {
        return [
            'data' => [
                'label'       => 'Data',
                'url'         => Backend::url('cnesmeteo/data/measurements'),
                'icon'        => 'icon-database',
                'permissions' => ['cnesmeteo.*'],
                'order'       => 500,

                'sideMenu' => [
                    'measurements' => [
                        'label'       => 'Measurements',
                        'icon'        => 'icon-database',
                        'url'         => Backend::url('cnesmeteo/data/measurements'),
                        'permissions' => ['cnesmeteo.access_data_measurements'],
                    ],
                    'temperatures' => [
                        'label'       => 'Temperatures',
                        'icon'        => 'icon-fire',
                        'url'         => Backend::url('cnesmeteo/data/temperatures'),
                        'permissions' => ['cnesmeteo.access_data_temperatures'],
                    ],
                    'pressures' => [
                        'label'       => 'Pressures',
                        'icon'        => 'icon-tachometer',
                        'url'         => Backend::url('cnesmeteo/data/pressures'),
                        'permissions' => ['cnesmeteo.access_data_pressures'],
                    ],
                    'precipitations' => [
                        'label'       => 'Precipitations',
                        'icon'        => 'icon-umbrella',
                        'url'         => Backend::url('cnesmeteo/data/precipitations'),
                        'permissions' => ['cnesmeteo.access_data_precipitations'],
                    ],
                    'snows' => [
                        'label'       => 'Snows',
                        'icon'        => 'icon-asterisk',
                        'url'         => Backend::url('cnesmeteo/data/snows'),
                        'permissions' => ['cnesmeteo.access_data_snows'],
                    ],
                    'humidities' => [
                        'label'       => 'Humidity',
                        'icon'        => 'icon-tint',
                        'url'         => Backend::url('cnesmeteo/data/humidities'),
                        'permissions' => ['cnesmeteo.access_data_humidities'],
                    ],
                    'windforces' => [
                        'label'       => 'Wind Forces',
                        'icon'        => 'icon-signal',
                        'url'         => Backend::url('cnesmeteo/data/windforces'),
                        'permissions' => ['cnesmeteo.access_data_windforces'],
                    ],
                    'winddirections' => [
                        'label'       => 'Wind Directions',
                        'icon'        => 'icon-compass',
                        'url'         => Backend::url('cnesmeteo/data/winddirections'),
                        'permissions' => ['cnesmeteo.access_data_winddirections'],
                    ],
                    'skies' => [
                        'label'       => 'Skies',
                        'icon'        => 'icon-sun-o',
                        'url'         => Backend::url('cnesmeteo/data/skies'),
                        'permissions' => ['cnesmeteo.access_data_skies'],
                    ],
                    'skytypes' => [
                        'label'       => 'Sky Types',
                        'icon'        => 'icon-tags',
                        'url'         => Backend::url('cnesmeteo/data/skytypes'),
                        'permissions' => ['cnesmeteo.access_data_skytypes'],
                    ],
                    'clouds' => [
                        'label'       => 'Clouds',
                        'icon'        => 'icon-cloud',
                        'url'         => Backend::url('cnesmeteo/data/clouds'),
                        'permissions' => ['cnesmeteo.access_data_clouds'],
                    ],
                    'photometers' => [
                        'label'       => 'Photometers',
                        'icon'        => 'icon-tablet',
                        'url'         => Backend::url('cnesmeteo/data/photometers'),
                        'permissions' => ['cnesmeteo.access_data_photometers'],
                    ],
                    'aerosols' => [
                        'label'       => 'Aerosols',
                        'icon'        => 'icon-shield',
                        'url'         => Backend::url('cnesmeteo/data/aerosols'),
                        'permissions' => ['cnesmeteo.access_data_aerosols'],
                    ]
                    /*,
                    'globe' => [
                        'label'       => 'GLOBE',
                        'icon'        => 'icon-globe',
                        'url'         => Backend::url('cnesmeteo/data/globe'),
                        'permissions' => ['cnesmeteo.access_data_globe'],
                    ]
                    */
                ]
            ]
        ];
    }

    public function registerPermissions()
    {
        return [
            'cnesmeteo.access_data_measurements'    => ['tab' => 'CnesMeteo.Data', 'label' => 'Data - Manage All Data'],
            'cnesmeteo.access_data_temperatures'    => ['tab' => 'CnesMeteo.Data', 'label' => 'Data - Manage Temperatures'],
            'cnesmeteo.access_data_pressures'       => ['tab' => 'CnesMeteo.Data', 'label' => 'Data - Manage Pressures'],
            'cnesmeteo.access_data_precipitations'  => ['tab' => 'CnesMeteo.Data', 'label' => 'Data - Manage Precipitations'],
            'cnesmeteo.access_data_snows'           => ['tab' => 'CnesMeteo.Data', 'label' => 'Data - Manage Snows'],
            'cnesmeteo.access_data_humidities'      => ['tab' => 'CnesMeteo.Data', 'label' => 'Data - Manage Humidity'],
            'cnesmeteo.access_data_windforces'      => ['tab' => 'CnesMeteo.Data', 'label' => 'Data - Manage Wind Force'],
            'cnesmeteo.access_data_winddirections'  => ['tab' => 'CnesMeteo.Data', 'label' => 'Data - Manage Wind Direction'],
            'cnesmeteo.access_data_skies'           => ['tab' => 'CnesMeteo.Data', 'label' => 'Data - Manage Skies'],
            'cnesmeteo.access_data_clouds'          => ['tab' => 'CnesMeteo.Data', 'label' => 'Data - Manage Clouds'],
            'cnesmeteo.access_data_skytypes'        => ['tab' => 'CnesMeteo.Data', 'label' => 'Data - Manage Sky Types'],
            //'cnesmeteo.access_data_cloudtypes'      => ['tab' => 'CnesMeteo.Data', 'label' => 'Data - Manage Cloud Types'],
            'cnesmeteo.send_data_globe'             => ['tab' => 'CnesMeteo.Data', 'label' => 'Data - Send to GLOBE'],
            'cnesmeteo.access_data_globe'           => ['tab' => 'CnesMeteo.Data', 'label' => 'Data - Manage Data in GLOBE'],
            'cnesmeteo.access_data_aerosols'        => ['tab' => 'CnesMeteo.Data', 'label' => 'Data - Manage Aerosols'],
            'cnesmeteo.access_data_photometers'     => ['tab' => 'CnesMeteo.Data', 'label' => 'Data - Manage Photometers'],
        ];
    }

    public function registerSettings()
    {
        /*
        return [
            'settings' => [
                'label'       => 'Meteo Data Settings',
                'description' => 'Configure the settings for the meteorological data.',
                'category'    => 'Data',
                'icon'        => 'icon-cog',
                'class'       => 'CnesMeteo\Data\Models\Settings',
                'sort'        => 100
            ]
        ];
        */
    }

}
