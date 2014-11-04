<?php namespace CnesMeteo\User;


use Hash;
use RuntimeException;
use Exception;
use DateTime;
use DB;
use Input;
use BackendAuth;
use CnesMeteo\User\Models\User;
use CnesMeteo\User\Models\Group;
use CnesMeteo\User\Models\Organization;
use CnesMeteo\User\Models\Certification;
use CnesMeteo\Localization\Models\Language as CnesMeteoLanguage;
use CnesMeteo\Data\Models\Measurement as MeasurementModel;
use CnesMeteo\Data\Models\Cloudtype;
use CnesMeteo\Data\Models\Skytype;


class Helpers
{
    public static function getColumnEnumNames($table, $column, $in_index = false)
    {
        $enums = [];
        $column = DB::select("SHOW COLUMNS FROM ".$table." LIKE '".$column."'");
        if (!empty($column[0]->Type)){
            $enums = explode("','", substr($column[0]->Type, 6, -2));
        }

        if ( (!empty($enums)) && ($in_index === true) ){
            $enums = array_combine(array_values($enums), array_values($enums));
        }

        return $enums;
    }

    // --------------------------------------------------------------------------------------------------------

    public static function getUserAccessLevel($user = null)
    {
        // 0: No access     <-->    Student         <-->   ! backend.access_dashboard
        // 1: Basic         <-->    Teacher         <-->   ! cnesmeteo.access_organizations
        // 2: Medium        <-->    Manager         <-->   ! superuser
        // 3: Advanced      <-->    Administrator

        $access_level = 0;
        $access_level_1 = ['backend.access_dashboard'];
        $access_level_2 = ['cnesmeteo.access_organizations'];
        $access_level_3 = ['superuser'];

        if (empty($user)){
            $user = BackendAuth::getUser();
        }

        if (!empty($user)){
            if     ( $user->isSuperUser() )                   { $access_level = 3; }
            elseif ( $user->hasAnyAccess($access_level_2) )   { $access_level = 2; }
            elseif ( $user->hasAnyAccess($access_level_1) )   { $access_level = 1; }
        }

        return $access_level;
    }

    public static function getClassroomsRelated_for_Filtering($currentUser_backendModel = null,
                                                              $output_array_ids = false)
    {
        $classrooms = null;
        if (empty($currentUser_backendModel)){
            $currentUser_backendModel = BackendAuth::getUser();
        }
        $current_user = User::find($currentUser_backendModel->id);

        if (!empty($current_user)) {
            // Get ALL classrooms the current user belongs to
            if (!$output_array_ids) {
                $classrooms = $current_user->classrooms()->get();
            } else {
                $classrooms = $current_user->classrooms->lists('id');
            }
        }
        return $classrooms;
    }

    public static function getOrganizationsRelated_for_Filtering($currentUser_backendModel = null,
                                                                 $output_array_ids = false)
    {
        $organizations = null;
        if (empty($currentUser_backendModel)){
            $currentUser_backendModel = BackendAuth::getUser();
        }
        $current_user = User::find($currentUser_backendModel->id);

        if (!empty($current_user)) {
            // Get ALL classrooms the current user belongs to
            if (!$output_array_ids) {
                $organizations = $current_user->organizations()->get();
            } else {
                $organizations = $current_user->organizations->lists('id');
            }
        }
        return $organizations;
    }

    public static function getClassroomsUserIDs_Filtered($classrooms, $currentUser_ID, $currentUser_accessLevel)
    {
        $user_ids = [$currentUser_ID];
        foreach($classrooms as $classroom)
        {
            // 1) Get ALL users of the classroom
            $classroom_users = $classroom->users()->get();
            $classroom_user_ids = [];

            foreach($classroom_users as $classroom_user)
            {
                // 2) Only users with lower access_level !!! (Students)
                $classroom_user_access_level = Helpers::getUserAccessLevel($classroom_user);
                if ($classroom_user_access_level < $currentUser_accessLevel) {
                    $classroom_user_ids[] = $classroom_user->id;
                }
            }

            if (!empty($classroom_user_ids)) {
                // 3) Append the users of the classrooms to all users related to this user
                $user_ids = array_merge($user_ids, $classroom_user_ids);
            }
        }
        return $user_ids;
    }

