<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {Head} from '@inertiajs/vue3';
import OfferItem from "@/Components/OfferItem.vue";
import jsPDF from "jspdf";
import html2canvas from "html2canvas";
import {ref} from "vue";
import NProgress from 'nprogress'
import OfferComponent from "@/Components/OfferComponent.vue";

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
        if(i < canvases.length - 1) {
            pdf.addPage()
        }
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
        <offer-component :offer="props.offer" :user="props.auth.user" @download="download"/>
    </AuthenticatedLayout>
</template>
