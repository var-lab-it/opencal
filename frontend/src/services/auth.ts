import { jwtDecode } from 'jwt-decode';

export function isAuthenticated(): boolean {
    const token = sessionStorage.getItem('jwtToken');
    if (!token) {
        return false;
    };

    try {
        const decoded: any = jwtDecode(token);
        return decoded.exp > Date.now() / 1000;
    } catch (error) {
        return false;
    }
}


export function getToken(): string | null {
    return sessionStorage.getItem('jwtToken');
}

export function logout() {
    sessionStorage.removeItem('jwtToken');
}
