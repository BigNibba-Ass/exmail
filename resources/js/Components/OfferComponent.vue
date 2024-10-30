<script setup>
import OfferItem from "@/Components/OfferItem.vue";

const props = defineProps({
    offer: Object,
    user: Object
})

const emit = defineEmits(['download'])
</script>

<template>
    <form class="w-full">
        <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
            <div class="sm:col-span-6 text-center">
                <h2 class="text-2xl font-bold">
                    Коммерческое предложение №{{ props.offer.id }}
                </h2>
            </div>

            <div class="sm:col-span-6" id="offer">
                <offer-item :offer="props.offer" :user="props.user"/>
            </div>

            <div class="sm:col-span-3">
                <button type="button"
                        @click.prevent="emit('download')"
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
</template>