    public static function getOrganizationUserIDs_Filtered($organizations, $currentUser_ID, $currentUser_accessLevel)
    {
        return Helpers::getClassroomsUserIDs_Filtered($organizations, $currentUser_ID, $currentUser_accessLevel);
    }

    public static function getClassroomsSitesIDs_Filtered($classrooms)
    {
        $sites_ids = [];
        foreach($classrooms as $classroom)
        {
            // 1) Get ALL sites of the classroom
            $classroom_sites_ids = $classroom->sites()->lists('id');

            if (!empty($classroom_sites_ids)) {
                // 3) Append the users of the classrooms to all users related to this user
                $sites_ids = array_merge($sites_ids, $classroom_sites_ids);
            }
        }
        return $sites_ids;
    }

    public static function getOrganizationsClassroomsIDs_Filtered($organizations)
    {
        $classroom_ids = [];
        foreach($organizations as $organization)
        {
            // 1) Get ALL sites of the classroom
            $organization_classrooms_ids = $organization->classrooms()->lists('id');

            if (!empty($organization_classrooms_ids)) {
                // 3) Append the users of the classrooms to all users related to this user
                $classroom_ids = array_merge($classroom_ids, $organization_classrooms_ids);
            }
        }
        return $classroom_ids;
    }

    public static function getOrganizationsSitesIDs_Filtered($organizations)
    {
        return Helpers::getClassroomsSitesIDs_Filtered($organizations);
    }



    // --------------------------------------------------------------------------------------------------------


    public static function getOrganizationsList()
    {
        return Organization::all()->lists('name', 'id');
    }

    public static function makeUserList($users)
    {
        $users_list = [];

        if (!empty($users)){
            foreach($users as $user){
                // TODO: Add a swith for the diferent options to format the user name!!! (add a input param for the function)
                $name = Helpers::formatUser_LastName_FirstName($user);
                $users_list[$user->id] = $name;
            }
        }

        //dd($users_list); // DEBUG
        return $users_list;
    }

    // return: "Last Name, First Name"
    public static function formatUser_LastName_FirstName($user)
    {
        //return ( (!empty($user)) ? $user->last_name.', '.$user->first_name : '');

        $result = '';
        if (!empty($user)){
            if ( (!empty($user->last_name)) && (!empty($user->last_name)) ){
                $result = $user->last_name.', '.$user->first_name;
            }
            elseif ( (empty($user->last_name)) && (!empty($user->last_name)) ){
                $result = $user->first_name;
            }
            elseif ( (!empty($user->last_name)) && (empty($user->last_name)) ){
                $result = $user->last_name;
            }
        }

        return $result;
    }

    public static function getUsersList()
    {
        $users = User::all();
        return Helpers::makeUserList($users);
    }

    public static function getCertificatedUsersList($require_certification_active = false)
    {
        $users = null;

        // TODO
        // Get all certifications (optional: where "certified = true" if input flag)
        // Get the info of the users for all certifications (user_id column)
        // Use eager loading to load the "User" related data!!!

        if ($require_certification_active){

        }else{

        }

        return Helpers::makeUserList($users);
    }

    public static function getUnCertificatedUsersList()
    {
        $users = User::leftJoin('cnesmeteo_user_certifications', function($join) {
                $join->on('cnesmeteo_user_users.id', '=', 'cnesmeteo_user_certifications.user_id');
            })
            ->whereNull('cnesmeteo_user_certifications.user_id')
            ->get(['cnesmeteo_user_users.id as id',
                   'cnesmeteo_user_users.first_name',
                   'cnesmeteo_user_users.last_name']);

        //dd($users->toArray()); // DEBUG

        return Helpers::makeUserList($users);

        /*
         * USE THIS IN CASE THE ABOVE CODE DOESN'T WORK RIGHT !!!!!!!!!!!!!
         *
        $users_list = [];
        $users = User::all();
        if (!empty($users)){
            foreach ($users as $user){
                if ($user->dontHaveCertification()){
                    $name = Helpers::formatUser_LastName_FirstName($user);
                    $users_list[$user->id] = $name;
                }
            }
        }
        return $users_list;
        */
    }



