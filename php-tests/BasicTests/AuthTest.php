<?php

namespace BasicTests;


use kalanis\kw_auth_forms\Inputs\AuthCsrf;
use kalanis\kw_rules\Validate;


class AuthTest extends \CommonTestClass
{
    public function testCsrf(): void
    {
        $cookie = new \MockArray();
        $csrf = new AuthCsrf();
        $csrf->setHidden('sender', $cookie, 'died');

        $valid = new Validate();
        // check - first round
        $this->assertTrue($valid->validate($csrf));
        $this->assertEmpty($valid->getErrors());
        // second round - did not fail there - set new values
        $this->assertTrue($valid->validate($csrf));
        $this->assertEmpty($valid->getErrors());
        // third round - set bad value, this fails
        $csrf->setValue('kljhgfdsa');
        $this->assertFalse($valid->validate($csrf));
        $this->assertNotEmpty($valid->getErrors());
    }
}
