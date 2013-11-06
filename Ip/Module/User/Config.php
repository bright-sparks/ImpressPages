<?php
/**
 * @package ImpressPages
 *
 *
 */
namespace Ip\Module\User;



/**
 * User area (registration) configuration.
 * @package ImpressPages
 */

class Config{
    /** @var password hash salt*/
    public static $hashSalt = '';


    public static $urlPasswordReset = 'password-reset';
    public static $urlPasswordResetVerified = 'password-reset-verified';
    public static $urlPasswordResetVerificationError = 'password-reset-verification-error';
    public static $urlPasswordResetSentText = 'password-reset-sent-text';
    public static $urlRegistrationVerificationRequired = 'registration-verification-required';
    public static $urlEmailVerificationRequired = 'new-email-verification-required';
    public static $urlRegistrationVerified = 'registration-verified';
    public static $urlNewEmailVerified = 'new-email-verified';
    public static $urlRegistrationVerificationError = 'registration-verification-error';
    public static $urlNewEmailVerificationError = 'new-email-verification-error';
    public static $urlLogin = 'login';
    public static $urlProfile = 'profile';
    public static $urlRegistration = 'registration';
    public static $urlRenewedRegistration = 'renewed-registration';
    public static $urlRenewRegistrationError = 'renew-registration-error';
    public static $urlVerificationErrorUserExist = 'verification-error-user-exist';
    public static $urlVerificationErrorEmailExist = 'verification-error-email-exist';
    public static $urlLogout = 'logout';
    public static $autologinCookieName = 'ipAutologin';
    public static $autologinCookiePath = '/';


    public static function getRegistrationForm(){
        global $parametersMod;
        global $session;

        
        $form = new \Ip\Form();
        

        /*hidden fields (required)*/
        $field = new \Ip\Form\Field\Hidden(
        array(
        'name' => 'a',
        'defaultValue' => 'registration'
        ));
        $form->addField($field);

        $field = new \Ip\Form\Field\Hidden(
        array(
        'name' => 'g',
        'defaultValue' => 'community'
        ));
        $form->addField($field);
        

        $field = new \Ip\Form\Field\Hidden(
        array(
            'name' => 'm',
            'defaultValue' => 'user'
        ));
        $form->addField($field);

        /*predefined fields (required)*/
        $field = new \Ip\Form\Field\Blank(
        array(
            'name' => 'globalError'
        ));
        $form->addField($field);
        
        if($parametersMod->getValue('User.login_type') == 'login'){
            $field = new \Ip\Form\Field\Text(
            array(
                'name' => 'login',
                'dbField' => 'login',
                'label' => $parametersMod->getValue('User.field_login')
            ));
            $field->addValidator('Required');
            $form->addField($field);
        }

        $field = new \Ip\Form\Field\Email(
        array(
            'name' => 'email',
            'dbField' => 'email',
            'label' => $parametersMod->getValue('User.field_email')
        ));
        $field->addValidator('Required');
        $form->addField($field);

        $field = new \Ip\Form\Field\Password(
        array(
        'name' => 'password',
        'label' => $parametersMod->getValue('User.field_password')
        ));
        $field->addValidator('Required');
        $field->addAttribute('autocomplete', 'off');
        $form->addField($field);

        if($parametersMod->getValue('User.type_password_twice')){
            $field = new \Ip\Form\Field\Password(
            array(
            'name' => 'confirm_password',
            'disableAutocomplete' => true,
            'label' => $parametersMod->getValue('User.field_confirm_password')
            ));
            
            $field->addValidator('Required');
            $field->addAttribute('autocomplete', 'off');
            $form->addField($field);
        }

        /*add your additional fields here*/
        

        //Submit button
        $field = new \Ip\Form\Field\Submit(
        array(
        'name' => 'submit',
        'defaultValue' => $parametersMod->getValue('User.button_register')
        ));
        $form->addField($field);
        
        
        
        return $form;
    }

