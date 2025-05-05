import {onMounted, ref} from 'vue'
import apiClient from "../services/api";
import {User} from "../types/User";

export function getCurrentUser() {
    const user = ref<User | null>(null)

    onMounted(async () => {
        try {
            const response = await apiClient.get('/me')
            user.value = response.data
        } catch (error) {
            user.value = null
            console.error('Not authenticated or error loading user:', error)
        }
    })

    return {user}
}
