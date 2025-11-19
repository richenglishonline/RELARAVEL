<script setup>
import { reactive, ref, computed } from "vue";
import { Head, usePage, router } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Button from "@/Components/ui/Button.vue";
import { BanknotesIcon, ChartBarIcon } from "@heroicons/vue/24/outline";
import { usePayouts } from "@/lib/tanstack/payouts";

const page = usePage();

const filters = reactive({
    status: "",
    start_date: "",
    end_date: "",
    page: 1,
    limit: 20,
});

const { data: payoutsData, isFetching: loading, refetch } = usePayouts(filters);

const payouts = computed(() => payoutsData?.value?.payouts ?? []);
const pagination = computed(() => payoutsData?.value?.pagination ?? null);

const summary = computed(() => {
    if (!payouts.value.length) {
        return {
            total_payouts: 0,
            total_amount: 0,
            total_duration: 0,
            total_classes: 0,
            total_incentives: 0,
            pending: 0,
            processing: 0,
            completed: 0,
        };
    }

    const totalAmount = payouts.value.reduce(
        (sum, p) => sum + (parseFloat(p.amount) || 0),
        0
    );
    const totalDuration = payouts.value.reduce(
        (sum, p) => sum + (parseInt(p.duration) || 0),
        0
    );
    const totalClasses = payouts.value.reduce(
        (sum, p) => sum + (parseInt(p.total_class) || 0),
        0
    );
    const totalIncentives = payouts.value.reduce(
        (sum, p) => sum + (parseFloat(p.incentives) || 0),
        0
    );

    return {
        total_payouts: payouts.value.length,
        total_amount: totalAmount,
        total_duration: totalDuration,
        total_classes: totalClasses,
        total_incentives: totalIncentives,
        pending: payouts.value.filter((p) => p.status === "pending").length,
        processing: payouts.value.filter((p) => p.status === "processing")
            .length,
        completed: payouts.value.filter((p) => p.status === "completed").length,
    };
});

const goToPage = (pageNum) => {
    if (!pagination.value) return;
    if (pageNum < 1 || pageNum > pagination.value.totalPages) return;
    filters.page = pageNum;
    refetch();
};

const resetFilters = () => {
    filters.status = "";
    filters.start_date = "";
    filters.end_date = "";
    filters.page = 1;
    refetch();
};

const formatDate = (dateString) => {
    if (!dateString) return "—";
    return new Date(dateString).toLocaleDateString("en-US", {
        year: "numeric",
        month: "short",
        day: "numeric",
    });
};

const formatCurrency = (amount) => {
    if (!amount) return "$0.00";
    return new Intl.NumberFormat("en-US", {
        style: "currency",
        currency: "USD",
    }).format(amount);
};
</script>

