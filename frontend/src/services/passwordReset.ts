import apiClient from "./api";

export async function requestPassword(email: string): Promise<null> {
    try {
        const response = await apiClient.post(`/password/request`, {
            email: email,
        });
        return response.data;
    } catch (error) {
        console.error(error);
        throw error;
    }
}

export async function resetPassword(email: string, token: string, password: string): Promise<null> {
    try {
        const response = await apiClient.post(`/password/reset`, {
            email: email,
            token: token,
            password: password
        });
        return response.data;
    } catch (error) {
        console.error(error);
        throw error;
    }
}
