<?php

namespace Tests\Feature\Http\Controllers\UserController;

class DataProvider
{
    public function userPostLoginInputValidation()
    {
        return [
            'email, \'\''                  => ['email',                  '',],
            'invalid-email-field, \'\''    => ['invalid-name-field',     '',],
            'password, \'\''               => ['password',               '',],
            'invalid-password-field, \'\'' => ['invalid-password-field', '',],
        ];
    }


    public function userPostSignUpInputValidation()
    {
        return [
            'displayname, \'\''               => ['displayname',               '',],
            'displayname, 1'                  => ['displayname',               1,],
            'invalid-displayname-field, \'\'' => ['invalid-displayname-field', '',],
            'email, \'\''                     => ['email',                     '',],
            'email, 1'                        => ['email',                     1,],
            'invalid-email-field, \'\''       => ['invalid-name-field',        '',],
            'password, Not long enough'       => ['password',                  'n0tLong!',],
            'password, Missing uppercase'     => ['password',                  'n0uppercase!',],
            'password, Missing lowercase'     => ['password',                  'N0LOWERCASE!',],
            'password, Missing special char'  => ['password',                  'n0specialChar',],
            'password, 1'                     => ['password',                  1,],
            'invalid-password-field, \'\''    => ['invalid-password-field',    '',],
            'password_confirmation, 1'        => ['password_confirmation',     1,],
        ];
    }


    public function userPutUserInputValidation()
    {
        $tooLong = str_repeat('a', 65);

        return [
            'displayname, 1'                 => ['displayname', 1,               ['displayname']],
            'displayname, too-short'         => ['displayname', 'a',             ['displayname']],
            'displayname, too-long'          => ['displayname', $tooLong,        ['displayname']],
            'email, 1'                       => ['email',       1,               ['email']],
            'password, 1'                    => ['password',    1,               ['password']],
            'password, Not long enough'      => ['password',    'n0tLong!',      ['password']],
            'password, Missing uppercase'    => ['password',    'n0uppercase!',  ['password']],
            'password, Missing lowercase'    => ['password',    'N0LOWERCASE!',  ['password']],
            'password, Missing special char' => ['password',    'n0specialChar', ['password']],
        ];
    }
}
