import {EventTypes} from "../types/EventType";
import apiClient from "./api";

export async function requestPassword(email: string): Promise<null> {
    try {
        const response = await apiClient.get(`/event_types?&host.email=${email}`);
        return response.data;
    } catch (error) {
        console.error(error);
        throw error;
    }
}
