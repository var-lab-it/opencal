import {describe, expect, test} from "vitest";
import {render} from "vitest-browser-vue";
import Dashboard from "../../../src/components/user/Dashboard.vue";

describe('dashboard', () => {
    test('renders the dashboard component', async () => {
        const result = render(Dashboard);

        expect(result).toMatchSnapshot();
    });
});