    /**
     * List all available user groups
     */
    public static function getGroupsList($keyValue = null, $fieldName = null)
    {
        return Group::all()->lists('name', 'id');
    }

    public static function getOrganizationsTypes()
    {
        $enum_values = Helpers::getColumnEnumNames('cnesmeteo_user_organizations', 'type', true);
        // TODO -> Localization of the names!!!

        return $enum_values;
    }

    public static function getLanguagesList()
    {
        $languages = CnesMeteoLanguage::where('enabled', '=', true)->lists('name', 'id');
        // TODO -> Localization of the names!!!

        return $languages;
    }

    public static function getTimezonesList()
    {
        return DateTimeZone::listIdentifiers(DateTimeZone::ALL);
    }

    public static function getWindDirectionsList()
    {
        return[
            'N'     => Helpers::getWindDirectionTranslatedName('N'),
            'NE'    => Helpers::getWindDirectionTranslatedName('NE'),
            'E'     => Helpers::getWindDirectionTranslatedName('E'),
            'SE'    => Helpers::getWindDirectionTranslatedName('SE'),
            'S'     => Helpers::getWindDirectionTranslatedName('S'),
            'SW'    => Helpers::getWindDirectionTranslatedName('SW'),
            'W'     => Helpers::getWindDirectionTranslatedName('E'),
            'NW'    => Helpers::getWindDirectionTranslatedName('NW')
        ];
    }

    public static function getWindDirectionTranslatedName($wind_direction_code)
    {
        // TODO -> Localization of the wind directions names !!!!
        $output = 'North';
        switch(strtoupper($wind_direction_code))
        {
            case 'N':   $output = 'North';          break;
            case 'NE':  $output = 'North East';     break;
            case 'E':   $output = 'East';           break;
            case 'SE':  $output = 'South East';     break;
            case 'S':   $output = 'South';          break;
            case 'SW':  $output = 'South West';     break;
            case 'W':   $output = 'West';           break;
            case 'NW':  $output = 'North West';     break;
            case 'G':   $output = 'General';        break; // For Cloud Observation Direction case !!!
        }
        return $output;
    }

    public static function getSkyTypeEnumNamesList()
    {
        return[
            'sunny'     => Helpers::getSkyTypeTranslatedEnumName('Sunny'),
            'cloudy'    => Helpers::getSkyTypeTranslatedEnumName('Cloudy'),
            'rainy'     => Helpers::getSkyTypeTranslatedEnumName('Rainy')
            // TODO -> Add the rest possible sky states !!!
        ];
    }

    public static function getSkyTypeTranslatedEnumName($sky_type_enum_name)
    {
        // TODO -> Localization of the Sky Types enum names !!!!
        $output = 'Sunny';
        switch(strtolower($sky_type_enum_name))
        {
            case 'sunny':   $output = 'Sunny';     break;
            case 'cloudy':  $output = 'Cloudy';    break;
            case 'rainy':   $output = 'Rainy';     break;
        }
        return $output;
    }

    public static function getSkyTypeValuesList()
    {
        return Skytype::all()->lists('name', 'id');
    }



    public static function getCloudTypeEnumNamesList()
    {
        return[
            'cirrocumulus'  => Helpers::getCloudTypeTranslatedEnumName('cirrocumulus'),
            'cirrostratus'  => Helpers::getCloudTypeTranslatedEnumName('cirrostratus'),
            'cirrus'        => Helpers::getCloudTypeTranslatedEnumName('cirrus'),
            'altocumulus'   => Helpers::getCloudTypeTranslatedEnumName('altocumulus'),
            'altostratus'   => Helpers::getCloudTypeTranslatedEnumName('altostratus'),
            'cumulus'       => Helpers::getCloudTypeTranslatedEnumName('cumulus'),
            'stratus'       => Helpers::getCloudTypeTranslatedEnumName('stratus'),
            'stratocumulus' => Helpers::getCloudTypeTranslatedEnumName('stratocumulus'),
            'nimbostratus'  => Helpers::getCloudTypeTranslatedEnumName('nimbostratus'),
            'cumulonimbus'  => Helpers::getCloudTypeTranslatedEnumName('cumulonimbus')
        ];
    }

