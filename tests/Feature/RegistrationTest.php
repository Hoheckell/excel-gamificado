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

        $response->assertStatus(200)
            ->assertSee('Declaro que, ao me cadastrar, concordo com os Termos de Uso.')
            ->assertSee(route('terms.show'))
            ->assertSee(route('policy.show'))
            ->assertSee('name="terms"', false)
            ->assertSee('required', false)
            ->assertSee('name="educational_emails_consent"', false)
            ->assertSee('Opcional. Recusar não afeta o cadastro');
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
            'educational_emails_consent' => false,
        ]);
        $user = User::where('email', 'test@example.com')->firstOrFail();
        $this->assertNull($user->educational_emails_consented_at);
        Notification::assertSentTo($user, VerifyEmail::class);
        $this->assertAuthenticated();

        $this->get(route('dashboard'))
            ->assertRedirect(route('verification.notice'));
    }

    public function test_student_can_optionally_consent_to_educational_emails_when_registering(): void
    {
        if (! Features::enabled(Features::registration())) {
            $this->markTestSkipped('Registration support is not enabled.');
        }

        Notification::fake();

        $this->post('/register', [
            'name' => 'Aluno com consentimento',
            'email' => 'consentimento@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'terms' => true,
            'educational_emails_consent' => true,
        ])->assertSessionHasNoErrors();

        $user = User::where('email', 'consentimento@example.com')->firstOrFail();
        $this->assertTrue($user->educational_emails_consent);
        $this->assertNotNull($user->educational_emails_consented_at);
        $this->assertNull($user->educational_emails_consent_revoked_at);
    }

    public function test_registration_requires_terms_acceptance(): void
    {
        if (! Features::enabled(Features::registration())) {
            $this->markTestSkipped('Registration support is not enabled.');
        }

        $this->post('/register', [
            'name' => 'Aluno sem aceite',
            'email' => 'sem.aceite@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertSessionHasErrors('terms');

        $this->assertDatabaseMissing('users', ['email' => 'sem.aceite@example.com']);
        $this->assertGuest();
    }

    public function test_terms_of_use_page_is_public_and_contains_registration_rules(): void
    {
        $this->get(route('terms.show'))
            ->assertOk()
            ->assertSee('Termos de Uso')
            ->assertSee('Ao marcar o campo obrigatório no cadastro');
        $this->get(route('terms.show'))
            ->assertSee('Conteúdos educacionais opcionais por e-mail')
            ->assertSee('não compartilhar o contato com terceiros');
    }

    public function test_privacy_policy_is_public_and_describes_the_educational_data_processing(): void
    {
        $this->get(route('policy.show'))
            ->assertOk()
            ->assertSee('Política de Privacidade')
            ->assertSee('Dados pessoais tratados')
            ->assertSee('Direitos dos titulares')
            ->assertSee('Crianças e adolescentes')
            ->assertSee('Os dados não são vendidos');
        $this->get(route('policy.show'))
            ->assertSee('consentimento específico, separado e desmarcado por padrão')
            ->assertSee('não compartilhar com terceiros os endereços de e-mail')
            ->assertSee('XSRF-TOKEN')
            ->assertSee('não são submetidos a um banner de consentimento')
            ->assertSee('não utiliza cookies de publicidade');
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
