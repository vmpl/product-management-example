<?php

namespace App\Http\Requests;

use App\Providers\CrudAttributesService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\App;

class CrudRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        /** @var CrudAttributesService $attributesService */
        $attributesService = App::get(CrudAttributesService::class);
        return $attributesService->getFormValidation();
    }

    public function isPrecognitive()
    {
        return $this->header('Precognition', false);
    }
}
