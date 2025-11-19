<script setup>
import { reactive, ref, onMounted, computed } from 'vue';
import { Head, usePage, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import api from '@/lib/api';
import Button from '@/Components/ui/Button.vue';
import Dialog from '@/Components/ui/Dialog.vue';
import DialogHeader from '@/Components/ui/DialogHeader.vue';
import DialogTitle from '@/Components/ui/DialogTitle.vue';
import DialogDescription from '@/Components/ui/DialogDescription.vue';
import DialogFooter from '@/Components/ui/DialogFooter.vue';
import Input from '@/Components/ui/Input.vue';
import Label from '@/Components/ui/Label.vue';
import { VideoCameraIcon, PlusIcon } from '@heroicons/vue/24/outline';

const page = usePage();
const role = computed(() => page.props.auth?.user?.role ?? 'teacher');
const userId = computed(() => page.props.auth?.user?.id);

const loading = ref(false);
const rows = ref([]);
const pagination = ref(null);
const classOptions = ref([]);
const uploadDialogOpen = ref(false);
const uploading = ref(false);
const fileInput = ref(null);

const uploadForm = reactive({
    class_id: '',
    attendance_id: '',
    file: null,
});

const filters = reactive({
    class_id: '',
    attendance_id: '',
    page: 1,
    limit: 20,
});

const loadFilters = async () => {
    try {
        // Load classes for filter dropdown
        const { data } = await api.get('/class', {
            params: {
                teacher_id: role.value === 'teacher' ? userId.value : undefined,
                limit: 100,
            },
        });
        classOptions.value = data.classes || [];
    } catch (error) {
        console.error('Error loading filters:', error);
    }
};

const fetchRecordings = async () => {
    loading.value = true;
    try {
        const { data } = await api.get('/recording', { params: filters });
        rows.value = data.recordings;
        pagination.value = data.pagination;
    } catch (error) {
        console.error('Error fetching recordings:', error);
    } finally {
        loading.value = false;
    }
};

const goToPage = (pageNum) => {
    if (!pagination.value) return;
    if (pageNum < 1 || pageNum > pagination.value.totalPages) return;
    filters.page = pageNum;
    fetchRecordings();
};

const resetFilters = () => {
    filters.class_id = '';
    filters.attendance_id = '';
    filters.page = 1;
    fetchRecordings();
};

const formatDate = (dateString) => {
    if (!dateString) return '—';
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const getRecordingUrl = (recording) => {
    if (recording.drive?.link) {
        return recording.drive.link;
    }
    if (recording.path) {
        return `/storage/${recording.path}`;
    }
    return null;
};

const handleFileChange = (event) => {
    const [file] = event.target.files ?? [];
    uploadForm.file = file ?? null;
};

const resetUploadForm = () => {
    uploadForm.class_id = '';
    uploadForm.attendance_id = '';
    uploadForm.file = null;
    if (fileInput.value) {
        fileInput.value.value = '';
    }
};

const handleUpload = async () => {
    if (!uploadForm.class_id || !uploadForm.file) {
        alert('Please select a class and file');
        return;
    }

    uploading.value = true;
    try {
        const formData = new FormData();
        formData.append('class_id', uploadForm.class_id);
        if (uploadForm.attendance_id) {
            formData.append('attendance_id', uploadForm.attendance_id);
        }
        formData.append('file', uploadForm.file);

        await api.post('/recording', formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        });

        uploadDialogOpen.value = false;
        resetUploadForm();
        await fetchRecordings();
    } catch (error) {
        console.error('Error uploading recording:', error);
        alert(error.response?.data?.message || 'Failed to upload recording');
    } finally {
        uploading.value = false;
    }
};

onMounted(async () => {
    await loadFilters();
    await fetchRecordings();
});
</script>

<template>
    <Head title="Recordings" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold leading-tight text-gray-800">
                        Recordings
                    </h2>
                    <p class="text-sm text-gray-500">
                        View and manage class recordings.
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <Button
                        @click="uploadDialogOpen = true"
                        :disabled="loading"
                    >
                        <PlusIcon class="h-4 w-4 mr-2" />
                        Upload Recording
                    </Button>
                    <button
                        @click="fetchRecordings"
                        class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                        :disabled="loading"
                    >
                        Refresh
                    </button>
                </div>
            </div>
        </template>

        <div class="py-10">
            <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
                <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                    <form class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                Class
                            </label>
                            <select
                                v-model="filters.class_id"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            >
                                <option value="">All Classes</option>
                                <option
                                    v-for="classItem in classOptions"
                                    :key="classItem.id"
                                    :value="classItem.id"
                                >
                                    {{ classItem.student?.name ?? 'Unknown' }} - {{ classItem.start_date }}
                                </option>
                            </select>
                        </div>
                        <div class="flex items-end gap-3 sm:col-span-2 lg:col-span-3">
                            <button
                                type="button"
                                @click="fetchRecordings"
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

                <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr class="text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    <th scope="col" class="px-6 py-3">
                                        Recording
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Class
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Uploaded By
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Uploaded At
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                <tr v-if="!loading && rows.length === 0">
                                    <td
                                        colspan="5"
                                        class="px-6 py-4 text-center text-sm text-gray-500"
                                    >
                                        No recordings found.
                                    </td>
                                </tr>
                                <tr
                                    v-for="recording in rows"
                                    :key="recording.id"
                                    class="text-sm text-gray-700 hover:bg-gray-50"
                                >
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <VideoCameraIcon class="h-5 w-5 text-gray-400" />
                                            <div>
                                                <div class="font-medium text-gray-900">
                                                    {{ recording.filename ?? 'Untitled Recording' }}
                                                </div>
                                                <div v-if="recording.drive?.link" class="text-xs text-gray-500">
                                                    Google Drive
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div v-if="recording.classSchedule">
                                            <div class="font-medium text-gray-900">
                                                {{ recording.classSchedule.student?.name ?? 'Unknown Student' }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ recording.classSchedule.start_date }} {{ recording.classSchedule.start_time }}
                                            </div>
                                        </div>
                                        <span v-else class="text-gray-400">—</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ recording.uploader?.name ?? 'Unknown' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ formatDate(recording.created_at) }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <Button
                                                @click="router.visit(route('recordings.show', recording.id))"
                                                variant="outline"
                                                size="sm"
                                            >
                                                View
                                            </Button>
                                        </div>
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
                                :disabled="loading || pagination.page === pagination.totalPages"
                            >
                                Next
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Upload Dialog -->
        <Dialog :open="uploadDialogOpen" @update:open="uploadDialogOpen = $event">
            <div class="max-w-md">
                <DialogHeader>
                    <DialogTitle>Upload Recording</DialogTitle>
                    <DialogDescription>
                        Upload a class recording video file.
                    </DialogDescription>
                </DialogHeader>
                <div class="grid gap-4 py-4">
                    <div>
                        <Label for="upload-class">Class *</Label>
                        <select
                            id="upload-class"
                            v-model="uploadForm.class_id"
                            class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                            required
                        >
                            <option value="">Select a class</option>
                            <option
                                v-for="classItem in classOptions"
                                :key="classItem.id"
                                :value="classItem.id"
                            >
                                {{ classItem.student?.name ?? 'Unknown' }} - {{ classItem.start_date }} {{ classItem.start_time }}
                            </option>
                        </select>
                    </div>
                    <div>
                        <Label for="upload-attendance">Attendance (Optional)</Label>
                        <Input
                            id="upload-attendance"
                            v-model="uploadForm.attendance_id"
                            type="text"
                            placeholder="Attendance ID"
                        />
                    </div>
                    <div>
                        <Label for="upload-file">Video File *</Label>
                        <input
                            ref="fileInput"
                            id="upload-file"
                            type="file"
                            accept="video/*"
                            @change="handleFileChange"
                            class="mt-1 block w-full text-sm text-gray-700"
                            required
                        />
                        <p class="mt-1 text-xs text-gray-500">
                            Maximum size 100MB. Supported formats: MP4, MOV, WEBM, AVI.
                        </p>
                    </div>
                </div>
                <DialogFooter>
                    <Button
                        variant="outline"
                        @click="uploadDialogOpen = false"
                        :disabled="uploading"
                    >
                        Cancel
                    </Button>
                    <Button
                        @click="handleUpload"
                        :disabled="uploading || !uploadForm.class_id || !uploadForm.file"
                    >
                        {{ uploading ? 'Uploading...' : 'Upload' }}
                    </Button>
                </DialogFooter>
            </div>
        </Dialog>
    </AuthenticatedLayout>
</template>

