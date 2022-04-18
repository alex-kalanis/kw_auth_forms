<?php

namespace kalanis\kw_auth_forms;


use ArrayAccess;
use kalanis\kw_forms\Form;
use kalanis\kw_rules\Exceptions\RuleException;
use kalanis\kw_rules\Rules\MatchAll;
use kalanis\kw_rules\Rules\ProcessCallback;


/**
 * Class AuthForm
 * @package kalanis\kw_auth_forms
 * How it works:
 * Insert this one into the form and call it:
 *
 *    AuthForm::tokenAndDigest('digest2', new Methods\ImplodeHash($currentUserCertLib), $this, ['first', 'next', 'last'], $cookies)
 *
 * It adds hidden input which will be checked for token value or digest code
 * Then when the form will be processed this hidden input allow/deny processing further because it checks for code in added input
 */
class AuthForm
{
    /**
     * @param string $inputAlias
     * @param Rules\ARule $digest
     * @param Form $boundForm
     * @param array $whichInputs
     * @param ArrayAccess $cookies
     * @throws RuleException
     */
    public static function digest(string $inputAlias, Rules\ARule $digest, Form $boundForm, array $whichInputs, ArrayAccess $cookies)
    {
        // init input
        $csrf = new Inputs\AuthCsrf();
        $csrf->setHidden($inputAlias, $cookies);

        // check content for digested value
        $digest->setForm($boundForm);
        $digest->setAgainstValue($whichInputs);
        $digest->setErrorText('Digest fails');

        // add rule to input
        $csrf->removeRules();
        $csrf->addRules([$digest]);
        $boundForm->addControlDefaultKey($csrf);
    }

    /**
     * @param string $inputAlias
     * @param Rules\ARule $digest
     * @param Form $boundForm
     * @param array $whichInputs
     * @param ArrayAccess $cookies
     * @throws RuleException
     */
    public static function tokenAndDigest(string $inputAlias, Rules\ARule $digest, Form $boundForm, array $whichInputs, ArrayAccess $cookies)
    {
        // init input
        $csrf = new Inputs\AuthCsrf();
        $csrf->setHidden($inputAlias, $cookies);

        // check for classical CSRF token
        $check = new ProcessCallback();
        $check->setAgainstValue([$csrf, 'checkToken']);
        $check->setErrorText('Token fails');

        // check content for digested value
        $digest->setForm($boundForm);
        $digest->setAgainstValue($whichInputs);
        $digest->setErrorText('Digest fails');

        // match any rule
        $match = new MatchAll();
        $match->setErrorText('Nothing match');
        $match->setAgainstValue([$check, $digest]);

        // add rules to input
        $csrf->removeRules();
        $csrf->addRules([$match]);
        $boundForm->addControlDefaultKey($csrf);
    }
}
