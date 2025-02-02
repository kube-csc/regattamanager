<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRegattaTeamRequest extends FormRequest
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
            'teamname' => 'required|string|max:255',
            'verein' => 'required|string|max:255',
            'teamcaptain' => 'required|string|max:255',
            'strasse' => 'required|string|max:255',
            'plz' => 'required|string|max:255',
            'ort' => 'required|string|max:255',
            'telefon' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'homepage' => 'nullable|string|max:255',
            'captcha' => 'required|integer',
            'einverstaendnis' => 'accepted',
        ];
    }

    public function messages(): array
    {
        return [
            'einverstaendnis.accepted' => 'Sie müssen den Teilnahmebedingungen / Einverständniserklärung zustimmen.',
        ];
    }
}
