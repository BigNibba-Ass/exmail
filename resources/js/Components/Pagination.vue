<template>
    <nav class="cursor-pointer select-none border-t border-gray-200 px-4 flex items-center justify-between sm:px-0">
        <div class="cursor-pointer select-none -mt-px w-0 flex-1 flex">
            <a @click="this.switchPage(current_page - 1)"
               class="cursor-pointer select-none border-t-2 border-transparent pt-4 pr-1 inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                <ArrowLeftIcon class="cursor-pointer select-none mr-3 h-5 w-5 text-gray-400" aria-hidden="true"/>
                Назад
            </a>
        </div>
        <div class="cursor-pointer select-none hidden md:-mt-px md:flex">
            <a @click="this.switchPage(1)" v-if="current_page !== 1"
               class="cursor-pointer select-none border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 border-t-2 pt-4 px-4 inline-flex items-center text-sm font-medium">
                1
            </a>
            <a @click="this.switchPage(current_page - 1)" v-if="current_page - 1 && current_page - 1 !== 1"
               class="cursor-pointer select-none border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 border-t-2 pt-4 px-4 inline-flex items-center text-sm font-medium">
                {{ current_page - 1 }}
            </a>
            <a @click="this.switchPage(current_page)"
               class="cursor-pointer select-none border-indigo-500 text-indigo-600 border-t-2 pt-4 px-4 inline-flex items-center text-sm font-medium"
               aria-current="page">
                {{ current_page }}
            </a>
            <a @click="this.switchPage(current_page + 1)"
               v-if="current_page + 1 <= max && current_page + 1 !== max"
               class="cursor-pointer select-none border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 border-t-2 pt-4 px-4 inline-flex items-center text-sm font-medium">
                {{ current_page + 1 }}
            </a>
            <a @click="this.switchPage(max)" v-if="current_page !== max"
               class="cursor-pointer select-none border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 border-t-2 pt-4 px-4 inline-flex items-center text-sm font-medium">
                {{ max }}
            </a>
        </div>
        <div class="cursor-pointer select-none -mt-px w-0 flex-1 flex justify-end">
            <a @click="this.switchPage(current_page + 1)"
               class="cursor-pointer select-none border-t-2 border-transparent pt-4 pl-1 inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                Вперед
                <ArrowRightIcon class="cursor-pointer select-none ml-3 h-5 w-5 text-gray-400" aria-hidden="true"/>
            </a>
        </div>
    </nav>
</template>

<script>

import {ArrowRightIcon, ArrowLeftIcon} from "@heroicons/vue/20/solid"

export default {
    name: 'Pagination',
    props: {
        current: Number,
        max: Number,
    },
    components: {
        ArrowRightIcon,
        ArrowLeftIcon
    },
    data() {
        return {
            current_page: this.current || 1,
        }
    },
    methods: {
        switchPage(page) {
            if (page < 1 || page > this.max) return;
            this.current_page = page;
            this.$emit('switch_page', page)
        }
    }
}

</script>
