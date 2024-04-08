<?php

namespace kalanis\kw_auth_forms\Inputs;


use ArrayAccess;
use kalanis\kw_forms\Controls\Hidden;
use kalanis\kw_forms\Controls\Security\Csrf;
use kalanis\kw_forms\Interfaces\ICsrf;
use kalanis\kw_rules\Interfaces\IRules;


/**
 * Class AuthCsrf
 * @package kalanis\kw_auth_forms\Inputs
 * Authentication CSRF token
 * No limitations like in usual form CSRF token
 */
class AuthCsrf extends Hidden
{
    protected ICsrf $csrf;
    protected string $csrfTokenAlias = '';

    public function __construct()
    {
        $this->csrf = $this->getCsrfLib();
    }

    protected function getCsrfLib(): ICsrf
    {
        return new Csrf\Simple();
    }

    /**
     * @param string $alias
     * @param ArrayAccess<string|int, string|int|float|bool|null> $cookie
     * @param string $errorMessage
     * @return $this
     */
    public function setHidden(string $alias, ArrayAccess &$cookie, string $errorMessage = ''): self
    {
        $this->csrf->init($cookie);
        $this->setEntry($alias);
        $this->csrfTokenAlias = "{$alias}SubmitCheck";
        $this->setValue($this->csrf->getToken($this->csrfTokenAlias));
        parent::addRule(IRules::SATISFIES_CALLBACK, $errorMessage, [$this, 'checkToken']);
        return $this;
    }

    /**
     * @param string|int|float $incomingValue
     * @return bool
     */
    public function checkToken($incomingValue): bool
    {
        if ($this->csrf->checkToken(strval($incomingValue), $this->csrfTokenAlias)) {
            // token reload
            $this->csrf->removeToken($this->csrfTokenAlias);
            $this->setValue($this->csrf->getToken($this->csrfTokenAlias));
            return true;
        } else {
            return false;
        }
    }
}
