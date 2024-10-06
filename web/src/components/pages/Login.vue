<script setup lang="ts">
import { useRouter } from "vue-router";
import { onBeforeMount, inject, ref } from "vue";
import Toaster from "../components/Toaster/Toaster.vue";
import Login, { LoginInput } from "../../application/usecase/Login";
import CheckIsAuthenticated from "../../application/usecase/CheckIsAuthenticated";

const router = useRouter();
const toaster: Toaster = ref();

const email = ref("");
const password = ref("");

const loginUseCase: Login = inject("loginUseCase");

async function onClickLogin() {
    const input: LoginInput = { email: email.value, password: password.value };
    const authenticationResult = await loginUseCase.execute(input);
    if (authenticationResult.isRight()) {
        router.push("/forecast");
        return;
    }

    const error = authenticationResult.value;
    toaster.value.error(error.message || "Authentication error");
}
</script>

<template>
    <div data-bs-theme="dark">
        <div class="container mt-5">
            <h1>Weather</h1>
            <h6 class="mb-5">Forecast information</h6>
            <form>
                <div class="form-group">
                    <input type="text" class="form-control" rows="3" placeholder="Email" v-model="email" />
                </div>
                <div class="form-group mt-3">
                    <input type="password" class="form-control" rows="3" placeholder="Password" v-model="password" />
                </div>
                <div class="d-flex justify-content-end">
                    <button type="button" class="btn btn-light mt-4" @click="onClickLogin()">
                        Login
                    </button>
                </div>
            </form>
        </div>
        <Toaster ref="toaster"></Toaster>
    </div>
</template>

<style scoped>
.container {
    max-width: 480px;
    color: white;
    background-color: #363d45;
    padding: 30px;
    border: 1px solid gray;
    border-radius: 4px;
}

p {
    margin: 0;
    padding: 0;
}
</style>
