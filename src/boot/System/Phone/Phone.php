<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/


namespace Boot\System\Phone;

use Boot\System\Phone\Exceptions\NumberParseException;
use Boot\System\Phone\Exceptions\NumberFormatException;
use Boot\System\Phone\Exceptions\CountryCodeException;

use Boot\System\Phone\Traits\ParsesCountries;
use Boot\System\Phone\Traits\ParsesFormats;
use Boot\System\Phone\Traits\ParsesTypes;

use Boot\Support\Traits\Macroable;
use Boot\Contracts\Support\Jsonable;
use JsonSerializable;
use Serializable;

use libphonenumber\NumberParseException as libNumberParseException;
use \libphonenumber\PhoneNumberFormat;
use \libphonenumber\PhoneNumberUtil;
use \libphonenumber\PhoneNumberType;


class Phone implements Jsonable, JsonSerializable, Serializable
{
    use Macroable,
        ParsesCountries,
        ParsesFormats,
        ParsesTypes;

    /**
     * Указанный телефонный номер.
     *
     * @var string
     */
    protected $number;

    /**
     * Указанная страны или страны для валидация телефонного номера.
     *
     * @var array
     */
    protected $countries = [];

    /**
     * Страна которая определилась по указанному телефонному номеру
     *
     * @var string
     */
    protected $country;

    /**
     * Следует ли разрешить мягкие проверки (например, номера стационарных телефонов без кодов городов).
     *
     * @var bool
     */
    protected $lenient = false;

    /**
     * Массив с допустимыми телефонными форматами
     *
     * @var array
     */
    protected static $formats;

    /**
     * @var \libphonenumber\PhoneNumberUtil
     */
    protected $lib;

    /**
     * Конструктор класса
     *
     * @param string $number Номер телефона
     */
    public function __construct($number)
    {
        $this->number = $number;
        $this->lib = PhoneNumberUtil::getInstance();
    }

    /**
     * Создание экземпляра класса с указанием номера телефона и страны
     *
     * @param string       $number Номер телефона
     * @param string|array $country Страна для валидации номера телефона
     * @return static
     */
    public static function make($number, $country = [])
    {
        $instance = new static($number);
        return $instance->ofCountry($country);
    }

    /**
     * Установка страны, которой принадлежит номер телефона.
     *
     * @param string|array $country трана для валидации номера телефона
     * @return static
     */
    public function ofCountry($country)
    {
        $countries = is_array($country) ? $country : func_get_args();
        $instance = clone $this;
        $instance->countries = array_unique(
            array_merge($instance->countries, static::parseCountries($countries))
        );
        return $instance;
    }

    public function isValidNumber()
    {
       
        try {
            $instance = $this->lib->parse($this->number, 'RU');
            if ($this->lib->isValidNumber($instance)) {
                return true;
            }
        } catch (\Exception $e) {
            return false;
        }
        return false;
    }

    

    /**
     * Determine whether the given format is valid.
     *
     * @param string $format
     * @return bool
     */
    public static function isValidFormat($format)
    {
        return ! is_null(static::parseFormat($format));
    }

    /**
     * Parse a phone format.
     *
     * @param int|string $format
     * @return int|null
     */
    protected static function parseFormat($format)
    {
        static::loadFormats();

        // If the format equals a constant's value, just return it.
        if (in_array($format, static::$formats, true)) {
            return $format;
        }

        // Otherwise we'll assume the format is the constant's name.
        return \Boot\Support\Arr::get(static::$formats, strtoupper($format));
    }

    /**
     * Load all available formats once.
     */
    private static function loadFormats()
    {
        if (! static::$formats) {
            static::$formats = with(new \ReflectionClass(PhoneNumberFormat::class))->getConstants();
        }
    }

    /**
     * Parse the provided phone countries to a valid array.
     *
     * @param string|array $countries
     * @return array
     */
    protected function parseCountries($countries)
    {
        return \Boot\Support\Collection::make(is_array($countries) ? $countries : func_get_args())
                         ->map(function ($country) {
                             return strtoupper($country);
                         })
                         ->filter(function ($value) {
                             return static::isValidCountryCode($value);
                         })->toArray();
    }



