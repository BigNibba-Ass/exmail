<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {Head} from '@inertiajs/vue3';
import {Link} from "@inertiajs/vue3";

</script>

<template>
    <Head title="Коммерческое предложение №Тест"/>

    <AuthenticatedLayout>
        <form class="w-full">
            <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                <div class="sm:col-span-6 text-center">
                    <h2 class="text-2xl font-bold">
                        Коммерческое предложение №Тест
                    </h2>
                </div>

                <!--                                TODO: replace with actual offer-->
                <div class="sm:col-span-6 text-center" style="height: 500px">
                    <img width="100%" height="500" src="/">
                </div>


                <div class="sm:col-span-3">
                    <button type="button"
                            class="ms-auto inline-flex w-full text-center justify-center items-center p-3 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
                        Выгрузить
                    </button>
                </div>
                <div class="sm:col-span-3">
                    <button type="button"
                            class="ms-auto inline-flex w-full text-center justify-center items-center p-3 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
                        Отправить
                    </button>
                </div>
            </div>
        </form>
    </AuthenticatedLayout>
</template>
