<?php namespace CnesMeteo\User\Modules\Backend\FormWidgets;

use Backend\Classes\WidgetBase;
use CnesMeteo\User\Helpers;
use CnesMeteo\User\Models\Organization as CnesMeteoOrganization;
use CnesMeteo\User\Models\User as CnesMeteoUser;
use CnesMeteo\User\Models\Site as CnesMeteoSite;
use Input;
use Request;
use Cache;
use Lang;

class OrgUserSiteDropdown extends WidgetBase
{
    public $defaultAlias = "OrgUserSite";
    public $form_group = 'Measurement'; // default
    public $selected_organization_id = null;    // Set when creating the widget with the model values
    public $selected_user_id = null;            // Set when creating the widget with the model values
    public $selected_site_id = null;            // Set when creating the widget with the model values

    public function widgetDetails()
    {
        return [
            'name'        => 'Dropdown Organization -> Users + Sites',
            'description' => 'Select a organization and one of their associated users / sites'
        ];
    }

    public function render(){
        $this->prepareVars();
        return $this->makePartial('OrgUserSite');
    }

    public function prepareVars(){
        $this->vars['selected_organization_id'] = $this->selected_organization_id;
        $this->vars['selected_user_id'] = $this->selected_user_id;
        $this->vars['selected_site_id'] = $this->selected_site_id;

        $this->vars['organizations'] = $this->getOrganizationOptions();

        $first_organization_id = null;
        if (!empty($this->vars['organizations'])){

            $first_organization_id = array_keys($this->vars['organizations'])[0];

            if (!empty($this->selected_organization_id)){
                $first_organization_id = $this->selected_organization_id;
            }

        }

        $this->vars['users'] = $this->getUserOptions($first_organization_id);
        $this->vars['sites'] = $this->getSiteOptions($first_organization_id);
    }

    public function loadAssets(){
        //$this->addJs('js/OrgClassDropdown.js');
    }

    public function defineProperties()
    {
        return [
            'organization' => [
                'title' => 'Organization',
                'type' => 'dropdown',
                'placeholder' => 'Select a organization'
            ],
            'users' => [
                'title' => 'User',
                'type' => 'dropdown',
                'placeholder' => 'Select a user',
                'depends' => ['organization']
            ],
            'sites' => [
                'title' => 'Site',
                'type' => 'dropdown',
                'placeholder' => 'Select a site',
                'depends' => ['organization']
            ]
        ];
    }

    public function getOrganizationOptions()
    {
        return CnesMeteoOrganization::all()->lists('name', 'id');
    }

    public function getUserOptions($organization_id = null)
    {
        $users = [];
        if (!empty($organization_id) && (intval($organization_id) > 0)){

            $usersData = CnesMeteoOrganization::find($organization_id)->users()->get();

            /*
             * // TODO -> Fix for speed improvement??
            $usersData = CnesMeteoUser::leftJoin('cnesmeteo_user_organizations_users', 'cnesmeteo_user_organizations_users.organization_id', '=', $organization_id)
                ->leftJoin('cnesmeteo_user_users', 'cnesmeteo_user_users.id', '=', 'cnesmeteo_user_organizations_users.user_id')
                ->select(array( 'cnesmeteo_user_users.id as userID',
                                'cnesmeteo_user_users.last_name as userLastName',
                                'cnesmeteo_user_users.first_name as userFirstName'))
                ->get();
            */

            foreach($usersData as $userData){
                $users[ $userData->id ] = Helpers::formatUser_LastName_FirstName($userData);
            }
        }
        return $users;
    }

    public function getSiteOptions($organization_id = null)
    {
        $sites = [];
        if (!empty($organization_id)){
            $sites = CnesMeteoSite::where('organization_id', '=', $organization_id)->lists('name', 'id');
        }
        return $sites;
    }

    public function onOrganizationChange()
    {
        $this->vars['users'] = [];
        $this->vars['sites'] = [];
        $organization_id = intval(Input::get($this->form_group)['organization_id']);

        if (!empty($organization_id)){
            $this->vars['users'] = $this->getUserOptions($organization_id);
            $this->vars['sites'] = $this->getSiteOptions($organization_id);
        }

        // render HTML for the dropdown options using a Twig partial:
        $rendered_html_users = $this->makePartial('usersDropdownOptions');
        $rendered_html_sites = $this->makePartial('sitesDropdownOptions');

        return ['#Form-form-field-'.$this->form_group.'-user_id' => $rendered_html_users,
                '#Form-form-field-'.$this->form_group.'-site_id' => $rendered_html_sites];
    }
}