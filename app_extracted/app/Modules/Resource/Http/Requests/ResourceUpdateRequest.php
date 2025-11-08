<?php

namespace App\Modules\Resource\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\RequiredIf;



class ResourceUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'slug' => $this->getSlugRules(),
            'title' => 'required',
            'overview' => 'required',
            'publication_year' => 'nullable|integer|min:1900',
            
            'coauthors' => 'nullable', //comma delimited strings
            'author' => 'nullable', // string
            'type' => 'required', // resource field slug
            'field' => 'required', //  resource field slug
            'sub_fields' => 'required', //comma delimited strings

            'currency' => 'nullable',
            // 'price' => [ new RequiredIf($this->price != null ), 'integer'],
            'price' => new RequiredIf($this->currency != null ),

            'page_count' => 'nullable|integer',
            'preview_limit' => 'nullable|integer',
            'isbn' => 'nullable',
            'is_featured' => 'nullable',
            'is_private' => 'nullable',
            'is_active' => 'nullable',
        ];
    }

     private function getSlugRules()
    {
        $rules[] = Rule::unique('resource', 'slug');
        return $rules;
    }
}

