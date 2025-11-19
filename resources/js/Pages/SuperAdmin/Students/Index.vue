<script setup>
import { reactive, ref, computed } from "vue";
import { Head, usePage, router } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Button from "@/Components/ui/Button.vue";
import Dialog from "@/Components/ui/Dialog.vue";
import DialogHeader from "@/Components/ui/DialogHeader.vue";
import DialogTitle from "@/Components/ui/DialogTitle.vue";
import DialogDescription from "@/Components/ui/DialogDescription.vue";
import DialogFooter from "@/Components/ui/DialogFooter.vue";
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

// Pagination
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

// Formatting helpers
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

// Admin Dialog
const dialogOpen = ref(false);
const editingPayout = ref(null);
const form = reactive({
    teacher_name: "",
    amount: "",
    duration: "",
    total_class: "",
    incentives: "",
    status: "pending",
});

const openDialog = (payout = null) => {
    editingPayout.value = payout;
    if (payout) {
        form.teacher_name = payout.teacher_name;
        form.amount = payout.amount;
        form.duration = payout.duration;
        form.total_class = payout.total_class;
        form.incentives = payout.incentives;
        form.status = payout.status;
    } else {
        form.teacher_name = "";
        form.amount = "";
        form.duration = "";
        form.total_class = "";
        form.incentives = "";
        form.status = "pending";
    }
    dialogOpen.value = true;
};

const savePayout = () => {
    // For now just close dialog (replace with API call)
    dialogOpen.value = false;
    refetch();
};
</script>

<template>
    <Head title="Admin Payouts Management" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2
                        class="text-xl font-semibold leading-tight text-gray-800"
                    >
                        Admin Payouts Management
                    </h2>
                    <p class="text-sm text-gray-500">
                        Add, edit, and manage payouts.
                    </p>
                </div>
                <div class="flex gap-2">
                    <Button @click="refetch" :disabled="loading"
                        >Refresh</Button
                    >
                    <Button @click="openDialog()">Add Payout</Button>
                </div>
            </div>
        </template>

        <!-- Summary & Filters (same as before) -->
        <div class="py-10">
            <!-- ...Summary Cards & Filters Table code... keep as in your previous template -->

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
                                <th class="px-6 py-3">Period</th>
                                <th class="px-6 py-3">Duration</th>
                                <th class="px-6 py-3">Classes</th>
                                <th class="px-6 py-3">Amount</th>
                                <th class="px-6 py-3">Incentives</th>
                                <th class="px-6 py-3">Status</th>
                                <th class="px-6 py-3">Actions</th>
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
                                class="text-sm text-gray-700 hover:bg-gray-50"
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
                                                : payout.status === 'processing'
                                                ? 'bg-yellow-100 text-yellow-800'
                                                : 'bg-gray-100 text-gray-800',
                                        ]"
                                    >
                                        {{ payout.status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <Button
                                        @click="openDialog(payout)"
                                        variant="outline"
                                        size="sm"
                                        >Edit</Button
                                    >
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Admin Dialog -->
            <Dialog :open="dialogOpen" @update:open="dialogOpen = $event">
                <div class="sm:max-w-[600px] max-h-[90vh] overflow-y-auto p-6">
                    <DialogHeader>
                        <DialogTitle>{{
                            editingPayout ? "Update Payout" : "Add Payout"
                        }}</DialogTitle>
                        <DialogDescription>
                            {{
                                editingPayout
                                    ? "Update payout details"
                                    : "Add a new payout"
                            }}
                        </DialogDescription>
                    </DialogHeader>
                    <div class="mt-4 grid grid-cols-1 gap-4">
                        <input
                            v-model="form.teacher_name"
                            placeholder="Teacher Name"
                            class="w-full rounded border px-3 py-2"
                        />
                        <input
                            v-model="form.amount"
                            placeholder="Amount"
                            type="number"
                            class="w-full rounded border px-3 py-2"
                        />
                        <input
                            v-model="form.duration"
                            placeholder="Duration"
                            type="number"
                            class="w-full rounded border px-3 py-2"
                        />
                        <input
                            v-model="form.total_class"
                            placeholder="Total Classes"
                            type="number"
                            class="w-full rounded border px-3 py-2"
                        />
                        <input
                            v-model="form.incentives"
                            placeholder="Incentives"
                            type="number"
                            class="w-full rounded border px-3 py-2"
                        />
                        <select
                            v-model="form.status"
                            class="w-full rounded border px-3 py-2"
                        >
                            <option value="pending">Pending</option>
                            <option value="processing">Processing</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>
                    <DialogFooter class="mt-4 flex justify-end gap-2">
                        <Button @click="dialogOpen = false" variant="outline"
                            >Cancel</Button
                        >
                        <Button @click="savePayout">{{
                            editingPayout ? "Update" : "Save"
                        }}</Button>
                    </DialogFooter>
                </div>
            </Dialog>
        </div>
    </AuthenticatedLayout>
</template>
