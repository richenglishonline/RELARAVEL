<script setup>
import { computed, ref } from "vue";
import { Link, router, usePage } from "@inertiajs/vue3";
import {
    HomeIcon,
    UserGroupIcon,
    CalendarIcon,
    ClipboardDocumentListIcon,
    ClockIcon,
    ArrowRightOnRectangleIcon,
    Bars3Icon,
    XMarkIcon,
    BookOpenIcon,
    BanknotesIcon,
    FolderOpenIcon,
    VideoCameraIcon,
    MagnifyingGlassIcon,
    Cog6ToothIcon,
    UsersIcon,
    ClipboardDocumentCheckIcon,
} from "@heroicons/vue/24/outline";

const page = usePage();
const sidebarOpen = ref(false);
const profileMenuOpen = ref(false);

const role = computed(() => page.props.auth?.user?.role ?? "teacher");
const name = computed(() => page.props.auth?.user?.name ?? "User");

const goToProfile = () => {
    router.visit("/profile");
};

const handleLogout = () => {
    router.post(route("logout"));
};

const getNavigation = (role) => {
    if (role === "teacher") {
        return [
            {
                group: "Main",
                items: [
                    {
                        name: "Dashboard",
                        href: route("dashboard"),
                        icon: HomeIcon,
                    },
                ],
            },
            {
                group: "My Classes",
                items: [
                    {
                        name: "My Schedule",
                        href: route("schedule.index"),
                        icon: CalendarIcon,
                    },
                    {
                        name: "Classes",
                        href: route("classes.index"),
                        icon: CalendarIcon,
                    },
                    {
                        name: "Makeup Classes",
                        href: route("makeup-classes.index"),
                        icon: ClockIcon,
                    },
                ],
            },
            {
                group: "Students & Attendance",
                items: [
                    {
                        name: "My Students",
                        href: route("students.index"),
                        icon: UserGroupIcon,
                    },
                    {
                        name: "Attendance",
                        href: route("attendance.index"),
                        icon: ClipboardDocumentListIcon,
                    },
                ],
            },
            {
                group: "Resources",
                items: [
                    {
                        name: "Books",
                        href: route("books.index"),
                        icon: BookOpenIcon,
                    },
                    {
                        name: "Recordings",
                        href: route("recordings.index"),
                        icon: VideoCameraIcon,
                    },
                    {
                        name: "Reports",
                        href: route("reports.index"),
                        icon: FolderOpenIcon,
                    },
                ],
            },
        ];
    }

    if (role === "admin") {
        return [
            {
                group: "Main",
                items: [
                    {
                        name: "Dashboard",
                        href: route("dashboard"),
                        icon: HomeIcon,
                    },
                ],
            },
            {
                group: "User Management",
                items: [
                    {
                        name: "Teachers",
                        href: route("teachers.index"),
                        icon: UsersIcon,
                    },
                    {
                        name: "Students",
                        href: route("students.index"),
                        icon: UserGroupIcon,
                    },
                ],
            },
            {
                group: "Classes & Attendance",
                items: [
                    {
                        name: "Schedules",
                        href: route("classes.index"),
                        icon: CalendarIcon,
                    },
                    {
                        name: "Attendance",
                        href: route("attendance.index"),
                        icon: ClipboardDocumentListIcon,
                    },
                ],
            },
            {
                group: "Reports & Media",
                items: [
                    {
                        name: "Reports",
                        href: route("reports.index"),
                        icon: FolderOpenIcon,
                    },
                    {
                        name: "Screenshots",
                        href: route("screenshots.index"),
                        icon: VideoCameraIcon,
                    },
                    {
                        name: "Recordings",
                        href: route("recordings.index"),
                        icon: VideoCameraIcon,
                    },
                ],
            },
            {
                group: "Resources",
                items: [
                    {
                        name: "Books Archive",
                        href: route("books.index"),
                        icon: BookOpenIcon,
                    },
                ],
            },
            {
                group: "Tools",
                items: [
                    {
                        name: "Search",
                        href: route("search.index"),
                        icon: MagnifyingGlassIcon,
                    },
                ],
            },
        ];
    }

    if (role === "super-admin") {
        return [
            {
                group: "Main",
                items: [
                    {
                        name: "Dashboard",
                        href: route("dashboard"),
                        icon: HomeIcon,
                    },
                ],
            },
            {
                group: "User Management",
                items: [
                    {
                        name: "Teachers",
                        href: route("teachers.index"),
                        icon: UsersIcon,
                    },
                    {
                        name: "Teacher Applications",
                        href: route("teacher-applications.index"),
                        icon: ClipboardDocumentCheckIcon,
                    },
                    {
                        name: "Admins",
                        href: route("admins.index"),
                        icon: UsersIcon,
                    },
                    {
                        name: "Students",
                        href: route("students.index"),
                        icon: UserGroupIcon,
                    },
                ],
            },
            {
                group: "Classes & Attendance",
                items: [
                    {
                        name: "Schedules",
                        href: route("classes.index"),
                        icon: CalendarIcon,
                    },
                    {
                        name: "Attendance",
                        href: route("attendance.index"),
                        icon: ClipboardDocumentListIcon,
                    },
                ],
            },
            {
                group: "Content Management",
                items: [
                    {
                        name: "Books Management",
                        href: route("books.index"),
                        icon: BookOpenIcon,
                    },
                    {
                        name: "Assign Books",
                        href: route("assignments.index"),
                        icon: BookOpenIcon,
                    },
                    {
                        name: "Curriculum Access",
                        href: route("curriculum.index"),
                        icon: FolderOpenIcon,
                    },
                ],
            },
            {
                group: "Reports & Media",
                items: [
                    {
                        name: "Reports",
                        href: route("reports.index"),
                        icon: FolderOpenIcon,
                    },
                    {
                        name: "Screenshots",
                        href: route("screenshots.index"),
                        icon: VideoCameraIcon,
                    },
                    {
                        name: "Recordings",
                        href: route("recordings.index"),
                        icon: VideoCameraIcon,
                    },
                ],
            },
            {
                group: "Financial",
                items: [
                    {
                        name: "Salary Management",
                        href: route("salary.index"),
                        icon: BanknotesIcon,
                    },
                    {
                        name: "Payout Overview",
                        href: route("payouts.index"),
                        icon: BanknotesIcon,
                    },
                ],
            },
            {
                group: "Tools",
                items: [
                    {
                        name: "Search",
                        href: route("search.index"),
                        icon: MagnifyingGlassIcon,
                    },
                    {
                        name: "Settings",
                        href: route("settings.index"),
                        icon: Cog6ToothIcon,
                    },
                ],
            },
        ];
    }

    return [];
};

