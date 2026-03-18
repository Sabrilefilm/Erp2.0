<?php

namespace App\Http\Controllers;

use App\Models\Formation;
use App\Models\FormationQuestion;
use App\Models\FormationQuizTentative;
use App\Services\QuizQuestionGenerator;
use Illuminate\Http\Request;

class FormationQuizController extends Controller
{
    /** Nombre de questions tirées aléatoirement pour une tentative. */
    public const QUESTIONS_PAR_TENTATIVE = 6;

    public function __construct(
        private QuizQuestionGenerator $generator
    ) {}

    /** Affiche le quiz pour une formation (questions aléatoires, réponses mélangées). */
    public function show(Formation $formation, Request $request)
    {
        $difficulte = $request->get('difficulte', FormationQuestion::DIFFICULTE_MOYEN);
        $questions = $formation->questions()
            ->when($difficulte && $difficulte !== 'toutes', fn ($q) => $q->where('difficulte', $difficulte))
            ->with('reponses')
            ->inRandomOrder()
            ->limit(self::QUESTIONS_PAR_TENTATIVE)
            ->get();

        if ($questions->isEmpty()) {
            return redirect()
                ->route('formations.show', $formation)
                ->with('info', 'Aucune question disponible pour ce module. Un administrateur peut générer des questions à partir du contenu.');
        }

        // Mélanger les réponses pour chaque question
        $questions->each(function ($q) {
            $q->setRelation('reponses', $q->reponses->shuffle());
        });

        return view('formations.quiz', [
            'formation' => $formation,
            'questions' => $questions,
            'difficulte' => $difficulte,
        ]);
    }

    /** Soumet les réponses et enregistre la tentative. */
    public function submit(Formation $formation, Request $request)
    {
        $request->validate([
            'reponses' => 'required|array',
            'reponses.*' => 'nullable|string',
        ]);

        $reponses = $request->input('reponses', []);
        $questionIds = array_keys($reponses);
        $questions = FormationQuestion::where('formation_id', $formation->id)
            ->whereIn('id', $questionIds)
            ->with('reponses')
            ->get()
            ->keyBy('id');

        $score = 0;
        $total = $questions->count();

        foreach ($reponses as $qId => $reponseUtilisateur) {
            $q = $questions->get((int) $qId);
            if (! $q) {
                continue;
            }
            $correct = $q->reponses->firstWhere('est_correcte');
            if (! $correct) {
                continue;
            }
            $valeur = is_string($reponseUtilisateur) ? trim($reponseUtilisateur) : $reponseUtilisateur;
            if ($q->type === FormationQuestion::TYPE_QUESTION_SIMPLE) {
                $ok = strcasecmp(trim((string) $valeur), trim($correct->texte)) === 0;
            } else {
                // QCM et Vrai/Faux : valeur = id de la réponse
                $ok = (string) $correct->id === (string) $valeur;
            }
            if ($ok) {
                $score++;
            }
        }

        FormationQuizTentative::create([
            'user_id' => auth()->id(),
            'formation_id' => $formation->id,
            'score' => $score,
            'total' => $total,
            'difficulte' => $request->get('difficulte'),
            'completed_at' => now(),
        ]);

        return redirect()
            ->route('formations.quiz.result', $formation)
            ->with('quiz_score', ['score' => $score, 'total' => $total]);
    }

    /** Page de résultat du quiz. */
    public function result(Formation $formation)
    {
        $sessionScore = session('quiz_score');
        $dernieres = $formation->tentatives()
            ->where('user_id', auth()->id())
            ->orderByDesc('completed_at')
            ->limit(5)
            ->get();

        return view('formations.quiz-result', [
            'formation' => $formation,
            'score' => $sessionScore['score'] ?? null,
            'total' => $sessionScore['total'] ?? null,
            'dernieres_tentatives' => $dernieres,
        ]);
    }

    /** Génère des questions pour la formation (admin). */
    public function generate(Formation $formation, Request $request)
    {
        if (! auth()->user()->hasRoleOrAbove('agent')) {
            abort(403);
        }

        if (empty(config('services.openai.api_key'))) {
            return redirect()
                ->route('formations.show', $formation)
                ->with('error', 'Génération impossible : configurez OPENAI_API_KEY dans le fichier .env');
        }

        $difficulte = $request->get('difficulte', FormationQuestion::DIFFICULTE_MOYEN);
        $count = (int) $request->get('count', QuizQuestionGenerator::DEFAULT_COUNT);
        $count = max(3, min(20, $count));

        $created = $this->generator->generateForFormation($formation, $difficulte, $count);

        return redirect()
            ->route('formations.show', $formation)
            ->with('success', count($created).' question(s) ont été générées pour ce module.');
    }
}
