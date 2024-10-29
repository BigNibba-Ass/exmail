<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {Head} from '@inertiajs/vue3';
import OfferItem from "@/Components/OfferItem.vue";
import jsPDF from "jspdf";
import html2canvas from "html2canvas";
import {ref} from "vue";
import NProgress from 'nprogress'

const props = defineProps({
    offer: Object,
    auth: Object,
})

const emit = defineEmits(['setPageLoadingParam'])

const pageIsLoading = ref(false)

const download = async () => {
    pageIsLoading.value = true
    NProgress.start()
    const canvases = []
    for (let elem of document.querySelectorAll("[id*='pf']")) {
        const canvas = await html2canvas(elem, {
            scale: 4,
        })
        canvases.push(canvas)
    }
    let pdf = new jsPDF('p', 'px', [1788, 2528]);
    let i = 0
    for (const elem of canvases) {
        let imgData = elem.toDataURL("image/jpeg", 1.0);
        pdf.addImage(imgData, 'JPEG', 0, 0, 1788, 2528, "alias" + i)
        pdf.addPage()
        i++
    }
    pdf.save()
    pageIsLoading.value = false
    NProgress.done()
    NProgress.remove()
}
</script>

<template>
    <AuthenticatedLayout v-model="pageIsLoading">
        <Head :title="'Коммерческое предложение №' + props.offer.id"><title></title></Head>
        <form class="w-full">
            <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                <div class="sm:col-span-6 text-center">
                    <h2 class="text-2xl font-bold">
                        Коммерческое предложение №{{ props.offer.id }}
                    </h2>
                </div>

                <div class="sm:col-span-6" id="offer">
                    <offer-item :offer="props.offer" :user="props.auth.user"/>
                </div>

                <div class="sm:col-span-3">
                    <button type="button"
                            @click.prevent="download"
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
