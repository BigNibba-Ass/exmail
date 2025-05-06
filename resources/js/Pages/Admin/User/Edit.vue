<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import {computed, ref, watch} from "vue";
import {UserIcon, EyeIcon, EyeSlashIcon} from "@heroicons/vue/20/solid/index.js";
import {router, usePage} from "@inertiajs/vue3";
import {Link} from "@inertiajs/vue3";
import {NoSymbolIcon, CheckIcon} from "@heroicons/vue/20/solid/index.js";
import {generateRandomString} from "@/Traits.js";

const page = usePage()

const props = defineProps({errors: Object, user: Object})

const userForm = ref({
    ...props.user,
    password_confirmation: ''
})

const handleAdminAttempt = (userId) => {
    if (confirm('Вы уверены?')) {
        router.post(route('admin.users.handle-admin-attempt', userId))
    }
}

const handleBlockAttempt = (userId) => {
    if (confirm('Вы уверены?')) {
        router.post(route('admin.users.handle-block-attempt', userId))
    }
}

const generatePassword = () => {
    userForm.value.password = userForm.value.password_confirmation = generateRandomString()
}

const sendUserForm = () => {
    router.patch(route('admin.users.update', userForm.value.id), userForm.value, {
        onError: () => {
            alert(Object.values(props.errors)[0])
        }
    })
}
</script>

<template>
    <AdminLayout name="Профили">
        <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6 w-full">
            <div class="sm:col-span-6">
                <h2 class="text-2xl font-bold">
                    Профиль {{ props.user.name }}
                </h2>
            </div>
            <div class="sm:col-span-6 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6 w-full">
                <form @submit.prevent="sendUserForm"
                      class="sm:col-span-2 p-5 bg-gray-100 rounded grid grid-cols-1 gap-y-6 gap-x-4">
                    <div class="flex flex-col gap-2">
                        <button
                            @click.prevent="handleAdminAttempt(user.id)"
                            v-if="!user.is_admin"
                            type="button"
                            class="w-full justify-center flex items-center p-3 bg-gray-800 rounded text-white">
                            <user-icon class="w-5 me-auto text-white"/>
                            <span class="me-auto">Назначить администратором</span>
                        </button>
                        <button
                            @click.prevent="handleAdminAttempt(user.id)"
                            v-else
                            type="button"
                            class="w-full justify-center flex items-center p-3 bg-red-600 rounded text-white">
                            <user-icon class="w-5 me-auto text-white"/>
                            <span class="me-auto">Разжаловать администратора</span>
                        </button>
                        <button
                            @click.prevent="handleBlockAttempt(user.id)"
                            v-if="!user.is_blocked"
                            type="button"
                            class="w-full justify-center flex items-center p-3 bg-red-600 rounded text-white">
                            <no-symbol-icon class="w-5 me-auto text-white"/>
                            <span class="me-auto">Заблокировать</span>
                        </button>
                        <button
                            @click.prevent="handleBlockAttempt(user.id)"
                            v-else
                            type="button"
                            class="w-full justify-center flex items-center p-3 bg-green-600 rounded text-white">
                            <check-icon class="w-5 me-auto text-white"/>
                            <span class="me-auto">Разблокировать</span>
                        </button>
                    </div>
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">
                            ФИО </label>
                        <div class="mt-1">
                            <input type="text" id="name"

                                   v-model="userForm.name"
                                   class="flex-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full min-w-0 rounded-md sm:text-sm border-gray-300"/>
                        </div>
                    </div>
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">
                            Город </label>
                        <div class="mt-1">
                            <input type="text" id="name"

                                   v-model="userForm.city"
                                   class="flex-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full min-w-0 rounded-md sm:text-sm border-gray-300"/>
                        </div>
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">
                            E-Mail </label>
                        <div class="mt-1">
                            <input type="email" id="email"
                                   v-model="userForm.email"
                                   class="flex-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full min-w-0 rounded-md sm:text-sm border-gray-300"/>
                        </div>
                    </div>
                    <div>
                        <label for="password" class="block flex items-center text-sm font-medium text-gray-700">
                            Пароль
                            <button
                                type="button"
                                class="px-2 ms-3 justify-center inline-flex items-center p-1 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700"
                                @click.prevent="generatePassword()">
                                Сгенерировать
                            </button>
                        </label>
                        <div class="mt-1">
                            <input type="text" id="password"
                                   v-model="userForm.password"
                                   class="flex-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full min-w-0 rounded-md sm:text-sm border-gray-300"/>
                        </div>
                    </div>
                    <div>
                        <label for="password-confirmation" class="block text-sm font-medium text-gray-700">
                            Подтверждение пароля </label>
                        <div class="mt-1">
                            <input v-model="userForm.password_confirmation"
                                   type="text" id="password-confirmation"

                                   class="flex-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full min-w-0 rounded-md sm:text-sm border-gray-300"/>
                        </div>
                    </div>
                    <div>
                        <button
                            type="submit"
                            class="w-full transition justify-center inline-flex items-center p-3 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-green-700">
                            <user-icon class="w-5 me-auto text-indigo-white"/>
                            <span class="me-auto">Сохранить</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>

<style>
.icon-fix {
    width: 20px;
}
</style>
