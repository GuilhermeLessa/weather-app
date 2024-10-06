<script setup lang="ts">
import { inject, onBeforeMount } from "vue";
import { useRouter } from "vue-router";
import LogoutButton from "../components/LogoutButton.vue";
import CheckIsAuthenticated from "../../application/usecase/CheckIsAuthenticated";
import Logout from "../../application/usecase/Logout";

const router = useRouter();

const logoutUseCase: Logout = inject("logoutUseCase");
</script>

<template>
    <div class="container" data-bs-theme="">
        <div class="d-flex justify-content-end">
            <LogoutButton @onClickConfirm="(
                async () => {
                    await logoutUseCase.execute();
                    router.push('/');
                }
            )"></LogoutButton>
        </div>

        <h1 class="mb-5">Forecast</h1>

        <!--div>
            <h3>Search...</h3>
        </div-->
    </div>
</template>

<style scoped>
.container {
    color: white;
    background-color: #363d45;
    padding: 2vw;
}
</style>
