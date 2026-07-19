<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoriaController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Categoria::class, 'categoria');
    }

    public function index(): View
    {
        $categorias = Categoria::orderBy('pt_classificacao', 'desc')->get();

        return view('categorias.index', compact('categorias'));
    }

    public function create(): View
    {
        return view('categorias.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'pt_classificacao' => 'required|integer|min:0',
            'descricao' => 'nullable|string',
            'titulo_certificado' => 'nullable|string|max:255',
            'cor' => 'nullable|string|max:7',
        ]);

        Categoria::create($validated);

        return redirect()->route('categorias.index')
            ->with('success', 'Categoria criada com sucesso.');
    }

    public function edit(Categoria $categoria): View
    {
        return view('categorias.edit', compact('categoria'));
    }

    public function update(Request $request, Categoria $categoria): RedirectResponse
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'pt_classificacao' => 'required|integer|min:0',
            'descricao' => 'nullable|string',
            'titulo_certificado' => 'nullable|string|max:255',
            'cor' => 'nullable|string|max:7',
        ]);

        $categoria->update($validated);

        return redirect()->route('categorias.index')
            ->with('success', 'Categoria atualizada com sucesso.');
    }

    public function destroy(Categoria $categoria): RedirectResponse
    {
        $categoria->delete();

        return redirect()->route('categorias.index')
            ->with('success', 'Categoria removida com sucesso.');
    }
}
