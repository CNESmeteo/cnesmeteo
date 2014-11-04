<?php namespace CnesMeteo\User\Components;

use October\Rain\Support\Facade\Auth as Auth;;
use Mail;
use Validator;
use Cms\Classes\ComponentBase;
use October\Rain\Support\ValidationException;
use System\Classes\ApplicationException;

class ResetPassword extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name'        => 'CnesMeteo - Reset Password',
            'description' => 'Forgotten password form.'
        ];
    }

    /**
     * Trigger the password reset email
     */
    public function onRestorePassword()
    {
        $rules = [
            'email' => 'required|email|min:2|max:32'
        ];

        $validation = Validator::make(post(), $rules);
        if ($validation->fails())
            throw new ValidationException($validation);

        if (!($user = Auth::findUserByLogin(post('email'))))
            throw new ApplicationException('A user was not found with the given credentials.');

        $code = implode('!', [$user->id, $user->getResetPasswordCode()]);
        $link = $this->controller->currentPageUrl([
            $this->property('paramCode') => $code
        ]);

        $data = [
            'name' => $user->first_name.' '.$user->last_name,
            'link' => $link,
            'code' => $code
        ];

        Mail::send('cnesmeteo.user::emails.restore', $data, function($message) use ($user)
        {
            $message->to($user->email, $user->first_name.' '.$user->last_name);
        });
    }

    /**
     * Perform the password reset
     */
    public function onResetPassword()
    {
        $rules = [
            'code' => 'required',
            'password' => 'required|min:2'
        ];

        $validation = Validator::make(post(), $rules);
        if ($validation->fails())
            throw new ValidationException($validation);

        /*
         * Break up the code parts
         */
        $parts = explode('!', post('code'));
        if (count($parts) != 2)
            throw new ValidationException(['code' => 'Invalid activation code supplied']);

        list($userId, $code) = $parts;

        if (!strlen(trim($userId)) || !($user = Auth::findUserById($userId)))
            throw new ApplicationException('A user was not found with the given credentials.');

        if (!$user->attemptResetPassword($code, post('password')))
            throw new ValidationException(['code' => 'Invalid activation code supplied']);
    }

}