    public static function getCloudTypeTranslatedEnumName($cloud_type_enum_name)
    {
        // TODO -> Localization of the Cloud Types enum names !!!!
        $output = 'Cumulus';
        switch(strtolower($cloud_type_enum_name))
        {
            case 'cirrocumulus':    $output = 'Cirrocumulus';   break;
            case 'cirrostratus':    $output = 'Cirrostratus';   break;
            case 'cirrus':          $output = 'Cirrus';         break;
            case 'altocumulus':     $output = 'Altocumulus';    break;
            case 'altostratus':     $output = 'Altostratus';    break;
            case 'cumulus':         $output = 'Cumulus';        break;
            case 'stratus':         $output = 'Stratus';        break;
            case 'stratocumulus':   $output = 'Stratocumulus';  break;
            case 'nimbostratus':    $output = 'Nimbostratus';   break;
            case 'cumulonimbus':    $output = 'Cumulonimbus';   break;
        }
        return $output;
    }

    public static function getCloudTypesListTranslated()
    {
        $output = [];
        $clouds_TYPES = Cloudtype::all()->lists('type', 'id');
        foreach($clouds_TYPES as $clouds_TYPE_Key => $clouds_TYPE_Value){
            $output[$clouds_TYPE_Key]['id'] = $clouds_TYPE_Key;
            $output[$clouds_TYPE_Key]['type'] = $clouds_TYPE_Value;
            $output[$clouds_TYPE_Key]['name'] = Helpers::getCloudTypeTranslatedEnumName($clouds_TYPE_Value);
        }
        return $output;
    }

    public static function getListCloudDirections()
    {
        $output = [];
        $list = Helpers::getColumnEnumNames('cnesmeteo_data_clouds','direction');
        foreach($list as $enumValue){
            $output[$enumValue] = Helpers::getWindDirectionTranslatedName($enumValue);
        }
        return $output;
    }



    public static function getSkyStateTranslatedName($enumValue)
    {
        // TODO -> Localization of the Sky States enum names !!!!
        $output = 'Clear';
        switch(strtolower($enumValue))
        {
            case 'clear':       $output = 'Clear';      break;
            case 'visible':     $output = 'Visible';    break;
            case 'obscured':    $output = 'Obscured';   break;
        }
        return $output;
    }
    public static function getSkyStatesList()
    {
        $output = [];
        $list = Helpers::getColumnEnumNames('cnesmeteo_data_clouds','sky_state');
        foreach($list as $enumValue){
            $output[$enumValue] = Helpers::getSkyStateTranslatedName($enumValue);
        }
        return $output;
    }

    public static function getCloudsCoverPercentTranslatedName($enumValue)
    {
        // TODO -> Localization of the Clouds Cover Percent enum names !!!!
        $output = 'None';
        switch(strtolower($enumValue))
        {
            case 'none':        $output = 'None';       break;
            case 'clear':       $output = 'Clear';      break;
            case 'isolated':    $output = 'Isolated';   break;
            case 'scattered':   $output = 'Scattered';  break;
            case 'broken':      $output = 'Broken';     break;
            case 'overcast':    $output = 'Overcast';   break;
        }
        return $output;
    }
    public static function getCloudsCoverPercentList()
    {
        $output = [];
        $list = Helpers::getColumnEnumNames('cnesmeteo_data_clouds','clouds_cover_percent');
        foreach($list as $enumValue){
            $output[$enumValue] = Helpers::getCloudsCoverPercentTranslatedName($enumValue);
        }
        return $output;
    }

    public static function getContrailsVisibilityTranslatedName($enumValue)
    {
        // TODO -> Localization of the Contrails Visibility enum names !!!!
        $output = 'None';
        switch(strtolower($enumValue))
        {
            case 'none':                        $output = 'None';                       break;
            case 'short_lived':                 $output = 'Short lived';                break;
            case 'persistent_non_spreading':    $output = 'Persistent non spreading';   break;
            case 'persistent_spreading':        $output = 'Persistent Spreading';       break;
        }
        return $output;
    }
    public static function getContrailsVisibilityList()
    {
        $output = [];
        $list = Helpers::getColumnEnumNames('cnesmeteo_data_clouds','contrails_visibility');
        foreach($list as $enumValue){
            $output[$enumValue] = Helpers::getContrailsVisibilityTranslatedName($enumValue);
        }
        return $output;
    }

