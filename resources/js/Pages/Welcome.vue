<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
});


</script>

<template>

    <Head title="Welcome" />
    <div class="bg-gray-50 text-black/50 dark:bg-black dark:text-white/50">
        <div style="background-image: url('photos/background.png');"
            class="relative min-h-screen flex flex-col items-center justify-center selection:bg-[#FF2D20] selection:text-white">
            <div class="relative w-full max-w-2xl px-6 lg:max-w-7xl">
                <header class="grid grid-cols-2 items-center gap-2 py-10 lg:grid-cols-3">
                    <main class="flex justify-center mt-6">
                        <Link v-if="$page.props.auth.user" :href="route('people.index')"
                            class="border-2 rounded-md px-3 py-2 text-white ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white">
                        Pagina de Pessoas
                        </Link>

                        <template v-else>
                            <Link :href="route('login')"
                                class="rounded-md border-white border-4 px-3 py-2 text-white ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white">
                            Log in
                            </Link>
                        </template>
                    </main>
                </header>
                <div class="col-span-3">
                    <img src="/photos/astronauta.png" alt="Astronauta" class="floating">
                </div>
            </div>
        </div>
    </div>
</template>
