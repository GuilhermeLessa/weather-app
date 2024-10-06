<script setup lang="ts">
import { ref, onMounted, inject, onBeforeMount, nextTick } from "vue";

const props = defineProps({
    title: { required: true },
});

const emit = defineEmits(["onClickClose"]);

let bootstrap;
onBeforeMount(() => {
    bootstrap = inject("bootstrap");
});

const modalRef = ref();
let modal;
onMounted(async () => {
    modal = new bootstrap.Modal(modalRef.value);
});

function show() {
    modal.show();
}

function hide() {
    modal.hide();
}

function destroy() {
    modal.hide();
    modal.dispose();
}

defineExpose({
    show,
    hide,
    destroy,
});
</script>

<template>
    <!-- 
        to open modal by an element click:
        data-bs-toggle="modal"
        data-bs-target="#modal-id"
    -->
    <div
        class="modal fade"
        ref="modalRef"
        tabindex="-1"
        aria-labelledby="modal-label"
        aria-hidden="true"
        data-bs-backdrop="static"
        data-bs-theme="dark"
    >
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="modal-label">
                        {{ title }}
                    </h1>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                        @click="$emit('onClickClose')"
                    ></button>
                </div>
                <div class="modal-body">
                    <slot name="content"></slot>
                </div>
                <div class="modal-footer">
                    <slot name="footer"></slot>
                </div>
            </div>
        </div>
    </div>
</template>