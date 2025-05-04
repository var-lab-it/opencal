import pluginVue from 'eslint-plugin-vue'
import globals from 'globals'
import tseslint from '@typescript-eslint/eslint-plugin'
import tsParser from '@typescript-eslint/parser'
import vueParser from 'vue-eslint-parser'

export default [
    ...pluginVue.configs['flat/recommended'],

    {
        files: ['**/*.vue'],
        languageOptions: {
            parser: vueParser,
            parserOptions: {
                parser: tsParser,  // Vue-Parser nutzt dann TypeScript Parser für <script setup lang="ts">
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
            '@typescript-eslint': tseslint
        },
        rules: {
            // eigene Regeln hier
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
            // eigene Regeln hier
        }
    }
]
