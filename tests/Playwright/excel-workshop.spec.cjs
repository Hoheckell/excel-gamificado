const { test, expect } = require('@playwright/test');

const BASE = 'http://localhost:8989';
const PROF_EMAIL = 'hoheckell.info@gmail.com';
const PROF_PASS = 'password';

async function ensureLoggedIn(page) {
  await page.goto(BASE + '/dashboard');
  if (page.url().includes('/login')) {
    await page.getByLabel('E-mail').fill(PROF_EMAIL);
    await page.getByLabel('Senha').fill(PROF_PASS);
    await page.getByRole('button', { name: 'Entrar' }).click();
    await page.waitForLoadState('networkidle');
  }
  await expect(page).toHaveURL(/dashboard/, { timeout: 10000 });
}

test.describe('Páginas Públicas', () => {
  test('Welcome', async ({ page }) => {
    await page.goto(BASE);
    await expect(page).toHaveTitle('Excel Workshop');
    await expect(page.locator('h1')).toContainText('Curso de Excel');
    await expect(page.getByText('Sistema Pedagógico')).toBeVisible();
  });

  test('Login', async ({ page }) => {
    await page.goto(BASE + '/login');
    await expect(page.getByLabel('E-mail')).toBeVisible();
    await expect(page.getByLabel('Senha')).toBeVisible();
    await expect(page.getByRole('button', { name: 'Entrar' })).toBeVisible();
  });
});

test.describe('Telas Autenticadas', () => {
  test.beforeEach(async ({ page }) => { await ensureLoggedIn(page); });

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

    await page.goto(BASE + '/certificado-modelo');
    await expect(page.getByText('Certificado de Conquista')).toBeVisible();

    await page.goto(BASE + '/certificados/emitir');
    await expect(page.locator('h2').filter({ hasText: 'Emitir Certificado' })).toBeVisible();
  });
});
