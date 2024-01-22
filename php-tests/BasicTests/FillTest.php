<?php

namespace BasicTests;


use kalanis\kw_auth_forms\AuthForm;
use kalanis\kw_auth_forms\Rules\ImplodeHash;
use kalanis\kw_auth_sources\Statuses\Always;
use kalanis\kw_rules\Exceptions\RuleException;


class FillTest extends \CommonTestClass
{
    /**
     * @param string $hash
     * @param string[] $inputs
     * @param string $salt
     * @param bool $isValid
     * @throws RuleException
     * @dataProvider dataProvider
     */
    public function testFillDigest(string $hash, array $inputs, string $salt, bool $isValid): void
    {
        $cookie = new \MockArray();
        $form = $this->getForm(['dummy' => $hash]);
        $user = new \MockUser();
        $user->addCertInfo('empty', $salt);

        // set it
        AuthForm::digest('dummy', new ImplodeHash($user, new Always()), $form, $inputs, $cookie);

        // validate
        $this->assertEquals($isValid, $form->isValid());
    }

    /**
     * @param string $hash
     * @param string[] $inputs
     * @param string $salt
     * @param bool $isValid
     * @throws RuleException
     * @dataProvider dataProvider
     */
    public function testFillTokenDigest(string $hash, array $inputs, string $salt, bool $isValid): void
    {
        $cookie = new \MockArray();
        $form = $this->getForm(['dummy' => $hash]);
        $user = new \MockUser();
        $user->addCertInfo('empty', $salt);

        // set it
        AuthForm::tokenAndDigest('dummy', new ImplodeHash($user, new Always()), $form, $inputs, $cookie);

        // validate
        $this->assertEquals($isValid, $form->isValid());
    }

    public function dataProvider(): array
    {
        return [
            ['failed-different', ['abc', 'def'], '951', false], // failed one
            ['44ac295e9c7260fffe67bce5ab25d211', ['abc', 'def'], '357951', true],
            ['aa76c7fcac187295c5cbfe53072e9671', ['abc', 'def'], '753159', true],
        ];
    }
}
