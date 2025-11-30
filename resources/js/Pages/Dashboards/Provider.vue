<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

// Props: Dados da clínica e lista de próximos pacientes
defineProps({ 
    provider: Object,
    upcomingAppointments: Array 
});

const formatDateTime = (date) => new Date(date).toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit', hour: '2-digit', minute: '2-digit' });
</script>

<template>
    <Head title="Painel da Credenciada" />
    <AuthenticatedLayout>
        <template #header>Portal da Credenciada</template>

        <div class="space-y-8">
            
            <div class="bg-sesi-blue rounded-xl shadow-md overflow-hidden text-white relative">
                <div class="p-8 relative z-10">
                    <h3 class="text-2xl font-bold mb-2">Olá, {{ provider?.name || 'Parceiro' }}</h3>
                    <p class="text-blue-100">Bem-vindo ao portal Sesi Conecta. Gerencie sua agenda e resultados de forma integrada.</p>
                </div>
                <div class="absolute right-0 top-0 h-full w-1/3 bg-white/5 skew-x-12"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-all group">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-14 h-14 rounded-2xl bg-blue-50 flex items-center justify-center text-sesi-blue group-hover:bg-sesi-blue group-hover:text-white transition-colors">
                            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                        </div>
                        <div>
                             <h4 class="text-xl font-bold text-gray-800">Recepção / Agenda</h4>
                             <p class="text-gray-500 text-sm">Visualizar pacientes do dia e dar check-in.</p>
                        </div>
                    </div>
                    <Link :href="route('provider.appointments.index')" class="block w-full py-3 bg-blue-50 text-sesi-blue font-bold rounded-lg text-center hover:bg-sesi-blue hover:text-white transition-colors">
                        Acessar Recepção
                    </Link>
                </div>

                <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-all group">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-14 h-14 rounded-2xl bg-green-50 flex items-center justify-center text-sesi-green group-hover:bg-sesi-green group-hover:text-white transition-colors">
                            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-xl font-bold text-gray-800">Painel Médico</h4>
                            <p class="text-gray-500 text-sm">Atendimento clínico e preenchimento de ASO.</p>
                        </div>
                    </div>
                    <Link :href="route('doctor.index')" class="block w-full py-3 bg-green-50 text-sesi-green font-bold rounded-lg text-center hover:bg-sesi-green hover:text-white transition-colors">
                        Acessar Consultório
                    </Link>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                    <h3 class="text-lg font-bold text-gray-800">Próximos Pacientes (Agenda Futura)</h3>
                    <Link :href="route('provider.appointments.index')" class="text-sm text-sesi-blue hover:underline font-bold">Ver agenda completa</Link>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left min-w-[700px]">
                        <thead class="bg-gray-50 text-gray-500 text-xs uppercase font-bold">
                            <tr>
                                <th class="p-4">Data / Hora</th>
                                <th class="p-4">Paciente</th>
                                <th class="p-4">Empresa</th>
                                <th class="p-4">Procedimento</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            <tr v-for="apt in upcomingAppointments" :key="apt.id" class="hover:bg-blue-50/50">
                                <td class="p-4 font-mono font-bold text-gray-600">{{ formatDateTime(apt.scheduled_at) }}</td>
                                <td class="p-4 font-bold text-gray-800">{{ apt.employee_name || apt.employee?.name }}</td>
                                <td class="p-4 text-gray-600">{{ apt.company?.name || 'Particular/Avulso' }}</td>
                                <td class="p-4"><span class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs font-bold">{{ apt.exam_type }}</span></td>
                            </tr>
                            <tr v-if="upcomingAppointments.length === 0">
                                <td colspan="4" class="p-8 text-center text-gray-400">Sua agenda futura está livre.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </AuthenticatedLayout>
</template>