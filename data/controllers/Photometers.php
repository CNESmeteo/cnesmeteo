<?php namespace CnesMeteo\Data\Controllers;

use Input;
use Flash;
use BackendMenu;
use BackendAuth;
use Backend\Classes\Controller;
use CnesMeteo\User\Helpers as CnesMeteoHelpers;
use CnesMeteo\Data\Models\Measurement as MeasurementModel;

class Photometers extends Controller
{
    //protected $primaryKey = 'measurement_id'; // extends the measurement info

    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController'
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';

    public $requiredPermissions = ['cnesmeteo.access_data_photometers'];

    public $bodyClass = 'compact-container';


    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('CnesMeteo.Data', 'data', 'photometers');
    }
}