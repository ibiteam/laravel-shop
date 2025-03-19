import * as path from 'node:path';
import { defineConfig } from 'rspress/config';

export default defineConfig({
  root: path.join(__dirname, 'docs'),
  title: 'Laravel Shop',
  icon: '/laravel-shop.png',
  logo: {
    light: '/laravel-shop-logo.png',
    dark: '/laravel-shop-logo.png',
  },
  themeConfig: {
    socialLinks: [
      {
        icon: 'github',
        mode: 'link',
        content: 'https://github.com/ibiteam/laravel-shop',
      },
    ],
  },
});
