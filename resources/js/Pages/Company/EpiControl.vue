<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

// --- DADOS MOCKADOS (Fictícios para visualização) ---
const deliveries = [
    { 
        id: 1, 
        employee: 'João da Silva', 
        role: 'Soldador',
        epi: 'Capacete de Segurança Aba Frontal', 
        ca: '498', 
        date: '2024-01-15', 
        validity: '2026-01-15', 
        status: 'ok' 
    },
    { 
        id: 2, 
        employee: 'Maria Oliveira', 
        role: 'Auxiliar de Limpeza',
        epi: 'Luva de Látex Natural', 
        ca: '12345', 
        date: '2024-05-10', 
        validity: '2024-11-10', 
        status: 'warning' // Vencendo em breve
    },
    { 
        id: 3, 
        employee: 'Carlos Santos', 
        role: 'Eletricista',
        epi: 'Botina de Segurança Eletricista', 
        ca: '8742', 
        date: '2023-06-01', 
        validity: '2024-06-01', 
        status: 'expired' // Vencido
    },
    { 
        id: 4, 
        employee: 'Ana Costa', 
        role: 'Operadora de Máquina',
        epi: 'Protetor Auditivo Tipo Concha', 
        ca: '1423', 
        date: '2024-02-20', 
        validity: '2024-08-20', 
        status: 'ok' 
    },
    { 
        id: 5, 
        employee: 'Pedro Alves', 
        role: 'Almoxarife',
        epi: 'Óculos de Proteção Incolor', 
        ca: '9821', 
        date: '2024-03-10', 
        validity: '2025-03-10', 
        status: 'ok' 
    },
    { 
        id: 6, 
        employee: 'Fernanda Lima', 
        role: 'Técnica Química',
        epi: 'Respirador Semifacial', 
        ca: '4152', 
        date: '2024-06-01', 
        validity: '2024-12-01', 
        status: 'ok' 
    },
];

// Função auxiliar para formatar data
const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('pt-BR');
};
</script>

<template>
    <Head title="Controle de EPIs" />

    <AuthenticatedLayout>
        <template #header>Gestão de EPIs</template>

        <div class="space-y-6">
            
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center bg-white p-6 rounded-xl shadow-sm border border-gray-200 gap-4">
                <div>
                    <h2 class="text-xl font-bold text-sesi-blue">Ficha de Entrega e Controle</h2>
                    <p class="text-sm text-gray-500">Gerencie a entrega, troca e validade dos equipamentos de proteção.</p>
                </div>
                
                <button class="w-full md:w-auto px-6 py-3 bg-sesi-blue hover:bg-blue-800 text-white font-bold rounded-lg shadow-sm transition-all flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Registrar Nova Entrega
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-sesi-green">
                    <div class="text-sesi-green font-bold text-xs uppercase tracking-wide opacity-80">Entregues este Mês</div>
                    <div class="text-3xl font-extrabold text-gray-800 mt-2">12</div>
                    <p class="text-xs text-gray-400 mt-2">Equipamentos distribuídos</p>
                </div>
                
                <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-yellow-400">
                    <div class="text-yellow-600 font-bold text-xs uppercase tracking-wide opacity-80">Vencendo em 30 dias</div>
                    <div class="text-3xl font-extrabold text-gray-800 mt-2">3</div>
                    <p class="text-xs text-gray-400 mt-2">Prepare o estoque para troca</p>
                </div>
                
                <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-red-500">
                    <div class="text-red-500 font-bold text-xs uppercase tracking-wide opacity-80">Vencidos / Troca Urgente</div>
                    <div class="text-3xl font-extrabold text-gray-800 mt-2">1</div>
                    <p class="text-xs text-gray-400 mt-2">Funcionários irregulares</p>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left min-w-[1000px]"> <thead class="bg-gray-50 text-gray-600 text-sm uppercase font-bold border-b border-gray-200">
                            <tr>
                                <th class="p-4 w-1/4">Funcionário / Cargo</th>
                                <th class="p-4 w-1/4">Equipamento (EPI)</th>
                                <th class="p-4">C.A.</th>
                                <th class="p-4">Entrega</th>
                                <th class="p-4">Validade (Troca)</th>
                                <th class="p-4 text-center">Status</th>
                                <th class="p-4 text-right">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            <tr v-for="item in deliveries" :key="item.id" class="hover:bg-blue-50/30 transition-colors">
                                
                                <td class="p-4">
                                    <div class="font-bold text-gray-800">{{ item.employee }}</div>
                                    <div class="text-xs text-gray-500">{{ item.role }}</div>
                                </td>
                                
                                <td class="p-4">
                                    <span class="text-gray-700 font-medium">{{ item.epi }}</span>
                                </td>
                                
                                <td class="p-4 font-mono text-xs text-gray-500 bg-gray-50 rounded px-2 w-fit">
                                    {{ item.ca }}
                                </td>
                                
                                <td class="p-4 text-gray-600">
                                    {{ formatDate(item.date) }}
                                </td>
                                
                                <td class="p-4 font-bold font-mono" 
                                    :class="{
                                        'text-red-600': item.status === 'expired',
                                        'text-yellow-600': item.status === 'warning',
                                        'text-green-600': item.status === 'ok'
                                    }">
                                    {{ formatDate(item.validity) }}
                                </td>
                                
                                <td class="p-4 text-center">
                                    <span v-if="item.status === 'ok'" class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold uppercase border border-green-200">
                                        Em dia
                                    </span>
                                    <span v-if="item.status === 'warning'" class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-xs font-bold uppercase border border-yellow-200 animate-pulse">
                                        Vencendo
                                    </span>
                                    <span v-if="item.status === 'expired'" class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-bold uppercase border border-red-200">
                                        Vencido
                                    </span>
                                </td>
                                
                                <td class="p-4 text-right">
                                    <button class="text-sesi-blue hover:text-blue-800 font-bold text-xs flex items-center justify-end gap-1 ml-auto group">
                                        <svg class="w-4 h-4 group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        Ficha
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </AuthenticatedLayout>
</template>