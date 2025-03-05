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
            'gruppe_id' => 'min:1|integer',
            'Teamfoto' => 'mimes:jpg,jpeg|max:5120', // Prüft, ob die Datei ein jpg oder jpeg Bild ist und maximal 5MB groß ist
       ];
    }

    public function messages(): array
    {
        return [
            'einverstaendnis.accepted' => 'Du must den Teilnahmebedingungen / Einverständniserklärung zustimmen.',
            'gruppe_id.min' => 'Bitte wähle eine Wertung / Klasse.',
       ];
    }
}
