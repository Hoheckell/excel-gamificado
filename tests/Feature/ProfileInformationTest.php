<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Jetstream\Http\Livewire\UpdateProfileInformationForm;
use Livewire\Livewire;
use Tests\TestCase;

class ProfileInformationTest extends TestCase
{
    use RefreshDatabase;

    public function test_current_profile_information_is_available(): void
    {
        $this->actingAs($user = User::factory()->create());

        $component = Livewire::test(UpdateProfileInformationForm::class);

        $this->assertEquals($user->name, $component->state['name']);
        $this->assertEquals($user->email, $component->state['email']);
    }

    public function test_profile_information_can_be_updated(): void
    {
        $this->actingAs($user = User::factory()->create());

        Livewire::test(UpdateProfileInformationForm::class)
            ->set('state', ['name' => 'Test Name', 'email' => 'test@example.com'])
            ->call('updateProfileInformation');

        $this->assertEquals('Test Name', $user->fresh()->name);
        $this->assertEquals('test@example.com', $user->fresh()->email);
    }

    public function test_student_can_grant_and_revoke_educational_email_consent_from_profile(): void
    {
        $this->actingAs($user = User::factory()->create([
            'tipo' => 'aluno',
            'educational_emails_consent' => false,
        ]));

        $this->patch(route('profile.educational-emails.update'), [
            'educational_emails_consent' => true,
        ])->assertSessionHasNoErrors();

        $user->refresh();
        $this->assertTrue($user->educational_emails_consent);
        $this->assertNotNull($user->educational_emails_consented_at);
        $this->assertNull($user->educational_emails_consent_revoked_at);
        $consentedAt = $user->educational_emails_consented_at;

        $this->patch(route('profile.educational-emails.update'))
            ->assertSessionHasNoErrors();

        $user->refresh();
        $this->assertFalse($user->educational_emails_consent);
        $this->assertTrue($consentedAt->equalTo($user->educational_emails_consented_at));
        $this->assertNotNull($user->educational_emails_consent_revoked_at);
    }

    public function test_professor_cannot_set_student_only_educational_email_preference(): void
    {
        $this->actingAs($professor = User::factory()->create(['tipo' => 'professor']));

        $this->patch(route('profile.educational-emails.update'), [
            'educational_emails_consent' => true,
        ])->assertForbidden();

        $this->assertFalse($professor->fresh()->educational_emails_consent);
    }
}
