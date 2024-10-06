<script setup lang="ts">
import { ref } from "vue";
import Modal from "./Modal.vue";
import { useRouter } from "vue-router";

const emit = defineEmits(["onClickConfirm"]);

const router = useRouter();

const logoutConfirmation: Modal = ref();

</script>

<template>
    <span>
        <button
            type="button"
            class="btn btn-dark"
            @click="logoutConfirmation.show()"
        >
            <i class="bi bi-box-arrow-right"></i>
            Log out
        </button>
        <Modal
            ref="logoutConfirmation"
            title="Log out"
        >
            <template #content>Are you sure you want to quit?</template>
            <template #footer>
                <button
                    type="button"
                    class="btn btn-secondary"
                    data-bs-dismiss="modal"
                >
                    Cancel
                </button>
                <button
                    type="button"
                    class="btn btn-primary"
                    @click="
                        logoutConfirmation.destroy();
                        $emit('onClickConfirm')
                    "
                >
                    Log out
                </button>
            </template>
        </Modal>
    </span>
</template>