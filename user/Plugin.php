<?php namespace CnesMeteo\User;

use App;
use Event;
use Lang;
use Backend;
use System\Classes\PluginBase;
use Illuminate\Foundation\AliasLoader;
use RainLab\User\Models\Settings as DefaultUserSettings;

class Plugin extends PluginBase
{

    public function pluginDetails()
    {
        return [
            'name'        => 'User',
            'description' => 'CnesMeteo User management.',
            'author'      => 'CnesMeteo',
            'icon'        => 'icon-user'
        ];
    }

    public function register()
    {
        $alias = AliasLoader::getInstance();
        $alias->alias('CnesMeteoAuth', 'CnesMeteo\User\Facades\CnesMeteoAuth');

        App::singleton('cnesmeteouser.auth', function() {
            return \CnesMeteo\User\Classes\AuthManager::instance();
        });

        /*
         * Apply user-based mail blocking
         */
        Event::listen('mailer.beforeSend', function($mailer, $view, $message){
            return MailBlocker::filterMessage($view, $message);
        });
    }

    public function registerComponents()
    {
        return [
            'CnesMeteo\User\Components\Session'       => 'session',
            'CnesMeteo\User\Components\ResetPassword' => 'resetPassword',
			'CnesMeteo\User\Components\Account'       => 'account',
			'CnesMeteo\User\Components\DropdownOrganizationClassroom' => 'dropdownOrgClass',	
        ];
    }

    public function registerFormWidgets()
    {
        return [
            'CnesMeteo\User\Modules\Backend\FormWidgets\OrgClassDropdown'   => 'OrgClassDropdown',
            'CnesMeteo\User\Modules\Backend\FormWidgets\ManageCoordinates'  => 'ManageCoordinates',
            'CnesMeteo\User\Modules\Backend\FormWidgets\ManageLocation'     => 'ManageLocation'
        ];
    }

    public function registerNavigation()
    {
        return [
            'user' => [
                'label'       => 'Members',
                'url'         => Backend::url('cnesmeteo/user/users'),
                'icon'        => 'icon-user',
                'permissions' => ['cnesmeteo.*'],
                'order'       => 500,

                'sideMenu' => [
                    'users' => [
                        'label'       => 'Users',
                        'icon'        => 'icon-user',
                        'url'         => Backend::url('cnesmeteo/user/users'),
                        'permissions' => ['cnesmeteo.access_users'],
                    ],
                    'organizations' => [
                        'label'       => 'Organizations',
                        'icon'        => 'icon-building-o',
                        'url'         => Backend::url('cnesmeteo/user/organizations'),
                        'permissions' => ['cnesmeteo.access_organizations'],
                    ],
                    'classrooms' => [
                        'label'       => 'Classrooms',
                        'icon'        => 'icon-graduation-cap',
                        'url'         => Backend::url('cnesmeteo/user/classrooms'),
                        'permissions' => ['cnesmeteo.access_classrooms'],
                    ],
                    'sites' => [
                        'label'       => 'Sites',
                        'icon'        => 'icon-map-marker',
                        'url'         => Backend::url('cnesmeteo/user/sites'),
                        'permissions' => ['cnesmeteo.access_sites'],
                    ],
                    'groups' => [
                        'label'       => 'Groups',
                        'icon'        => 'icon-users',
                        'url'         => Backend::url('cnesmeteo/user/groups'),
                        'permissions' => ['cnesmeteo.access_groups'],
                    ],
                    'globe' => [
                        'label'       => 'GLOBE',
                        'icon'        => 'icon-globe',
                        'url'         => Backend::url('cnesmeteo/user/certifications'),
                        'permissions' => ['cnesmeteo.access_certifications'],
                    ],
                ]

            ]
        ];
    }

    public function registerPermissions()
    {
        return [
            'cnesmeteo.access_users'            => ['tab' => 'CnesMeteo.User', 'label' => 'User - Manage Users'],
            'cnesmeteo.access_organizations'    => ['tab' => 'CnesMeteo.User', 'label' => 'User - Manage Organizations'],
            'cnesmeteo.access_classrooms'       => ['tab' => 'CnesMeteo.User', 'label' => 'User - Manage Classrooms'],
            'cnesmeteo.access_sites'            => ['tab' => 'CnesMeteo.User', 'label' => 'User - Manage Measurements Sites'],
            'cnesmeteo.access_groups'           => ['tab' => 'CnesMeteo.User', 'label' => 'User - Manage Groups'],
            'cnesmeteo.access_certifications'   => ['tab' => 'CnesMeteo.User', 'label' => 'User - Manage Certifications (GLOBE)'],
        ];
    }

    public function registerSettings()
    {
        /*
        return [
            'settings' => [
                'label'       => 'CnesMeteo.User Settings',
                'description' => 'Manage user registration settings.',
                'category'    => 'Users',
                'icon'        => 'icon-cog',
                'class'       => 'CnesMeteo\User\Models\Settings',
                'sort'        => 100
            ]
        ];
        */
    }

    public function boot()
    {


        DefaultUserSettings::extend(function($model)
        {
            /*
             * Doesn't work the variable called as a function
             * The underlaying class "call" doesn't seem to support it... yet?
             *
            $model->getDefaultProvinceOptions = function ($model) {
                return \CnesMeteo\Localization\Models\Province::getNameList($model->default_state);
            };
            */

            $model->student_register_status = true;
            $model->first_teacher_becomes_manager = true;
        });


        Event::listen('backend.form.extendFields', function($widget){

            //Extend groups controller
            if (!$widget->getController() instanceof \System\Controllers\Settings) return;
            if (!$widget->model instanceof \RainLab\User\Models\Settings) return;

            $pluginDetails = $this->pluginDetails();
            $pluginFullName = $pluginDetails['author'].'.'.$pluginDetails['name'];

            $widget->addFields([

                /*
                 * Needs the function "getDefaultProvinceOptions" to be attached to the model !!!!
                 *
                'default_province' => [
                    'label' => 'Default Province',
                    'comment' => 'When a user does not specify their location, select a default province to use.',
                    'type' => 'dropdown',
                    'tab' => 'rainlab.user::lang.settings.location_tab',
                    'depends' => 'default_state'
                ]
                */

                'student_register_status' => [
                    'span' => 'left',
                    'label' => 'Enable Student Register',
                    'comment' => 'Register for Students in the frontend.',
                    'type' => 'switch',
                    'tab' => $pluginFullName
                ],

                'first_teacher_becomes_manager' => [
                    'span' => 'right',
                    'label' => '1st Teacher becomes Manager',
                    'comment' => 'The 1st teacher of the organization becomes the organization Manager.',
                    'type' => 'switch',
                    'tab' => $pluginFullName
                ]
            ], 'primary');
        });
    }



    public function registerMailTemplates()
    {
        /*
        return [
            'rainlab.user::mail.activate' => 'Activation email sent to new users.',
            'rainlab.user::mail.welcome' => 'Welcome email sent when a user is activated.',
            'rainlab.user::mail.restore' => 'Password reset instructions for front-end users.',
            'rainlab.user::mail.new_user' => 'Sent to administrators when a new user joins.',
        ];
        */
    }


    /**
     * Register new Twig variables
     * @return array
     */
    public function registerMarkupTags()
    {
        return [
            'functions' => [
                'form_select_organization' => ['CnesMeteo\User\Models\Organization', 'formSelect'],
                'form_select_classroom' => ['CnesMeteo\User\Models\Classroom', 'formSelect'],
            ]
        ];
    }


}