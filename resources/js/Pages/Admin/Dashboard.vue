<script setup>
import { computed, onMounted, ref } from "vue";
import { Head, usePage } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import DashboardHeader from "@/Components/DashboardHeader.vue";
import api from "@/lib/api";

const page = usePage();
const loading = ref(false);
const stats = ref(null);
const hasLoaded = ref(false);

// Only ADMIN allowed
const isAdmin = computed(() => page.props.auth?.user?.role === "admin");

// Cards only for admin
const cards = computed(() => {
    if (!stats.value || !isAdmin.value) return [];

    const data = stats.value.stats ?? {};

    return [
        { label: "Assigned Teachers", value: data.assignedTeachers },
        { label: "Students", value: data.totalStudents },
        { label: "Books", value: data.totalBooks },
        { label: "Classes Created", value: data.totalClasses },
        { label: "Notifications", value: data.totalNotifications },
        { label: "Payouts", value: data.totalPayouts },
        { label: "Hours This Month", value: data.totalHoursMonth },
        { label: "Hours This Week", value: data.totalHoursWeek },
        {
            label: "Estimated Month Payout",
            value: `₱${Number(data.totalPaymentMonth ?? 0).toFixed(2)}`,
        },
    ];
});

const loadStats = async () => {
    if (loading.value || hasLoaded.value) return;

    loading.value = true;
    try {
        const { data } = await api.get("/dashboard/stats");
        stats.value = data;
        hasLoaded.value = true;
    } catch (error) {
        console.error("Dashboard stats error:", error);
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    loadStats();
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <div class="space-y-6">
            <DashboardHeader />

            <!-- Loading -->
            <div v-if="loading" class="space-y-6">
                <div
                    class="w-full h-96 mb-12 mt-5 bg-gray-200 animate-pulse rounded"
                ></div>
                <div
                    class="w-full h-96 bg-gray-200 animate-pulse rounded"
                ></div>
            </div>

            <!-- Not an admin -->
            <div
                v-else-if="!isAdmin"
                class="text-center text-gray-500 py-20 text-xl"
            >
                You do not have access to the admin dashboard.
            </div>

            <!-- Admin Dashboard -->
            <div v-else class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <div
                    v-for="card in cards"
                    :key="card.label"
                    class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm"
                >
                    <dt class="text-sm font-medium text-gray-500">
                        {{ card.label }}
                    </dt>
                    <dd class="mt-2 text-3xl font-semibold text-gray-900">
                        {{ card.value ?? 0 }}
                    </dd>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
