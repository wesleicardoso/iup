<script setup>
import { ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

// Ícones SVG
const icons = {
    home: "M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6",
    users: "M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z",
    calendar: "M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z",
    clipboard: "M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01",
    settings: "M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z",
    logout: "M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1",
    check: "M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4",
    heart: "M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z",
    chart: "M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z",
    helmet: "M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z"
};

const page = usePage();
const user = page.props.auth.user;

// Estado do Menu Mobile
const showMobileMenu = ref(false);

// Configuração do Menu
const menuItems = [
    { label: 'Dashboard', route: 'dashboard', icon: icons.home, show: true },
    
    // Apenas VENDEDOR
    { label: 'Comercial / Vendas', route: 'sales.index', icon: icons.chart, show: user.role === 'sales' || user.role === 'admin' },

    // Apenas EMPRESA
    { label: 'Meus Agendamentos', route: 'appointments.index', icon: icons.calendar, show: user.role === 'company' || user.role === 'admin'},
    { label: 'Funcionários', route: 'employees.index', icon: icons.users, show: user.role === 'company'|| user.role === 'admin' },
    { label: 'Controle de EPIs', route: 'epi.index', icon: icons.helmet, show: user.role === 'company'|| user.role === 'admin' },
    
    // Apenas CREDENCIADA/MÉDICO
    // ESTE É O LINK PARA CONFIRMAR A DATA DE SOLICITAÇÕES:
    { label: 'Recepção / Agenda', route: 'provider.appointments.index', icon: icons.check, show: user.role === 'provider' || user.role === 'admin' },
   
    
    // Segurança
    { label: 'Segurança / PGR', route: 'safety.index', icon: icons.clipboard, show: user.role === 'safety' || user.role === 'admin' },

    // Comum
    { label: 'Suporte', route: 'support.index', icon: icons.settings, show: true },
];

const isActive = (routeKey) => {
    if (!routeKey) return false;
    // Lógica para manter ativo em sub-rotas
    return route().current(routeKey) || route().current(routeKey.replace('.index', '.*'));
};
</script>

<template>
    <div class="flex h-screen w-full bg-gray-50 font-sans text-gray-800">
        
        <div v-if="showMobileMenu" 
             class="fixed inset-0 bg-black/50 z-30 lg:hidden"
             @click="showMobileMenu = false">
        </div>

        <aside class="fixed inset-y-0 left-0 z-40 w-64 bg-sesi-blue flex flex-col justify-between shadow-xl transition-transform duration-300 lg:static lg:translate-x-0"
               :class="showMobileMenu ? 'translate-x-0' : '-translate-x-full'">
            
            <div class="h-16 flex items-center px-6 bg-sesi-blue-dark border-b border-white/10 justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded bg-white flex items-center justify-center">
                        <span class="text-sesi-blue font-extrabold text-lg">S</span>
                    </div>
                    <span class="text-xl font-bold text-white tracking-wide">Sesi<span class="text-sesi-green">Conecta</span></span>
                </div>
                <button @click="showMobileMenu = false" class="lg:hidden text-white">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <nav class="flex-1 py-6 overflow-y-auto custom-scrollbar">
                <ul class="space-y-1">
                    <template v-for="item in menuItems" :key="item.label">
                        <li v-if="item.show">
                            <Link 
                                :href="item.route ? route(item.route) : '#'"
                                class="flex items-center gap-4 px-6 py-3 border-l-4 transition-all duration-200 group relative"
                                :class="{ 
                                    'border-sesi-green bg-black/20 text-white': isActive(item.route), 
                                    'border-transparent text-blue-100 hover:bg-white/5 hover:text-white': !isActive(item.route)
                                }"
                                @click="showMobileMenu = false" 
                            >
                                <svg class="w-5 h-5" :class="isActive(item.route) ? 'text-sesi-green' : 'text-blue-200 group-hover:text-white'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon" />
                                </svg>
                                <span class="font-medium text-sm">{{ item.label }}</span>
                            </Link>
                        </li>
                    </template>
                </ul>
            </nav>

            <div class="p-4 bg-sesi-blue-dark border-t border-white/10">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-sesi-green flex items-center justify-center text-sesi-blue font-bold text-sm">
                        {{ user.name.charAt(0) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-white truncate">{{ user.name }}</p>
                        <p class="text-xs text-blue-300 truncate capitalize">{{ user.role || 'Usuário' }}</p>
                    </div>
                    <Link :href="route('logout')" method="post" as="button" class="text-blue-300 hover:text-red-400 transition-colors">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="icons.logout" />
                        </svg>
                    </Link>
                </div>
            </div>
        </aside>

        <div class="flex-1 flex flex-col h-full min-w-0 overflow-hidden">
            <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-4 lg:px-8 shadow-sm z-10 flex-shrink-0">
                
                <div class="flex items-center gap-4">
                    <button @click="showMobileMenu = true" class="lg:hidden p-2 rounded-md text-gray-500 hover:bg-gray-100">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    <div class="flex items-center gap-2 text-sm">
                        <span class="text-gray-400 hidden sm:inline">Home</span>
                        <span class="text-gray-300 hidden sm:inline">/</span>
                        <span class="text-sesi-blue font-bold text-base truncate max-w-[200px]">
                            <slot name="header"></slot>
                        </span>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <button class="p-2 rounded-full text-gray-400 hover:text-sesi-blue hover:bg-blue-50 transition-colors relative">
                        <div class="absolute top-2 right-2 w-2 h-2 bg-sesi-green rounded-full border-2 border-white"></div>
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                    </button>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto bg-gray-50 p-4 lg:p-8 custom-scrollbar">
                <div class="max-w-7xl mx-auto">
                    <slot />
                </div>
            </main>
        </div>
    </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { width: 8px; }
.custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; }
.custom-scrollbar::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 4px; }
.custom-scrollbar:hover::-webkit-scrollbar-thumb { background-color: #94a3b8; }
</style>