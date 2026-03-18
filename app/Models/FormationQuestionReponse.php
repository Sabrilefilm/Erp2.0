<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormationQuestionReponse extends Model
{
    protected $table = 'formation_question_reponses';

    protected $fillable = [
        'formation_question_id',
        'texte',
        'est_correcte',
        'ordre',
    ];

    protected $casts = [
        'est_correcte' => 'boolean',
    ];

    public function formationQuestion()
    {
        return $this->belongsTo(FormationQuestion::class, 'formation_question_id');
    }
}
