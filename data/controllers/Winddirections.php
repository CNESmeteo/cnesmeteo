<?php namespace CnesMeteo\Data\Controllers;

use Input;
use Flash;
use BackendMenu;
use BackendAuth;

class Winddirections extends MeasurementsControllerBase
{
    public $requiredPermissions = ['cnesmeteo.access_data_winddirections'];

    // Widgets
    public $inputsGroupedBy = 'Winddirections';
    public $inputGroupBaseName = 'Winddirection';
    public $measurementType = 'winddirection'; // enum for the "measurement_type" !!!

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('CnesMeteo.Data', 'data', 'winddirections');
    }
}