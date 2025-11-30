<script setup>
import { Head, Link } from '@inertiajs/vue3';
// Não é necessário importar GuestLayout, pois a estrutura split está no próprio Welcome.vue

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
});

// Ícones SVG para os diferenciais (Minimalistas)
const features = [
    { name: 'Compliance Legal', desc: 'Fim das multas do eSocial.', icon: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' },
    { name: 'Fluxo Intuitivo', desc: '', icon: 'M13 10V3L4 14h7v7l9-11h-7z' },
    { name: 'Gestão Ágil', desc: 'Reduza o tempo de admissão com menos burocracia.', icon: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z' },
];
</script>

<template>
    <Head title="IUp | Saúde Ocupacional Ágil" />

    <div class="min-h-screen flex w-full bg-white font-sans">
        
        <div class="hidden lg:flex w-7/12 bg-iup-blue relative overflow-hidden flex-col justify-between p-16 text-white">
            
            <div class="absolute inset-0 bg-iup-dark-blue-text/20 z-0"></div>
            <div class="absolute -top-24 -left-24 w-96 h-96 bg-iup-green/30 rounded-full blur-3xl z-0 mix-blend-screen"></div>

            <div class="relative z-10 flex items-center gap-4">
                <!-- <div class="w-12 h-12 rounded-lg bg-iup-green flex items-center justify-center shadow-lg">
                    <span class="text-white font-extrabold text-2xl">iUp</span>
                </div> -->
                <span class="text-4xl font-bold tracking-wide">
                    IUp
                    <span class="text-iup-green ml-1 text-5xl leading-none"></span>
                </span>
            </div>

            <div class="relative z-10 mb-12">
                <h1 class="text-5xl xl:text-6xl font-bold leading-tight mb-6">
                    Saúde Ocupacional Ágil.
                </h1>
                <h2 class="text-3xl font-semibold text-iup-green mb-8">
                    Fim da planilha. Início da agilidade.
                </h2>
                <p class="text-lg text-blue-100 max-w-xl leading-relaxed">
                    Transformamos a burocracia do eSocial em um fluxo intuitivo, conectando RH, Técnicos e Clínicas em tempo real.
                </p>
            </div>

            <div class="relative z-10 text-sm text-blue-200">
                &copy; {{ new Date().getFullYear() }} IUp Conexões Inteligentes.
            </div>
        </div>

        <div class="w-full lg:w-5/12 flex flex-col justify-center items-start p-8 sm:p-12 lg:p-20 bg-white relative overflow-y-auto">
            
            <div class="max-w-md w-full mx-auto space-y-10">
                
                <div class="pt-16 lg:pt-0">
                    <h2 class="text-3xl font-bold text-gray-900 mb-2">
                        Ganhe Tempo. Garanta Compliance.
                    </h2>
                    <p class="text-gray-600 text-lg">
                        Acesse a plataforma ou solicite a implantação.
                    </p>
                </div>

                <div class="space-y-6">
                    <div v-for="feature in features" :key="feature.name" class="flex items-start gap-4">
                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-iup-green/10 flex items-center justify-center text-iup-green">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="feature.icon" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800">{{ feature.name }}</h4>
                            <p class="text-sm text-gray-500">{{ feature.desc }}</p>
                        </div>
                    </div>
                </div>

                <div class="space-y-4 pt-6 border-t border-gray-200">
                    
                    <Link
                        v-if="canLogin"
                        :href="route('login')"
                        class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-lg font-bold rounded-xl text-white bg-iup-green hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-iup-green transition-all transform hover:scale-[1.01]"
                    >
                        Acessar Minha Plataforma
                    </Link>

                    <Link
                        v-if="canRegister"
                        :href="route('register')"
                        class="w-full flex items-center justify-center px-8 py-3 border border-iup-blue text-lg font-bold rounded-xl text-iup-blue hover:bg-iup-blue hover:text-white transition-colors"
                    >
                        Solicitar Implantação
                    </Link>

                    <div v-else-if="canLogin" class="text-center text-sm text-gray-500 pt-4">
                        <p>Entre em contato para iniciar o onboarding.</p>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</template>