<?php

namespace App\Http\Controllers;

use App\Models\Badge;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BadgeController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Badge::class, 'badge');
    }

    public function index(): View
    {
        $badges = Badge::withCount('equipes')->orderBy('nome')->get();

        return view('badges.index', compact('badges'));
    }

    public function create(): View
    {
        return view('badges.create');
    }

    public function store(Request $request): RedirectResponse
    {
        Badge::create($this->validated($request));

        return redirect()->route('badges.index')
            ->with('success', 'Badge criada com sucesso.');
    }

    public function edit(Badge $badge): View
    {
        return view('badges.edit', compact('badge'));
    }

    public function update(Request $request, Badge $badge): RedirectResponse
    {
        $badge->update($this->validated($request, $badge));

        return redirect()->route('badges.index')
            ->with('success', 'Badge atualizada com sucesso.');
    }

    public function destroy(Badge $badge): RedirectResponse
    {
        $badge->delete();

        return redirect()->route('badges.index')
            ->with('success', 'Badge removida com sucesso.');
    }

    private function validated(Request $request, ?Badge $badge = null): array
    {
        return $request->validate([
            'nome' => ['required', 'string', 'max:255', Rule::unique('badges')->ignore($badge)],
            'icone' => ['required', 'string', 'max:255'],
            'descricao' => ['required', 'string'],
            'pontos_bonus' => ['required', 'integer', 'min:0'],
        ]);
    }
}
