<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormationQuestion extends Model
{
    public const TYPE_QCM = 'qcm';
    public const TYPE_VRAI_FAUX = 'vrai_faux';
    public const TYPE_QUESTION_SIMPLE = 'question_simple';

    public const TYPES = [
        self::TYPE_QCM => 'QCM',
        self::TYPE_VRAI_FAUX => 'Vrai / Faux',
        self::TYPE_QUESTION_SIMPLE => 'Question ouverte',
    ];

    public const DIFFICULTE_FACILE = 'facile';
    public const DIFFICULTE_MOYEN = 'moyen';
    public const DIFFICULTE_AVANCE = 'avance';

    public const DIFFICULTES = [
        self::DIFFICULTE_FACILE => 'Facile',
        self::DIFFICULTE_MOYEN => 'Moyen',
        self::DIFFICULTE_AVANCE => 'Avancé',
    ];

    protected $fillable = [
        'formation_id',
        'type',
        'question',
        'difficulte',
        'ordre',
    ];

    public function formation()
    {
        return $this->belongsTo(Formation::class);
    }

    public function reponses()
    {
        return $this->hasMany(FormationQuestionReponse::class, 'formation_question_id')->orderBy('ordre');
    }
}
