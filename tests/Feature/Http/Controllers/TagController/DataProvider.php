<?php

namespace Tests\Feature\Http\Controllers\TagController;

class DataProvider
{
    private $tooShort;
    private $tooLong;

    function __construct() {
        $this->tooShort = str_repeat('a', 1);
        $this->tooLong = str_repeat('a', 65);
    }

    public function tagPostInputValidation()
    {
        return [
            'title, \'\''               => ['title',               ''],
            'title, Too short'          => ['title',               $this->tooShort],
            'title, Too long'           => ['title',               $this->tooLong],
            'invalid-title-field, \'\'' => ['invalid-title-field', ''],
            'color, \'\''               => ['color',               ''],
            'invalid-color-field, \'\'' => ['invalid-color-field', ''],
            'color, #FFAAF'             => ['color',               '#FFAAF'],
        ];
    }



    public function tagPutInputValidation()
    {
        return [
            'title, \'\''               => ['title',               '',              ['title']],
            'title, Too short'          => ['title',               $this->tooShort, ['title']],
            'title, Too long'           => ['title',               $this->tooLong,  ['title']],
            'color, \'\''               => ['color',               '',              ['color']],
            'color, #FFAAF'             => ['color',               '#FFAAF',        ['color']]
        ];
    }
}
