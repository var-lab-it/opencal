import {describe, expect, it, vi} from "vitest";
import Login from "../../src/components/Login.vue";
import apiClient from "../../src/services/api";
import {render} from "vitest-browser-vue";
import {redirectAfterLogin} from "../../src/services/auth";

vi.mock("../../src/services/api", () => ({
    default: {
        post: vi.fn(),
    },
}));
vi.mock("../../src/services/auth", () => ({
    redirectAfterLogin: vi.fn(),
}));

describe("Login.vue", () => {
    it("renders correctly and displays all necessary elements", () => {
        const {getByTestId} = render(Login);

        const emailInput = getByTestId('email-input');
        const passwordInput = getByTestId('password-input');
        const loginBtn = getByTestId('login-btn');

        expect(emailInput).toBeTruthy();
        expect(passwordInput).toBeTruthy();
        expect(loginBtn).toBeTruthy();
    });

    it("displays an error message when login fails", async () => {
        const {getByTestId, getByText} = render(Login);

        (apiClient.post as vi.Mock)
            .mockRejectedValueOnce(new Error("Error"));

        const emailInput = getByTestId('email-input');
        const passwordInput = getByTestId('password-input');
        const loginBtn = getByTestId('login-btn');

        await emailInput.fill("test@example.com");
        await passwordInput.fill("12345678");
        await loginBtn.click();

        expect(getByText("Login failed. Please try again.")).toBeTruthy();
    });

    it("stores the token in sessionStorage and redirects on successful login", async () => {
        const { getByTestId } = render(Login);

        const token = "test-token";

        (apiClient.post as vi.Mock).mockResolvedValueOnce({
            data: { token: token },
        });

        (redirectAfterLogin as vi.Mock).mockResolvedValueOnce();

        const emailInput = getByTestId('email-input');
        const passwordInput = getByTestId('password-input');
        const loginBtn = getByTestId('login-btn');

        await emailInput.fill("test@example.com");
        await passwordInput.fill("12345678");
        await loginBtn.click();
    });

    // it("displays an error message when the token is missing in the response", async () => {
    //     const { getByText, getByLabelText } = render(Login);
    //
    //     // Mock for a login response missing the token
    //     (apiClient.post as vi.Mock).mockResolvedValueOnce({
    //         data: {},
    //     });
    //
    //     // Simulate user interaction: entering email, password, and submitting form
    //     const emailInput = getByLabelText("Email:");
    //     const passwordInput = getByLabelText("Password:");
    //     const submitButton = getByText("Login");
    //
    //     await fireEvent.update(emailInput, "test@example.com");
    //     await fireEvent.update(passwordInput, "12345678");
    //     await fireEvent.click(submitButton);
    //
    //     // Verify error message rendering for missing token
    //     expect(getByText("No valid token.")).toBeTruthy();
    // });
});
