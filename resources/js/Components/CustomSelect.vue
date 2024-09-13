<script setup>

const props = defineProps({
    values: Object,
    placeholder: {
        type: [String, Boolean],
        default: false,
        required: false
    },
    modelValue: [String, Number, Object, Array]
})

const emit = defineEmits(['update:modelValue']);
</script>

<template>
    <select
        @change="emit('update:modelValue', $event.target.value)"
        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
        <option v-if="props.placeholder" disabled selected>{{ props.placeholder }}</option>
        <option :value="value.value" v-for="value of props.values" :selected="props.modelValue === value.value">{{ value.text }}</option>
    </select>
</template>
