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
    protected ?Form $boundForm = null;

    public function setBoundForm(Form $boundForm): void
    {
        $this->boundForm = $boundForm;
    }

    /**
     * @throws RuleException
     * @return array<string|int, string|int|float|bool|null>
     */
    protected function sentInputs()
    {
        $this->getBoundForm()->setSentValues();
        // we want only predefined ones
        $whichInputs = $this->againstValue;
        if (!is_array($whichInputs)) {
            throw new RuleException('Compare only against list of known keys!');
        }
        $data = array_filter($this->getBoundForm()->getValues(), function ($k) use ($whichInputs) {
            return in_array($k, $whichInputs);
        }, ARRAY_FILTER_USE_KEY);

        // now set it in predefined order
        $flippedInputs = array_flip($whichInputs);
        uksort($data, function ($a, $b) use ($flippedInputs) {
            return strcmp(strval($flippedInputs[$a]), strval($flippedInputs[$b]));
        });

        return $data;
    }

    /**
     * @throws RuleException
     * @return Form
     */
    protected function getBoundForm(): Form
    {
        if (!$this->boundForm) {
            throw new RuleException('Set form first!');
        }
        return $this->boundForm;
    }
}
