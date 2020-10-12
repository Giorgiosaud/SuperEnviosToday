module.exports = {
  env: {
    browser: true,
  },
  extends: [
    'plugin:vue/essential',
    'airbnb-base',
  ],
  globals: {
    $http: true,
    _: true,
  },
  parserOptions: {
    ecmaVersion: 2021,
    sourceType: 'module',
  },
  plugins: [
    'vue',
  ],
  rules: {
    'max-len': ['error', { code: 120 }],

  },
};
