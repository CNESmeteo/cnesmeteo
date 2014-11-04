<?php namespace CnesMeteo\User\Updates;

use October\Rain\Database\Updates\Seeder;
use CnesMeteo\User\Models\Group;
use Backend\Models\UserGroup as BackendUserGroup;
use RainLab\User\Models\Settings as RainLabUserSettings;

class SeedAllTables extends Seeder
{

    public function run()
    {
        /*
         * ------------------------------------
         * GROUPS (with predefined permissions)
         * ------------------------------------
         */
        // TODO -> Update permissions list when needed!!!

        $datetime_now = new \DateTime;
        Group::insert([
            
            /*
             * Administrator
             */
            ["name" => "Administrator",
                "description" => "Manages everything",
                "permissions" => '{"superuser":1}',
                'created_at' => $datetime_now,
                'updated_at' => $datetime_now],

            /*
             * Manager
             */
            ["name" => "Manager",
                "description" => "Manages Organizations and GLOBE certifications",
                "permissions" => '{"cnesmeteo.access_data_cloudtypes":"1","cnesmeteo.send_data_globe":"1","cnesmeteo.access_data_globe":"1","cnesmeteo.access_data_skytypes":"1","cnesmeteo.access_data_clouds":"1","cnesmeteo.access_data_winddirections":"1","cnesmeteo.access_data_skies":"1","cnesmeteo.access_data_aerosols":"1","cnesmeteo.access_data_photometers":"1","cnesmeteo.access_data_windforces":"1","cnesmeteo.access_data_humidities":"1","cnesmeteo.access_data_precipitations":"1","cnesmeteo.access_data_snows":"1","cnesmeteo.access_data_pressures":"1","cnesmeteo.access_data_temperatures":"1","cnesmeteo.access_data_measurements":"1","cnesmeteo.access_groups":"0","cnesmeteo.access_certifications":"0","cnesmeteo.access_sites":"1","cnesmeteo.access_classrooms":"1","cnesmeteo.access_users":"1","cnesmeteo.access_organizations":"1","backend.access_dashboard":"1","cnesmeteo":{"access_data_cloudtypes":0,"send_data_globe":0,"access_data_globe":0,"access_data_skytypes":0,"access_data_clouds":0,"access_data_winddirections":0,"access_data_skies":0,"access_data_aerosols":0,"access_data_photometers":0,"access_groups":0,"access_certifications":0,"access_sites":0,"access_classrooms":0,"access_users":0,"access_organizations":0,"access_data_windforces":0,"access_data_humidities":0,"access_data_precipitations":0,"access_data_snows":0,"access_data_pressures":0,"access_data_temperatures":0,"access_data_measurements":0},"cms":{"manage_layouts":0,"manage_partials":0,"manage_themes":0,"manage_pages":0,"manage_assets":0,"manage_content":0},"backend":{"manage_users":0,"access_dashboard":0},"system":{"manage_settings":0,"manage_updates":0,"manage_mail_templates":0}}',
                'created_at' => $datetime_now,
                'updated_at' => $datetime_now],

            /*
             * Teacher
             */
            ["name" => "Teacher",
                "description" => "Manages Classrooms users and data",
                "permissions" => '{"cnesmeteo.access_data_cloudtypes":"1","cnesmeteo.send_data_globe":"1","cnesmeteo.access_data_globe":"1","cnesmeteo.access_data_skytypes":"1","cnesmeteo.access_data_clouds":"1","cnesmeteo.access_data_winddirections":"1","cnesmeteo.access_data_skies":"1","cnesmeteo.access_data_aerosols":"1","cnesmeteo.access_data_photometers":"1","cnesmeteo.access_data_windforces":"1","cnesmeteo.access_data_humidities":"1","cnesmeteo.access_data_precipitations":"1","cnesmeteo.access_data_snows":"1","cnesmeteo.access_data_pressures":"1","cnesmeteo.access_data_temperatures":"1","cnesmeteo.access_data_measurements":"1","cnesmeteo.access_users":"1","backend.access_dashboard":"1","cnesmeteo":{"access_data_cloudtypes":0,"send_data_globe":0,"access_data_globe":0,"access_data_skytypes":0,"access_data_clouds":0,"access_data_winddirections":0,"access_data_skies":0,"access_data_aerosols":0,"access_data_photometers":0,"access_groups":0,"access_certifications":0,"access_sites":1,"access_classrooms":1,"access_users":0,"access_organizations":0,"access_data_windforces":0,"access_data_humidities":0,"access_data_precipitations":0,"access_data_snows":0,"access_data_pressures":0,"access_data_temperatures":0,"access_data_measurements":0},"cms":{"manage_layouts":0,"manage_partials":0,"manage_themes":0,"manage_pages":0,"manage_assets":0,"manage_content":0},"backend":{"manage_users":0,"access_dashboard":0},"system":{"manage_settings":0,"manage_updates":0,"manage_mail_templates":0}}',
                'created_at' => $datetime_now,
                'updated_at' => $datetime_now],

            /*
             * Student
             */
            ["name" => "Student",
                "description" => "NO backend access. Only Frontend data input",
                "permissions" => '{"cnesmeteo":{"access_data_cloudtypes":0,"send_data_globe":0,"access_data_globe":0,"access_data_skytypes":0,"access_data_clouds":0,"access_data_winddirections":0,"access_data_skies":0,"access_data_aerosols":0,"access_data_photometers":0,"access_groups":0,"access_certifications":0,"access_sites":0,"access_classrooms":0,"access_users":0,"access_organizations":0,"access_data_windforces":0,"access_data_humidities":0,"access_data_precipitations":0,"access_data_snows":0,"access_data_pressures":0,"access_data_temperatures":0,"access_data_measurements":0},"cms":{"manage_layouts":0,"manage_partials":0,"manage_themes":0,"manage_pages":0,"manage_assets":0,"manage_content":0},"backend":{"manage_users":0,"access_dashboard":0},"system":{"manage_settings":0,"manage_updates":0,"manage_mail_templates":0}}',
                'created_at' => $datetime_now,
                'updated_at' => $datetime_now]
        ]);


        // --------------------------------
        // Default CnesMeteo.User Settings:
        // --------------------------------
        $UserSettings = RainLabUserSettings::whereItem('user_settings')->first(); // For "Option A"
        //$UserSettingsValue = RainLabUserSettings::get('user_settings'); // For "Option B"

        // Add default values for "student_register_status" and "first_teacher_becomes_manager"
        // ------------------------------------------------------------------------------------
        /*
         * Option A: Access the inherent model directly <-- Test if it works?
         *
         */
        $UserSettings->value = array_add($UserSettings->value, 'student_register_status', true);
        $UserSettings->value = array_add($UserSettings->value, 'first_teacher_becomes_manager', true);
        $UserSettings->value = array_add($UserSettings->value, 'default_country', 4); // France
        $UserSettings->save();

        /*
         * Option B: Using OctoberCMS static Settings methods get/set
         */
        /*
        if (!empty($UserSettingsValue))
        {
            if (!array_key_exists("student_register_status", $UserSettingsValue)) {
                $UserSettingsValue = array_add($UserSettingsValue, 'student_register_status', true);
            }
            if (!array_key_exists("first_teacher_becomes_manager", $UserSettingsValue)) {
                $UserSettingsValue = array_add($UserSettingsValue, 'first_teacher_becomes_manager', true);
            }
            RainLabUserSettings::set('user_settings', $UserSettingsValue);
        }
        */
    }

}
