<?php

namespace Modules\Basic\Traits;

use Illuminate\Validation\Rule;
use Modules\Order\Actions\Order\FakeNumberOrderAction;
use function language;
use function languageLocale;

trait validationRulesTrait
{
    /**
     * This function generates validation rules for translations based on the given class, keys, and
     * language.
     *
     * param class The class name of the model being validated.
     * param rules an array of validation rules for the model
     * param keys An array of translation keys that need to be validated.
     * param id The ID of the translation record that is being validated. If provided, the validation
     * rule will ignore this ID when checking for uniqueness.
     *
     * return an array of validation rules for translations, based on the provided class, rules, keys,
     * and optional ID. The rules are generated for each language in the system, and include a unique
     * check for the translation value in the translations table, as well as a string type check. If
     * the language is the current language locale, a required check is also added.
     */
    public function translationValidationRules($class, $rules, $keys, $id = null, $unique = [])
    {
        foreach(language() as $lang)
        {
            foreach($keys as $key)
            {
                if(!in_array($key, $unique))
                {
                    $rules[$key . '.' . $lang->code] = 'string';
                    if(languageLocale() == $lang->code)
                    {
                        $rules[$key . '.' . languageLocale()] .= '|required';
                    }
                }else
                {
                    $rule = Rule::unique('translations', 'value')
                        ->where('category_type', $class)
                        ->where('key', $key)
                        ->where('language_id', $lang->id);
                    if($id)
                    {
                        $rule = $rule->ignore($id, 'category_id');
                    }
                    $rules[$key . '.' . $lang->code] = $rule;
                    $rules[$key . '.' . $lang->code] .= "|string";
                    if(languageLocale() == $lang->code)
                    {
                        $rules[$key . '.' . languageLocale()] .= '|required';
                    }
                }
            }
        }
        return $rules;
    }

    /**
     * The function `translationValidationSingleRules` takes in a class, rules, keys, an optional id, a
     * boolean unique, and a language code, and returns an updated set of rules for validating
     * translations.
     *
     * param class The class parameter is the category type of the translation. It is used to filter
     * the translations based on the category type.
     * param rules An array of validation rules for the translation fields.
     * param keys An array of keys that represent the translation fields to be validated.
     * param id The "id" parameter is used to specify the ID of the translation record that is being
     * validated. It is optional and can be set to null if not needed.
     * param unique The "unique" parameter determines whether the validation rule should check for
     * uniqueness of the translation value in the database. If set to true, it will add a unique rule
     * to the validation rules for each key. If set to false, it will only add a string and required
     * rule for each key.
     * param languageCode The languageCode parameter is the code representing the language for which
     * the translations are being validated. In this case, the code "2" represents the Arabic language.
     *
     * return the updated array of rules.
     */
    public function translationValidationSingleRules($class, $rules, $keys, $id = null, $unique = [], $languageCode)
    {
        foreach($keys as $key)
        {
            if(!in_array($key, $unique))
            {
                $rules[$key] = 'string';
                $rules[$key] .= '|required';
            }else
            {
                $rule = Rule::unique('translations', 'value')
                    ->where('category_type', $class)
                    ->where('key', $key)
                    ->where('language_id', 2); //2 = ar lang
                if($id)
                {
                    $rule = $rule->ignore($id, 'category_id');
                }
                $rules[$key] = $rule;
                $rules[$key] .= "|string";
                $rules[$key] .= '|required';
            }
        }
        return $rules;
    }

    /**
     * The function converts Persian/Arabic numerals in a string to Western numerals.
     *
     * param string The input string that needs to be converted from Persian/Arabic numerals to
     * English numerals.
     *
     * return The function `convertPersian` takes a string as input and replaces Persian and Arabic
     * numerals with their corresponding English numerals. The function returns the modified string.
     */
    public function convertPersian($string)
    {
        return strtr($string,
            array('۰' => '0', '۱' => '1', '۲' => '2', '۳' => '3', '۴' => '4', '۵' => '5', '۶' => '6', '۷' => '7', '۸' => '8', '۹' => '9', '٠' => '0', '١' => '1', '٢' => '2', '٣' => '3', '٤' => '4', '٥' => '5', '٦' => '6', '٧' => '7', '٨' => '8', '٩' => '9', '+' => '00'));
    }

    /**
     * This PHP function converts Arabic numerals in a string to their corresponding Persian numerals.
     *
     * param string The input string that needs to be converted from Arabic numerals to Persian
     * numerals.
     *
     * return The function `convertPersianAr` is returning a string with the numbers in the input
     * string replaced with their Persian/Arabic equivalents. The mapping of the numbers is done using
     * the `strtr` function and an array of key-value pairs where the keys are the English numbers and
     * the values are the Persian/Arabic numbers.
     */
    public function convertPersianAr($string)
    {
        return strtr($string,
            array('0' => '۰', '1' => '۱', '2' => '۲', '3' => '۳', '4' => '٤', '5' => '٥', '6' => '٦', '7' => '۷', '8' => '۸', '9' => '۹'));
    }

    public function handlePhoneKSA($oldPhone)
    {
        // Remove all non-numeric characters
        $newPhone = preg_replace('/\D/', '', $oldPhone);
        $newPhone = $this->convertPersian($newPhone);
        // Check if the number starts with '00'
        if(substr($newPhone, 0, 1) === '+')
        {
            $newPhone = substr($newPhone, 1); // Remove '00'
        }
        if(substr($newPhone, 0, 2) === '00')
        {
            $newPhone = substr($newPhone, 2); // Remove '00'
        }
        // Check if the number starts with '966'
        if(substr($newPhone, 0, 3) === '966')
        {
            $newPhone = substr($newPhone, 3); // Remove '966'
        }
        // Check if the number starts with '55' and has a length of 8
        if(substr($newPhone, 0, 2) === '55' && strlen($newPhone) == 10)
        {
            $newPhone = substr($newPhone, 1);
        }
        // Check if the number starts with '05' and has a length of 10
        if(substr($newPhone, 0, 2) === '05' && strlen($newPhone) == 10)
        {
            return $newPhone; // Valid number, return it as is
        }
        // Check if the number starts with '5' and has a length of 9
        if(substr($newPhone, 0, 1) === '5' && strlen($newPhone) == 9)
        {
            return '0' . $newPhone; // Valid number, return it as is
        }
        // If none of the above conditions are met, return false or an error
        return $newPhone;
    }

    public function hasInvalidPhoneKSA($phone)
    {
        if(substr($phone, 0, 2) !== '05' || strlen($phone) != 10)
        {
            return false;
        }
        return true;
    }

    public function checkFake($phone)
    {
        return app(FakeNumberOrderAction::class)->execute($phone);
    }
}
