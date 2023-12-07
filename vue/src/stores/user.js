import { ref, computed, inject } from 'vue'
import { defineStore } from 'pinia'
import avatarNoneUrl from '@/assets/avatar-none.png'

export const useUserStore = defineStore('user', () => {

    const serverBaseUrl = inject('serverBaseUrl')
    const axios = inject('axios')

    const user = ref(null)

    const userName = computed(() => user.value?.name ?? 'Anonymous')

    const userId = computed(() => user.value?.id ?? -1)

    const userType = computed(() => user.value?.user_type ?? 'V')


    const userPhotoUrl = computed(() =>
        user.value?.photo_url
            ? serverBaseUrl + '/storage/fotos/' + user.value.photo_url
            : avatarNoneUrl)

            
    async function loadUser() {
        try {
            const response = await axios.get('users/me')
            user.value = response.data.data
            //console.log("user.js: ", response.data.data)

        } catch (error) {
            console.log("erro" + error)
            clearUser()
            throw error
        }
    }

    function clearUser() {
        delete axios.defaults.headers.common.Authorization
        sessionStorage.removeItem('token')
        user.value = null
    }

    async function login(credentials) {
        try {
            //console.log(credentials)
            const response = await axios.post('login', credentials)
            axios.defaults.headers.common.Authorization = "Bearer " + response.data.access_token
            sessionStorage.setItem('token', response.data.access_token)

            await loadUser()
            return true
        }
        catch (error) {
            clearUser()
            return false
        }
    }

    async function logout() {
        try {
            await axios.post('logout')
            axios.defaults.headers.common.Authorization = "Bearer " + sessionStorage.getItem("token")
            clearUser()
            return true
        } catch (error) {
            console.log("erro user.js: " + error)
            return false
        }
    }

    async function restoreToken() {
        let storedToken = sessionStorage.getItem('token')
        if (storedToken) {
            axios.defaults.headers.common.Authorization = "Bearer " + storedToken
            await loadUser()
            return true
        }
        clearUser()
        return false
    }




    return {
        user,
        userId,
        userName,
        userType,
        userPhotoUrl,
        loadUser,
        clearUser,
        login,
        logout,
        restoreToken,
    }
})
