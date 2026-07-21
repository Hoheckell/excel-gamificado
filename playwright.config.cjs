const { defineConfig, devices } = require('@playwright/test');
const path = require('path');

module.exports = defineConfig({
  testDir: './tests/Playwright',
  timeout: 30000,
  expect: { timeout: 10000 },
  fullyParallel: false,
  retries: 0,
  workers: 1,
  reporter: 'list',
  use: {
    baseURL: 'http://localhost:8989',
    trace: 'on-first-retry',
    screenshot: 'only-on-failure',
    headless: true,
  },
  projects: [
    {
      name: 'auth-setup',
      testMatch: /auth\.setup\.cjs/,
    },
    {
      name: 'chromium-public',
      use: { ...devices['Desktop Chrome'] },
      grep: /@public/,
      testIgnore: /auth\.setup\.cjs/,
    },
    {
      name: 'chromium-authenticated',
      use: {
        ...devices['Desktop Chrome'],
        storageState: path.join(__dirname, 'test-results/.auth/professor.json'),
      },
      dependencies: ['auth-setup'],
      grep: /@authenticated/,
      testIgnore: /auth\.setup\.cjs/,
    },
  ],
  webServer: {
    command: 'php artisan serve --port=8989',
    url: 'http://localhost:8989',
    reuseExistingServer: true,
    cwd: '.',
    timeout: 10000,
  },
});
