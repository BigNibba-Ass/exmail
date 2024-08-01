<script setup>

const props = defineProps({
    values: Object
})
</script>

<template>
    <select class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
        <option :value="value.value" v-for="value of props.values">{{ value.text }}</option>
    </select>
</template>
