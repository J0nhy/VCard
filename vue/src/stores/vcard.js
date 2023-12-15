import { ref, computed, inject } from 'vue'
import { defineStore } from 'pinia'
import avatarNoneUrl from '@/assets/avatar-none.png'

export const useVcardStore = defineStore('vcard', () => {

    const serverBaseUrl = inject('serverBaseUrl')
    const axios = inject('axios')

    const vcard = ref(null)

    const vcardName = computed(() => vcard.value?.name ?? 'Anonymous')

    const vcardId = computed(() => vcard.value?.id ?? -1)

    const vcardBalance = computed(() => vcard.value?.balance ?? 0)

    const vcardPiggy = computed(() => vcard.value?.custom_data ?? null)



    const vcardPhotoUrl = computed(() =>
    vcard.value?.photo_url
        ? serverBaseUrl + '/storage/fotos/' + vcard.value.photo_url
        : avatarNoneUrl)

    async function loadVcard() {
        try {
            const response = await axios.get('vcard/me')
            vcard.value = response.data.data
        } catch (error) {
            clearUser()
            console.log(error)
            throw error
        }
    }

    function clearVcard() {
        delete axios.defaults.headers.common.Authorization
        sessionStorage.removeItem('token')
        vcard.value = null
    }
/*
    async function login(credentials) {
        try {
            const response = await axios.post('login', credentials)
            axios.defaults.headers.common.Authorization = "Bearer " + response.data.access_token
            sessionStorage.setItem('token', response.data.access_token)
            await loadVcard()
            return true
        }
        catch(error) {
            clearVcard()
            console.log(error)

            return false
        }
    }

    async function logout () {
        try {
            await axios.post('logout')
            axios.defaults.headers.common.Authorization = "Bearer " + sessionStorage.getItem("token")
            clearVcard()
            return true
        } catch (error) {
            return false
        }
    }*/

    async function changePassword(credentials) {
        if (vcardId.value < 0) {
            throw 'Anonymous users cannot change the password!'
        }
        try {
            await axios.patch(`users/${vcard.value.id}/password`, credentials)
            return true
        } catch (error) {
            throw error
        }
    }


    async function restoreToken () {
        let storedToken = sessionStorage.getItem('token')
        if (storedToken) {
            axios.defaults.headers.common.Authorization = "Bearer " + storedToken
            await loadVcard()
            return true
        }
        clearVcard()
        return false
    }

    async function deleteVcard( deleteVcard) {
        // Note that when an error occours, the exception should be
        // catch by the function that called the deleteProject
        const response = await axios.delete('vcard/' + deleteVcard.phone_number)
     
        return response.data.data
    }  


    return {
        vcard,
        vcardId,
        vcardName,
        vcardPhotoUrl,
        vcardBalance,
        vcardPiggy,
        loadVcard,
        clearVcard,
        restoreToken,
        changePassword,
        deleteVcard
    }
})
