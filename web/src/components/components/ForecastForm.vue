<script setup lang="ts">
import { defineEmits, ref } from "vue";

const city = ref("");
const country = ref("US");

const emit = defineEmits(["onSearch"]);
</script>

<template>
    <div style="display: flex; flex-direction: row; justify-content: space-around;">
        <form class="row" @submit.prevent>
            <div class="col-auto mb-3">
                <input type="text" class="form-control form-control-lg" v-model="city"
                    placeholder="Type a city name...">
            </div>
            <div class="col-auto mb-3">
                <select class="form-select form-select-lg mb-3" v-model="country">
                    <option value="BR">Brazil</option>
                    <option value="US">United States</option>
                    <option value="UK">United Kingdom</option>
                </select>
            </div>
            <div class="col-auto mb-3">
                <button type="button" class="btn btn-primary btn-dark btn-lg" @click="(
                    () => {
                        $emit('onSearch', { city, country });
                        city = '';
                    }
                )">Search</button>
            </div>
        </form>
    </div>
</template>