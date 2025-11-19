<script setup>
import { reactive, ref, onMounted, computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Button from '@/Components/ui/Button.vue';
import api from '@/lib/api';
import { Cog6ToothIcon } from '@heroicons/vue/24/outline';

const loading = ref(false);
const saving = ref(false);
const settings = ref([]);
const pagination = ref(null);
const groupedSettings = computed(() => {
    const groups = {};
    settings.value.forEach(setting => {
        const group = setting.group || 'general';
        if (!groups[group]) {
            groups[group] = [];
        }
        groups[group].push(setting);
    });
    return groups;
});

const fetchSettings = async () => {
    loading.value = true;
    try {
        const { data } = await api.get('/settings', { params: { limit: 100 } });
        settings.value = data.settings;
        pagination.value = data.pagination;
    } catch (error) {
        console.error('Error fetching settings:', error);
    } finally {
        loading.value = false;
    }
};

const handleSave = async () => {
    saving.value = true;
    try {
        const settingsToUpdate = settings.value.map(s => ({
            key: s.key,
            value: s.value,
        }));
        await api.post('/settings/bulk-update', { settings: settingsToUpdate });
        alert('Settings saved successfully!');
    } catch (error) {
        console.error('Error saving settings:', error);
        alert('Failed to save settings');
    } finally {
        saving.value = false;
    }
};

const getInputType = (type) => {
    switch (type) {
        case 'number':
            return 'number';
        case 'boolean':
            return 'checkbox';
        default:
            return 'text';
    }
};

const getInputValue = (setting) => {
    if (setting.type === 'boolean') {
        return setting.value === '1' || setting.value === 'true' || setting.value === true;
    }
    return setting.value || '';
};

const updateSetting = (setting, value) => {
    if (setting.type === 'boolean') {
        setting.value = value ? '1' : '0';
    } else {
        setting.value = value;
    }
};

onMounted(fetchSettings);
</script>

<template>
    <Head title="Settings" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold leading-tight text-gray-800">
                        System Settings
                    </h2>
                    <p class="text-sm text-gray-500">
                        Configure system-wide settings and preferences.
                    </p>
                </div>
                <Button
                    @click="handleSave"
                    :disabled="saving"
                >
                    {{ saving ? 'Saving...' : 'Save Settings' }}
                </Button>
            </div>
        </template>

        <div class="py-10">
            <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
                <div v-if="loading" class="text-center py-12 text-gray-500">Loading settings...</div>
                <template v-else>
                    <div
                        v-for="(groupSettings, groupName) in groupedSettings"
                        :key="groupName"
                        class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm"
                    >
                        <div class="flex items-center gap-4 mb-6">
                            <Cog6ToothIcon class="h-8 w-8 text-gray-400" />
                            <h3 class="text-lg font-semibold text-gray-900 capitalize">{{ groupName }} Settings</h3>
                        </div>
                        <div class="space-y-4">
                            <div
                                v-for="setting in groupSettings"
                                :key="setting.id"
                            >
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ setting.key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()) }}
                                    <span v-if="setting.description" class="text-xs font-normal text-gray-500 ml-2">
                                        ({{ setting.description }})
                                    </span>
                                </label>
                                <input
                                    v-if="setting.type !== 'boolean'"
                                    :type="getInputType(setting.type)"
                                    :value="getInputValue(setting)"
                                    @input="updateSetting(setting, $event.target.value)"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                />
                                <div v-else class="flex items-center">
                                    <input
                                        type="checkbox"
                                        :checked="getInputValue(setting)"
                                        @change="updateSetting(setting, $event.target.checked)"
                                        class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                    />
                                    <label class="ml-2 text-sm text-gray-700">
                                        {{ getInputValue(setting) ? 'Enabled' : 'Disabled' }}
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-if="settings.length === 0" class="text-center py-12">
                        <Cog6ToothIcon class="mx-auto h-12 w-12 text-gray-400" />
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No settings found</h3>
                        <p class="mt-1 text-sm text-gray-500">Settings will appear here once created.</p>
                    </div>
                </template>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

