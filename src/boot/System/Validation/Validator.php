<?php

/**
 * @name        JMY CMS
 * @link        http://jmy.su/
 * @copyright   Copyright (C) 2012-2021 JMY LTD
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      Komarov Ivan
 */

namespace Boot\System\Validation;

use \Boot\System\Validation\Validation;
use \Boot\System\Validation\Rule;
use \Boot\System\Validation\RuleNotFoundException;
use \Boot\Support\Facades\Request;


class Validator
{

    use Traits\TranslationsTrait, Traits\MessagesTrait;


    /** @var array */
    protected $translations = [];

    /** @var array */
    protected $validators = [];

    /** @var bool */
    protected $allowRuleOverride = false;

    /** @var bool */
    protected $useHumanizedKeys = true;

    /**
     * Constructor
     *
     * @param array $messages
     * @return void
     */
    public function __construct(array $messages = [])
    {
        $this->messages = $messages;
        $this->registerBaseValidators();
    }

    
    /**
     * Given $inputs, $rules and $messages to make the Validation class instance
     *
     * @param array $inputs
     * @param array $rules
     * @param array $messages
     * @return Validation
     */
    public function make(array $rules, array $messages = [], $customInput = []): Validation
    {
        if(empty($customInput)) {
            $inputs = Request::all();
            $inputs = array_merge(Request::query(), $inputs);
            // $inputs = array_merge(Request::files(), $inputs);
        } else {
            $inputs = $customInput;
        } 
        foreach ($rules as $key => $value) {
            if(is_array($value)){
                $inputs[$key] = $value['input']; 
                $rules[$key] = $value['rule']; 
            }
        }       
        $messages = array_merge($this->messages, $messages);
        $validation = new Validation($this, $inputs, $rules, $messages);
        $validation->setTranslations($this->getTranslations());

        return $validation;
    }

    /**
     * Register or override existing validator
     *
     * @param mixed $key
     * @param \Rakit\Validation\Rule $rule
     * @return void
     */
    public function setValidator(string $key, Rule $rule)
    {
        $this->validators[$key] = $rule;
        $rule->setKey($key);
    }

    /**
     * Get validator object from given $key
     *
     * @param mixed $key
     * @return mixed
     */
    public function getValidator($key)
    {
        return isset($this->validators[$key]) ? $this->validators[$key] : null;
    }

    /**
     * Validate $inputs
     *
     * @param array $inputs
     * @param array $rules
     * @param array $messages
     * @return Validation
     */
    public function validate(array $rules, array $messages = [], $customInput = []): Validation
    {
        $validation = $this->make($rules, $messages, $customInput);
        $validation->validate();
        return $validation;
    }

    /**
     * Magic invoke method to make Rule instance
     *
     * @param string $rule
     * @return Rule
     * @throws RuleNotFoundException
     */
    public function __invoke(string $rule): Rule
    {
        $args = func_get_args();
        $rule = array_shift($args);
        $params = $args;
        $validator = $this->getValidator($rule);
        if (!$validator) {
            throw new RuleNotFoundException("Validator '{$rule}' is not registered", 1);
        }

        $clonedValidator = clone $validator;
        $clonedValidator->fillParameters($params);

        return $clonedValidator;
    }

