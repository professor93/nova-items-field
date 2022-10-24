<?php

namespace Teneraulg\ItemsField;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Laravel\Nova\Fields\Field;
use Laravel\Nova\Fields\SupportsDependentFields;
use Laravel\Nova\Http\Requests\NovaRequest;

class Items extends Field
{
    use SupportsDependentFields;

    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'items';

    /**
     * The rules that should be used to validate items
     *
     * @var array
     */
    public array $itemRules;

    /**
     * The label that should be used for the value heading.
     *
     * @var string|null
     */
    public ?string $valueLabel;

    /**
     * The label that should be used for the "add row" button.
     *
     * @var string|null
     */
    public ?string $actionText;

    /**
     * Determine if the number of items is shown on Index or the list of values
     *
     * @var bool
     */
    public bool $indexCountItem = true;

    /**
     * Determine if new items are able to be edited.
     *
     * @var bool
     */
    public bool $canEditRow = true;

    /**
     * Determine if new items are able to be added.
     *
     * @var bool
     */
    public bool $canAddRow = true;

    /**
     * Determine if items are able to be deleted.
     *
     * @var bool
     */
    public bool $canDeleteRow = true;

    /**
     * Hydrate the given attribute on the model based on the incoming request.
     *
     * @param NovaRequest $request
     * @param string $requestAttribute
     * @param object $model
     * @param string $attribute
     * @throws ValidationException
     */
    protected function fillAttributeFromRequest(NovaRequest $request, $requestAttribute, $model, $attribute): void
    {
        if ($request->exists($requestAttribute)) {
            $field = json_decode($request[$requestAttribute], true);

            if (!empty($this->itemRules)) {
                $requestField = new FormRequest([$requestAttribute => $field]);
                $currentRules = [
                    "$requestAttribute" => ['array'],
                    "$requestAttribute.*" => $this->itemRules,
                ];

                try {
                    $requestField->validate($currentRules);
                } catch (ValidationException $e) {
                    $errors = $e->errors();
                    $returnErrors = [];
                    foreach ($errors as $error) {
                        $returnErrors[$requestAttribute] = preg_replace(
                            "#".$requestAttribute."\.[0-9]+#",
                            $this->valueLabel ?? $requestAttribute,
                            $error
                        );
                    }

                    throw ValidationException::withMessages($returnErrors);
                }
            }

            $model->{$attribute} = json_decode($request[$requestAttribute], true);
        }
    }

    /**
     * The rules that should be used to validate items
     *
     * @param array $rules
     * @return $this
     */
    public function itemRules(array $rules): static
    {
        $this->itemRules = $rules;

        return $this;
    }

    /**
     * The label that should be used for the value table heading.
     *
     * @param string $label
     * @return $this
     */
    public function valueLabel(string $label): static
    {
        $this->valueLabel = $label;

        return $this;
    }

    /**
     * The label that should be used for the add row button.
     *
     * @param string $label
     * @return $this
     */
    public function actionText(string $label): static
    {
        $this->actionText = $label;

        return $this;
    }

    /**
     * Disable count items on Index and show the list of values
     *
     * @return $this
     */
    public function disableIndexCountItem(): static
    {
        $this->indexCountItem = false;

        return $this;
    }

    /**
     * Disable adding new rows.
     *
     * @return $this
     */
    public function disableEditingRows(): static
    {
        $this->canEditRow = false;

        return $this;
    }

    /**
     * Disable adding new rows.
     *
     * @return $this
     */
    public function disableAddingRows(): static
    {
        $this->canAddRow = false;

        return $this;
    }

    /**
     * Disable deleting rows.
     *
     * @return $this
     */
    public function disableDeletingRows(): static
    {
        $this->canDeleteRow = false;

        return $this;
    }

    /**
     * Prepare the field element for JSON serialization.
     *
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return array_merge(parent::jsonSerialize(), [
            'valueLabel' => $this->valueLabel ?? __('Value'),
            'actionText' => $this->actionText ?? __('Add item'),
            'indexCountItem' => $this->indexCountItem,
            'canEditRow' => $this->canEditRow,
            'canAddRow' => $this->canAddRow,
            'canDeleteRow' => $this->canDeleteRow,
        ]);
    }
}
