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

const props = defineProps({errors: Object})

const users = computed(() => {
    return page.props.users
})


const currentTab = ref(0)

const showPassword = ref(false)

const tabs = [
    {name: 'Добавление'},
    {name: 'Список'},
]

const generatePassword = () => {
    userForm.value.password = userForm.value.password_confirmation = generateRandomString();
    showPassword.value = true
}

const userForm = ref({
    name: '',
    email: '',
    city: '',
    phone_number: '',
    password: '',
    password_confirmation: '',
})

const sendUserForm = () => {
    console.log(userForm.value)
    router.post(route('admin.users.store'), userForm.value, {
        onSuccess: () => {
            userForm.value = {
                name: '',
                email: '',
                password: '',
                password_confirmation: '',
            }
            currentTab.value = 1
        }, onError: () => {
            alert(Object.values(props.errors)[0])
        }
    })
}

const userSearchQuery = ref(null)

watch(userSearchQuery, (val) => {
    router.get(route('admin.users.index', {name: val}), {}, {
        preserveScroll: true,
        preserveState: true,
    })
})
</script>

<template>
    <AdminLayout name="Профили">
        <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6 w-full">
            <div class="sm:col-span-6">
                <h2 class="text-2xl font-bold">
                    Профили
                </h2>
            </div>
            <form class="sm:col-span-6">
                <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                    <!--                    TODO: вынести в компонент табов-->
                    <ul class="flex col-span-6 flex-wrap text-sm font-medium text-center text-gray-500 dark:text-gray-400">
                        <li class="me-2" v-for="(tab, key) of tabs">
                            <a
                                :class="currentTab === key ? 'text-white bg-blue-600' : 'hover:text-gray-900 hover:bg-gray-300'"
                                href="#" @click.prevent="currentTab = key"
                                class="inline-block px-4 py-3 rounded-lg transition" aria-current="page">{{
                                    tab.name
                                }}</a>
                        </li>
                    </ul>
                    <form @submit.prevent="sendUserForm"
                          class="sm:col-span-2 p-5 bg-gray-100 rounded grid grid-cols-1 gap-y-6 gap-x-4"
                          v-if="currentTab === 0">
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
                            <label for="email" class="block text-sm font-medium text-gray-700">
                                E-Mail </label>
                            <div class="mt-1">
                                <input type="email" id="email"

                                       v-model="userForm.email"
                                       class="flex-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full min-w-0 rounded-md sm:text-sm border-gray-300"/>
                            </div>
                        </div>
                        <div>
                            <label for="city" class="block text-sm font-medium text-gray-700">
                                Город </label>
                            <div class="mt-1">
                                <input type="text" id="city"
                                       v-model="userForm.city"
                                       class="flex-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full min-w-0 rounded-md sm:text-sm border-gray-300"/>
                            </div>
                        </div>
                        <div>
                            <label for="phone_number" class="block text-sm font-medium text-gray-700">
                                Телефон </label>
                            <div class="mt-1">
                                <input type="text" id="phone_number"
                                       v-model="userForm.phone_number"
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
                                <div class="flex">
                                    <input v-model="userForm.password" :type="!showPassword ? 'password' : 'text'"
                                           id="password"

                                           class="flex-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full min-w-0 rounded-md sm:text-sm border-gray-300"/>
                                    <div @click="showPassword = !showPassword"
                                         style="left:-40px;position:relative;width: 5px;position:relative;">
                                        <div class="flex items-center h-full cursor-pointer">
                                            <eye-icon v-if="!showPassword" class="absolute icon-fix text-indigo-600"/>
                                            <eye-slash-icon v-else class="absolute icon-fix text-indigo-600"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label for="password-confirmation" class="block text-sm font-medium text-gray-700">
                                Подтверждение пароля </label>
                            <div class="mt-1">
                                <div class="flex">
                                    <input v-model="userForm.password_confirmation"
                                           :type="!showPassword ? 'password' : 'text'" id="password-confirmation"

                                           class="flex-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full min-w-0 rounded-md sm:text-sm border-gray-300"/>
                                    <div @click="showPassword = !showPassword"
                                         style="left:-40px;position:relative;width: 5px;position:relative;">
                                        <div class="flex items-center h-full cursor-pointer">
                                            <eye-icon v-if="!showPassword" class="absolute icon-fix text-indigo-600"/>
                                            <eye-slash-icon v-else class="absolute icon-fix text-indigo-600"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div>
                            <button
                                type="submit"
                                class="w-full justify-center inline-flex items-center p-3 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
                                <user-icon class="w-5 me-auto text-indigo-white"/>
                                <span class="me-auto">Создать</span>
                            </button>
                        </div>
                    </form>
                    <div class="sm:col-span-6 p-5 rounded grid grid-cols-1 gap-y-6 gap-x-4 rounded"
                         v-if="currentTab === 1">
                        <div>
                            <input v-model="userSearchQuery" class="w-full rounded border-gray-200 shadow" placeholder="Поиск по имени"/>
                        </div>
                        <table class="w-full rounded divide-y divide-gray-200 shadow rounded">
                            <thead class="bg-gray-50">
                            <tr>
                                <th class="relative px-6 py-3">
                                    Имя
                                </th>
                                <th class="relative px-6 py-3">
                                    E-Mail
                                </th>
                                <th class="relative px-6 py-3">
                                    Администратор
                                </th>
                                <th class="relative px-6 py-3">
                                    Город
                                </th>
                                <th class="relative px-6 py-3">
                                    Телефон
                                </th>
                                <th class="relative px-6 py-3">
                                    Доступ
                                </th>
                                <th class="relative px-6 py-3">
                                    Изменить
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr class="bg-white" v-for="user of users">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                                    {{ user.name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                                    {{ user.email }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                                    {{ user.is_admin ? 'Да' : 'Нет' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                                    {{ user.city }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                                    {{ user.phone_number }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                                    <div class="mt-5 w-full flex justify-center">
                                        <check-icon v-if="!user.is_blocked" class="w-5 h-5 text-green-600"/>
                                        <no-symbol-icon v-else class="w-5 h-5 text-red-600"/>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                                    <Link
                                        :href="route('admin.users.edit', user.id)"
                                        class="text-white bg-blue-600 inline-block px-4 py-3 rounded-lg transition"
                                        aria-current="page">
                                        Изменить
                                    </Link>
                                </td>
                            </tr>
                            </tbody>
                        </table>

                    </div>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>

<style>
.icon-fix {
    width: 20px;
}
</style>
