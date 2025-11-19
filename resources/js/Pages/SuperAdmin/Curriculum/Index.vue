<script setup>
import { reactive, ref, onMounted } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import api from '@/lib/api';
import Button from '@/Components/ui/Button.vue';
import { BookOpenIcon, FolderOpenIcon } from '@heroicons/vue/24/outline';

const loading = ref(false);
const books = ref([]);
const pagination = ref(null);

const filters = reactive({
    search: '',
    level: '',
    is_active: '',
    page: 1,
    limit: 20,
});

const fetchCurricula = async () => {
    loading.value = true;
    try {
        const { data } = await api.get('/curriculum', { params: filters });
        books.value = data.curricula;
        pagination.value = data.pagination;
    } catch (error) {
        console.error('Error fetching curricula:', error);
    } finally {
        loading.value = false;
    }
};

const goToPage = (pageNum) => {
    if (!pagination.value) return;
    if (pageNum < 1 || pageNum > pagination.value.totalPages) return;
    filters.page = pageNum;
    fetchCurricula();
};

const resetFilters = () => {
    filters.search = '';
    filters.level = '';
    filters.is_active = '';
    filters.page = 1;
    fetchCurricula();
};

onMounted(fetchCurricula);
</script>

<template>
    <Head title="Curriculum Access" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold leading-tight text-gray-800">
                        Curriculum Access
                    </h2>
                    <p class="text-sm text-gray-500">
                        Manage curriculum resources and access control.
                    </p>
                </div>
                <Button
                    @click="fetchCurricula"
                    :disabled="loading"
                >
                    Refresh
                </Button>
            </div>
        </template>

        <div class="py-10">
            <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
                <!-- Filters -->
                <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                    <form class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                Search
                            </label>
                            <input
                                v-model="filters.search"
                                type="text"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                placeholder="Search by title or description"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                Level
                            </label>
                            <select
                                v-model="filters.level"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            >
                                <option value="">All Levels</option>
                                <option value="Beginner">Beginner</option>
                                <option value="Intermediate">Intermediate</option>
                                <option value="Advanced">Advanced</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                Status
                            </label>
                            <select
                                v-model="filters.is_active"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            >
                                <option value="">All</option>
                                <option value="true">Active</option>
                                <option value="false">Inactive</option>
                            </select>
                        </div>
                        <div class="flex items-end gap-3 sm:col-span-2 lg:col-span-3">
                            <button
                                type="button"
                                @click="fetchCurricula"
                                class="flex-1 rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
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

                <!-- Curriculum Grid -->
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <div
                        v-for="curriculum in books"
                        :key="curriculum.id"
                        class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm hover:shadow-md transition-shadow cursor-pointer"
                        @click="router.visit(route('curriculum.index'))"
                    >
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0">
                                <BookOpenIcon class="h-8 w-8 text-indigo-600" />
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-2">
                                    <h3 class="text-lg font-semibold text-gray-900">
                                        {{ curriculum.title }}
                                    </h3>
                                    <span
                                        v-if="curriculum.is_active"
                                        class="inline-flex items-center rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-800"
                                    >
                                        Active
                                    </span>
                                </div>
                                <p v-if="curriculum.description" class="text-sm text-gray-500 line-clamp-2 mb-2">
                                    {{ curriculum.description }}
                                </p>
                                <div class="flex items-center gap-4 text-xs text-gray-500">
                                    <span v-if="curriculum.level">{{ curriculum.level }}</span>
                                    <span v-if="curriculum.duration_minutes">{{ curriculum.duration_minutes }} min</span>
                                </div>
                                <div class="mt-4">
                                    <Button
                                        @click.stop="router.visit(route('curriculum.index'))"
                                        variant="outline"
                                        size="sm"
                                    >
                                        View Details
                                    </Button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="!loading && books.length === 0" class="text-center py-12">
                    <FolderOpenIcon class="mx-auto h-12 w-12 text-gray-400" />
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No curriculum found</h3>
                    <p class="mt-1 text-sm text-gray-500">Create a new curriculum to get started.</p>
                </div>

                <div
                    v-if="pagination"
                    class="flex flex-col items-center justify-between gap-4 border-t border-gray-200 px-6 py-4 text-sm text-gray-600 sm:flex-row"
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
                            :disabled="loading || pagination.page === pagination.totalPages"
                        >
                            Next
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

