<?php namespace CnesMeteo\Data\Controllers;

use Illuminate\Support\Facades\Log;
use Input;
use Flash;
use BackendMenu;
use BackendAuth;
use CnesMeteo\User\Helpers as CnesMeteoHelpers;
use CnesMeteo\Data\Models\Measurement as MeasurementModel;

class Clouds extends MeasurementsControllerBase
{
    public $requiredPermissions = ['cnesmeteo.access_data_clouds'];

    // Widgets
    public $inputsGroupedBy = 'Clouds';
    public $inputGroupBaseName = 'Cloud';
    public $measurementType = 'clouds'; // enum for the "measurement_type" !!!

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('CnesMeteo.Data', 'data', 'clouds');
    }

    /**
     * Add TabControls -> Tabs (Direction: N,E,S,W) -> Observers Name, List of checkboxes (one per cloud type)
     */
    protected function formExtendFields($host)
    {
        $cloudsFields = [];
        $cloud_DIRECTIONS = CnesMeteoHelpers::getListCloudDirections();
        $cloud_TYPES = CnesMeteoHelpers::getCloudTypeEnumNamesList();
        //dd($cloud_DIRECTIONS, $cloud_TYPES); // DEBUG

        foreach ($cloud_DIRECTIONS as $cloud_DIRECTION_Key => $cloud_DIRECTION_Value) {

            // 1) Observers Name
            $fieldName = $this->clouds_agrupation_name.'['.$cloud_DIRECTION_Key.'_ObserverName]'; // Field Name
            $fieldConfig = [
                'label' => 'Observer Name',  // Field Label
                'type' => 'text',
                'tab' => $cloud_DIRECTION_Value // Tab Name
            ];
            $cloudsFields[$fieldName] = $fieldConfig;

            // 2) List of checkboxes (one per cloud type)
            foreach ($cloud_TYPES as $cloud_TYPE_Key => $cloud_TYPE_Value) {

                $fieldName = $this->clouds_agrupation_name.'['.$cloud_DIRECTION_Key.'_'.$cloud_TYPE_Key.']'; // Field Name
                $fieldConfig = [
                    'label' => $cloud_TYPE_Value,  // Field Label
                    'type' => 'checkbox',
                    'tab' => $cloud_DIRECTION_Value // Tab Name
                ];

                $cloudsFields[$fieldName] = $fieldConfig;
            }
        }

        //dd($cloudsFields); // DEBUG
        $host->addTabFields($cloudsFields);
    }

}