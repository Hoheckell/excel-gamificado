const { test, expect } = require('@playwright/test');

test.describe('Páginas Públicas', { tag: '@public' }, () => {
  test('Welcome', async ({ page }) => {
    await page.goto('/');
    await expect(page).toHaveTitle('Excel Workshop');
    await expect(page.locator('h1')).toContainText('Curso de Excel');
    await expect(page.getByText('Sistema Pedagógico')).toBeVisible();
  });

  test('Login', async ({ page }) => {
    await page.goto('/login');
    await expect(page.getByLabel('E-mail')).toBeVisible();
    await expect(page.getByLabel('Senha')).toBeVisible();
    await expect(page.getByRole('button', { name: 'Entrar' })).toBeVisible();
  });
});

test.describe('Telas Autenticadas', { tag: '@authenticated' }, () => {
  test.beforeEach(async ({ page }) => { await page.goto('/dashboard'); });

  test('Dashboard, Alunos, Equipes', async ({ page }) => {
    await expect(page.getByText('Painel Operacional')).toBeVisible();
    await expect(page.getByRole('heading', { name: 'Emitir Certificado' })).toBeVisible();

    await page.getByRole('link', { name: 'Alunos' }).first().click();
    await expect(page.getByRole('columnheader', { name: 'Nome' })).toBeVisible();
    await expect(page.getByText('alunos encontrados')).toBeVisible();
    await expect(page.getByRole('link', { name: 'Novo Aluno' })).toBeVisible();

    await page.getByRole('link', { name: 'Equipes' }).first().click();
    await expect(page.getByRole('heading', { name: 'Equipes' })).toBeVisible();
    await expect(page.getByRole('link', { name: 'Sorteio' })).toBeVisible();
  });

  test('Turmas e Categorias', async ({ page }) => {
    await page.getByRole('link', { name: 'Turmas' }).click();
    await expect(page.getByText('EXC001').first()).toBeVisible();

    await page.getByRole('link', { name: 'Categorias' }).click();
    await expect(page.getByText('Mestres dos Dados')).toBeVisible();
  });

  test('Regras e Certificados', async ({ page }) => {
    await page.getByRole('link', { name: 'Regras' }).click();
    await expect(page.getByRole('heading', { name: 'Regras do Sistema' })).toBeVisible();

    await page.goto('/certificado-modelo');
    await expect(page.getByText('Certificado de Conquista')).toBeVisible();

    await page.goto('/certificados/emitir');
    await expect(page.locator('h2').filter({ hasText: 'Emitir Certificado' })).toBeVisible();
  });

  test('Missões exibem título, ordem e pontuação', async ({ page }) => {
    await page.getByRole('link', { name: 'Missões' }).first().click();
    await expect(page.getByRole('heading', { name: 'Missões', exact: true })).toBeVisible();

    await page.getByRole('link', { name: 'Editar' }).first().click();
    await expect(page.getByRole('heading', { name: 'Editar Missão' })).toBeVisible();
    await expect(page.getByLabel('Título da Missão')).not.toHaveValue('');

    await page.getByRole('link', { name: 'Missões' }).first().click();
    await page.getByRole('link', { name: 'Nova Missão' }).click();
    await expect(page.getByLabel('Título da Missão')).toHaveValue('Missão Prática');
    await expect(page.getByLabel('Ordem cronológica')).toHaveValue('1');
    await expect(page.getByLabel('Descrição da Missão')).toBeVisible();
    await expect(page.getByLabel('Pontuação')).toHaveValue('100');
  });

  test('Placar exibe XP, Zequinhômetro e vitrine de badges', async ({ page }) => {
    await page.goto('/placar');
    await expect(page.getByRole('heading', { name: 'Placar Geral' })).toBeVisible();
    await expect(page.getByText('Humor do Juvenildo')).toBeVisible();
    await expect(page.getByText('pontos / 500').first()).toBeVisible();

    for (const badge of ['Zero Mouse', 'Código Limpo', 'Visual Executivo', 'Salva-Vidas']) {
      await expect(page.getByText(badge).first()).toBeVisible();
    }
  });

  test('Professor gerencia o catálogo de badges', async ({ page }) => {
    const suffix = Date.now();
    const nomeInicial = `Badge E2E ${suffix}`;
    const nomeEditado = `Badge E2E Editada ${suffix}`;

    await page.getByRole('link', { name: 'Badges', exact: true }).click();
    await expect(page.getByRole('heading', { name: 'Badges', exact: true })).toBeVisible();

    await page.getByRole('link', { name: 'Nova Badge' }).click();
    await page.getByLabel('Nome da Badge').fill(nomeInicial);
    await page.getByLabel('Ícone').fill('🎯');
    await page.getByLabel('Descrição').fill('Criada pelo teste de interface.');
    await page.getByLabel('Pontos de bônus').fill('18');
    await page.getByRole('button', { name: 'Criar Badge' }).click();

    await expect(page).toHaveURL(/\/badges$/);
    await expect(page.getByText(nomeInicial, { exact: true })).toBeVisible();

    let card = page.getByText(nomeInicial, { exact: true }).locator('..').locator('..');
    await card.getByRole('link', { name: 'Editar' }).click();
    await page.getByLabel('Nome da Badge').fill(nomeEditado);
    await page.getByLabel('Pontos de bônus').fill('25');
    await page.getByRole('button', { name: 'Salvar' }).click();

    await expect(page.getByText(nomeEditado, { exact: true })).toBeVisible();
    await expect(page.getByText('+25 XP', { exact: true })).toBeVisible();

    card = page.getByText(nomeEditado, { exact: true }).locator('..').locator('..');
    page.once('dialog', dialog => dialog.accept());
    await card.getByRole('button', { name: 'Excluir' }).click();
    await expect(page.getByText(nomeEditado, { exact: true })).toHaveCount(0);
  });

  test('Regras documentam economia de XP e rodízio de papéis', async ({ page }) => {
    await page.getByRole('link', { name: 'Regras' }).click();
    await expect(page.getByText('Meta Máxima: 500 Pontos')).toBeVisible();
    await expect(page.getByText('Arquiteto de Dados', { exact: true })).toBeVisible();
    await expect(page.getByText('Auditor de Qualidade', { exact: true })).toBeVisible();
    await expect(page.getByText('Designer Visual', { exact: true })).toBeVisible();
    await expect(page.getByText('Gestor de Entregas', { exact: true })).toBeVisible();
  });
});
