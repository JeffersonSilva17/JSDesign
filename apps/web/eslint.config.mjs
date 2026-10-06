import react from 'eslint-plugin-react';
import reactHooks from 'eslint-plugin-react-hooks';
import tseslint from 'typescript-eslint';

const localNextCoreWebVitals = {
  rules: {
    'no-img-element': jsxElementRule('img', 'Use next/image instead of an img element.'),
    'no-head-element': jsxElementRule('head', 'Use the Next.js metadata APIs instead of a head element.'),
    'no-sync-scripts': jsxElementRule('script', 'Use next/script instead of a script element.'),
    'no-css-tags': {
      meta: { type: 'problem', messages: { forbidden: 'Import styles instead of adding stylesheet link tags.' } },
      create(context) {
        return {
          JSXOpeningElement(node) {
            if (jsxName(node) === 'link' && staticAttribute(node, 'rel') === 'stylesheet') context.report({ node, messageId: 'forbidden' });
          },
        };
      },
    },
    'no-html-link-for-pages': {
      meta: { type: 'problem', messages: { forbidden: 'Use next/link for internal navigation.' } },
      create(context) {
        return {
          JSXOpeningElement(node) {
            const href = staticAttribute(node, 'href');
            if (jsxName(node) === 'a' && href?.startsWith('/')) context.report({ node, messageId: 'forbidden' });
          },
        };
      },
    },
    'no-async-client-component': {
      meta: { type: 'problem', messages: { forbidden: 'Client Components cannot be async functions.' } },
      create(context) {
        let isClientComponent = false;
        return {
          Program(node) { isClientComponent = node.body[0]?.type === 'ExpressionStatement' && node.body[0].expression.type === 'Literal' && node.body[0].expression.value === 'use client'; },
          'FunctionDeclaration[async=true], ArrowFunctionExpression[async=true], FunctionExpression[async=true]'(node) {
            if (isClientComponent) context.report({ node, messageId: 'forbidden' });
          },
        };
      },
    },
    'no-assign-module-variable': {
      meta: { type: 'problem', messages: { forbidden: 'Do not assign to the module variable.' } },
      create(context) {
        return {
          AssignmentExpression(node) {
            if (node.left.type === 'Identifier' && node.left.name === 'module') context.report({ node, messageId: 'forbidden' });
          },
        };
      },
    },
  },
};

function jsxElementRule(element, message) {
  return {
    meta: { type: 'problem', messages: { forbidden: message } },
    create(context) {
      return { JSXOpeningElement(node) { if (jsxName(node) === element) context.report({ node, messageId: 'forbidden' }); } };
    },
  };
}

function jsxName(node) {
  return node.name.type === 'JSXIdentifier' ? node.name.name : null;
}

function staticAttribute(node, name) {
  const attribute = node.attributes.find((item) => item.type === 'JSXAttribute' && item.name.name === name);
  if (!attribute?.value) return null;
  if (attribute.value.type === 'Literal' && typeof attribute.value.value === 'string') return attribute.value.value;
  if (attribute.value.type === 'JSXExpressionContainer' && attribute.value.expression.type === 'Literal' && typeof attribute.value.expression.value === 'string') return attribute.value.expression.value;
  return null;
}

const eslintConfig = [
  { ignores: ['.next*/**', 'node_modules/**', 'coverage/**', 'playwright-report/**', 'test-results/**'] },
  ...tseslint.configs.recommended,
  react.configs.flat.recommended,
  react.configs.flat['jsx-runtime'],
  reactHooks.configs.flat.recommended,
  {
    files: ['**/*.{js,jsx,ts,tsx,mjs,cjs}'],
    plugins: { 'local-next': localNextCoreWebVitals },
    settings: { react: { version: 'detect' } },
    languageOptions: { parserOptions: { ecmaFeatures: { jsx: true } } },
    rules: {
      'no-eval': 'error',
      'no-implied-eval': 'error',
      'no-new-func': 'error',
      'local-next/no-img-element': 'error',
      'local-next/no-head-element': 'error',
      'local-next/no-sync-scripts': 'error',
      'local-next/no-css-tags': 'error',
      'local-next/no-html-link-for-pages': 'error',
      'local-next/no-async-client-component': 'error',
      'local-next/no-assign-module-variable': 'error',
    },
  },
];

export default eslintConfig;
