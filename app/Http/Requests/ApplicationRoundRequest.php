<?php

namespace App\Http\Requests;

use App\Enums\RoundStatus;
use App\Enums\Semester;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class ApplicationRoundRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'academic_year' => 'required|integer|min:2023|max:2099',
            'semester' => ['required', new Enum(Semester::class)],
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'status' => ['required', new Enum(RoundStatus::class)],
        ];
    }


}
