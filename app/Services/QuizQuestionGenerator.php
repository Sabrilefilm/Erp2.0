<?php

namespace App\Services;

use App\Models\Formation;
use App\Models\FormationQuestion;
use App\Models\FormationQuestionReponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class QuizQuestionGenerator
{
    /** Nombre de questions à générer par défaut (mélange de types). */
    public const DEFAULT_COUNT = 8;

    /** Génère des questions à partir du contenu de la formation et les enregistre. */
    public function generateForFormation(Formation $formation, string $difficulte = 'moyen', int $count = self::DEFAULT_COUNT): array
    {
        $contexte = $this->buildContexte($formation);
        $json = $this->callOpenAI($contexte, $difficulte, $count);
        if (empty($json)) {
            return [];
        }
        return $this->persistQuestions($formation, $json, $difficulte);
    }

    /** Construit le bloc titre + mots-clés + description pour le prompt. */
    public function buildContexte(Formation $formation): string
    {
        $parts = [
            'Sujet / thème du module : '.$formation->titre,
        ];
        if ($formation->mots_cles) {
            $parts[] = 'Mots-clés : '.$formation->mots_cles;
        }
        if ($formation->description) {
            $parts[] = 'Contenu étudié :'."\n".Str::limit(strip_tags($formation->description), 3000);
        }
        return implode("\n\n", $parts);
    }

    /** Appel à l’API OpenAI pour générer les questions (JSON). */
    public function callOpenAI(string $contexte, string $difficulte, int $count): array
    {
        $apiKey = config('services.openai.api_key');
        if (empty($apiKey)) {
            return [];
        }

        $system = <<<PROMPT
Tu es un expert pédagogique. Tu génères des questions de quiz UNIQUEMENT en lien avec le sujet et le contenu fournis.
Règles strictes :
- Les questions doivent porter UNIQUEMENT sur le thème du module (pas de mélange avec d'autres sujets).
- Les réponses correctes doivent être cohérentes avec le contenu étudié.
- Les mauvaises réponses doivent être plausibles (vraisemblables mais fausses) pour rendre le test sérieux.
- Réponds UNIQUEMENT avec un JSON valide, sans texte avant ou après.
PROMPT;

        $user = <<<USER
Contexte du module de formation :

{$contexte}

Génère exactement {$count} questions de quiz pour ce module. Mélange les types :
- "qcm" : question à choix multiples (4 réponses, une seule correcte)
- "vrai_faux" : affirmation avec réponses "Vrai" et "Faux"
- "question_simple" : question courte avec une réponse correcte (texte) et 2 ou 3 mauvaises réponses plausibles pour afficher en QCM

Niveau de difficulté demandé : {$difficulte}.

Format JSON attendu (tableau d'objets) :
[
  {
    "type": "qcm",
    "question": "Texte de la question ?",
    "difficulte": "facile|moyen|avance",
    "reponses": [
      { "texte": "Réponse A", "est_correcte": true },
      { "texte": "Réponse B", "est_correcte": false }
    ]
  },
  {
    "type": "vrai_faux",
    "question": "Affirmation à évaluer.",
    "difficulte": "moyen",
    "reponses": [
      { "texte": "Vrai", "est_correcte": true },
      { "texte": "Faux", "est_correcte": false }
    ]
  }
]

Pour "question_simple", fournis quand même des reponses (une correcte, les autres plausibles). Réponds uniquement par le JSON.
USER;

        $response = Http::withToken($apiKey)
            ->timeout(60)
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => config('services.openai.model', 'gpt-4o-mini'),
                'messages' => [
                    ['role' => 'system', 'content' => $system],
                    ['role' => 'user', 'content' => $user],
                ],
                'temperature' => 0.7,
            ]);

        if (! $response->successful()) {
            return [];
        }

        $body = $response->json();
        $content = $body['choices'][0]['message']['content'] ?? '';
        $content = trim($content);
        // Retirer d'éventuels blocs markdown ```json ... ```
        if (preg_match('/```(?:json)?\s*([\s\S]*?)```/', $content, $m)) {
            $content = trim($m[1]);
        }
        $decoded = json_decode($content, true);
        return is_array($decoded) ? $decoded : [];
    }

    /** Enregistre les questions et réponses en base. */
    private function persistQuestions(Formation $formation, array $items, string $difficulteDefaut): array
    {
        $created = [];
        $ordre = 0;
        foreach ($items as $item) {
            $type = $this->normalizeType($item['type'] ?? 'qcm');
            $question = $item['question'] ?? null;
            $reponses = $item['reponses'] ?? [];
            if (empty($question) || empty($reponses)) {
                continue;
            }
            $difficulte = $this->normalizeDifficulte($item['difficulte'] ?? $difficulteDefaut);

            $q = FormationQuestion::create([
                'formation_id' => $formation->id,
                'type' => $type,
                'question' => $question,
                'difficulte' => $difficulte,
                'ordre' => $ordre++,
            ]);
            $ordRep = 0;
            foreach ($reponses as $rep) {
                FormationQuestionReponse::create([
                    'formation_question_id' => $q->id,
                    'texte' => $rep['texte'] ?? '',
                    'est_correcte' => (bool) ($rep['est_correcte'] ?? false),
                    'ordre' => $ordRep++,
                ]);
            }
            $created[] = $q;
        }
        return $created;
    }

    private function normalizeType(string $type): string
    {
        $map = [
            'qcm' => FormationQuestion::TYPE_QCM,
            'vrai_faux' => FormationQuestion::TYPE_VRAI_FAUX,
            'vrai/faux' => FormationQuestion::TYPE_VRAI_FAUX,
            'question_simple' => FormationQuestion::TYPE_QUESTION_SIMPLE,
            'ouverte' => FormationQuestion::TYPE_QUESTION_SIMPLE,
        ];
        $t = strtolower(trim($type));
        return $map[$t] ?? FormationQuestion::TYPE_QCM;
    }

    private function normalizeDifficulte(string $d): string
    {
        $map = [
            'facile' => FormationQuestion::DIFFICULTE_FACILE,
            'moyen' => FormationQuestion::DIFFICULTE_MOYEN,
            'avance' => FormationQuestion::DIFFICULTE_AVANCE,
            'avancé' => FormationQuestion::DIFFICULTE_AVANCE,
        ];
        $t = strtolower(trim($d));
        return $map[$t] ?? FormationQuestion::DIFFICULTE_MOYEN;
    }
}
