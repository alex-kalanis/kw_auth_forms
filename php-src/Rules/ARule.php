<?php

namespace kalanis\kw_auth_forms\Rules;


use kalanis\kw_forms\Form;
use kalanis\kw_rules\Exceptions\RuleException;
use kalanis\kw_rules\Rules\ARule as OrigRule;


/**
 * Class ARule
 * @package kalanis\kw_auth_forms\Rules
 * Abstract class which process inputs
 */
abstract class ARule extends OrigRule
{
    /** @var Form|null */
    protected $boundForm = null;

    public function setForm(Form $boundForm): void
    {
        $this->boundForm = $boundForm;
    }

    /**
     * @param string[] $whichInputs
     * @return string[]
     * @throws RuleException
     */
    protected function sentInputs(array $whichInputs)
    {
        $this->checkForm();
        $this->boundForm->setSentValues();
        // we want only predefined ones
        $data = array_filter($this->boundForm->getValues(), function ($k) use ($whichInputs) {
            return in_array($k, $whichInputs);
        }, ARRAY_FILTER_USE_KEY);

        // now set it in predefined order
        $flippedInputs = array_flip($whichInputs);
        uksort($data, function ($a, $b) use ($flippedInputs) {
            return strval($flippedInputs[$a]) <=> strval($flippedInputs[$b]);
        });

        return $data;
    }

    /**
     * @throws RuleException
     */
    protected function checkForm(): void
    {
        if (!$this->boundForm) {
            throw new RuleException('Set form first!');
        }
    }
}
