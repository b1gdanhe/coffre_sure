<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>

    <Head title="Accueil">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    <div
        class="relative flex min-h-screen flex-col items-center p-6 text-white lg:justify-center lg:p-8 overflow-hidden">
        <!-- Couche de fond avec image -->
        <div class="absolute inset-0 z-0">
            <div class="absolute inset-0 bg-[url(/img/home.jpeg)] bg-cover bg-center"></div>
            <!-- Overlay avec dégradé radial -->
            <div class="absolute inset-0 bg-gradient-to-r from-green-900/80 via-green-800/70 to-green-700/80"></div>
            <div class="absolute inset-0 bg-radial-gradient"></div>
        </div>

        <!-- Contenu principal (au premier plan) -->
        <header class="not-has-[nav]:hidden mb-6 w-full max-w-[335px] text-sm lg:max-w-4xl z-10">
            <nav class="flex items-center justify-end gap-4">
                <Link v-if="$page.props.auth.user" :href="route('user.dashboard')"
                    class="inline-block rounded-sm border border-white/30 px-5 py-1.5 text-sm leading-normal text-white hover:border-white/60 hover:bg-white/10 transition-all duration-300">
                Dashboard
                </Link>
                <template v-else>
                    <Link :href="route('login')"
                        class="inline-block rounded-sm border border-transparent px-5 py-1.5 text-sm leading-normal text-white hover:border-white/30 hover:bg-white/10 transition-all duration-300">
                    Se connecter
                    </Link>
                    <Link :href="route('register')"
                        class="inline-block rounded-sm border border-white px-5 py-1.5 text-sm leading-normal bg-white/90 text-green-900 hover:bg-white transition-all duration-300 hover:shadow-md">
                    S'inscrire
                    </Link>
                </template>
            </nav>
        </header>

        <div
            class="duration-750 starting:opacity-0 flex w-full items-center justify-center opacity-100 transition-opacity lg:grow z-10">
            <main class="flex w-full max-w-[335px] justify-center overflow-hidden lg:max-w-4xl lg:flex-row">
                <div class="text-center">
                    <h1 class="text-6xl sm:text-7xl md:text-8xl font-extrabold">
                        <span
                            class="bg-gradient-to-r from-green-200 via-green-300 to-green-100 bg-clip-text text-transparent drop-shadow-sm">
                            Coffre Sûr
                        </span>
                    </h1>
                    <p class="mt-4 text-xl text-white/90 max-w-2xl mx-auto">
                        Sécurisez vos identifiants numériques avec la solution la plus fiable du marché
                    </p>
                    <div class="mt-8 flex justify-center gap-4">
                        <button
                            class="px-6 py-3 bg-green-600 hover:bg-green-700 rounded-md transition-all duration-300 font-medium">
                            Découvrir
                        </button>
                        <button
                            class="px-6 py-3 bg-transparent border border-white/50 hover:border-white hover:bg-white/10 rounded-md transition-all duration-300">
                            En savoir plus
                        </button>
                    </div>
                </div>
            </main>
        </div>

        <!-- Effet de lumière radiale -->
        <div class="absolute top-1/4 left-1/4 w-1/2 h-1/2 bg-green-400/20 blur-3xl rounded-full z-0 animate-pulse">
        </div>
        <div class="absolute bottom-0 right-1/4 w-1/3 h-1/3 bg-green-300/10 blur-2xl rounded-full z-0"></div>

        <div class="h-14 hidden lg:block z-10"></div>
    </div>
</template>
<style>
/* Style pour le dégradé radial custom */
.bg-radial-gradient {
    background: radial-gradient(circle at center, rgba(34, 197, 94, 0.3) 0%, rgba(6, 78, 59, 0.6) 50%, rgba(0, 0, 0, 0.8) 100%);
}

/* Animation de pulsation légère */
@keyframes pulse {
    0% {
        opacity: 0.5;
    }

    50% {
        opacity: 0.7;
    }

    100% {
        opacity: 0.5;
    }
}

.animate-pulse {
    animation: pulse 5s infinite ease-in-out;
}
</style>
