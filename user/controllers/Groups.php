<?php namespace CnesMeteo\User\Controllers;

use Input;
use Flash;
use BackendMenu;
use BackendAuth;
use Backend\Classes\Controller;
use CnesMeteo\User\Helpers as CnesMeteoHelpers;

class Groups extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController'
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';

    public $requiredPermissions = ['cnesmeteo.access_groups'];

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('CnesMeteo.User', 'user', 'groups');
    }

    /**
     * Add available permission fields to the Group form.
     */
    protected function formExtendFields($host)
    {
        $permissionFields = [];
        foreach (BackendAuth::listPermissions() as $permission) {

            $fieldName = 'permissions['.$permission->code.']';

            if (CnesMeteoHelpers::startsWith(strtolower($permission->code), 'cnesmeteo')){
                $fieldConfig = [
                    'label' => $permission->label,
                    'comment' => $permission->comment,
                    'type' => 'checkbox',
                ];

                if (isset($permission->tab))
                    $fieldConfig['tab'] = $permission->tab;

                $permissionFields[$fieldName] = $fieldConfig;
            }
        }

        $host->addTabFields($permissionFields);
    }
}