<?php

namespace Modules\Basic\Traits;


use Illuminate\Http\Request;
use function language;

trait LanguageTrait
{
    /**
     * The function updates or creates translations for a given set of data and language keys.
     * 
     * param data It is a variable that holds the data object that needs to be updated or created.
     * param Request request The  parameter is an instance of the Request class, which is used
     * to retrieve data from the HTTP request made to the server. It contains information such as form
     * data, query parameters, and headers. In this function, it is used to retrieve the values of the
     * translation keys for each language.
     * param key The  parameter is an array of keys that are used to identify the translations in
     * the  object.
     */
    public function updateOrCreateLanguage($data, Request $request, $key)
    {
        foreach (language() as $lang) {
            foreach ($key as $value) {
                $translation = $data->translation->where('language_id', $lang->id)->where('key', $value)->first();
                if ($translation) {
                    $translation->update(['value' => $request->$value[$lang->code] ?? " "]);
                } elseif (!$translation && isset($request->$value[$lang->code]) && !empty($request->{$value}{
                    $lang->code})) {
                    $data->translation()->create(['key' => $value, 'value' => $request->$value[$lang->code], 'language_id' => $lang->id]);
                }
            }
        }
    }
}