    /**
     * Initialize base validators array
     *
     * @return void
     */
    protected function registerBaseValidators()
    {
        $baseValidator = [
            'required'                  => new Rules\Required,
            'required_if'               => new Rules\RequiredIf,
            // 'required_unless'           => new \Rakit\Validation\Rules\RequiredUnless,
            // 'required_with'             => new \Rakit\Validation\Rules\RequiredWith,
            // 'required_without'          => new \Rakit\Validation\Rules\RequiredWithout,
            // 'required_with_all'         => new \Rakit\Validation\Rules\RequiredWithAll,
            // 'required_without_all'      => new \Rakit\Validation\Rules\RequiredWithoutAll,
            'email'                     => new Rules\Email,
            // 'alpha'                     => new \Rakit\Validation\Rules\Alpha,
            'numeric'                   => new Rules\Numeric,
            // 'alpha_num'                 => new \Rakit\Validation\Rules\AlphaNum,
            // 'alpha_dash'                => new \Rakit\Validation\Rules\AlphaDash,
            // 'alpha_spaces'              => new \Rakit\Validation\Rules\AlphaSpaces,
            'in'                        => new Rules\In,
            // 'not_in'                    => new \Rakit\Validation\Rules\NotIn,
            'min'                       => new Rules\Min,
            // 'max'                       => new \Rakit\Validation\Rules\Max,
            // 'between'                   => new \Rakit\Validation\Rules\Between,
            // 'url'                       => new \Rakit\Validation\Rules\Url,
            // 'integer'                   => new \Rakit\Validation\Rules\Integer,
            'boolean'                   => new Rules\Boolean,
            // 'ip'                        => new \Rakit\Validation\Rules\Ip,
            // 'ipv4'                      => new \Rakit\Validation\Rules\Ipv4,
            // 'ipv6'                      => new \Rakit\Validation\Rules\Ipv6,
            // 'extension'                 => new \Rakit\Validation\Rules\Extension,
            'array'                     => new Rules\TypeArray,
            'same'                      => new Rules\Same,
            'recaptcha'                 => new Rules\Recaptcha,
            'phone'                     => new Rules\Phone,
            'phone_dd'                  => new Rules\Dadata\Phone,
            'name_dd'                   => new Rules\Dadata\Name,
            'name_paid'                 => new Rules\Dadata\Name,
            'phone_paid'                => new Rules\Dadata\Phone,
            'email_paid'                => new Rules\Dadata\Email,
            'email_registered'          => new Rules\EmailRegistered,
            'phone_registered'          => new Rules\PhoneRegistered,
            // 'regex'                     => new \Rakit\Validation\Rules\Regex,
            // 'date'                      => new \Rakit\Validation\Rules\Date,
            // 'accepted'                  => new \Rakit\Validation\Rules\Accepted,
            // 'present'                   => new \Rakit\Validation\Rules\Present,
            // 'different'                 => new \Rakit\Validation\Rules\Different,
            'uploaded_file'             => new Rules\UploadedFile,
            'mimes'                     => new Rules\Mimes,
            'password_strength'         => new Rules\PasswordStrength,
            // 'callback'                  => new \Rakit\Validation\Rules\Callback,
            // 'before'                    => new \Rakit\Validation\Rules\Before,
            // 'after'                     => new \Rakit\Validation\Rules\After,
            // 'lowercase'                 => new \Rakit\Validation\Rules\Lowercase,
            // 'uppercase'                 => new \Rakit\Validation\Rules\Uppercase,
            // 'json'                      => new \Rakit\Validation\Rules\Json,
            // 'digits'                    => new \Rakit\Validation\Rules\Digits,
            // 'digits_between'            => new \Rakit\Validation\Rules\DigitsBetween,
            // 'defaults'                  => new \Rakit\Validation\Rules\Defaults,
            // 'default'                   => new \Rakit\Validation\Rules\Defaults, // alias of defaults
            // 'nullable'                  => new \Rakit\Validation\Rules\Nullable,           
        ];

        foreach ($baseValidator as $key => $validator) {
            $this->setValidator($key, $validator);
        }
    }

    /**
     * Given $ruleName and $rule to add new validator
     *
     * @param string $ruleName
     * @param \Boot\System\Validation\Rule $rule
     * @return void
     */
    public function addValidator(string $ruleName, Rule $rule)
    {
        if (!$this->allowRuleOverride && array_key_exists($ruleName, $this->validators)) {
            throw new \Exception(
                "You cannot override a built in rule. You have to rename your rule"
            );
        }

        $this->setValidator($ruleName, $rule);
    }

    /**
     * Set rule can allow to be overrided
     *
     * @param boolean $status
     * @return void
     */
    public function allowRuleOverride(bool $status = false)
    {
        $this->allowRuleOverride = $status;
    }

    /**
     * Set this can use humanize keys
     *
     * @param boolean $useHumanizedKeys
     * @return void
     */
    public function setUseHumanizedKeys(bool $useHumanizedKeys = true)
    {
        $this->useHumanizedKeys = $useHumanizedKeys;
    }

    /**
     * Get $this->useHumanizedKeys value
     *
     * @return void
     */
    public function isUsingHumanizedKey(): bool
    {
        return $this->useHumanizedKeys;
    }
}
