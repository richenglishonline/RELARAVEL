<script setup>
import { onMounted, ref } from "vue";
import { Head } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import DashboardHeader from "@/Components/DashboardHeader.vue";
import { computed } from "vue";

import {
    UserGroupIcon,
    CalendarIcon,
    ClipboardDocumentListIcon,
    ClockIcon,
    CurrencyDollarIcon,
} from "@heroicons/vue/24/outline";

import api from "@/lib/api";

const loading = ref(false);
const stats = ref(null);
const hasLoaded = ref(false);

// Card data formatted for teacher
const statCards = computed(() => {
    if (!stats.value) return [];

    const s = stats.value.stats || {};

    return [
        {
            name: "Total Students",
            value: s.totalStudents ?? 0,
            icon: UserGroupIcon,
            color: "bg-blue-500",
        },
        {
            name: "Total Classes",
            value: s.totalClasses ?? 0,
            icon: CalendarIcon,
            color: "bg-green-500",
        },
        {
            name: "Total Assignments",
            value: s.totalAssignments ?? 0,
            icon: ClipboardDocumentListIcon,
            color: "bg-yellow-500",
        },
        {
            name: "Notifications",
            value: s.totalNotifications ?? 0,
            icon: ClockIcon,
            color: "bg-red-500",
        },
        {
            name: "Hours This Month",
            value: s.totalHoursMonth ?? 0,
            icon: ClockIcon,
            color: "bg-purple-500",
        },
        {
            name: "Hours This Week",
            value: s.totalHoursWeek ?? 0,
            icon: ClockIcon,
            color: "bg-indigo-500",
        },
        {
            name: "Payment This Month",
            value: `₱${Number(s.totalPaymentMonth ?? 0).toFixed(2)}`,
            icon: CurrencyDollarIcon,
            color: "bg-orange-500",
        },
        {
            name: "Payment This Week",
            value: `₱${Number(s.totalPaymentWeek ?? 0).toFixed(2)}`,
            icon: CurrencyDollarIcon,
            color: "bg-teal-500",
        },
    ];
});

// Load stats from backend
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
    <Head title="Teacher Dashboard" />

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

            <!-- Teacher Dashboard -->
            <template v-else>
                <!-- Stats Cards -->
                <div
                    class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4"
                >
                    <div
                        v-for="card in statCards"
                        :key="card.name"
                        class="bg-white overflow-hidden shadow rounded-lg"
                    >
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div
                                        :class="[card.color, 'p-3 rounded-md']"
                                    >
                                        <component
                                            :is="card.icon"
                                            class="h-6 w-6 text-white"
                                        />
                                    </div>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt
                                            class="text-sm font-medium text-gray-500 truncate"
                                        >
                                            {{ card.name }}
                                        </dt>
                                        <dd
                                            class="text-lg font-medium text-gray-900"
                                        >
                                            {{ card.value }}
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Classes -->
                <div class="mt-8 bg-white shadow-lg rounded-lg p-6">
                    <h2 class="text-lg font-semibold mb-4">Recent Classes</h2>

                    <template v-if="stats?.stats?.classes?.length">
                        <ul class="space-y-3">
                            <li
                                v-for="cls in stats.stats.classes"
                                :key="cls.id"
                                class="p-4 border rounded-md"
                            >
                                <div class="font-medium text-gray-800">
                                    {{ cls.type }}
                                </div>
                                <div class="text-sm text-gray-600">
                                    {{ cls.start_date }} → {{ cls.end_date }}
                                </div>
                                <div class="text-sm text-gray-600">
                                    {{ cls.start_time }} - {{ cls.end_time }}
                                </div>
                            </li>
                        </ul>
                    </template>

                    <div v-else class="text-gray-500 text-center py-6">
                        No recent classes found.
                    </div>
                </div>

                <!-- Calendar Placeholder -->
                <div class="mt-8 bg-white rounded-lg shadow-lg p-6">
                    <div class="text-center text-gray-500 py-8">
                        Calendar will be displayed here
                    </div>
                </div>
            </template>
        </div>
    </AuthenticatedLayout>
</template>
