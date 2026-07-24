# Excel Workshop — Sistema Pedagógico Gamificado

Sistema web para gerenciamento de turmas, equipes e missões gamificadas do curso de Excel Básico, com emissão de certificados personalizados.

## Funcionalidades

### Autenticação
- Login/registro com verificação de e-mail (Jetstream + Fortify)
- Acesso às rotas internas bloqueado até a confirmação do endereço
- Alunos cadastrados pelo professor também recebem o link de verificação
- Reenvio disponível para contas que ainda não confirmaram o e-mail
- Perfis: **Professor** e **Aluno**
- Interface totalmente em português

### Turmas
- CRUD completo (professor)
- Código único gerado automaticamente (6 caracteres alfanuméricos)
- Alunos podem entrar em turmas ativas digitando o código
- Filtro de turmas ativas/encerradas
- Conclusão explícita e irreversível pelo professor responsável
- Ao concluir, anexos de entregas e materiais das missões vinculadas são apagados definitivamente
- Downloads removidos são substituídos por aviso de indisponibilidade, preservando o histórico da atividade

### Equipes
- Sorteio automático com distribuição aleatória
- Criação manual pelo professor ou por alunos (com senha do professor)
- Alunos podem sair da equipe (com autorização)
- Gerenciamento de pontuação (+/- pontos)

### Missões
- CRUD de missões com descrição HTML sanitizada, URL opcional e pontuação (professor)
- Quantidade ilimitada de materiais anexos, com até 3 MB por arquivo e validação de extensão e conteúdo
- Formatos aceitos: PNG, JPG, XLS, XLSX, DOCS, DOC, CSV, TXT e PDF
- Arquivos armazenados de forma privada e servidos somente por rotas autenticadas e autorizadas
- Atribuição de missões a múltiplas equipes
- Alunos iniciam/finalizam missões com **timer H:mm:ss**
- Professor avalia cada aluno com nota, rubrica de competências, feedback e próximo passo
- Tempo médio da equipe calculado quando todos concluem
- Multiclasse automática para 1 a 4 presentes, sempre cobrindo os quatro papéis
- Equipes com até 3 presentes recebem 5 minutos extras sem alteração de XP
- Rodízio compara a distribuição anterior e se adapta às mudanças de presença

### Minha Jornada
- Painel do aluno com missão atual, papel, andamento, próxima ação e feedback privado
- Progresso por missões concluídas, sem streaks ou punições por pausa
- Badges apresentados como evidências de competências e comportamentos
- Funções acumuladas e compensação de tempo apresentadas de forma transparente

### Contingência de equipes
- 3 presentes: Arquiteto, Designer e Auditor + Gestor
- 2 presentes: Núcleo Técnico e Núcleo Executivo
- 1 presente: Consultor Sênior com os quatro papéis
- Após a terceira missão, equipes com menos de 3 alunos autorizados geram sugestão de reagrupamento no painel docente
- O sistema nunca funde equipes automaticamente nem transfere XP

### Progresso e Placar
- Alunos acompanham apenas a própria equipe contra os patamares
- Ranking completo de equipes disponível somente para professores
- Categoria atual baseada na pontuação (Ouro, Prata, Bronze, Crescimento)
- Detalhamento pedagógico com notas e rubricas conforme a autorização do usuário

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
| Backend | Laravel 13 + PHP 8.3 ou superior |
| Frontend | Blade + Tailwind CSS + Alpine.js |
| Autenticação | Laravel Jetstream (Livewire) + Fortify + Sanctum |
| Banco | SQLite (dev) |
| PDF | DomPDF 3.x |
| Testes | PHPUnit + Playwright |

O build utiliza Node.js 22.12 ou superior dentro da linha 22. A versão recomendada está registrada em `.nvmrc`.

## Instalação

```bash
git clone git@github.com:Hoheckell/excel-gamificado.git
cd excel-gamificado
composer install
npm ci
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

Os testes cobrem autenticação, regras de autorização, turmas, equipes, missões, categorias, badges, placar, regras e certificados. A cobertura de missões inclui sanitização de HTML, validação e armazenamento privado de anexos, limite por arquivo, exclusão ao concluir a turma, bloqueio de novos envios e indisponibilidade de URLs antigas.

## Design System

Interface inspirada no Microsoft Excel:
- Paleta verde institucional (`#107c41` / `#1f9a55`)
- Grid de planilha como fundo
- Cards interativos com efeito de "célula ativa"
- Navegação estilo Ribbon + abas de planilha
- Ícones SVG inline com inversão de cor no hover
- Tipografia Montserrat + Playfair Display
