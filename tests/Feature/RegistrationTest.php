<?php

namespace Tests\Feature;

use App\Models\Turma;
use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Laravel\Fortify\Features;
use Laravel\Jetstream\Jetstream;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        if (! Features::enabled(Features::registration())) {
            $this->markTestSkipped('Registration support is not enabled.');
        }

        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_registration_screen_cannot_be_rendered_if_support_is_disabled(): void
    {
        if (Features::enabled(Features::registration())) {
            $this->markTestSkipped('Registration support is enabled.');
        }

        $response = $this->get('/register');

        $response->assertStatus(404);
    }

    public function test_new_users_can_register(): void
    {
        if (! Features::enabled(Features::registration())) {
            $this->markTestSkipped('Registration support is not enabled.');
        }

        Notification::fake();

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature(),
        ]);

        $response->assertRedirect(route('dashboard', absolute: false));
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'tipo' => 'aluno',
            'autorizado' => false,
            'email_verified_at' => null,
        ]);
        $user = User::where('email', 'test@example.com')->firstOrFail();
        Notification::assertSentTo($user, VerifyEmail::class);
        $this->assertAuthenticated();

        $this->get(route('dashboard'))
            ->assertRedirect(route('verification.notice'));
    }

    public function test_student_created_by_professor_also_receives_email_verification(): void
    {
        Notification::fake();
        $professor = User::factory()->create(['tipo' => 'professor']);
        $turma = Turma::create([
            'codigo' => 'VER001',
            'descricao' => 'Turma de verificação',
            'dt_inicio' => now(),
            'dt_fim' => now()->addMonth(),
        ]);
        $turma->users()->attach($professor);

        $this->actingAs($professor)->post(route('alunos.store'), [
            'name' => 'Aluno Verificação',
            'email' => 'aluno.verificacao@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'turma_id' => $turma->id,
        ])->assertRedirect(route('alunos.index'))
            ->assertSessionHasNoErrors();

        $student = User::where('email', 'aluno.verificacao@example.com')->firstOrFail();
        $this->assertNull($student->email_verified_at);
        Notification::assertSentTo($student, VerifyEmail::class);
    }
}
