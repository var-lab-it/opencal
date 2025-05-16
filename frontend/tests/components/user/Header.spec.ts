import {describe, expect, test} from "vitest";
import {render} from "vitest-browser-vue";
import Header from "../../../src/components/user/Header.vue";
import {User} from "../../../src/types/User";

const userMock: User = {
    email: 'test@test.tld',
    id: 123,
    familyName: 'Hans',
    givenName: 'Doe',
    locale: 'en_GB',
    roles: [],
}

describe('header', () => {
    test('renders the header component', async () => {
        const result = render(Header, {
            props: {
                user: userMock,
            },
            global: {
                mocks: {
                    $t: (msg: string) => msg,
                }
            }
        });

        expect(result).toMatchSnapshot();
    });
});