    public static function getContrailsPercentTranslatedName($enumValue)
    {
        // TODO -> Localization of the Contrails Percent enum names !!!!
        $output = '0-10%';
        switch(strtolower($enumValue))
        {
            case '0_10':    $output = '0-10%';      break;
            case '10_25':   $output = '10-25%';     break;
            case '25_50':   $output = '25-50%';     break;
            case '50_100':  $output = '50-100%';    break;
        }
        return $output;
    }
    public static function getContrailsPercentList()
    {
        $output = [];
        $list = Helpers::getColumnEnumNames('cnesmeteo_data_clouds','contrails_percent');
        foreach($list as $enumValue){
            $output[$enumValue] = Helpers::getContrailsPercentTranslatedName($enumValue);
        }
        return $output;
    }


    public static function getObservedSkyColorTranslatedName($enumValue)
    {
        // TODO -> Localization of the Observed Sky Color enum names !!!!
        $output = 'Blue';
        switch(strtolower($enumValue))
        {
            case 'deep_blue':   $output = 'Deep blue';      break;
            case 'blue':        $output = 'Blue';           break;
            case 'light_blue':  $output = 'Light blue';     break;
            case 'pale_blue':   $output = 'Pale blue';      break;
            case 'milky':       $output = 'Milky';          break;
        }
        return $output;
    }
    public static function getObservedSkyColorsList()
    {
        $output = [];
        $list = Helpers::getColumnEnumNames('cnesmeteo_data_aerosols','observed_sky_color');
        foreach($list as $enumValue){
            $output[$enumValue] = Helpers::getObservedSkyColorTranslatedName($enumValue);
        }
        return $output;
    }

    public static function getObservedSkyClarityTranslatedName($enumValue)
    {
        // TODO -> Localization of the Observed Sky Clarity enum names !!!!
        $output = 'Clear';
        switch(strtolower($enumValue))
        {
            case 'unusually_clear': $output = 'Unusually clear';    break;
            case 'clear':           $output = 'Clear';              break;
            case 'somewhat_hazy':   $output = 'Somewhat hazy';      break;
            case 'very_hazy':       $output = 'Very hazy';          break;
            case 'extremely_hazy':  $output = 'Extremely hazy';     break;
        }
        return $output;
    }
    public static function getObservedSkyClarityList()
    {
        $output = [];
        $list = Helpers::getColumnEnumNames('cnesmeteo_data_aerosols','observed_sky_clarity');
        foreach($list as $enumValue){
            $output[$enumValue] = Helpers::getObservedSkyClarityTranslatedName($enumValue);
        }
        return $output;
    }

    /*
    * -----------------------------------------------------------------------------------------------------------------
    * -----------------------------------------------------------------------------------------------------------------
    */


    public static function processMeasurementForm_BeforeCreate($controller, $model)
    {
        // Reset temp var:
        $controller->new_measurement_id = 0;

        // Get form data: "user_id", "organization_id", "site_id"
        $user_id = intval(Input::get($controller->inputsGroupedBy)['user_id']);
        $organization_id = intval(Input::get($controller->inputsGroupedBy)['organization_id']);
        $site_id = intval(Input::get($controller->inputsGroupedBy)['site_id']);
        $comments = Input::get($controller->inputGroupBaseName.'.comments');
        $measured_at_timestampt = Input::get($controller->inputGroupBaseName.'.measured_at');

        // 1) Create the measurement object (datable row after saving) with the meteo data:
        if ( (!empty($user_id)) && ($user_id > 0)
            && (!empty($organization_id)) && ($organization_id > 0)
            && (!empty($site_id)) && ($site_id > 0))
        {
            $measurement_obj = new MeasurementModel();

            $measurement_obj->type = $controller->measurementType;
            $measurement_obj->user_id = $user_id;
            $measurement_obj->organization_id = $organization_id;
            $measurement_obj->site_id = $site_id;
            $measurement_obj->comments = $comments;
            $measurement_obj->measured_at = $measured_at_timestampt;

            // Save the new measurement
            $measurement_obj->save();

            // Set the new measurement_id from the just created row into the Current model:
            $model->measurement_id = $measurement_obj->id;
            $controller->new_measurement_id = $measurement_obj->id;
        }
        return $model;
    }

