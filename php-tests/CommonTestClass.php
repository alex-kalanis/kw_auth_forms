<?php

use kalanis\kw_auth\Interfaces\IUserCert;
use kalanis\kw_forms\Form;
use kalanis\kw_rules\Interfaces\IValidate;
use PHPUnit\Framework\TestCase;


/**
 * Class CommonTestClass
 * The structure for mocking and configuration seems so complicated, but it's necessary to let it be totally idiot-proof
 */
class CommonTestClass extends TestCase
{
    protected function getForm(array $additional = []): Form
    {
        $form = new Form('testForm');
        $form->addText('abc');
        $form->addText('def');
        $form->addText('jkl');
        $form->addText('mno');
        $form->setInputs(new \MockInputs($additional));
        return $form;
    }
}


class MockUser implements IUserCert
{
    protected $authId = '0';
    protected $authName = '';
    protected $group = '0';
    protected $class = 3;
    protected $display = '';
    protected $status = null;
    protected $dir = '';
    protected $key = '';
    protected $salt = '';

    public function setUserData(?string $authId, ?string $authName, ?string $authGroup, ?int $authClass, ?int $authStatus, ?string $displayName, ?string $dir): void
    {
        $this->authId = $authId ?? $this->authId;
        $this->authName = $authName ?? $this->authName;
        $this->group = $authGroup ?? $this->group;
        $this->class = $authClass ?? $this->class;
        $this->status = $authStatus;
        $this->display = $displayName ?? $this->display;
        $this->dir = $dir ?? $this->dir;
    }

    public function addCertInfo(?string $key, ?string $salt): void
    {
        $this->key = $key ?? $this->key;
        $this->salt = $salt ?? $this->salt;
    }

    public function getAuthId(): string
    {
        return $this->authId;
    }

    public function getAuthName(): string
    {
        return $this->authName;
    }

    public function getGroup(): string
    {
        return $this->group;
    }

    public function getClass(): int
    {
        return $this->class;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function getDisplayName(): string
    {
        return $this->display;
    }

    public function getDir(): string
    {
        return $this->dir;
    }

    public function getPubSalt(): string
    {
        return $this->salt;
    }

    public function getPubKey(): string
    {
        return $this->key;
    }
}


class MockInput implements IValidate
{
    protected $key = '';
    protected $value = '';

    public function __construct(string $key, string $value)
    {
        $this->key = $key;
        $this->value = $value;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getRules(): array
    {
        return [];
    }
}


class MockInputs extends \kalanis\kw_forms\Adapters\AAdapter
{
    protected $additional = [];

    public function __construct(array $additional)
    {
        $this->additional = $additional;
    }

    public function loadEntries(string $inputType): void
    {
        $this->vars = [
            'abc' => 'rdx1',
            'def' => 'esy2',
            'ghi' => 'okm3',
            'jkl' => 'ijn4',
            'mno' => 'uhb5',
            'pqr' => 'zgv6',
            'stu' => 'tfc7',
        ] + $this->additional;
    }

    public function getSource(): string
    {
        return \kalanis\kw_input\Interfaces\IEntry::SOURCE_GET;
    }
}


class MockArray implements ArrayAccess, Countable, Iterator
{
    protected $key = null;
    protected $vars = [];

    public function getKey(): string
    {
        return $this->key;
    }

    public function getValue()
    {
        return $this->current();
    }

    public function current()
    {
        return $this->valid() ? $this->offsetGet($this->key) : null ;
    }

    public function next()
    {
        next($this->vars);
        $this->key = key($this->vars);
    }

    public function key()
    {
        return $this->key;
    }

    public function valid()
    {
        return $this->offsetExists($this->key);
    }

    public function rewind()
    {
        reset($this->vars);
        $this->key = key($this->vars);
    }

    public function offsetExists($offset)
    {
        return isset($this->vars[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->vars[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this->vars[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->vars[$offset]);
    }

    public function count()
    {
        return count($this->vars);
    }
}


class MockStatusNever implements \kalanis\kw_auth\Interfaces\IStatus
{
    public function allowLogin(?int $status): bool
    {
        return false;
    }

    public function allowCert(?int $status): bool
    {
        return false;
    }
}
