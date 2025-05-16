import {describe, expect, test} from "vitest";
import {render} from 'vitest-browser-vue'
import Logo from "../../src/components/Logo.vue";

describe('logo', () => {
    test('renders the logo with url', async () => {
        const result = render(Logo, {
            props: {
                logoUrl: 'https://example.com/logo.png'
            }
        });

        expect(result).toMatchSnapshot();
    });

    test('renders the logo without url', async () => {
        const result = render(Logo, {
            props: {
                logoUrl: undefined
            }
        });

        expect(result).toMatchSnapshot();
    });
})
