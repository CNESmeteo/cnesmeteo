<?php namespace CnesMeteo\User\Classes;

use Backend\Classes\AuthManager as BackendAuthManager;
use RainLab\User\Models\Settings as UserSettings;
use BackendAuth;
use Redirect;
use Cookie;
use Session;

class AuthManager extends BackendAuthManager
{
    protected static $instance;

    //protected $sessionKey = 'user_auth';

    protected $userModel = 'CnesMeteo\User\Models\User';
    protected $groupModel = 'CnesMeteo\User\Models\Group';
    //protected $throttleModel = 'CnesMeteo\User\Models\Throttle';

    public function init()
    {
        $this->useThrottle = UserSettings::get('use_throttle', $this->useThrottle);
        $this->requireActivation = UserSettings::get('require_activation', $this->requireActivation);
        parent::init();
    }
}