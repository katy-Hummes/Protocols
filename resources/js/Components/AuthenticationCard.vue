<template>

    <div class="min-h-screen grid grid-cols-5 sm:justify-center items-center pt-6 sm:pt-0 bg-transparent" style="background-image: url('photos/background.png');">
        <div>
            <slot name="logo" />
        </div>

        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-gray-800 bg-opacity-90 border-black border-2 shadow-md overflow-hidden sm:rounded-lg">
            <slot />
        </div>
        <div class="col-span-3">
            <img src="/photos/astronauta.png" alt="Astronauta" class="floating">
        </div>
    </div>
</template>

<style>
@keyframes floatAnimation {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-20px);
    }
}

.floating {
    animation: floatAnimation 5s ease-in-out infinite;
}
</style>
