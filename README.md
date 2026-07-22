# Excel Workshop — Sistema Pedagógico Gamificado

Sistema web para gerenciamento de turmas, equipes e missões gamificadas do curso de Excel Básico, com emissão de certificados personalizados.

## Funcionalidades

### Autenticação
- Login/registro com verificação de e-mail (Jetstream + Fortify)
- Perfis: **Professor** e **Aluno**
- Interface totalmente em português

### Turmas
- CRUD completo (professor)
- Código único gerado automaticamente (6 caracteres alfanuméricos)
- Alunos podem entrar em turmas ativas digitando o código
- Filtro de turmas ativas/encerradas

### Equipes
- Sorteio automático com distribuição aleatória
- Criação manual pelo professor ou por alunos (com senha do professor)
- Alunos podem sair da equipe (com autorização)
- Gerenciamento de pontuação (+/- pontos)

### Missões
- CRUD de missões com descrição e pontuação (professor)
- Atribuição de missões a múltiplas equipes
- Alunos iniciam/finalizam missões com **timer H:mm:ss**
- Professor pontua cada aluno individualmente
- Tempo médio da equipe calculado quando todos concluem

### Placar Geral
- Ranking de equipes por pontuação
- Categoria atual baseada na pontuação (Ouro, Prata, Bronze, Crescimento)
- Detalhamento de membros e missões com notas

### Badges
- Catálogo de conquistas com ícone, descrição e bônus de XP
- CRUD do catálogo protegido para professores
- Concessão e remoção de badges por equipe
- Seeder idempotente que mantém badges adicionais existentes

### Certificados
- Modelo visual com proteção contra impressão/captura de tela
- Emissão com preview ao vivo (Alpine.js)
- PDF gerado via DomPDF em A4 paisagem
- Envio automático por e-mail com PDF anexo
- QR Code para validação pública online
- Professor pode reenviar certificados

### Regras
- Página expositiva com as 4 categorias e mecânicas anti-desmotivação
- Sistema de patamares por metas (não ranking competitivo)

## Stack

| Camada | Tecnologia |
|--------|-----------|
| Backend | Laravel 12 + PHP 8.4 |
| Frontend | Blade + Tailwind CSS + Alpine.js |
| Autenticação | Laravel Jetstream (Livewire) + Fortify + Sanctum |
| Banco | SQLite (dev) |
| PDF | DomPDF 3.x |
| Testes | PHPUnit + Playwright |

## Instalação

```bash
git clone git@github.com:Hoheckell/excel-gamificado.git
cd excel-gamificado
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm run build
```

## Executar

```bash
php artisan serve --port=8989
```

### Seeders

O comando de instalação popula dados demonstrativos para desenvolvimento. O README não documenta registros, credenciais ou outros valores armazenados no banco. Consulte os seeders locais e defina credenciais adequadas ao ambiente antes de disponibilizar a aplicação.

Para atualizar somente o catálogo padrão de badges, sem excluir badges adicionais existentes:

```bash
php artisan db:seed --class=BadgeSeeder
```

## Testes

```bash
php artisan test
npm run test:e2e
```

Os testes cobrem autenticação, regras de autorização, turmas, equipes, missões, categorias, badges, placar, regras e certificados.

## Design System

Interface inspirada no Microsoft Excel:
- Paleta verde institucional (`#107c41` / `#1f9a55`)
- Grid de planilha como fundo
- Cards interativos com efeito de "célula ativa"
- Navegação estilo Ribbon + abas de planilha
- Ícones SVG inline com inversão de cor no hover
- Tipografia Montserrat + Playfair Display
