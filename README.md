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
- A resposta textual da equipe pode ser corrigida enquanto nenhum integrante tiver sido avaliado
- Depois da primeira avaliação, a resposta textual fica bloqueada para preservar o conteúdo avaliado
- O professor pode solicitar um novo anexo após a conclusão, sempre com feedback obrigatório
- O reenvio substitui o arquivo anterior, preserva a conclusão e a pontuação vigente e libera nova avaliação

### Fluxo de entrega e reavaliação
1. Cada integrante presente inicia a missão com o papel definido para a rodada.
2. A conclusão é individual; a entrega da equipe só é liberada quando todos os presentes concluírem.
3. A equipe envia uma única resposta textual e/ou um único anexo, conforme a configuração da missão.
4. Antes da avaliação, a equipe pode editar a resposta textual. Um anexo já enviado não pode ser trocado espontaneamente.
5. O professor avalia cada integrante com nota, rubrica, feedback e próximo passo.
6. Se o arquivo precisar de correção, o professor solicita o reenvio e escreve um feedback específico para a equipe.
7. A equipe envia o novo anexo; o sistema apaga o arquivo substituído e mantém as pontuações registradas.
8. O professor pode revisar as avaliações diante do novo arquivo. A resposta textual continua bloqueada depois da primeira avaliação.

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

O ciclo de entrega possui cenários específicos para:

- bloqueio até todos os integrantes presentes concluírem;
- edição da resposta textual somente antes da avaliação;
- solicitação de reenvio restrita ao professor, após a conclusão e com feedback obrigatório;
- substituição segura do anexo e remoção do arquivo anterior;
- preservação da pontuação durante o reenvio;
- nova avaliação do professor após o recebimento do arquivo corrigido;
- comunicação da jornada completa na página de regras.

## Design System

Interface inspirada no Microsoft Excel:
- Paleta verde institucional (`#107c41` / `#1f9a55`)
- Grid de planilha como fundo
- Cards interativos com efeito de "célula ativa"
- Navegação estilo Ribbon + abas de planilha
- Ícones SVG inline com inversão de cor no hover
- Tipografia Montserrat + Playfair Display