    /**
     * Format the phone number in international format.
     *
     * @return string
     */
    public function formatInternational()
    {
        return $this->format(PhoneNumberFormat::INTERNATIONAL);
    }

    /**
     * Format the phone number in national format.
     *
     * @return string
     */
    public function formatNational()
    {
        return $this->format(PhoneNumberFormat::NATIONAL);
    }

    /**
     * Format the phone number in E164 format.
     *
     * @return string
     */
    public function formatE164()
    {
        return $this->format(PhoneNumberFormat::E164);
    }

    /**
     * Format the phone number in RFC3966 format.
     *
     * @return string
     */
    public function formatRFC3966()
    {
        return $this->format(PhoneNumberFormat::RFC3966);
    }

    /**
     * Format the phone number in a given format.
     *
     * @param string|int $format
     * @return string
     * @throws \Propaganistas\LaravelPhone\Exceptions\NumberFormatException
     */
    public function format($format)
    {
        $parsedFormat = static::parseFormat($format);

        if (is_null($parsedFormat)) {
            throw NumberFormatException::invalid($format);
        }

        return $this->lib->format(
            $this->getPhoneNumberInstance(),
            $parsedFormat
        );
    }

    /**
     * Format the phone number in a way that it can be dialled from the provided country.
     *
     * @param string $country
     * @return string
     * @throws \Propaganistas\LaravelPhone\Exceptions\CountryCodeException
     */
    public function formatForCountry($country)
    {
        if (! static::isValidCountryCode($country)) {
            throw CountryCodeException::invalid($country);
        }

        return $this->lib->formatOutOfCountryCallingNumber(
            $this->getPhoneNumberInstance(),
            $country
        );
    }

    /**
     * Format the phone number in a way that it can be dialled from the provided country using a cellphone.
     *
     * @param string $country
     * @param bool   $withFormatting
     * @return string
     * @throws \Propaganistas\LaravelPhone\Exceptions\CountryCodeException
     */
    public function formatForMobileDialingInCountry($country, $withFormatting = false)
    {
        if (! static::isValidCountryCode($country)) {
            throw CountryCodeException::invalid($country);
        }

        return $this->lib->formatNumberForMobileDialing(
            $this->getPhoneNumberInstance(),
            $country,
            $withFormatting
        );
    }

    /**
     * Get the phone number's country.
     *
     * @return string
     */
    public function getCountry()
    {
        if (! $this->country) {
            $this->country = $this->filterValidCountry($this->countries);
        }

        return $this->country;
    }

    /**
     * Check if the phone number is of (a) given country(ies).
     *
     * @param string|array $country
     * @return bool
     */
    public function isOfCountry($country)
    {
        $countries = static::parseCountries($country);

        return in_array($this->getCountry(), $countries);
    }

    /**
     * Filter the provided countries to the one that is valid for the number.
     *
     * @param string|array $countries
     * @return string
     * @throws \Propaganistas\LaravelPhone\Exceptions\NumberParseException
     */
    protected function filterValidCountry($countries)
    {
        $result = \Boot\Support\Collection::make($countries)
            ->filter(function ($country) {
                try {
                    $instance = $this->lib->parse($this->number, $country);

                    return $this->lenient
                        ? $this->lib->isPossibleNumber($instance, $country)
                        : $this->lib->isValidNumberForRegion($instance, $country);
                } catch (libNumberParseException $e) {
                    return false;
                }
            })->first();

        // If we got a new result, return it.
        if ($result) {
            return $result;
        }

        // Last resort: try to detect it from an international number.
        if ($this->numberLooksInternational()) {
            $countries[] = null;
        }

        foreach ($countries as $country) {
            $instance = $this->lib->parse($this->number, $country);

            if ($this->lib->isValidNumber($instance)) {
                return $this->lib->getRegionCodeForNumber($instance);
            }
        }

        if ($countries = array_filter($countries)) {
            throw NumberParseException::countryMismatch($this->number, $countries);
        }

        throw NumberParseException::countryRequired($this->number);
    }

