<?php namespace CnesMeteo\User\Components;

use CnesMeteo\Localization\Models\Country;
use CnesMeteo\Localization\Models\Province;
use CnesMeteo\Localization\Models\State;
use CnesMeteo\User\Models\Group;
use CnesMeteo\User\Models\Organization;
use CnesMeteo\User\Models\Classroom;
use CnesMeteo\User\Models\Site;
use CnesMeteoAuth;
use Mail;
use Flash;
use Input;
use Redirect;
use Validator;
use Request;
use Exception;
use Log;
use Backend\Models\AccessLog;
use Cms\Classes\Page;
use System\Classes\ApplicationException;
use October\Rain\Support\ValidationException;
use RainLab\User\Models\Settings as UserSettings;


class Account extends \RainLab\User\Components\Account
{
    protected $organizations = [];
    protected $classrooms = [];
    protected $countries = [];
    protected $states = [];
    protected $provinces = [];
    protected $organization_types = [];

    public function componentDetails()
    {
        return [
            'name'        => 'CnesMeteo - Account',
            'description' => 'User management form.'
        ];
    }

	public function defineProperties()
    {
        return [
            'redirect' => [
                'title'       => 'Redirect to',
                'description' => 'Page name to redirect to after update, sign in or registration.',
                'type'        => 'dropdown',
                'default'     => ''
            ],
            'paramCode' => [
                'title'       => 'Activation Code Param',
                'description' => 'The page URL parameter used for the registration activation code',
                'type'        => 'string',
                'default'     => 'code'
            ],
			'accountAction' => [
                'title' => 'Account Action',
                'type' => 'dropdown',
                'placeholder' => 'Select the action to perform',
                'options' => [
                    'signin' => 'Sign In',
                    'registerUser' => 'Register a User/Student',
                    'registerOrganization' => 'Register a Organization/School'
                ]
            ],
            'userType' => [
                'title' => 'User type',
                'type' => 'dropdown',
                'placeholder' => 'Select the group of the user',
                'options' => [
                    'none' => 'None',
                    'student' => 'Student',
                    'teacher' => 'Teacher'
                ]
            ],
            // ORGANIZATION --> CLASSROOM
            'organization' => [
                'title' => 'Organization',
                'type' => 'dropdown',
                'placeholder' => 'Select a default organization'
            ],
            'classroom' => [
                'title' => 'Classroom',
                'type' => 'dropdown',
                'placeholder' => 'Select a default classroom',
                'depends' => ['organization']
            ],
            'orgtype' => [
                'title' => 'Organization type',
                'type' => 'dropdown',
                'placeholder' => 'Select the default organization type'
            ],
            // LOCATION
            'country' => [
                'title' => 'Country',
                'type' => 'dropdown',
                'placeholder' => 'Select a default country'
            ],
            'state' => [
                'title' => 'State',
                'type' => 'dropdown',
                'placeholder' => 'Select a default state',
                'depends' => ['country']
            ],
            'province' => [
                'title' => 'Province',
                'type' => 'dropdown',
                'placeholder' => 'Select a default province',
                'depends' => ['state']
            ]
        ];
    }

    public function onInit()
    {
        $this->prepareVars();
    }

    protected function prepareVars()
    {
        /*
         * Plugin Properties
         */
        $this->accountAction = $this->page['accountAction'] = $this->property('accountAction');
        $this->userType = $this->page['userType'] = $this->property('userType');

        if ($this->accountAction != 'signin')
        {
            // Organization -> Classroom
            $this->page['organization'] = $this->property('organization');
            $this->page['classroom'] = $this->property('classroom');
            $this->organizations = $this->page['organizations'] = $this->getOrganizationOptions();
            $this->classrooms = $this->page['classrooms'] = $this->getClassroomOptions($this->property('organization'));

            // Location
            //$this->page['country'] = $this->property('country');
            //$this->page['state'] = $this->property('state');
            //$this->page['province'] = $this->property('province');
            $this->countries = $this->page['countries'] = $this->getCountryOptions();
            $this->states = $this->page['states'] = $this->getStateOptions($this->property('country'));
            $this->provinces = $this->page['provinces'] = $this->getProvinceOptions($this->property('state'));

            if ($this->accountAction == 'registerOrganization')
            {
                // Organization Type (only for Organization registration)
                $this->page['orgtype'] = $this->property('orgtype');
                $this->organization_types = $this->page['organization_types'] = $this->getOrgtypeOptions();
            }
        }
    }

