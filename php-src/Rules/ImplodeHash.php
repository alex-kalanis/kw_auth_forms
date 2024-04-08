<?php

namespace kalanis\kw_auth_forms\Rules;


use kalanis\kw_accounts\Interfaces\ICert;
use kalanis\kw_accounts\Interfaces\IUser;
use kalanis\kw_auth_sources\Interfaces\IStatus;
use kalanis\kw_rules\Exceptions\RuleException;
use kalanis\kw_rules\Interfaces\IValidate;


/**
 * Class ImplodeHash
 * @package kalanis\kw_auth_forms\Rules
 * Check digest value for preselected inputs - validate hash
 */
class ImplodeHash extends ARule
{
    protected IUser $userSource;
    protected ICert $certSource;
    protected IStatus $libStatus;
    protected string $glue = '';
    protected string $algorithm = '';

    public function __construct(IUser $userSource, ICert $certSource, IStatus $libStatus, string $glue = '|', string $algorithm = 'md5')
    {
        $this->userSource = $userSource;
        $this->certSource = $certSource;
        $this->libStatus = $libStatus;
        $this->glue = $glue;
        $this->algorithm = $algorithm;
    }

    public function validate(IValidate $entry): void
    {
        if (!$this->libStatus->allowCert($this->userSource->getStatus())) {
            throw new RuleException($this->errorText);
        }
        $col = implode($this->glue, $this->sentInputs() + [$this->certSource->getSalt()]);
        if (hash($this->algorithm, $col) != strval($entry->getValue())) {
            throw new RuleException($this->errorText);
        }
    }
}
