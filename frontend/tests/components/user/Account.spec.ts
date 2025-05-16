import {describe, expect, test, vi} from "vitest";
import {render} from "vitest-browser-vue";
import Account from "../../../src/components/user/Account.vue";
import {User} from "../../../src/types/User";

const userMock: User = {
    email: 'test@test.tld',
    id: 123,
    familyName: 'Hans',
    givenName: 'Doe',
    locale: 'en_GB',
    roles: [],
}

vi.mock("vue-i18n", () => {
    return {
        useI18n: () => ({
            t: (msg: string) =>
                ({}[msg] || msg),
        }),
    };
});

vi.mock("../../src/composables/CurrentUser.ts", () => ({
    getCurrentUser: vi.fn,
}));

describe('account', () => {
    test('renders the account', async () => {
        const result = render(Account, {
            props: {
                user: userMock,
            },
            global: {
                mocks: {
                    $t: (msg: string) => msg,
                    useI18n: () => ({
                        t: (key: string) => key, // gibt Schlüssel zurück
                    }),

                }
            }
        });

        expect(result).toMatchSnapshot();
    });
})
