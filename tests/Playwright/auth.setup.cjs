const { test: setup, expect } = require('@playwright/test');
const path = require('path');

const authFile = path.join(__dirname, '../../test-results/.auth/professor.json');

setup('autenticar professor', async ({ page }) => {
  await page.goto('/login');
  await page.getByLabel('E-mail').fill('hoheckell.info@gmail.com');
  await page.getByLabel('Senha').fill('password');
  await page.getByRole('button', { name: 'Entrar' }).click();
  await expect(page).toHaveURL(/dashboard/);
  await page.context().storageState({ path: authFile });
});
