<?php

namespace kalanis\kw_auth_forms\Rules;


use kalanis\kw_auth\Interfaces\IUserCert;
use kalanis\kw_rules\Exceptions\RuleException;
use kalanis\kw_rules\Interfaces\IValidate;


/**
 * Class ImplodeKeys
 * @package kalanis\kw_auth_forms\Rules
 * Check digest value for preselected inputs - validate certificate
 */
class ImplodeKeys extends ARule
{
    /** @var IUserCert|null */
    protected $keySource = null;
    /** @var string */
    protected $glue = '';

    public function __construct(IUserCert $keySource, string $glue = '|')
    {
        $this->keySource = $keySource;
        $this->glue = $glue;
    }

    public function validate(IValidate $entry): void
    {
        $col = implode($this->glue, $this->sentInputs($this->againstValue) + [$this->keySource->getPubSalt()]);
        if (1 !== openssl_verify($col, strval($entry->getValue()), $this->keySource->getPubKey(), OPENSSL_ALGO_SHA256)) {
            throw new RuleException($this->errorText);
        }
    }
}
