<?php

namespace Tests\Feature;

use App\Models\Equipe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RolesAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
    }

    /** Un créateur tente une URL admin (import Excel) → 403 */
    public function test_createur_cannot_access_import_excel(): void
    {
        $createur = User::where('role', User::ROLE_CREATEUR)->first();
        $this->actingAs($createur);

        $response = $this->get(route('import.index'));
        $response->assertStatus(403);
    }

    /** Un manager (directeur) tente import Excel → 403 (réservé fondateur) */
    public function test_directeur_cannot_access_import_excel(): void
    {
        $directeur = User::where('role', User::ROLE_DIRECTEUR)->first();
        $this->actingAs($directeur);

        $response = $this->get(route('import.index'));
        $response->assertStatus(403);
    }

    /** Le fondateur a accès à l'import Excel */
    public function test_fondateur_can_access_import_excel(): void
    {
        $fondateur = User::where('role', User::ROLE_FONDATEUR)->first();
        $this->actingAs($fondateur);

        $response = $this->get(route('import.index'));
        $response->assertStatus(200);
    }

    /** Un créateur peut accéder au dashboard (ses données) */
    public function test_createur_can_access_dashboard(): void
    {
        $createur = User::where('role', User::ROLE_CREATEUR)->first();
        $this->actingAs($createur);

        $response = $this->get(route('dashboard'));
        $response->assertStatus(200);
    }

    /** Un invité est redirigé vers login */
    public function test_guest_redirected_to_login(): void
    {
        $response = $this->get(route('dashboard'));
        $response->assertRedirect(route('login'));
    }
}
