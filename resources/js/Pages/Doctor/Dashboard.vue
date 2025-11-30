<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    queue: Array
});

// Estatísticas Computadas em Tempo Real
const stats = computed(() => {
    return {
        total: props.queue.length,
        waiting: props.queue.filter(a => a.status === 'aguardando').length,
        done: props.queue.filter(a => a.status === 'concluido').length,
        pending: props.queue.filter(a => a.status === 'pendente').length,
    };
});

// Função para atualizar a lista manualmente
const refreshQueue = () => {
    router.reload({ only: ['queue'] });
};

// Formatação de Hora
const formatTime = (dateString) => {
    return new Date(dateString).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
};
</script>

<template>
    <Head title="Painel Médico" />

    <AuthenticatedLayout>
        <template #header>Fila de Atendimento</template>

        <div class="space-y-6">
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white p-4 rounded-lg shadow-sm border-l-4 border-sesi-green flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wide">Na Sala de Espera</p>
                        <p class="text-3xl font-extrabold text-sesi-green">{{ stats.waiting }}</p>
                    </div>
                    <div class="p-2 bg-green-50 rounded-lg text-sesi-green animate-pulse">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>

                <div class="bg-white p-4 rounded-lg shadow-sm border-l-4 border-sesi-blue flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wide">Atendidos Hoje</p>
                        <p class="text-3xl font-extrabold text-sesi-blue">{{ stats.done }}</p>
                    </div>
                    <div class="p-2 bg-blue-50 rounded-lg text-sesi-blue">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>

                <div class="bg-white p-4 rounded-lg shadow-sm border-l-4 border-gray-300 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wide">Ainda não chegaram</p>
                        <p class="text-3xl font-extrabold text-gray-600">{{ stats.pending }}</p>
                    </div>
                    <div class="p-2 bg-gray-100 rounded-lg text-gray-400">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>

                <button @click="refreshQueue" class="flex flex-col items-center justify-center bg-sesi-blue hover:bg-sesi-blue-dark text-white rounded-lg shadow-sm transition-colors p-4">
                    <svg class="w-6 h-6 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    <span class="text-sm font-bold">Atualizar Fila</span>
                </button>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                    <h3 class="text-lg font-bold text-gray-700">Pacientes do Dia ({{ new Date().toLocaleDateString() }})</h3>
                </div>

                <table class="w-full text-left">
                    <thead class="bg-gray-100 text-gray-500 text-sm uppercase font-semibold">
                        <tr>
                            <th class="p-4 w-24 text-center">Horário</th>
                            <th class="p-4">Paciente / Empresa</th>
                            <th class="p-4">Procedimento</th>
                            <th class="p-4">Status</th>
                            <th class="p-4 text-right">Ação</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        
                        <tr v-for="apt in queue" :key="apt.id" 
                            class="transition-colors"
                            :class="{
                                'bg-green-50/60': apt.status === 'aguardando', // Destaque suave verde
                                'bg-white hover:bg-gray-50': apt.status !== 'aguardando',
                                'opacity-60': apt.status === 'concluido' // Meio apagado se já foi
                            }">
                            
                            <td class="p-4 text-center font-mono text-gray-600 font-bold">
                                {{ formatTime(apt.scheduled_at) }}
                            </td>

                            <td class="p-4">
                                <div class="font-bold text-gray-800 text-base">{{ apt.employee?.name || apt.employee_name }}</div>
                                <div class="text-xs text-gray-500 uppercase font-semibold">
                                    {{ apt.company?.name || 'Empresa não vinculada' }}
                                </div>
                            </td>

                            <td class="p-4">
                                <span class="inline-block px-2 py-1 bg-gray-200 text-gray-700 text-xs rounded font-bold">
                                    {{ apt.exam_type }}
                                </span>
                            </td>

                            <td class="p-4">
                                <div v-if="apt.status === 'aguardando'" class="flex items-center gap-2 text-sesi-green font-bold animate-pulse">
                                    <span class="relative flex h-3 w-3">
                                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                      <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                                    </span>
                                    NA SALA DE ESPERA
                                </div>

                                <div v-else-if="apt.status === 'pendente'" class="flex items-center gap-2 text-gray-400 font-bold">
                                    <span class="h-2.5 w-2.5 rounded-full bg-gray-300"></span>
                                    Não chegou
                                </div>

                                <div v-else-if="apt.status === 'concluido'" class="flex items-center gap-2 text-sesi-blue font-bold">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Atendimento Finalizado
                                </div>
                            </td>

                            <td class="p-4 text-right">
                                <Link v-if="apt.status !== 'concluido'" 
                                    :href="route('doctor.attend', apt.id)" 
                                    class="inline-flex items-center gap-2 px-4 py-2 text-white font-bold rounded-lg shadow-sm transition-all"
                                    :class="apt.status === 'aguardando' ? 'bg-sesi-green hover:bg-green-600 hover:-translate-y-0.5 shadow-md' : 'bg-gray-400 hover:bg-gray-500'"
                                >
                                    <span>{{ apt.status === 'aguardando' ? 'Chamar Agora' : 'Iniciar' }}</span>
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                    </svg>
                                </Link>

                                <span v-else class="text-xs font-bold text-gray-400">
                                    Concluído às {{ new Date(apt.updated_at).toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'}) }}
                                </span>
                            </td>
                        </tr>

                        <tr v-if="queue.length === 0">
                            <td colspan="5" class="p-12 text-center">
                                <div class="flex flex-col items-center justify-center text-gray-400">
                                    <svg class="w-16 h-16 mb-4 text-gray-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p class="text-xl font-bold text-gray-500">Agenda Livre</p>
                                    <p class="text-sm">Nenhum paciente agendado ou aguardando para hoje.</p>
                                </div>
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>
        </div>
    </AuthenticatedLayout>
</template>