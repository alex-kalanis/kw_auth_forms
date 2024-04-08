<?php

namespace kalanis\kw_auth_forms\Rules;


use kalanis\kw_accounts\Interfaces\ICert;
use kalanis\kw_accounts\Interfaces\IUser;
use kalanis\kw_auth_sources\Interfaces\IStatus;
use kalanis\kw_rules\Exceptions\RuleException;
use kalanis\kw_rules\Interfaces\IValidate;


/**
 * Class ImplodeKeys
 * @package kalanis\kw_auth_forms\Rules
 * Check digest value for preselected inputs - validate certificate
 */
class ImplodeKeys extends ARule
{
    protected IUser $userSource;
    protected ICert $certSource;
    protected IStatus $libStatus;
    protected string $glue = '';

    public function __construct(IUser $userSource, ICert $certSource, IStatus $libStatus, string $glue = '|')
    {
        $this->userSource = $userSource;
        $this->certSource = $certSource;
        $this->libStatus = $libStatus;
        $this->glue = $glue;
    }

    public function validate(IValidate $entry): void
    {
        if (!$this->libStatus->allowCert($this->userSource->getStatus())) {
            throw new RuleException($this->errorText);
        }
        $col = implode($this->glue, $this->sentInputs() + [$this->certSource->getSalt()]);
        if (1 !== openssl_verify($col, strval($entry->getValue()), $this->certSource->getPubKey(), OPENSSL_ALGO_SHA256)) {
            throw new RuleException($this->errorText);
        }
    }
}
