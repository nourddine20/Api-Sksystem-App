<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class Language extends Model
{
    

function langString($string_ref)
{
    // Assuming you have a model named StringModel
    $ws_lang = 'fr'; // Assuming Laravel localization is used

    $row_langs = DB::table('delevery_string')
        ->where('string_lang', $ws_lang)
        ->where('string_ref', $string_ref)
        ->first();

    if (!$row_langs) {
        // Handle the case when no record is found
        return null;
    }


    return $row_langs->string_text;
}


}

