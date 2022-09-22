<?php

namespace Pixelfusion\CustomFields;

use Laravel\Nova\Fields\Field;

class MultichoiceField extends Field
{
    public $items = [];
    public $component = 'multichoice-field';
    public $placeholder = "Add a new item";
    public $inputType = 'text';
    public $max = false;
    public $fullWidth = true;
    public $maxHeight = false;
    public $draggable = false;
    public $hideCreateButton = false;
    public $createButtonValue = "Add";
    public $deleteButtonValue = "x";
    public $listFirst = false;

    public function resolve($resource, $attribute = null)
    {
        parent::resolve($resource, $attribute);

        $this->fillUsing(function ($request, $model, $attribute, $requestAttribute) {
            $model->$attribute = $this->isNullValue($request->$attribute)
                ? null
                : \Safe\json_decode($request->$attribute, true);
        });

        $this->withMeta([
            'max'               => $this->max,
            'items'             => $this->items,
            'listFirst'         => $this->listFirst,
            'inputType'         => $this->inputType,
            'draggable'         => $this->draggable,
            'fullWidth'         => $this->fullWidth,
            'maxHeight'         => $this->maxHeight,
            'placeholder'       => $this->placeholder,
            'hideCreateButton'  => $this->hideCreateButton,
            'createButtonValue' => $this->createButtonValue,
            'deleteButtonValue' => $this->deleteButtonValue,
        ]);
    }

    public function values($values): self
    {
        if (is_array($values) && count($values)) {
            $this->items = $values;
        }
        return $this;
    }

    public function max($max): self
    {
        $this->max = $max;
        return $this;
    }

    public function hideCreateButton($hideCreateButton = true): self
    {
        $this->hideCreateButton = $hideCreateButton;
        return $this;
    }

    public function inputType($inputType): self
    {
        $this->inputType = $inputType;

        return $this;
    }

    public function fullWidth($fullWidth = true): self
    {
        $this->fullWidth = $fullWidth;
        return $this;
    }

    public function maxHeight($maxHeight): self
    {
        $this->maxHeight = $maxHeight;
        return $this;
    }

    public function draggable($draggable = true)
    {
        $this->draggable = $draggable;

        return $this;
    }

    public function placeholder($text): self
    {
        $this->placeholder = $text;
        return $this;
    }

    public function listFirst($listFirst = true): self
    {
        $this->listFirst = $listFirst;
        return $this;
    }

    public function deleteButtonValue($deleteButtonValue): self
    {
        $this->deleteButtonValue = $deleteButtonValue;
        return $this;
    }

    public function createButtonValue($createButtonValue): self
    {
        $this->createButtonValue = $createButtonValue;
        return $this;
    }
}
