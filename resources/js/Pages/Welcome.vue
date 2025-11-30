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
    <Head title="Bem-vindo" />

    <div class="min-h-screen flex w-full bg-white font-sans">
        
        <div class="hidden lg:flex lg:w-7/12 bg-sesi-blue relative overflow-hidden flex-col justify-between p-16 text-white">
            
            <div class="absolute inset-0 bg-gradient-to-br from-sesi-blue-dark via-sesi-blue to-blue-500 opacity-90 z-0"></div>
            <div class="absolute -top-32 -left-32 w-[500px] h-[500px] bg-sesi-green/20 rounded-full blur-3xl z-0 mix-blend-overlay"></div>
            <div class="absolute bottom-0 right-0 w-full h-2/3 bg-gradient-to-t from-sesi-blue-dark/50 to-transparent z-0"></div>

            <div class="relative z-10 flex items-center gap-4">
                <div class="w-12 h-12 rounded-lg bg-white shadow-lg flex items-center justify-center transition-transform hover:scale-105">
                    <span class="text-sesi-blue font-extrabold text-2xl">S</span>
                </div>
                <span class="text-4xl font-bold tracking-wide flex items-center">
                    Sesi <span class="text-sesi-green ml-1 text-5xl leading-none">+</span>
                </span>
            </div>

            <div class="relative z-10 mb-12">
                <h1 class="text-5xl xl:text-6xl font-bold leading-tight mb-8">
                    Mais Saúde.<br>
                    Mais Segurança.<br>
                    <span class="text-sesi-green">Mais Resultados.</span>
                </h1>
                <p class="text-xl text-blue-100 max-w-xl leading-relaxed">
                    A plataforma integrada para a gestão completa da saúde ocupacional e segurança do trabalho da sua indústria. Simples, eficiente e em conformidade.
                </p>
            </div>

            <div class="relative z-10 flex items-center gap-8 text-sm text-blue-200 font-medium">
                <span>© {{ new Date().getFullYear() }} Sesi.</span>
                <a href="#" class="hover:text-white transition-colors">Política de Privacidade</a>
                <a href="#" class="hover:text-white transition-colors">Termos de Uso</a>
            </div>
        </div>

        <div class="w-full lg:w-5/12 flex flex-col justify-center items-start p-8 sm:p-12 lg:p-20 bg-white relative">
            
            <div class="lg:hidden absolute top-8 left-8 flex items-center gap-3">
                 <div class="w-10 h-10 rounded bg-sesi-blue flex items-center justify-center">
                    <span class="text-white font-extrabold text-xl">S</span>
                </div>
                <span class="text-2xl font-bold text-sesi-blue tracking-wide flex items-center">
                    Sesi <span class="text-sesi-green ml-1">+</span>
                </span>
            </div>

            <div class="max-w-md w-full mx-auto lg:mx-0">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">
                    Comece agora sua jornada.
                </h2>
                <p class="text-gray-600 text-lg mb-10 leading-relaxed">
                    Acesse o ecossistema Sesi + para gerenciar vidas, agendar exames e garantir a conformidade da sua empresa.
                </p>

                <div class="space-y-4 flex flex-col w-full">
                    
                    <Link
                        v-if="canRegister"
                        :href="route('register')"
                        class="w-full flex items-center justify-center px-8 py-4 border border-transparent text-lg font-bold rounded-xl text-white bg-sesi-green hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sesi-green transition-all transform hover:-translate-y-1 shadow-sm hover:shadow-md"
                    >
                        Criar minha conta corporativa
                    </Link>

                    <div class="flex items-center justify-center gap-2 mt-4">
                        <span class="text-gray-600">Já é cliente?</span>
                        <Link
                            :href="route('login')"
                            class="font-bold text-sesi-blue hover:text-sesi-blue-dark hover:underline text-lg transition-colors"
                        >
                            Fazer Login
                        </Link>
                    </div>
                </div>
               
                <div class="mt-16 pt-8 border-t border-gray-100 grid grid-cols-2 gap-6">
                    <div class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-sesi-green flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-sm text-gray-600 font-medium">Conformidade com eSocial</span>
                    </div>
                    <div class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-sesi-green flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-sm text-gray-600 font-medium">Agendamento Ágil</span>
                    </div>
                     <div class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-sesi-green flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <span class="text-sm text-gray-600 font-medium">Rede Credenciada</span>
                    </div>
                    <div class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-sesi-green flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span class="text-sm text-gray-600 font-medium">Laudos Digitais</span>
                    </div>
                </div>

            </div>
        </div>
    </div>
</template>