    public static function processMeasurementForm_AfterCreate($controller, $model)
    {
        if ($controller->new_measurement_id > 0)
        {
            $model->measurement_id = $controller->new_measurement_id;
            $model->save();
        }
        return $model;
    }

    public static function processMeasurementForm_BeforeUpdate($controller, $model)
    {
        // Get form data: "user_id", "organization_id", "site_id"
        $user_id = intval(Input::get($controller->inputsGroupedBy)['user_id']);
        $organization_id = intval(Input::get($controller->inputsGroupedBy)['organization_id']);
        $site_id = intval(Input::get($controller->inputsGroupedBy)['site_id']);
        $comments = Input::get($controller->inputGroupBaseName.'.comments');
        $measured_at_timestampt = Input::get($controller->inputGroupBaseName.'.measured_at');

        if ( (!empty($user_id)) && ($user_id > 0)
            && (!empty($organization_id)) && ($organization_id > 0)
            && (!empty($site_id)) && ($site_id > 0))
        {
            $measurement_obj = MeasurementModel::findOrFail($model->measurement_id);

            if (!empty($measurement_obj))
            {
                $measurement_obj->type = $controller->measurementType;
                $measurement_obj->user_id = $user_id;
                $measurement_obj->organization_id = $organization_id;
                $measurement_obj->site_id = $site_id;
                $measurement_obj->comments = $comments;
                $measurement_obj->measured_at = $measured_at_timestampt;

                // Save the new measurement
                $measurement_obj->save();
            }
        }
        return $model;
    }

    public static function processMeasurementForm_AfterDelete(&$controller, $model)
    {
        if ( (!empty($model->measurement_id)) && ($model->measurement_id > 0) )
        {
            // Find parent measurement object:
            $measurement_obj = MeasurementModel::findOrFail($model->measurement_id);

            // Delete parent measurement object (datatable row):
            $measurement_obj->delete();
        }
        return $model;
    }

    public static function processCloudsGroups($cloudgroups)
    {
        $cloudgroups_final = [];

        if ( (!empty($cloudgroups)) && (count($cloudgroups) > 0) )
        {
            foreach($cloudgroups as $cloud_measurement_key => $cloud_measurement_value)
            {
                if (!empty($cloud_measurement_value)){

                    // 1) Parse cloud data
                    $cloud_measurement_key_array = explode("_", $cloud_measurement_key);
                    $cloud_observation_direction = $cloud_measurement_key_array[0];
                    $cloud_type = $cloud_measurement_key_array[1]; // "CloudTypesEnums" + "ObserverName"

                    $value = null;
                    if (!empty($cloud_measurement_key_array[2])){
                        $value = $cloud_measurement_key_array[2]; // cloud_id
                    }else{
                        $value = $cloud_measurement_value;
                    }

                    // 2) Create cloud data FORMATTED:
                    $cloudgroups_final[$cloud_observation_direction][$cloud_type] = $value;
                }
            }
        }
        return $cloudgroups_final;
    }



    /*
     * -----------------------------------------------------------------------------------------------------------------
     * -----------------------------------------------------------------------------------------------------------------
     */

    public static function startsWith($haystack, $needle)
    {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }

    public static function endsWith($haystack, $needle)
    {
        $length = strlen($needle);
        if ($length == 0) {
            return true;
        }

        return (substr($haystack, -$length) === $needle);
    }

    /**
     * Generate a random string
     * @return string
     */
    public static function getRandomString($length = 42)
    {
        /*
         * Use OpenSSL (if available)
         */
        if (function_exists('openssl_random_pseudo_bytes')) {
            $bytes = openssl_random_pseudo_bytes($length * 2);

            if ($bytes === false)
                throw new RuntimeException('Unable to generate a random string');

            return substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $length);
        }

        $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        return substr(str_shuffle(str_repeat($pool, 5)), 0, $length);
    }
}