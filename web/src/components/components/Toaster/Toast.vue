<script>
export default {
    props: {
        title: {
            required: true,
        },
        message: {
            required: true,
        },
        type: {
            required: true,
        },
    },
    data() {
        return {};
    },
    methods: {},
};
</script>

<template>
    <div
        class="toast"
        :class="{
            'toast-error text-bg-danger': type == 'error',
            'toast-success text-bg-primary': type == 'success',
            'toast-info text-bg-secondary': type == 'info',
        }"
        role="alert"
        aria-live="assertive"
        aria-atomic="true"
    >
        <div class="toast-header">
            <strong class="me-auto">{{ title }}</strong>
            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="toast"
                aria-label="Close"
            ></button>
        </div>
        <div class="toast-body">{{ message }}</div>
    </div>
</template>