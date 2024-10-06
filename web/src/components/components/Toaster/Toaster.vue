<script setup lang="ts">
import { ref, nextTick, inject, onBeforeMount } from "vue";
import Toast from "./Toast.vue";

const toasts = ref([]);

let bootstrap;
onBeforeMount(() => {
    bootstrap = inject("bootstrap");
});

async function create(title, message, type) {
    toasts.value.push({
        title,
        message,
        type,
    });
    await nextTick();
    const element = toasts.value[toasts.value.length - 1].el.$el;
    const toast = new bootstrap.Toast(element);
    toast.show();
}

function error(message, title = "Error") {
    create(title, message, "error");
}

function success(message, title = "Success") {
    create(title, message, "success");
}

function info(message, title = "Information") {
    create(title, message, "info");
}

defineExpose({
    error,
    success,
    info,
});
</script>

<template>
    <div class="toast-container position-fixed top-0 end-0 p-3">
        <Toast
            v-for="(toast, index) in toasts"
            :key="index"
            :ref="
                (el) => {
                    toasts[index].el = el;
                }
            "
            :title="toast.title"
            :message="toast.message"
            :type="toast.type"
        ></Toast>
    </div>
</template>