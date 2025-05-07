import pluginVue from 'eslint-plugin-vue'
import globals from 'globals'
import tseslint from '@typescript-eslint/eslint-plugin'
import tsParser from '@typescript-eslint/parser'
import vueParser from 'vue-eslint-parser'
import unicorn from 'eslint-plugin-unicorn'

export default [
    ...pluginVue.configs['flat/recommended'],
    {
        files: ['**/*.vue'],
        languageOptions: {
            parser: vueParser,
            parserOptions: {
                parser: tsParser,
                project: './tsconfig.json',
                extraFileExtensions: ['.vue'],
                ecmaVersion: 'latest',
                sourceType: 'module'
            },
            globals: {
                ...globals.browser
            }
        },
        plugins: {
            '@typescript-eslint': tseslint,
            unicorn
        },
        rules: {
            'vue/multi-word-component-names': 'off',
            'vue/no-unused-vars': 'warn',
            'vue/html-indent': ['error', 2],
            'vue/html-self-closing': [
                'error',
                {
                    html: {
                        void: 'always',
                        normal: 'never',
                        component: 'always'
                    }
                }
            ],
            'unicorn/prevent-abbreviations': 'off'
        }
    },
    {
        files: ['**/*.ts'],
        languageOptions: {
            parser: tsParser,
            parserOptions: {
                project: './tsconfig.json',
                ecmaVersion: 'latest',
                sourceType: 'module'
            },
            globals: {
                ...globals.browser
            }
        },
        plugins: {
            '@typescript-eslint': tseslint
        },
        rules: {
            '@typescript-eslint/no-unused-vars': ['warn', { argsIgnorePattern: '^_' }],
            '@typescript-eslint/no-explicit-any': 'warn'
        }
    }
]
