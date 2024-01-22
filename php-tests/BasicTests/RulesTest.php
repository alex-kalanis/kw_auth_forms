<?php

namespace BasicTests;


use kalanis\kw_auth_forms\Rules\ARule;
use kalanis\kw_auth_forms\Rules\ImplodeHash;
use kalanis\kw_auth_forms\Rules\ImplodeKeys;
use kalanis\kw_auth_sources\Statuses\Always;
use kalanis\kw_forms\Adapters\ArrayAdapter;
use kalanis\kw_forms\Exceptions\FormsException;
use kalanis\kw_forms\Form;
use kalanis\kw_rules\Exceptions\RuleException;
use kalanis\kw_rules\Interfaces\IValidate;


class RulesTest extends \CommonTestClass
{
    /**
     * @throws RuleException
     */
    public function testBasicFail()
    {
        $lib = new XRule();
        $this->expectException(RuleException::class);
        $lib->tryInputs();
    }

    /**
     * @throws RuleException
     * @throws FormsException
     */
    public function testBasicFailNoArray()
    {
        $lib = new XfRule();
        $this->expectException(RuleException::class);
        $lib->tryInputs();
    }

    /**
     * @param string $hashValue
     * @param string $inputValue
     * @param string[] $against
     * @param string $userSalt
     * @param bool $displayError
     * @param string $glue
     * @throws RuleException
     * @dataProvider dataProvider
     */
    public function testHash(string $hashValue, string $inputValue, array $against, string $userSalt, bool $displayError, string $glue)
    {
        $user = new \MockUser();
        $user->addCertInfo('none', $userSalt);
        $lib = new ImplodeHash($user, new Always(), $glue);
        $lib->setBoundForm($this->getForm());
        $lib->setAgainstValue($against);
        $lib->setErrorText('Failed');
        if ($displayError) $this->expectException(RuleException::class);
        $lib->validate(new \MockInput('none', $hashValue));
        $this->assertTrue(true); // it passed, remove risky
    }

    /**
     * @param string $hashValue
     * @param string $inputValue
     * @param string[] $against
     * @param string $userSalt
     * @param bool $displayError
     * @param string $glue
     * @throws RuleException
     * @dataProvider dataProvider
     */
    public function testHashFail(string $hashValue, string $inputValue, array $against, string $userSalt, bool $displayError, string $glue)
    {
        $user = new \MockUser();
        $user->addCertInfo('none', $userSalt);
        $lib = new ImplodeHash($user, new \MockStatusNever(), $glue); // due mock status class it will always fails
        $lib->setBoundForm($this->getForm());
        $lib->setAgainstValue($against);
        $lib->setErrorText('Failed');
        $this->expectException(RuleException::class);
        $lib->validate(new \MockInput('none', $hashValue));
    }

    /**
     * @param string $hashValue
     * @param string $inputValue
     * @param string[] $against
     * @param string $userSalt
     * @param bool $displayError
     * @param string $glue
     * @throws RuleException
     * @dataProvider dataProvider
     */
    public function testKeys(string $hashValue, string $inputValue, array $against, string $userSalt, bool $displayError, string $glue)
    {
        // create signature
        $privateKey = openssl_pkey_new([
            'private_key_bits' => 1024,  # not need too long for testing purposes
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ]);
        $privateData = openssl_pkey_get_details($privateKey);
        $publicKey = openssl_pkey_get_public($privateData['key']);
        $publicData = openssl_pkey_get_details($publicKey);

        openssl_sign($inputValue, $signature, $privateKey, "sha256WithRSAEncryption");

        $user = new \MockUser();
        $user->addCertInfo($publicData['key'], $userSalt);

        $lib = new ImplodeKeys($user, new Always(), $glue);
        $lib->setBoundForm($this->getForm());
        $lib->setAgainstValue($against);
        $lib->setErrorText('Failed');
        if ($displayError) $this->expectException(RuleException::class);
        $lib->validate(new \MockInput('none', $signature));
        $this->assertTrue(true); // it passed, remove risky
    }

    /**
     * @param string $hashValue
     * @param string $inputValue
     * @param string[] $against
     * @param string $userSalt
     * @param bool $displayError
     * @param string $glue
     * @throws RuleException
     * @dataProvider dataProvider
     */
    public function testKeysFails(string $hashValue, string $inputValue, array $against, string $userSalt, bool $displayError, string $glue)
    {
        // create signature
        $privateKey = openssl_pkey_new([
            'private_key_bits' => 1024,  # not need too long for testing purposes
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ]);
        $privateData = openssl_pkey_get_details($privateKey);
        $publicKey = openssl_pkey_get_public($privateData['key']);
        $publicData = openssl_pkey_get_details($publicKey);

        openssl_sign($inputValue, $signature, $privateKey, "sha256WithRSAEncryption");

        $user = new \MockUser();
        $user->addCertInfo($publicData['key'], $userSalt);

        $lib = new ImplodeKeys($user, new \MockStatusNever(), $glue); // due mock status class it will always fails
        $lib->setBoundForm($this->getForm());
        $lib->setAgainstValue($against);
        $lib->setErrorText('Failed');
        $this->expectException(RuleException::class);
        $lib->validate(new \MockInput('none', $signature));
    }

    public function dataProvider(): array
    {
        return [
            ['failed-different', 'failed-different', ['abc', 'def'], '951', true, '|'], // failed one
            ['bb48787b08e205485e134e0f3e74313b', 'rdx1|esy2|951', ['abc', 'def'], '951', false, '|'], // basic one
            ['d0c105af93745e0a005b5d690a801f94', 'ijn4#uhb5#soolt', ['ghi', 'jkl', 'mno'], 'soolt', false, '#'], // not all defined in array
            ['38cbad6ecafdc9be821c890041b6cc59', 'ijn4#rdx1#uhb5#different', ['jkl', 'abc', 'mno'], 'different', false, '#'], // order is by elements in array
        ];
    }
}


class XRule extends ARule
{
    public function validate(IValidate $entry): void
    {
        // nothing need here
    }

    /**
     * @throws RuleException
     */
    public function tryInputs(): void
    {
        $this->againstValue = [];
        $this->sentInputs();
    }
}


class XfRule extends ARule
{
    public function validate(IValidate $entry): void
    {
        // nothing need here
    }

    /**
     * @throws RuleException
     * @throws FormsException
     */
    public function tryInputs(): void
    {
        $this->setBoundForm(new Form());
        $this->getBoundForm()->setInputs(new ArrayAdapter([]));
        $this->againstValue = 'this is string, check with this class will fail';
        $this->sentInputs();
    }
}
