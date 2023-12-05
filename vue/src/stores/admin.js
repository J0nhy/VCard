import { ref, computed, inject } from 'vue'
import { defineStore } from 'pinia'
import avatarNoneUrl from '@/assets/avatar-none.png'

export const useAdminStore = defineStore('admin', () => {

    const serverBaseUrl = inject('serverBaseUrl')
    const axios = inject('axios')

    const admin = ref(null)

    const adminName = computed(() => admin.value?.name ?? 'Anonymous')

    const adminId = computed(() => admin.value?.id ?? -1)

    const adminType = computed(() => admin.value?.type ?? 'M')

    const adminPhotoUrl = computed(() =>
        admin.value?.photo_url
        ? serverBaseUrl + '/storage/fotos/' + admin.value.photo_url
        : avatarNoneUrl)

    async function loadAdmin() {
        try {
            const response = await axios.get('admins/me')
            admin.value = response.data.data
        } catch (error) {
            console.log(error)
            clearAdmin()
            throw error
        }
    }

    function clearAdmin() {
        delete axios.defaults.headers.common.Authorization
        sessionStorage.removeItem('token')
        admin.value = null
    }

    async function login(credentials) {
        try {
            console.log(credentials)
            const response = await axios.post('login', credentials)
            axios.defaults.headers.common.Authorization = "Bearer " + response.data.access_token
            sessionStorage.setItem('token', response.data.access_token)
            await loadAdmin()
            return true
        }
        catch(error) {
            clearAdmin()
            return false
        }
    }

    async function logout () {
        try {
            await axios.post('logout')
            axios.defaults.headers.common.Authorization = "Bearer " + sessionStorage.getItem("token")
            clearAdmin()
            return true
        } catch (error) {
            return false
        }
    }

    async function changePassword(credentials) {
        if (adminId.value < 0) {
            throw 'Anonymous admins cannot change the password!'
        }
        try {
            await axios.patch(`admins/${admin.value.id}/password`, credentials)
            return true
        } catch (error) {
            throw error
        }
    }


    async function restoreToken () {
        let storedToken = sessionStorage.getItem('token')
        if (storedToken) {
            axios.defaults.headers.common.Authorization = "Bearer " + storedToken
            await loadAdmin()
            return true
        }
        clearAdmin()
        return false
    }

    async function deletevCard( deleteVcard) {
        // Note that when an error occours, the exception should be
        // catch by the function that called the deleteProject
        const response = await axios.delete('vcard/' + deleteVcard.phone_number)
        return response.data.data
    }  


    return {
        admin,
        adminId,
        adminName,
        adminType,
        adminPhotoUrl,
        loadAdmin,
        clearAdmin,
        login,
        logout,
        restoreToken,
        changePassword,
        deletevCard
    }
})
