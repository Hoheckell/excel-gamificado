const { defineConfig, devices } = require('@playwright/test');

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
      name: 'chromium',
      use: { ...devices['Desktop Chrome'] },
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