    // -----------------------------------------------------------------------------------------------------------------
    // -----------------------------------------------------------------------------------------------------------------

    public function getOrgtypeOptions()
    {
        if ($this->organization_types)
            return $this->organization_types;
        else{
            $this->organization_types = Organization::listOrganizationTypes();
        }
        return $this->organization_types;
    }
    public function getOrganizationOptions()
    {
        if ($this->organizations)
            return $this->organizations;
        else{
            $this->organizations = Organization::getNameList();
        }
        return $this->organizations;
    }
    public function getClassroomOptions($organization_id = null)
    {
        if (empty($organization_id)){
            $organization_id = Request::input('organization');
        }

        $classrooms = [];
        if (!empty($organization_id)){
            //$this->page['organization'] = $organization_id;
            $classrooms = $this->classrooms = $this->page['classrooms'] = Classroom::getNameList($organization_id);
        }
        return $classrooms;
    }

    // -----------------------------------------------------------------------------------------------------------------

    public function getCountryOptions()
    {
        if ($this->countries)
            return $this->countries;
        else{
            $this->countries = Country::getNameList();
        }
        return $this->countries;
    }
    public function getStateOptions($country_id = null)
    {
        if (empty($country_id)){
            $country_id = Request::input('country');
        }

        $states = [];
        if (!empty($country_id)){
            //$this->page['country_id'] = $country_id;
            $states = $this->states = $this->page['states'] = State::getNameList($country_id);
        }
        return $states;
    }
    public function getProvinceOptions($state_id = null)
    {
        if (empty($state_id)){
            $state_id = Request::input('state');
            Log::info('getProvinceOptions - $state_id', [$state_id]);
        }

        $provinces = [];
        if (!empty($state_id)){
            //$this->page['state'] = $state_id;
            $provinces = $this->provinces = $this->page['provinces'] = Province::getNameList($state_id);
        }
        return $provinces;
    }

    // -----------------------------------------------------------------------------------------------------------------

    public function onOrganizationChange()
    {
        // Set organization_id
        $organization_id = Request::input('organization');
        $this->page['organization'] = $organization_id;
        //$this->organization = $this->page['organization'] = $this->property('organization') = $organization_id;
        // Set "classrooms"
        $this->classrooms = $this->page['classrooms'] = $this->getClassroomOptions($organization_id);
    }

    public function onCountryChange()
    {
        // Set country_id
        $country_id = Request::input('country');
        $this->page['country'] = $country_id;
        //$this->country = $this->page['country'] = $this->property('country') = $country_id;
        // Set "states"
        $this->states = $this->page['states'] = $this->getStateOptions($country_id);
    }

    public function onStateChange()
    {
        // Set state_id
        $state_id = Request::input('state');
        $this->page['state'] = $state_id;
        //$this->state = $this->page['state'] = $this->property('state') = $state_id;
        // Set "provinces"
        $this->provinces = $this->page['provinces'] = $this->getProvinceOptions($state_id);
    }

    // -----------------------------------------------------------------------------------------------------------------
    // -----------------------------------------------------------------------------------------------------------------

	
    /**
     * Returns the logged in user, if available
     */
    public function user()
    {
        if (!CnesMeteoAuth::check())
            return null;

        return CnesMeteoAuth::getUser();
    }


    /**
     * Sign in the user
     */
    public function onSignin()
    {
        /*
         * Validate input
         */
        $rules = [
            'password' => 'required|min:5'
        ];

        if (array_key_exists('email', Input::all()))
            $rules['email'] = 'required|email|between:6,128';
        else
            $rules['login'] = 'required|between:4,64';

        $validation = Validator::make(post(), $rules);
        if ($validation->fails())
            throw new ValidationException($validation);

        /*
         * Authenticate user
         */
        /*
        $user = \BackendAuth::authenticate([
            'login' => post('login', post('email')),
            'password' => post('password')
        ], true);
        */
        $user = \CnesMeteo\User\Facades\CnesMeteoAuth::authenticate([
            'login' => post('login', post('email')),
            'password' => post('password')
        ], true);

        // Log the sign in event
        AccessLog::add($user);

        /*
         * Redirect to the intended page after successful sign in
         */
        $redirectUrl = $this->pageUrl($this->property('redirect'));

        if ($redirectUrl = post('redirect', $redirectUrl))
            return Redirect::intended($redirectUrl);
    }



