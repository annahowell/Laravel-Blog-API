<?php

namespace Tests\Feature\Http\Controllers\PostController;

class DataProvider
{
    public function postPostPutInputValidation()
    {
        $tooShort = str_repeat('a', 7);
        $tooLong = str_repeat('a', 256);

        return [
            'title, \'\''                   => ['title',               '',        ['title', 'body']],
            'title, too-long'               => ['title',               $tooShort, ['title', 'body']],
            'title, too-short'              => ['title',               $tooLong,  ['title', 'body']],
            'invalid-title-field, \'\''     => ['invalid-title-field', '',        ['title', 'body']],
            'body, \'\''                    => ['body',                '',        ['title', 'body']],
            'invalid-body-field, \'\''      => ['invalid-body-field',  '',        ['title', 'body']],
            'tags, \'\''                    => ['tags',                '',        ['title', 'body']],
            'invalid-tags-field, \'\''      => ['invalid-tags-field',  '',        ['title', 'body']],
            'tags, invalid-negative-tag-id' => ['tags',                -999,      ['title', 'body', 'tags']],
            'tags, invalid-zero-tag-id'     => ['tags',                0,         ['title', 'body', 'tags']],
            'tags, invalid-positive-tag-id' => ['tags',                999,       ['title', 'body', 'tags']],
        ];
    }
}
