<div class="space-y-5">
    <div>
        <x-label for="nome" value="Nome da Badge" />
        <x-input id="nome" class="block mt-1 w-full" type="text" name="nome" :value="old('nome', $badge?->nome)" placeholder="Ex: Zero Mouse" required autofocus />
        <x-input-error for="nome" class="mt-1" />
    </div>

    <div>
        <x-label for="icone" value="Ícone" />
        <x-input id="icone" class="block mt-1 w-full" type="text" name="icone" :value="old('icone', $badge?->icone)" placeholder="Ex: ⌨️" required />
        <p class="text-xs text-[--text-muted] mt-1">Use um emoji ou uma classe de ícone.</p>
        <x-input-error for="icone" class="mt-1" />
    </div>

    <div>
        <x-label for="descricao" value="Descrição" />
        <textarea id="descricao" name="descricao" rows="4" class="mt-1 block w-full border border-[--border-light] rounded-excel px-3 py-2 text-[--text-main] bg-white focus:border-excel-dark focus:ring-excel-light text-sm" required>{{ old('descricao', $badge?->descricao) }}</textarea>
        <x-input-error for="descricao" class="mt-1" />
    </div>

    <div>
        <x-label for="pontos_bonus" value="Pontos de bônus" />
        <x-input id="pontos_bonus" class="block mt-1 w-full" type="number" name="pontos_bonus" :value="old('pontos_bonus', $badge?->pontos_bonus ?? 15)" min="0" required />
        <x-input-error for="pontos_bonus" class="mt-1" />
    </div>
</div>