    /**
     * Register the organization (school)
     */
    public function onRegisterOrganization()
    {
        /*
         * Validate input
         */
        $data = post();
        //Log::info('Post data onRegisterOrganization', $data); // DEBUG

        $rules = [
            'name'    => 'required|min:4|max:128',
            'email'   => 'required|email|min:4|max:64',
            'type'    => 'required|min:2|max:16',
            'address' => 'required|min:6',
            'RNE' => 'min:8|max:8'
        ];

        $validation = Validator::make($data, $rules);
        if ($validation->fails())
            throw new ValidationException($validation);


        /*
         * Register organization
         */
        $new_organization = new Organization();

        // Step 1
        $new_organization->name = $data['name'];
        $new_organization->address = $data['address'];
        $new_organization->email = $data['email'];
        $new_organization->phone = (!empty($data['phone'])) ? $data['phone'] : '';


        // Step 2
        $new_organization->type = $data['type'];
        $new_organization->RNE = (!empty($data['RNE'])) ? $data['RNE'] : NULL;
        $new_organization->website = (!empty($data['website'])) ? $data['website'] : '';

        // Step 3
        $new_organization->country_id = (!empty($data['country_id'])) ? $data['country_id'] : NULL;
        $new_organization->state_id = (!empty($data['state_id'])) ? $data['state_id'] : NULL;
        $new_organization->province_id = (!empty($data['province_id'])) ? $data['province_id'] : NULL;

        // Step 4
        $new_organization->latitude = (!empty($data['latitude'])) ? $data['latitude'] : NULL;
        $new_organization->longitude = (!empty($data['longitude'])) ? $data['longitude'] : NULL;
        $new_organization->altitude = (!empty($data['altitude'])) ? $data['altitude'] : NULL;

        $new_organization->save();



        // TODO: (Send email to all admin for manual activation)


        // ------------------------------------------------------------------
        //          Default Measurement Site for this Organization
        // ------------------------------------------------------------------
        $new_site = new Site();

        $new_site->organization_id = $new_organization->id;

        // Basic info
        $new_site->name = "default measurement site";
        $new_site->address = $new_organization->address;

        // Site Location
        $new_site->country_id = $new_organization->country;
        $new_site->state_id = $new_organization->state;
        $new_site->province_id = $new_organization->province;

        // Site Coord
        $new_site->latitude = $new_organization->latitude;
        $new_site->longitude = $new_organization->longitude;
        $new_site->altitude = $new_organization->altitude;

        $new_site->save();


        // ------------------------------------------------------------------
        //          Default Classroom for this Organization
        // ------------------------------------------------------------------
        $new_classroom = new Classroom();

        $new_classroom->organization_id = $new_organization->id;

        // Basic info
        $new_classroom->name = "default classroom";
        $new_classroom->academic_year_start = date("Y");

        $new_classroom->save();

        // Add the default classroom to the default site
        // Option 1)
        $new_site->classrooms()->sync( [$new_classroom->id] );
        // Option 2)
        /*
        if ( (!empty($new_site)) && (!empty($new_classroom)) ) {
            $new_site->classrooms()->save($new_classroom);
        }
        */

        Flash::success('Successfully requested the registration of the organization.');


        // TODO:
        // Redireccionar a una web de creación del 1er sitio de medidas / 1er clase (param: ID organization --> :organization_id)
        // [Eliminar el stio de medidas / 1ª clase por defecto si se guarda alguna]

        /*
        * Redirect to the intended page after successful sign in
        */
        $redirectUrl = $this->pageUrl($this->property('redirect'));

        if ($redirectUrl = post('redirect', $redirectUrl))
            return Redirect::intended($redirectUrl);
    }


