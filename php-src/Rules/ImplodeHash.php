<?php

namespace kalanis\kw_auth_forms\Rules;


use kalanis\kw_accounts\Interfaces\IUserCert;
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
    /** @var IUserCert */
    protected $keySource = null;
    /** @var IStatus */
    protected $libStatus = null;
    /** @var string */
    protected $glue = '';
    /** @var string */
    protected $algorithm = '';

    public function __construct(IUserCert $keySource, IStatus $libStatus, string $glue = '|', string $algorithm = 'md5')
    {
        $this->keySource = $keySource;
        $this->libStatus = $libStatus;
        $this->glue = $glue;
        $this->algorithm = $algorithm;
    }

    public function validate(IValidate $entry): void
    {
        if (!$this->libStatus->allowCert($this->keySource->getStatus())) {
            throw new RuleException($this->errorText);
        }
        $col = implode($this->glue, $this->sentInputs() + [$this->keySource->getPubSalt()]);
        if (hash($this->algorithm, $col) != strval($entry->getValue())) {
            throw new RuleException($this->errorText);
        }
    }
}
