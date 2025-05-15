import {expect, test} from "vitest";
import { render } from 'vitest-browser-vue'
import Logo from "../../src/components/Logo.vue";

test('renders the logo', async () => {
    const {getByAltText} = render(Logo);

    expect(getByAltText('Logo')).toBeInTheDocument();
});
