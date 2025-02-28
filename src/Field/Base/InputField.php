<?php

declare(strict_types=1);

namespace Yiisoft\Form\Field\Base;

use Yiisoft\Form\Field\Part\Error;
use Yiisoft\Form\Field\Part\Hint;
use Yiisoft\Form\Field\Part\Label;
use Yiisoft\Form\Helper\HtmlForm;
use Yiisoft\Html\Html;

abstract class InputField extends PartsField
{
    use FormAttributeTrait;

    protected ?string $inputId = null;
    protected ?string $inputIdFromTag = null;
    protected bool $setInputId = true;

    protected array $inputAttributes = [];

    /**
     * Specifies the form element the tag input element belongs to. The value of this attribute must be the ID
     * attribute of a form element in the same document.
     *
     * @link https://html.spec.whatwg.org/multipage/form-control-infrastructure.html#attr-fae-form
     */
    final public function form(?string $value): static
    {
        $new = clone $this;
        $new->inputAttributes['form'] = $value;
        return $new;
    }

    final public function inputId(?string $inputId): static
    {
        $new = clone $this;
        $new->inputId = $inputId;
        return $new;
    }

    final public function setInputId(bool $value): static
    {
        $new = clone $this;
        $new->setInputId = $value;
        return $new;
    }

    final public function inputAttributes(array $attributes): static
    {
        $new = clone $this;
        $new->inputAttributes = $attributes;
        return $new;
    }

    final public function addInputAttributes(array $attributes): static
    {
        $new = clone $this;
        $new->inputAttributes = array_merge($new->inputAttributes, $attributes);
        return $new;
    }

    /**
     * Replace input tag CSS classes with a new set of classes.
     *
     * @param string|null ...$class One or many CSS classes.
     */
    final public function inputClass(?string ...$class): static
    {
        $new = clone $this;
        $new->inputAttributes['class'] = array_filter($class, static fn ($c) => $c !== null);
        return $new;
    }

    /**
     * Add one or more CSS classes to the input tag.
     *
     * @param string|null ...$class One or many CSS classes.
     */
    final public function addInputClass(?string ...$class): static
    {
        $new = clone $this;
        Html::addCssClass(
            $new->inputAttributes,
            array_filter($class, static fn ($c) => $c !== null),
        );
        return $new;
    }

    final protected function getInputName(): string
    {
        return HtmlForm::getInputName($this->getFormModel(), $this->formAttribute);
    }

    final protected function getInputAttributes(): array
    {
        $attributes = $this->inputAttributes;

        $this->prepareIdInInputAttributes($attributes);

        $this->prepareInputAttributes($attributes);

        return $attributes;
    }

    protected function prepareInputAttributes(array &$attributes): void
    {
    }

    final protected function prepareIdInInputAttributes(array &$attributes): void
    {
        /** @var mixed $idFromTag */
        $idFromTag = $attributes['id'] ?? null;
        if ($idFromTag !== null) {
            $this->inputIdFromTag = (string) $idFromTag;
        }

        if ($this->setInputId) {
            if ($this->inputId !== null) {
                $attributes['id'] = $this->inputId;
            } elseif ($idFromTag === null) {
                $attributes['id'] = $this->getInputId();
            }
        }
    }

    final protected function renderLabel(Label $label): string
    {
        $label = $label->formAttribute($this->getFormModel(), $this->formAttribute);

        if ($this->setInputId === false) {
            $label = $label->useInputId(false);
        }

        if ($this->inputId !== null) {
            $label = $label->forId($this->inputId);
        } elseif ($this->inputIdFromTag !== null) {
            $label = $label->forId($this->inputIdFromTag);
        }

        return $label->render();
    }

    final protected function renderHint(Hint $hint): string
    {
        return $hint
            ->formAttribute($this->getFormModel(), $this->formAttribute)
            ->render();
    }

    final protected function renderError(Error $error): string
    {
        return $error
            ->formAttribute($this->getFormModel(), $this->formAttribute)
            ->render();
    }
}
