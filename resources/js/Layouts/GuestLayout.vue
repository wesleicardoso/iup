<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen flex w-full">
        
        <div class="hidden lg:flex w-1/2 bg-sesi-blue relative overflow-hidden flex-col justify-between p-12 text-white">
            
            <div class="absolute inset-0 bg-sesi-blue-dark/20 z-0"></div>
            <div class="absolute -top-24 -left-24 w-96 h-96 bg-sesi-green/20 rounded-full blur-3xl z-0"></div>
            <div class="absolute bottom-0 right-0 w-full h-1/2 bg-gradient-to-t from-sesi-blue-dark to-transparent z-0"></div>

            <div class="relative z-10">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded bg-white flex items-center justify-center">
                        <span class="text-sesi-blue font-extrabold text-xl">S</span>
                    </div>
                    <span class="text-3xl font-bold tracking-wide">Sesi<span class="text-sesi-green">+</span></span>
                </div>
            </div>

            <div class="relative z-10 mb-20">
                <h1 class="text-5xl font-bold leading-tight mb-6">
                    Saúde e Segurança <br>
                    <span class="text-sesi-green">inteligentes</span> para <br>
                    sua indústria.
                </h1>
                <p class="text-lg text-blue-100 max-w-md">
                    Gerencie exames, laudos e conformidade com o eSocial em uma única plataforma integrada.
                </p>
            </div>

            <div class="relative z-10 text-sm text-blue-200">
                &copy; {{ new Date().getFullYear() }} Sesi Saúde. Todos os direitos reservados.
            </div>
        </div>

        <div class="w-full lg:w-1/2 flex flex-col justify-center items-center bg-white p-8 sm:p-12 lg:p-24 overflow-y-auto">
            <div class="w-full max-w-md space-y-8">
                <slot />
            </div>
        </div>
        
    </div>
</template>