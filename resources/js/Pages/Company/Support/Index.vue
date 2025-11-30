<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
defineProps({ tickets: Object });
</script>

<template>
    <Head title="Suporte" />
    <AuthenticatedLayout>
        <template #header>Central de Ajuda</template>
        <div class="space-y-6">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center bg-white p-6 rounded-lg shadow-sm border border-gray-200 gap-4">
                <div>
                    <h2 class="text-xl font-bold text-sesi-blue">Meus Chamados</h2>
                </div>
                <Link :href="route('support.create')" class="w-full md:w-auto text-center px-6 py-3 bg-sesi-green text-white font-bold rounded-lg shadow-sm">+ Novo Chamado</Link>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left min-w-[700px]">
                        <thead class="bg-gray-50 text-gray-500 text-sm uppercase">
                            <tr>
                                <th class="p-4">Assunto</th>
                                <th class="p-4">Data</th>
                                <th class="p-4">Prioridade</th>
                                <th class="p-4">Status</th>
                                <th class="p-4 text-right">Ação</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr v-for="ticket in tickets.data" :key="ticket.id" class="hover:bg-gray-50">
                                <td class="p-4 font-bold">{{ ticket.subject }}</td>
                                <td class="p-4 text-sm">{{ new Date(ticket.created_at).toLocaleDateString() }}</td>
                                <td class="p-4 capitalize">{{ ticket.priority }}</td>
                                <td class="p-4"><span class="bg-gray-100 px-2 py-1 rounded text-xs font-bold uppercase">{{ ticket.status }}</span></td>
                                <td class="p-4 text-right"><Link :href="route('support.show', ticket.id)" class="text-blue-600 hover:underline">Ver</Link></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>