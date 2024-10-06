<script setup lang="ts">
import { defineEmits, ref } from "vue";
import DateUtils from "../../utils/DateUtils";
import Modal from "../components/Modal.vue";

const inactivateConfirmation: Modal = ref();

const props = defineProps({
    uuid: { required: true, type: String },
    city: { required: true, type: String },
    country: { required: true, type: String },
    description: { required: true, type: String },
    icon: { required: true, type: String },
    temperature: { required: true, type: String },
    minimumTemperature: { required: true, type: String },
    maximumTemperature: { required: true, type: String },
    humidity: { required: true, type: String },
    wind: { required: true, type: String },
    created_at: { required: true, type: String }
});

const emit = defineEmits(["onConfirmInactivation"]);
</script>

<template>
    <div class="col-md-6 offset-md-3 col-lg-4 offset-lg-0 mb-4">
        <div class="card shadow-0 border">
            <div class="card-body p-4">
                <button type="button" class="btn-close float-end" aria-label="Close"
                    @click="inactivateConfirmation.show()"></button>
                <h4 class="mb-1 sfw-normal text-capitalize">{{ city }}, {{ country }}</h4>
                <div class="d-flex flex-row align-items-center" style="justify-content: center;">
                    <p class="mb-0 me-4 text-capitalize text-nowrap ">{{ description }}</p>
                    <div class="row">
                        <img class="float-end" :src="icon" />
                    </div>
                </div>
                <p class="mb-1">Temperature: <strong>{{ temperature }}°F</strong></p>
                <p class="mb-1">
                    Max: <strong>{{ minimumTemperature }}°F</strong>,
                    Min: <strong>{{ maximumTemperature }}°F</strong>
                </p>
                <p class="mb-3">
                    Humidity: <strong>{{ humidity }}%</strong>,
                    Wind: <strong>{{ wind }}m/h</strong>
                </p>
                <p class="mb-1 fw-light  text-muted text-center" style="font-size: smaller;">
                    created at {{ DateUtils.formatDateTime(new Date(created_at)) }}
                </p>
            </div>
        </div>
    </div>
    <Modal ref="inactivateConfirmation" title="Log out">
        <template #content>Are you sure you want to remove this forecast?</template>
        <template #footer>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                Back
            </button>
            <button type="button" class="btn btn-primary" @click="(
                    () => {
                        inactivateConfirmation.destroy();
                        $emit('onConfirmInactivation', uuid);
                    }
                )">
                Remove
            </button>
        </template>
    </Modal>
</template>