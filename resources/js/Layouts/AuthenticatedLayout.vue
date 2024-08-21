<script setup>
import {ref} from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import {Link} from '@inertiajs/vue3';
import {BriefcaseIcon, CircleStackIcon, DeviceTabletIcon, UserCircleIcon} from "@heroicons/vue/20/solid/index.js";

const showingNavigationDropdown = ref(false);

const getBack = () => {
    window.history.back()
}


const pages = [
    {name: 'Профили', icon: UserCircleIcon, route: 'admin.users.index'},
    {name: 'Экран 1', icon: DeviceTabletIcon, route: 'admin.main_screen.index'},
    {name: 'Инф. база', icon: CircleStackIcon, route: 'admin.informations.index'},
    {name: 'База КП', icon: BriefcaseIcon, route: 'dashboard'},
]
</script>

<template>
    <div>
        <div class="min-h-screen bg-gray-200">
            <!-- Page Content -->
            <main>
                <div class="py-4">
                    <div class="max-w-full mx-auto sm:px-6 lg:px-8">
                        <div class="flex flex-row sm:col-span-1 rounded gap-3">
                            <slot name="header"/>
                            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg w-full">
                                <div class="w-full flex">
                                    <div v-if="route().current('offer-test')" class="w-full flex">
                                        <a @click.prevent="getBack()"
                                           class="me-auto cursor-pointer inline-flex rounded-br-md items-center p-3 border border-transparent text-sm leading-4 font-medium shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
                                            Назад
                                        </a>
                                    </div>
                                    <div class="ms-auto relative">
                                        <Dropdown align="right" width="48">
                                            <template #trigger>
                                        <span class="inline-flex">
                                            <button
                                                type="button"
                                                class="inline-flex items-center px-5 py-3 border border-transparent leading-4 text-sm font-medium rounded-bl bg-indigo-800 hover:bg-indigo-600 text-white focus:outline-none transition ease-in-out"
                                            >
                                                {{ $page.props.auth.user.name }}

                                                <svg
                                                    class="ms-2 -me-0.5 h-4 w-4"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                            </button>
                                        </span>
                                            </template>

                                            <template #content>
                                                <!--                                        <DropdownLink :href="route('profile.edit')"> Профиль </DropdownLink>-->
                                                <DropdownLink :href="route('logout')" method="post" as="button">
                                                    Выйти
                                                </DropdownLink>
                                            </template>
                                        </Dropdown>
                                    </div>
                                </div>
                                <div class="w-full flex p-5">
                                    <slot/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</template>