const navigation = computed(() => getNavigation(role.value));

const isCurrentPath = (href) => {
    const currentUrl = page.url.split("?")[0].split("#")[0];
    const targetUrl = href.split("?")[0].split("#")[0];
    if (currentUrl === targetUrl) return true;
    if (currentUrl.startsWith(targetUrl + "/")) return true;
    return false;
};
</script>

<template>
    <div class="h-screen flex overflow-hidden bg-gray-100">
        <div
            :class="{
                'fixed inset-0 flex justify-end z-40 md:hidden': true,
                hidden: !sidebarOpen,
            }"
        >
            <div
                class="fixed inset-0 bg-gray-600 bg-opacity-75"
                @click="sidebarOpen = false"
            />

            <!-- Slide-in from RIGHT on mobile -->
            <div
                class="relative flex flex-col max-w-xs w-full bg-white translate-x-0"
            >
                <div class="absolute top-0 right-0 -mr-12 pt-2">
                    <button
                        type="button"
                        class="ml-1 flex items-center justify-center h-10 w-10 rounded-full"
                        @click="sidebarOpen = false"
                    >
                        <XMarkIcon class="h-6 w-6 text-white" />
                    </button>
                </div>
                <div class="flex-1 h-0 pt-5 pb-4 overflow-y-auto">
                    <div
                        class="flex-shrink-0 flex items-center px-4 justify-between"
                    >
                        <h1 class="text-xl font-bold text-gray-900">
                            Rich English
                        </h1>

                        <button
                            type="button"
                            class="h-12 w-12 flex items-center justify-center rounded-md text-gray-600 hover:text-gray-900"
                            @click="sidebarOpen = false"
                        >
                            <XMarkIcon class="h-6 w-6" />
                        </button>
                    </div>

                    <nav class="mt-5 px-2 space-y-6">
                        <div
                            v-for="group in navigation"
                            :key="group.group"
                            class="space-y-1"
                        >
                            <div
                                class="px-2 py-1 text-xs font-semibold text-gray-500 uppercase tracking-wider"
                            >
                                {{ group.group }}
                            </div>

                            <Link
                                v-for="item in group.items"
                                :key="item.name"
                                :href="item.href"
                                :class="{
                                    'bg-blue-100 text-blue-900': isCurrentPath(
                                        item.href
                                    ),
                                    'text-gray-600 hover:bg-gray-50 hover:text-gray-900':
                                        !isCurrentPath(item.href),
                                }"
                                class="group flex items-center px-2 py-2 text-base font-medium rounded-md"
                            >
                                <component
                                    :is="item.icon"
                                    class="mr-4 h-6 w-6"
                                />
                                {{ item.name }}
                            </Link>
                        </div>
                    </nav>
                </div>

                <div
                    class="flex-shrink-0 border-t border-gray-200 p-4 relative"
                >
                    <button
                        @click="profileMenuOpen = !profileMenuOpen"
                        class="flex items-center w-full text-left"
                    >
                        <div class="flex-1">
                            <p class="text-base font-medium text-gray-700">
                                {{ name }}
                            </p>
                            <p class="text-xs text-gray-500">Account</p>
                        </div>
                        <svg
                            class="h-5 w-5 text-gray-500"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M19 9l-7 7-7-7"
                            />
                        </svg>
                    </button>

                    <div
                        v-if="profileMenuOpen"
                        class="fixed inset-0 z-50 max-w-80"
                        @click.self="profileMenuOpen = false"
                    >
                        <div
                            class="absolute bottom-14 left-4 right-4 bg-white shadow-lg rounded-xl py-2 border"
                        >
                            <button
                                @click="goToProfile"
                                class="w-full text-right px-4 py-2 text-sm text-gray-700 hover:bg-gray-50"
                            >
                                My Profile
                            </button>
                            <button
                                @click="handleLogout"
                                class="w-full text-right px-4 py-2 text-sm text-red-600 hover:bg-red-50"
                            >
                                Logout
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="hidden md:flex md:flex-shrink-0">
            <div class="flex flex-col w-64">
                <div class="flex flex-col h-0 flex-1">
                    <div class="flex-1 flex flex-col pt-5 pb-4 overflow-y-auto">
                        <div class="flex items-center flex-shrink-0 px-4">
                            <h1 class="text-xl font-bold text-gray-900">
                                Rich English
                            </h1>
                        </div>

                        <nav
                            class="mt-5 flex-1 px-2 bg-white space-y-6 overflow-y-auto"
                        >
                            <div
                                v-for="group in navigation"
                                :key="group.group"
                                class="space-y-1"
                            >
                                <div
                                    class="px-2 py-1 text-xs font-semibold text-gray-500 uppercase tracking-wider"
                                >
                                    {{ group.group }}
                                </div>

                                <Link
                                    v-for="item in group.items"
                                    :key="item.name"
                                    :href="item.href"
                                    :class="{
                                        'bg-blue-100 text-blue-900':
                                            isCurrentPath(item.href),
                                        'text-gray-600 hover:bg-gray-50 hover:text-gray-900':
                                            !isCurrentPath(item.href),
                                    }"
                                    class="group flex items-center px-2 py-2 text-sm font-medium rounded-md"
                                >
                                    <component
                                        :is="item.icon"
                                        class="mr-3 h-6 w-6"
                                    />
                                    {{ item.name }}
                                </Link>
                            </div>
                        </nav>
                    </div>

                    <div
                        class="flex-shrink-0 border-t border-gray-200 p-4 relative"
                    >
                        <button
                            @click="profileMenuOpen = !profileMenuOpen"
                            class="flex items-center w-full text-left"
                        >
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-700">
                                    {{ name }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    Manage Account
                                </p>
                            </div>
                            <svg
                                class="h-4 w-4 text-gray-500"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M19 9l-7 7-7-7"
                                />
                            </svg>
                        </button>

                        <div
                            v-if="profileMenuOpen"
                            class="fixed inset-0 z-50 max-w-xs"
                            @click.self="profileMenuOpen = false"
                        >
                            <div
                                class="absolute bottom-14 left-4 right-4 bg-white shadow-lg rounded-xl py-2 border"
                            >
                                <button
                                    @click="goToProfile"
                                    class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50"
                                >
                                    My Profile
                                </button>
                                <button
                                    @click="handleLogout"
                                    class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50"
                                >
                                    Logout
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex flex-col w-0 flex-1 overflow-hidden">
            <div
                class="md:hidden flex items-center justify-between px-4 py-3 bg-white shadow"
            >
                <!-- Left: LOGO -->
                <h1 class="text-lg font-bold text-gray-900">Rich English</h1>

                <!-- Right: HAMBURGER -->
                <button
                    type="button"
                    class="h-12 w-12 flex items-center justify-center rounded-md text-gray-600 hover:text-gray-900"
                    @click="sidebarOpen = true"
                >
                    <Bars3Icon class="h-6 w-6" />
                </button>
            </div>

            <main class="flex-1 relative overflow-y-auto">
                <div class="py-6">
                    <div class="sm:mx-auto px-4 sm:px-6 md:px-8">
                        <div v-if="$slots.header" class="mb-6">
                            <slot name="header" />
                        </div>
                        <slot />
                    </div>
                </div>
            </main>
        </div>
    </div>
</template>