    public static function getProfileForm(){
        global $parametersMod;
        global $session;

        $form = new \Ip\Form();
        
        $dbMod = new Db();

        $profileFields = array();

        /*hidden fields (required)*/
        $field = new \Ip\Form\Field\Hidden(
        array(
        'name' => 'a',
        'defaultValue' => 'updateProfile'
        ));
        $form->addField($field);

        $field = new \Ip\Form\Field\Hidden(
        array(
        'name' => 'g',
        'defaultValue' => 'community'
        ));
        $form->addField($field);
        

        $field = new \Ip\Form\Field\Hidden(
        array(
            'name' => 'm',
            'defaultValue' => 'user'
        ));
        $form->addField($field);
        


        /*predefined fields (required)*/
        if($session->loggedIn()){ //security check
            $user = $dbMod->userById($session->userId());


            $field = new \Ip\Form\Field\Email(
            array(
                        'name' => 'email',
                        'dbField' => 'email',
                        'label' => $parametersMod->getValue('User.field_email'),
                        'defaultValue' => $user['email']
            ));
            $field->addValidator('Required');
            $form->addField($field);
            
            $field = new \Ip\Form\Field\Password(
            array(
                    'name' => 'password',
                    'label' => $parametersMod->getValue('User.field_password')
            ));
            $field->addAttribute('autocomplete', 'off');
            $form->addField($field);
            
            if($parametersMod->getValue('User.type_password_twice')){
                $field = new \Ip\Form\Field\Password(
                array(
                        'name' => 'confirm_password',
                        'disableAutocomplete' => true,
                        'label' => $parametersMod->getValue('User.field_confirm_password')
                ));
            
                $field->addAttribute('autocomplete', 'off');
                $form->addField($field);
            }            
            

            /*add your additional fields*/

        }


        
        //Submit button
        $field = new \Ip\Form\Field\Submit(
        array(
                'name' => 'submit',
                'defaultValue' => $parametersMod->getValue('User.button_update')
        ));
        $form->addField($field);
        

        return $form;
    }


    
    public static function getLoginForm(){
        global $parametersMod;

        $form = new \Ip\Form();
        


        /*hidden fields (required)*/
        $field = new \Ip\Form\Field\Blank(
        array(
                    'name' => 'globalError'
        ));
        $form->addField($field);
        
        $field = new \Ip\Form\Field\Hidden(
        array(
        'name' => 'a',
        'defaultValue' => 'login'
        ));
        $form->addField($field);

        $field = new \Ip\Form\Field\Hidden(
        array(
        'name' => 'g',
        'defaultValue' => 'community'
        ));
        $form->addField($field);
        

        $field = new \Ip\Form\Field\Hidden(
        array(
            'name' => 'm',
            'defaultValue' => 'user'
        ));
        $form->addField($field);

        /*predefined fields (required)*/
        if($parametersMod->getValue('User.login_type') == 'login'){
            $field = new \Ip\Form\Field\Text(
            array(
                'name' => 'login',
                'label' => $parametersMod->getValue('User.field_login')
            ));
            $field->addValidator('Required');
            $form->addField($field);
        }else{
            $field = new \Ip\Form\Field\Email(
            array(
                'name' => 'email',
                'label' => $parametersMod->getValue('User.field_email')
            ));
            $field->addValidator('Required');
            $form->addField($field);
        }


        $field = new \Ip\Form\Field\Password(
        array(
        'name' => 'password',
        'label' => $parametersMod->getValue('User.field_password')
        ));
        $field->addValidator('Required');
        $form->addField($field);

        if($parametersMod->getValue('User.enable_autologin')){
            $field = new \Ip\Form\Field\Check(
            array(
            'name' => 'autologin',
            'label' => $parametersMod->getValue('User.autologin')
            ));
            $form->addField($field);
        }

        /*add your additional fields*/


        //Submit button
        $field = new \Ip\Form\Field\Submit(
        array(
        'name' => 'submit',
        'defaultValue' => $parametersMod->getValue('User.button_login')
        ));
        $form->addField($field);
        
        
        
        return $form;
    }

    public static function getPasswordResetForm(){
        global $parametersMod;
        global $session;

        $passwordResetFields = array();

        $form = new \Ip\Form();
        
        
        
        /*hidden fields (required)*/
        $field = new \Ip\Form\Field\Blank(
        array(
            'name' => 'globalError'
        ));
        $form->addField($field);
        
        $field = new \Ip\Form\Field\Hidden(
        array(
            'name' => 'a',
            'defaultValue' => 'passwordReset'
        ));
        $form->addField($field);
        
        $field = new \Ip\Form\Field\Hidden(
        array(
            'name' => 'g',
            'defaultValue' => 'community'
        ));
        $form->addField($field);
        
        
        $field = new \Ip\Form\Field\Hidden(
        array(
            'name' => 'm',
            'defaultValue' => 'user'
        ));
        $form->addField($field);
        

        /*predefined fields (required)*/

        $field = new \Ip\Form\Field\Email(
        array(
            'name' => 'email',
            'dbField' => 'email',
            'label' => $parametersMod->getValue('User.field_email')
        ));
        $field->addValidator('Required');
        $form->addField($field);
        
        $field = new \Ip\Form\Field\Password(
        array(
            'name' => 'password',
            'disableAutocomplete' => true,
            'label' => $parametersMod->getValue('User.field_password')
        ));
        $field->addAttribute('autocomplete', 'off');
        $form->addField($field);
        

        if($parametersMod->getValue('User.type_password_twice')){
            $field = new \Ip\Form\Field\Password(
            array(
                'name' => 'confirm_password',
                'disableAutocomplete' => true,
                'label' => $parametersMod->getValue('User.field_confirm_password')
            ));
            
            $field->addAttribute('autocomplete', 'off');
            $form->addField($field);
            
        }

        //Submit button
        $field = new \Ip\Form\Field\Submit(
        array(
        'name' => 'submit',
        'defaultValue' => $parametersMod->getValue('User.button_password_reset')
        ));
        $form->addField($field);
        
        
        return $form;
    }

    public static function getCookieDomain() {
        if ($_SERVER['HTTP_HOST'] != 'localhost'){
            return $_SERVER['HTTP_HOST'];
        } else {
            return false;
        }
    }

}