    /**
     * Register the user (student or teacher)
     */
    public function onRegisterUser()
    {
        /*
         * Validate input
         */
        $data = post();
        //Log::info('Post data registerUser', $data);

        if (!post('password_confirmation'))
            $data['password_confirmation'] = post('password');

        $rules = [
            'email'    => 'required|email|min:4|max:64|unique:backend_users',
            'password' => 'required|min:6'
        ];

        if (!array_key_exists('login', Input::all()))
            $data['login'] = post('email');
        else
            $rules['login'] = 'required|between:4,64';

        $validation = Validator::make($data, $rules);
        if ($validation->fails())
            throw new ValidationException($validation);

        /*
         * Register user
         */
        $requireActivation = UserSettings::get('require_activation', true);
        $automaticActivation = UserSettings::get('activate_mode') == UserSettings::ACTIVATE_AUTO;
        $userActivation = UserSettings::get('activate_mode') == UserSettings::ACTIVATE_USER;
        $FirstTeacherBecomesManager = UserSettings::get('first_teacher_becomes_manager', false);
        $user = CnesMeteoAuth::register($data, $automaticActivation);

        // Set the user organization and get the relevant info from it
        $organization_model = null;
        if (!empty($data['organization'])){
            $organization_model = Organization::find($data['organization']);

            // Option 1)
            $user->organizations()->sync( [intval($data['organization'])] );
            // Option 2)
            /*
            if (!empty($organization_model)) {
                $user->organizations()->save($organization_model);
            }
            */

            if (!empty($organization_model)) {
                // Laguage
                $user->language_id = $organization_model->language_id;
                // Location
                $user->country_id = $organization_model->country_id;
                $user->state_id = $organization_model->state_id;
                $user->province_id = $organization_model->province_id;
            }
        }
        // Set the user classroom:
        if (!empty($data['classroom'])){
            $user->classrooms()->sync( [intval($data['classroom'])] );
        }

        $new_usergroup = null;
        switch( $this->userType )
        {
            case 'student':
                // Select Students group
                $new_usergroup = Group::whereName('Student')->first();
                break;

            case 'teacher':
                if ( (!empty($organization_model))
                    && ($FirstTeacherBecomesManager)
                    && ($organization_model->hasOrganization_any_UserInGroup( ['Teacher', 'Manager'] ) === false) )
                {
                    // Select the Managers group
                    $new_usergroup = Group::whereName('Manager')->first();
                } else {
                    // Select the Teachers group
                    $new_usergroup = Group::whereName('Teacher')->first();
                }
                break;
        }

        // Set the user group the user will belong to
        if ( (!empty($new_usergroup)) && (!empty($new_usergroup->id)) )
        {
            // Option 1)
            $user->groups()->sync( [intval($new_usergroup->id)] );
            // Option 2)
            /*
            if (!empty($organization_model)) {
                $user->groups()->save($new_usergroup);
            }
            */
        }


        /*
         * Activation is by the user, send the email
         */
        if ($userActivation) {
            $this->sendActivationEmail($user);
        }

        /*
         * Automatically activated or not required, log the user in
         */
        if ($automaticActivation || !$requireActivation) {
            CnesMeteoAuth::login($user);
        }


        Flash::success('Successfully registered.');

        /*
         * Redirect to the intended page after successful sign in
         */
        $redirectUrl = $this->pageUrl($this->property('redirect'));

        if ($redirectUrl = post('redirect', $redirectUrl))
            return Redirect::intended($redirectUrl);
    }

    /**
     * Activate the user
     * @param  string $code Activation code
     */
    public function onActivate($isAjax = true, $code = null)
    {
        try {
            $code = post('code', $code);

            /*
             * Break up the code parts
             */
            $parts = explode('!', $code);
            if (count($parts) != 2)
                throw new ValidationException(['code' => 'Invalid activation code supplied']);

            list($userId, $code) = $parts;
			
			Log::info('user id in onActivate',[$userId]);

            if (!strlen(trim($userId)) || !($user = Auth::findUserById($userId)))
                throw new ApplicationException('A user was not found with the given credentials.');

            if (!$user->attemptActivation($code))
                throw new ValidationException(['code' => 'Invalid activation code supplied']);

            Flash::success('Successfully activated your account.');

            /*
             * Sign in the user
             */
            Auth::login($user);

        }
        catch (Exception $ex) {
            if ($isAjax) throw $ex;
            else Flash::error($ex->getMessage());
        }
    }

    /**
     * Update the user
     */
    public function onUpdate()
    {
        if ($user = $this->user())
            $user->save(post());

        Flash::success(post('flash', 'Settings successfully saved!'));

        /*
         * Redirect to the intended page after successful update
         */
        $redirectUrl = $this->pageUrl($this->property('redirect'));

        if ($redirectUrl = post('redirect', $redirectUrl))
            return Redirect::to($redirectUrl);
    }

    /**
     * Sends the activation email to a user
     * @param  User $user
     * @return void
     */
    protected function sendActivationEmail($user)
    {
        $code = implode('!', [$user->id, $user->getActivationCode()]);
        $link = $this->currentPageUrl([
            $this->property('paramCode') => $code
        ]);

        $data = [
            'name' => $user->first_name.' '.$user->last_name,
            'link' => $link,
            'code' => $code
        ];

        Mail::send('rainlab.user::mail.activate', $data, function($message) use ($user)
        {
            $message->to($user->email, $user->first_name.' '.$user->last_name);
        });
    }

}