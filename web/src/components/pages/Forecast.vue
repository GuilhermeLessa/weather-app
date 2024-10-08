<script setup lang="ts">
import { inject, onBeforeMount, ref } from "vue";
import { useRouter } from "vue-router";
import ForecastCard from "../components/ForecastCard.vue";
import LogoutButton from "../components/LogoutButton.vue";
import Logout from "../../application/usecase/Logout";
import ListForecast from "../../application/usecase/ListForecast";
import ForecastForm from "../components/ForecastForm.vue";
import FindForecast from "../../application/usecase/FindForecast";
import Toaster from "../components/Toaster/Toaster.vue";
import InactivateForecast from "../../application/usecase/InactivateForecast";

const router = useRouter();
const toaster: Toaster = ref();

const logoutUseCase: Logout = inject("logoutUseCase");
const listForecastUseCase: ListForecast = inject("listForecastUseCase");
const findForecastUseCase: FindForecast = inject("findForecastUseCase");
const inactivateForecastUseCase: InactivateForecast = inject("inactivateForecastUseCase");

const forecasts = ref([]);

onBeforeMount(async () => {
    const listForecastOrError = await listForecastUseCase.execute();
    if (listForecastOrError.isRight()) {
        forecasts.value = listForecastOrError.value;
    }
});

async function onSearch(terms: any) {
    const { city, country } = terms;
    const forecastDataOrError = await findForecastUseCase.execute({ city, country });

    if (forecastDataOrError.isRight()) {
        const forecast = forecastDataOrError.value;
        forecasts.value = forecasts.value.filter(f => !(f.city == forecast.city && f.country == forecast.country));
        forecasts.value.unshift(forecast);
        toaster.value.success("Weather forecast loaded.")
        return;
    }

    const error = forecastDataOrError.value;
    toaster.value.error(error.message || "Error loading forecast.");
}

async function inactivate(uuid) {
    const inactivateOrErrror = await inactivateForecastUseCase.execute({ uuid });
    if (inactivateOrErrror.isLeft()) {
        const error = inactivateOrErrror.value;
        toaster.value.error(error.message || "Error removing forecast.");
    }

    forecasts.value = forecasts.value.filter(f => f.uuid != uuid);
    toaster.value.success("Weather forecast removed.");
}
</script>

<template>
    <div class="container" data-bs-theme="dark">
        <div class="d-flex justify-content-end mb-5">
            <LogoutButton @onClickConfirm="(
                async () => {
                    await logoutUseCase.execute();
                    router.push('/');
                }
            )"></LogoutButton>
        </div>

        <h2 class="mb-5">Weather Forecast</h2>

        <ForecastForm @onSearch="onSearch"></ForecastForm>

        <div class="row">
            <ForecastCard v-for="forecast in forecasts" :uuid="forecast.uuid" :city="forecast.city"
                :country="forecast.country" :description="forecast.description" :icon="forecast.icon"
                :temperature="forecast.temperature" :minimumTemperature="forecast.minimumTemperature"
                :maximumTemperature="forecast.maximumTemperature" :humidity="forecast.humidity" :wind="forecast.wind"
                :created_at="forecast.created_at" @onConfirmInactivation="inactivate">
            </ForecastCard>
        </div>
    </div>
    <Toaster ref="toaster"></Toaster>
</template>

<style scoped>
.container {
    color: white;
    background-color: #363d45;
    padding: 2vw;
}
</style>