<template>
    <Head title="Salary Management" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2
                        class="text-xl font-semibold leading-tight text-gray-800"
                    >
                        Salary Management
                    </h2>
                    <p class="text-sm text-gray-500">
                        View your payouts and salary summary.
                    </p>
                </div>
                <button
                    @click="refetch"
                    class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500"
                    :disabled="loading"
                >
                    Refresh
                </button>
            </div>
        </template>

        <div class="py-10">
            <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
                <!-- Summary Cards -->
                <div
                    v-if="summary"
                    class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4"
                >
                    <div
                        class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm"
                    >
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <BanknotesIcon class="h-8 w-8 text-green-600" />
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">
                                    Total Amount
                                </p>
                                <p class="text-2xl font-semibold text-gray-900">
                                    {{ formatCurrency(summary.total_amount) }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div
                        class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm"
                    >
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <ChartBarIcon class="h-8 w-8 text-blue-600" />
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">
                                    Total Hours
                                </p>
                                <p class="text-2xl font-semibold text-gray-900">
                                    {{ summary.total_duration }}h
                                </p>
                            </div>
                        </div>
                    </div>
                    <div
                        class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm"
                    >
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <ChartBarIcon class="h-8 w-8 text-purple-600" />
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">
                                    Total Classes
                                </p>
                                <p class="text-2xl font-semibold text-gray-900">
                                    {{ summary.total_classes }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div
                        class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm"
                    >
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <BanknotesIcon
                                    class="h-8 w-8 text-yellow-600"
                                />
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">
                                    Total Incentives
                                </p>
                                <p class="text-2xl font-semibold text-gray-900">
                                    {{
                                        formatCurrency(summary.total_incentives)
                                    }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div
                    class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm"
                >
                    <form
                        class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4"
                    >
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700"
                                >Status</label
                            >
                            <select
                                v-model="filters.status"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            >
                                <option value="">All Statuses</option>
                                <option value="pending">Pending</option>
                                <option value="processing">Processing</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700"
                                >Start Date</label
                            >
                            <input
                                v-model="filters.start_date"
                                type="date"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            />
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700"
                                >End Date</label
                            >
                            <input
                                v-model="filters.end_date"
                                type="date"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            />
                        </div>
                        <div
                            class="flex items-end gap-3 sm:col-span-2 lg:col-span-4"
                        >
                            <button
                                type="button"
                                @click="refetch"
                                class="flex-1 rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500"
                                :disabled="loading"
                            >
                                Apply Filters
                            </button>
                            <button
                                type="button"
                                @click="resetFilters"
                                class="flex-1 rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50"
                                :disabled="loading"
                            >
                                Reset
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Payouts Table -->
                <div
                    class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm"
                >
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr
                                    class="text-left text-xs font-semibold uppercase tracking-wider text-gray-500"
                                >
                                    <th scope="col" class="px-6 py-3">
                                        Period
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Duration
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Classes
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Amount
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Incentives
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Status
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                <tr v-if="!loading && payouts.length === 0">
                                    <td
                                        colspan="7"
                                        class="px-6 py-4 text-center text-sm text-gray-500"
                                    >
                                        No payouts found.
                                    </td>
                                </tr>
                                <tr
                                    v-for="payout in payouts"
                                    :key="payout.id"
                                    class="text-sm text-gray-700 hover:bg-gray-50 cursor-pointer"
                                >
                                    <td class="px-6 py-4">
                                        {{ formatDate(payout.start_date) }} -
                                        {{ formatDate(payout.end_date) }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ payout.duration ?? "—" }} hours
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ payout.total_class ?? "—" }}
                                    </td>
                                    <td
                                        class="px-6 py-4 font-semibold text-gray-900"
                                    >
                                        {{ formatCurrency(payout.amount) }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ formatCurrency(payout.incentives) }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span
                                            :class="[
                                                'px-2 py-1 text-xs font-medium rounded',
                                                payout.status === 'completed'
                                                    ? 'bg-green-100 text-green-800'
                                                    : payout.status ===
                                                      'processing'
                                                    ? 'bg-yellow-100 text-yellow-800'
                                                    : 'bg-gray-100 text-gray-800',
                                            ]"
                                            >{{ payout.status }}</span
                                        >
                                    </td>
                                    <td class="px-6 py-4" @click.stop>
                                        <Button
                                            @click="
                                                router.visit(
                                                    route(
                                                        'payouts.show',
                                                        payout.id
                                                    )
                                                )
                                            "
                                            variant="outline"
                                            size="sm"
                                            >View</Button
                                        >
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div
                        v-if="pagination"
                        class="flex flex-col items-center justify-between gap-4 border-t border-gray-100 px-6 py-4 text-sm text-gray-600 sm:flex-row"
                    >
                        <div>
                            Page {{ pagination.page }} of
                            {{ pagination.totalPages ?? 1 }}
                        </div>
                        <div class="flex items-center gap-2">
                            <button
                                @click="goToPage(pagination.page - 1)"
                                class="rounded-md border border-gray-300 px-3 py-1 hover:bg-gray-50"
                                :disabled="loading || pagination.page === 1"
                            >
                                Previous
                            </button>
                            <button
                                @click="goToPage(pagination.page + 1)"
                                class="rounded-md border border-gray-300 px-3 py-1 hover:bg-gray-50"
                                :disabled="
                                    loading ||
                                    pagination.page === pagination.totalPages
                                "
                            >
                                Next
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
