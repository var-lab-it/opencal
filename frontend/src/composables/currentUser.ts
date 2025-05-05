import { ref, onMounted } from 'vue'
import axios from 'axios'
import apiClient from "../services/api";

export function useCurrentUser() {
    const user = ref<{ id: number, email: string } | null>(null)

    onMounted(async () => {
        try {
            const response = await apiClient.get('/me')
            user.value = response.data
        } catch (error) {
            user.value = null
            console.error('Not authenticated or error loading user:', error)
        }
    })

    return { user }
}
