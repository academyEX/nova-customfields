<?php

namespace Pixelfusion\CustomFields;

use Advoor\NovaEditorJs\NovaEditorJs;
use App\Models\AssessmentSubmission;
use App\Nova\Fields\SimpleNovaEditorJs;
use Laravel\Nova\Fields\Field;
use Laravel\Nova\Http\Requests\NovaRequest;
use Throwable;

class SubmissionMarkingField extends Field
{
    public $component = 'submission-marking-field';

    public function __construct($name, $attribute = null, callable $resolveCallback = null)
    {
        parent::__construct($name, $attribute, $resolveCallback);

        $this->withMeta([
            'editorJs' => SimpleNovaEditorJs::getNovaConfig(),
        ]);
    }

    /**
     * @param AssessmentSubmission $resource
     * @param string $attribute
     * @throws Throwable
     */
    public function resolve($resource, $attribute = null)
    {
        parent::resolve($resource, $attribute);

        // Save json safely
        $this->fillUsing(function (NovaRequest $request, $model, $attribute, $requestAttribute) {
            if ($request->has($attribute)) {
                $model->$attribute = $request->get($attribute);
            }
        });

        $language = $resource->language ?: AssessmentSubmission::LANG_EN;
        $languageName = AssessmentSubmission::LANGUAGES[$language];
        $criteria = [];

        $json = $this->value ? \Safe\json_decode($this->value, true) : [];
        foreach ($resource->assessment->assessmentCriterias as $assessmentCriteria) {
            // Render html version of each criteria too for display view
            $html = '';
            foreach ($json as $item) {
                if ($item['id'] === $assessmentCriteria->id) {
                    $html = NovaEditorJs::generateHtmlOutput($item['content'] ?: []);
                }
            }

            $criteria[] = [
                'id'           => $assessmentCriteria->id,
                'criteria'     => $assessmentCriteria->getLocalisedCriteria($language),
                'description'  => $assessmentCriteria->getLocalisedDescription($language),
                'html'         => $html,
                'language'     => $language,
                'languageName' => $languageName,
            ];
        }

        $this->withMeta([
            'criteria'     => $criteria,
            'language'     => $language,
            'languageName' => $languageName,
        ]);
    }
}