    /**
     * Get the phone number's type.
     *
     * @param bool $asConstant
     * @return string|int|null
     */
    public function getType($asConstant = false)
    {
        $type = $this->lib->getNumberType($this->getPhoneNumberInstance());

        if ($asConstant) {
            return $type;
        }

        $stringType = \Boot\Support\Arr::get(static::parseTypesAsStrings($type), 0);

        return $stringType ? strtolower($stringType) : null;
    }

    


    /**
     * Array of available phone types.
     *
     * @var array
     */
    protected static $resolvedTypes;

    /**
     * Determine whether the given type is valid.
     *
     * @param int|string $type
     * @return bool
     */
    public static function isValidType($type)
    {
        return ! empty(static::parseTypes($type));
    }

    /**
     * Parse a phone type into constant's value.
     *
     * @param int|string|array $types
     * @return array
     */
    protected static function parseTypes($types)
    {
        static::loadTypes();

        return \Boot\Support\Collection::make(is_array($types) ? $types : func_get_args())
                         ->map(function ($type) {
                             // If the type equals a constant's value, just return it.
                             if (is_numeric($type) && in_array($type, static::$resolvedTypes)) {
                                 return (int) $type;
                             }

                             // Otherwise we'll assume the type is the constant's name.
                             return \Boot\Support\Arr::get(static::$resolvedTypes, strtoupper($type));
                         })
                         ->reject(function ($value) {
                             return is_null($value) || $value === false;
                         })->toArray();
    }

    /**
     * Parse a phone type into its string representation.
     *
     * @param int|string|array $types
     * @return array
     */
    protected static function parseTypesAsStrings($types)
    {
        static::loadTypes();

        return array_keys(
            array_intersect(
                static::$resolvedTypes,
                static::parseTypes($types)
            )
        );
    }

    /**
     * Load all available formats once.
     */
    private static function loadTypes()
    {
        if (! static::$resolvedTypes) {
            static::$resolvedTypes = with(new \ReflectionClass(PhoneNumberType::class))->getConstants();
        }
    }

    /**
     * Check if the phone number is of (a) given type(s).
     *
     * @param string $type
     * @return bool
     */
    public function isOfType($type)
    {
        $types = static::parseTypes($type);

        // Add the unsure type when applicable.
        if (array_intersect([PhoneNumberType::FIXED_LINE, PhoneNumberType::MOBILE], $types)) {
            $types[] = PhoneNumberType::FIXED_LINE_OR_MOBILE;
        }

        return in_array($this->getType(true), $types, true);
    }

    /**
     * Get the raw provided number.
     *
     * @return string
     */
    public function getRawNumber()
    {
    	return $this->number;
    }

    /**
     * Get the PhoneNumber instance of the current number.
     *
     * @return \libphonenumber\PhoneNumber
     */
    public function getPhoneNumberInstance()
    {
        return $this->lib->parse($this->number, $this->getCountry());
    }

    /**
     * Determine whether the phone number seems to be in international format.
     *
     * @return bool
     */
    public function numberLooksInternational()
    {
        return \Boot\Support\Str::startsWith($this->number, '+');
    }

    /**
     * Enable lenient number parsing.
     *
     * @return $this
     */
    public function lenient()
    {
        $this->lenient = true;

        return $this;
    }

    /**
     * Convert the phone instance to JSON.
     *
     * @param  int $options
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->jsonSerialize(), $options);
    }

    /**
     * Convert the phone instance into something JSON serializable.
     *
     * @return string
     */
    public function jsonSerialize()
    {
        return $this->formatE164();
    }

    /**
     * Convert the phone instance into a string representation.
     *
     * @return string
     */
    public function serialize()
    {
        return $this->formatE164();
    }

    /**
     * Reconstructs the phone instance from a string representation.
     *
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        $this->lib = PhoneNumberUtil::getInstance();
        $this->number = $serialized;
        $this->country = $this->lib->getRegionCodeForNumber($this->getPhoneNumberInstance());
    }

    /**
     * Convert the phone instance to a formatted number.
     *
     * @return string
     */
    public function __toString()
    {
        // Formatting the phone number could throw an exception, but __toString() doesn't cope well with that.
        // Let's just return the original number in that case.
        try {
            return $this->formatE164();
        } catch (\Exception $exception) {
            return (string) $this->number;
        }
    }


   
}
