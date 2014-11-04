<?php namespace CnesMeteo\User\Components;

use System\Classes\ApplicationException;
use Cms\Classes\ComponentBase;
use CnesMeteo\User\Models\Organization;
use CnesMeteo\User\Models\Classroom;
use Cache;
use Request;
use Input;
use Lang;


class DropdownOrganizationClassroom extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name'        => 'Dropdown Organization->Classroom',
            'description' => 'Select a organization and one of their associated classrooms'
        ];
    }

    public function defineProperties()
    {
        return [
            'organization' => [
                'title' => 'Organization',
                'type' => 'dropdown',
                'placeholder' => 'Select a organization'
            ],
            'classroom' => [
                'title' => 'Classroom',
                'type' => 'dropdown',
                'placeholder' => 'Select a classroom',
                'depends' => ['organization']
            ]
        ];
    }

    public function getOrganizationOptions()
    {
        return Organization::all()->lists('name', 'id');
    }

    public function getClassroomOptions()
    {
        $classrooms = [];
        $organization_id = Request::input('organization');

        if (!empty($organization_id)){
            Classroom::where('organization_id', '=', $organization_id)->lists('name', 'id');
        }

        return $classrooms;
    }
}