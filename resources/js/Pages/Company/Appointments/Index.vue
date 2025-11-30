<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({ appointments: Object });

const formatDate = (date) => new Date(date).toLocaleDateString('pt-BR');
</script>

<template>
    <Head title="Meus Agendamentos" />
    <AuthenticatedLayout>
        <template #header>Controle de Exames</template>

        <div class="space-y-6">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center bg-white p-6 rounded-lg shadow-sm border border-gray-200 gap-4">
                <div>
                    <h2 class="text-xl font-bold text-sesi-blue">Histórico</h2>
                    <p class="text-sm text-gray-500">Gerencie seus exames.</p>
                </div>
                <Link :href="route('appointments.create')" class="w-full md:w-auto text-center px-6 py-3 bg-sesi-green hover:bg-green-600 text-white font-bold rounded-lg shadow-sm transition-all">
                    + Novo Agendamento
                </Link>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse min-w-[900px]">
                        <thead class="bg-gray-50 text-gray-500 text-xs uppercase font-semibold border-b border-gray-200">
                            <tr>
                                <th class="p-4 pl-6">Colaborador</th>
                                <th class="p-4">Exame</th>
                                <th class="p-4">Clínica</th>
                                <th class="p-4">Data</th>
                                <th class="p-4">Status</th>
                                <th class="p-4 text-right pr-6">Ação</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700 divide-y divide-gray-100">
                            <tr v-for="apt in appointments.data" :key="apt.id" class="hover:bg-blue-50/30 transition-colors">
                                
                                <td class="p-4 pl-6 font-bold text-gray-800">
                                    {{ apt.employee_name }}
                                </td>
                                
                                <td class="p-4">
                                    <span class="bg-gray-100 px-3 py-1 rounded-md text-xs font-bold border border-gray-200">
                                        {{ apt.exam_type }}
                                    </span>
                                </td>
                                
                                <td class="p-4 text-sm text-gray-600">
                                    {{ apt.provider?.name || '-' }}
                                </td>
                                
                                <td class="p-4 font-mono text-sm">
                                    <span v-if="apt.scheduled_at" class="font-bold text-gray-700">
                                        {{ formatDate(apt.scheduled_at) }}
                                    </span>
                                    <span v-else class="inline-flex items-center gap-1 text-xs font-bold text-orange-500 bg-orange-50 px-2 py-1 rounded border border-orange-100">
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Aguardando Confirmação
                                    </span>
                                </td>
                                
                                <td class="p-4">
                                    <span v-if="apt.status === 'concluido'" class="text-green-600 font-bold text-xs uppercase bg-green-50 px-2 py-1 rounded">Pronto</span>
                                    <span v-else-if="apt.status === 'solicitado'" class="text-purple-600 font-bold text-xs uppercase bg-purple-50 px-2 py-1 rounded">Solicitado</span>
                                    <span v-else class="text-yellow-600 font-bold text-xs uppercase bg-yellow-50 px-2 py-1 rounded">Agendado</span>
                                </td>
                                
                                <td class="p-4 text-right pr-6">
                                    <a v-if="apt.status === 'concluido' && apt.result_url" :href="route('appointments.download', apt.id)" target="_blank" class="text-sesi-blue font-bold hover:underline text-sm">
                                        Baixar PDF
                                    </a>
                                    <span v-else class="text-gray-300 text-xs">-</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>