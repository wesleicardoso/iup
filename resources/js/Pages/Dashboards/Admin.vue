<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({ 
    user: Object, 
    upcomingAppointments: Array,
    stats: Object 
});

// Formatadores
const formatDate = (date) => new Date(date).toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit', hour: '2-digit', minute: '2-digit' });
</script>

<template>
    <Head title="Painel Administrativo" />
    
    <AuthenticatedLayout>
        <template #header>Central de Comando</template>

        <div class="space-y-8 animate-fade-in">
            
            <div class="flex flex-col md:flex-row justify-between items-center bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                <div>
                    <h2 class="text-2xl font-bold text-sesi-blue">Visão Geral do Sistema</h2>
                    <p class="text-sm text-gray-500">Monitoramento em tempo real de todas as unidades e clientes.</p>
                </div>
                <div class="flex gap-3 mt-4 md:mt-0">
                    <Link :href="route('sales.index')" class="px-5 py-2.5 bg-blue-50 text-sesi-blue font-bold rounded-lg border border-blue-100 hover:bg-blue-100 transition-colors text-sm">
                        Gerenciar Vendas
                    </Link>
                    <Link :href="route('support.index')" class="px-5 py-2.5 bg-sesi-green text-white font-bold rounded-lg shadow-sm hover:bg-green-600 transition-colors text-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                        Ver Chamados
                    </Link>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-sesi-blue flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wide">Empresas na Base</p>
                        <p class="text-3xl font-extrabold text-sesi-blue">{{ stats.total_companies }}</p>
                    </div>
                    <div class="p-3 bg-blue-50 rounded-lg text-sesi-blue">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-purple-500 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wide">Em Onboarding</p>
                        <p class="text-3xl font-extrabold text-purple-600">{{ stats.onboarding_companies }}</p>
                    </div>
                    <div class="p-3 bg-purple-50 rounded-lg text-purple-600">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-sesi-green flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wide">Total de Exames</p>
                        <p class="text-3xl font-extrabold text-sesi-green">{{ stats.total_exams }}</p>
                    </div>
                    <div class="p-3 bg-green-50 rounded-lg text-sesi-green">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-red-500 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wide">Suporte Pendente</p>
                        <p class="text-3xl font-extrabold text-red-500">{{ stats.pending_tickets }}</p>
                    </div>
                    <div class="p-3 bg-red-50 rounded-lg text-red-500">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
                            Agenda Global (Próximos)
                        </h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left min-w-[700px]">
                            <thead class="bg-white text-gray-500 text-xs uppercase font-bold border-b border-gray-100">
                                <tr>
                                    <th class="p-4">Horário</th>
                                    <th class="p-4">Colaborador</th>
                                    <th class="p-4">Empresa (Cliente)</th>
                                    <th class="p-4">Local (Clínica)</th>
                                    <th class="p-4 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 text-sm text-gray-700">
                                <tr v-for="apt in upcomingAppointments" :key="apt.id" class="hover:bg-blue-50/30 transition-colors">
                                    <td class="p-4 font-mono font-bold text-sesi-blue">{{ formatDate(apt.scheduled_at) }}</td>
                                    <td class="p-4 font-bold">{{ apt.employee_name || apt.employee?.name }}</td>
                                    <td class="p-4 text-xs uppercase font-semibold text-gray-500">{{ apt.company?.name || 'Particular' }}</td>
                                    <td class="p-4 text-xs">{{ apt.provider?.name || 'Matriz Sesi' }}</td>
                                    <td class="p-4 text-center">
                                        <span class="px-2 py-1 rounded text-xs font-bold uppercase border"
                                            :class="{
                                                'bg-green-100 text-green-700 border-green-200': apt.status === 'concluido',
                                                'bg-yellow-100 text-yellow-700 border-yellow-200': apt.status === 'pendente' || apt.status === 'aguardando'
                                            }">
                                            {{ apt.status }}
                                        </span>
                                    </td>
                                </tr>
                                <tr v-if="upcomingAppointments.length === 0">
                                    <td colspan="5" class="p-8 text-center text-gray-400 italic">Nenhum agendamento futuro no sistema.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="space-y-6">
                    
                    <!-- <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h3 class="font-bold text-gray-800 mb-4">Saúde do Ecossistema</h3>
                        <div class="space-y-4">
                            <div>
                                <div class="flex justify-between text-xs font-bold text-gray-500 mb-1">
                                    <span>Ocupação da Agenda</span>
                                    <span>85%</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-2">
                                    <div class="bg-sesi-blue h-2 rounded-full" style="width: 85%"></div>
                                </div>
                            </div>
                            <div>
                                <div class="flex justify-between text-xs font-bold text-gray-500 mb-1">
                                    <span>Conformidade eSocial</span>
                                    <span>92%</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-2">
                                    <div class="bg-sesi-green h-2 rounded-full" style="width: 92%"></div>
                                </div>
                            </div>
                            <div>
                                <div class="flex justify-between text-xs font-bold text-gray-500 mb-1">
                                    <span>Satisfação (NPS)</span>
                                    <span>4.8/5</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-2">
                                    <div class="bg-purple-500 h-2 rounded-full" style="width: 96%"></div>
                                </div>
                            </div>
                        </div>
                    </div> -->

                    <div class="bg-sesi-blue-dark rounded-xl shadow-lg p-6 text-white">
                        <h3 class="font-bold text-lg mb-2">Acesso Rápido</h3>
                        <p class="text-blue-200 text-sm mb-4">Ferramentas de gestão.</p>
                        
                        <div class="grid grid-cols-2 gap-3">
                            <button class="p-3 bg-white/10 hover:bg-white/20 rounded-lg text-sm font-bold transition-colors text-center">
                                Usuários
                            </button>
                            <button class="p-3 bg-white/10 hover:bg-white/20 rounded-lg text-sm font-bold transition-colors text-center">
                                Configurações
                            </button>
                            <button class="p-3 bg-white/10 hover:bg-white/20 rounded-lg text-sm font-bold transition-colors text-center">
                                Auditoria
                            </button>
                            <button class="p-3 bg-white/10 hover:bg-white/20 rounded-lg text-sm font-bold transition-colors text-center">
                                Relatórios
                            </button>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.animate-fade-in { animation: fadeIn 0.5s ease-out; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>