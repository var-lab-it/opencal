import {jwtDecode, JwtPayload} from 'jwt-decode';

export function isAuthenticated(): boolean {
    const token = sessionStorage.getItem('jwtToken');
    if (!token) {
        return false;
    }

    try {
        const decoded: JwtPayload = jwtDecode<JwtPayload>(token);
        if (decoded.exp !== undefined) {
            return decoded.exp > Date.now() / 1000;
        } else {
            return false;
        }
    } catch (error) {
        console.log(error);
        return false;
    }
}


export function getToken(): string | null {
    return sessionStorage.getItem('jwtToken');
}

export function logout() {
    sessionStorage.removeItem('jwtToken');
}
