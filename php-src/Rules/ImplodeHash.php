<?php

namespace kalanis\kw_auth_forms\Rules;


use kalanis\kw_auth\Interfaces\IUserCert;
use kalanis\kw_rules\Exceptions\RuleException;
use kalanis\kw_rules\Interfaces\IValidate;


/**
 * Class ImplodeHash
 * @package kalanis\kw_auth_forms\Rules
 * Check digest value for preselected inputs - validate hash
 */
class ImplodeHash extends ARule
{
    /** @var IUserCert|null */
    protected $keySource = null;
    /** @var string */
    protected $glue = '';
    /** @var string */
    protected $algorithm = '';

    public function __construct(IUserCert $keySource, string $glue = '|', string $algorithm = 'md5')
    {
        $this->keySource = $keySource;
        $this->glue = $glue;
        $this->algorithm = $algorithm;
    }

    public function validate(IValidate $entry): void
    {
        $col = implode($this->glue, $this->sentInputs($this->againstValue) + [$this->keySource->getPubSalt()]);
        if (hash($this->algorithm, $col) != strval($entry->getValue())) {
            throw new RuleException($this->errorText);
        }
    }
}
