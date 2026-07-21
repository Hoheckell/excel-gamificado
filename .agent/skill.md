# SKILL: Desenvolvimento e Adaptação do Sistema GBL — Excel Básico

## 1. Finalidade e Conceito do Sistema
O sistema aplica Game-Based Learning (GBL) e RPG para gamificar o curso presencial de Excel Básico (40 horas/aula). A narrativa central transforma os alunos em **Consultorias de Inteligência de Dados** contratadas para estruturar o escritório da **Iguatu Agro&Comércio**, lidando com os problemas do gerente **Juvenildo Canindé** e do estagiário **Zequinha**.

**Objetivo Técnico:** Sustentar o engajamento, validar a avaliação contínua do PPC e emitir certificados pedagógicos baseados no patamar de desempenho (XP).

---

## 2. Metodologia e Estratégia de Sala (Regras de Negócio)

### A. Dinâmica de Entrega
* **Prática Híbrida:** Todos praticam individualmente nas máquinas, mas cada equipe entrega apenas **1 Arquivo Mestre** por aula para avaliação.
* **Continuidade (Base Mestra):** O arquivo final aprovado em uma missão é o arquivo de entrada obrigatório da missão seguinte.
* **Rodízio de Papéis (RPG Roles):** Os integrantes alternam funções diárias para evitar passividade:
  * **Arquiteto de Dados:** Digitação de fórmulas e lógica.
  * **Auditor de Qualidade:** Caça a erros (`#VALOR!`, `#DIV/0!`) e validação de tipos de dados.
  * **Designer Visual:** Estética, formatação e legibilidade.
  * **Gestor de Entregas:** Controle de tempo e envio do arquivo consolidado.

### B. Economia de Pontos (500 XP Máximo)
* **Missão Principal (100 pts):** Entrega obrigatória de negócio.
* **Quest Secundária (+20 a +40 pts):** Desafio opcional avançado pedido pelo Zequinha.
* **Estilo e Identidade (+10 a +30 pts):** Acabamento visual e capricho na planilha.
* **Badges Diárias (+15 pts):** Prêmios de comportamento técnico (ex: *Zero Mouse*, *Código Limpo*, *Visual Executivo*, *Salva-Vidas*).

### C. Mecânicas Anti-Desmotivação
* **Pontuação Progressiva:** Primeiras missões valem menos (50-80 pts); últimas valem mais (até 150 pts).
* **Bônus de Colaboração (+20 pts):** Equipes que terminam cedo podem orientar outras (sem tocar no mouse).
* **Desafio Relâmpago (+15 pts):** Microtarefas de 10 minutos no início da aula para equipes em desvantagem subirem no ranking.

---

## 3. Patamares de Conquista (Categorias no Certificado)
A pontuação substitui o ranking competitivo por metas de excelência:
* **450 a 500 pts — Ouro (Mestres dos Dados):** Fórmulas e lógica sem erros, visual profissional.
* **380 a 449 pts — Prata (Analistas de Planilhas):** Estrutura e funções essenciais dominadas.
* **300 a 379 pts — Bronze (Especialistas em Formatação):** Foco em design, organização e gráficos.
* **0 a 299 pts — Crescimento (Superação e Trabalho em Equipe):** Valorização do esforço, assiduidade e colaboração.

---

## 4. Análise da Estrutura Atual do Banco de Dados
O banco atual cobre com precisão o fluxo acadêmico principal:
* **`categorias`**: Perfeitamente alinhada para os patamares de XP (`pt_classificacao`, `cor`, `titulo_certificado`).
* **`equipes` & `missoes`**: Suportam a pontuação progressiva.
* **`equipe_missao_user`**: Estrutura ideal para rastrear o envolvimento individual nas entregas em grupo.
* **`certificados`**: Pronta para receber os dados automatizados da gamificação.

---

## 5. Atualizações Necessárias no Banco de Dados (Migrations)
Para ativar o RPG (Iguatu Agro&Comércio), o rodízio de papéis e o sistema de Badges diárias sem inflar o sistema, execute apenas as seguintes adições:

### A. Adicionar Papéis e Ordem nas Tabelas Existentes
Na tabela `equipe_missao_user`, falta o registro da "Classe/Papel" do aluno naquela missão. Na tabela `missoes`, falta a ordenação cronológica.

```sql
-- Migration: Add columns to missoes and equipe_missao_user
ALTER TABLE "missoes" ADD COLUMN "titulo" varchar not null default 'Missão Prática';
ALTER TABLE "missoes" ADD COLUMN "ordem" integer not null default 1;

ALTER TABLE "equipe_missao_user" ADD COLUMN "papel" varchar null; 
-- Valores esperados: 'arquiteto', 'auditor', 'designer', 'gestor'


```

### B. Criar Sistema de Conquistas (Badges/Medalhas)

Para premiar atitudes no laboratório (+15 pts: *Zero Mouse*, *Código Limpo*, etc.), crie duas tabelas simples:

