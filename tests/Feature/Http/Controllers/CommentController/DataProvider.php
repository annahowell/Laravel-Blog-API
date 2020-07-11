<?php

namespace Tests\Feature\Http\Controllers\CommentController;

class DataProvider
{
    private $tooShort;
    private $tooLong;

    function __construct() {
        $this->tooShort = str_repeat('a', 1); // smiley :)
        $this->tooLong = str_repeat('a', 1001);
    }



    public function commentPostInputValidation()
    {
        return [
            'body, \'\''                  => ['body',                  ''],
            'body, Too short'             => ['body',                  $this->tooShort],
            'body, Too long'              => ['body',                  $this->tooLong],
            'invalid-body-field, \'\''    => ['invalid-body-field',    ''],
            'post_id, \'\''               => ['post_id',               ''],
            'invalid-post-id-field, \'\'' => ['invalid-post-id-field', ''],
        ];
    }



    public function commentPutInputValidation()
    {
        return [
            'body, \'\''                  => ['body',                  ''],
            'body, Too short'             => ['body',                  $this->tooShort],
            'body, Too long'              => ['body',                  $this->tooLong],
            'invalid-body-field, \'\''    => ['invalid-body-field',    ''],
            'post_id, \'\''               => ['post_id',               ''],
            'invalid-post-id-field, \'\'' => ['invalid-post-id-field', ''],
        ];
    }
}