```sql
-- Tabela de cadastro das medalhas
CREATE TABLE "badges" (
    "id" integer primary key autoincrement not null,
    "nome" varchar not null,
    "icone" varchar not null, -- Emoji ou classe icon (ex: '⌨️')
    "descricao" text not null,
    "pontos_bonus" integer not null default 15
);

-- Tabela pivô: quais equipes ganharam quais medalhas
CREATE TABLE "equipe_badge" (
    "id" integer primary key autoincrement not null,
    "equipe_id" integer not null,
    "badge_id" integer not null,
    "created_at" datetime default CURRENT_TIMESTAMP,
    foreign key("equipe_id") references "equipes"("id") on delete cascade,
    foreign key("badge_id") references "badges"("id") on delete cascade
);
CREATE UNIQUE INDEX "equipe_badge_unique" on "equipe_badge" ("equipe_id", "badge_id");

```

---

## 6. Lógica de Backend (Regras para Controllers/Services)

### A. Cálculo Dinâmico de XP e Categoria

A pontuação total de uma equipe consolida a soma das missões e das medalhas extras:


$$\text{XP Total} = \sum (\text{missoes.pontuacao}) + \sum (\text{badges.pontos\_bonus})$$

* No Model `Equipe`, crie um atributo acessor `getXpTotalAttribute()` que faça essa soma.


* Para definir a Categoria no certificado, consulte a tabela `categorias` onde `pt_classificacao <= XP Total`, ordenando de forma decrescente para pegar o maior patamar alcançado.



### B. O "Zequinhômetro" (Medidor de Humor)

O status da empresa exibido no topo da interface deve ser calculado via Controller através da **média de XP da Turma**:

* **Média < 200 pts:** Retorna estado `panico` (Zequinha em Pânico / Chefe Zangado 😱).


* **Média 200 a 350 pts:** Retorna estado `tenso` (Zequinha Sobrevivendo 😰).


* **Média > 350 pts:** Retorna estado `satisfeito` (Chefe Satisfeito / Zequinha Promovido 😎).



### C. Validação de Rodízio de Papéis (GBL Core)

No momento em que o líder da equipe iniciar ou submeter uma missão via `equipe_missao_user`:

* O backend deve validar se todos os membros ativos da equipe receberam um `papel` preenchido.


* **Regra de Negócio:** Impedir que o mesmo `user_id` repita o papel da missão imediatamente anterior (`ordem - 1`), forçando o rodízio prático.



---

## 7. Integração e UI/UX no Frontend (Blade)

O portal deve integrar os componentes visuais de imersão diretamente com os dados do backend:

1. **Barra de Progresso (XP Bar):** Componente dinâmico no topo do painel substituindo larguras estáticas pelo cálculo real em porcentagem: `width: {{ min(100, ($equipe->xp_total / 500) * 100) }}%`.


2. **Medidor de Humor (Zequinhômetro):** Widget no cabeçalho alimentado pela variável `$humorChefe` calculada no Controller.


3. **Grid de Conquistas Dinâmico (Badges):** Loop em `App\Models\Badge::all()`, checando via `in_array` se a equipe possui o ID da medalha para alternar o CSS entre opaco (bloqueado) e colorido/brilhante (desbloqueado).


4. **Seletor de Papéis da Rodada:** Modal de seleção acionado antes de iniciar a missão para que a equipe defina no dropdown quem será o *Arquiteto*, *Auditor*, *Designer* e *Gestor*, gravando na coluna `papel`.



---

## 8. Mapeamento de Contexto: Usuário Logado vs. Estrutura do Sistema

Para estruturar as views e evitar consultas redundantes no banco de dados, a separação de responsabilidades no envio de variáveis deve seguir o seguinte mapeamento:

| Contexto / Origem dos Dados | Variáveis / Elementos Disponibilizados | Aplicação na Interface |
| --- | --- | --- |
| **Usuário Logado**<br>

<br>*(Sessão Auth / Equipe Ativa)* | `auth()->user()->id`<br>

<br>`auth()->user()->equipe`<br>

<br>`$equipe->xp_total`<br>

<br>`$equipe->badges`<br>

<br>`$p->papel` | * Identificação individual e da equipe na navegação.

<br>

<br>* Preenchimento da barra de XP da equipe.

<br>

<br>* Iluminação das Badges destravadas na vitrine.

<br>

<br>* Exibição da tag de cargo do aluno no acordeão (🛠️, 🔍, 🎨, ⏱️).

 |
| **Estrutura do Sistema**<br>

<br>*(Catálogos e Variáveis Globais)* | `$missoes` *(Ordenadas)*<br>

<br>`$badges` *(Catálogo Geral)*<br>

<br>`$categorias` *(Patamares)*<br>

<br>`$humorChefe` *(Média Turma)* | * Listagem cronológica das missões e desafios disponíveis.

<br>

<br>* Desenho do grid de medalhas (incluindo as bloqueadas).

<br>

<br>* Legenda de pontuação máxima (500 pts) e metas de faixas.

<br>

<br>* Exibição do "Zequinhômetro" no cabeçalho superior.

 |

```

```
