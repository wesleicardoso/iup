<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

// Props vindas do DashboardController
const props = defineProps({
    company: Object,
    appointments: Array, // Histórico geral (para contagem)
    upcomingAppointments: Array // Lista filtrada de futuros
});

// Configuração das Etapas da Timeline (Adicionando quem é o responsável)
const stepsConfig = [
    { 
        key: 'contrato_assinado', 
        label: 'Contrato Assinado', 
        icon: '📝', 
        desc: 'Documentação jurídica finalizada.',
        responsible: 'Vendas/Admin' 
    },
    { 
        key: 'importacao_m1', 
        label: 'Importação de Dados (M1)', 
        icon: '📊', 
        desc: 'Carga de dados de funcionários e cargos no sistema.',
        responsible: 'Vendas/Admin'
    },
    { 
        key: 'visita_tecnica', 
        label: 'Visita Técnica', 
        icon: '👷', 
        desc: 'Engenheiro de segurança realiza levantamento de riscos in-loco.',
        responsible: 'Técnica de Segurança'
    },
    { 
        key: 'aprovacao_pgr', 
        label: 'Elaboração do PGR', 
        icon: '✅', 
        desc: 'Programa de Gerenciamento de Riscos em análise final.' ,
        responsible: 'Técnica de Segurança'
    },
    { 
        key: 'aprovacao_pcmso', 
        label: 'Emissão do PCMSO', 
        icon: '🩺', 
        desc: 'Médico coordenador define os exames ocupacionais obrigatórios.',
        responsible: 'Médico Coordenador'
    },
    { 
        key: 'concluido', 
        label: 'Acesso Liberado', 
        icon: '🚀', 
        desc: 'Sua empresa está 100% apta a operar.',
        responsible: 'Sistema' 
    },
];

// Lógica para verificar o status visual de cada etapa
const getStepStatus = (stepKey) => {
    const keys = stepsConfig.map(s => s.key);
    const currentIndex = keys.indexOf(props.company.onboarding_step);
    const stepIndex = keys.indexOf(stepKey);

    if (stepIndex < currentIndex) return 'completed';
    if (stepIndex === currentIndex) return 'current';
    return 'pending';
};

// Formatadores de Data
const formatVisitDate = (date) => {
    if (!date) return '';
    return new Date(date + 'T12:00:00').toLocaleDateString('pt-BR');
};

const formatDateTime = (date) => new Date(date).toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit', hour: '2-digit', minute: '2-digit' });
</script>

<template>
    <Head title="Painel da Empresa" />
    
    <AuthenticatedLayout>
        <template #header>
            {{ company.is_active ? 'Visão Geral' : 'Status da Implantação' }}
        </template>

        <div v-if="!company.is_active" class="max-w-4xl mx-auto space-y-8 animate-fade-in">
            
            <div class="bg-white p-8 rounded-xl shadow-sm border-t-4 border-sesi-blue text-center">
                <h2 class="text-2xl font-bold text-sesi-blue mb-2">Preparando seu ambiente, {{ company.name }}!</h2>
                <p class="text-gray-600 max-w-2xl mx-auto text-lg leading-relaxed">
                    Acompanhe em tempo real o andamento das etapas de ativação.
                </p>
            </div>

            <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-200">
                <h3 class="font-bold text-gray-800 mb-8 text-xl border-b pb-4 flex items-center gap-2">
                    <span class="bg-sesi-green w-2 h-6 rounded-full"></span>
                    Etapas do Processo
                </h3>
                
                <div class="relative pl-4">
                    <div class="absolute left-9 top-4 bottom-10 w-0.5 bg-gray-200 z-0"></div>

                    <div v-for="(step, index) in stepsConfig" :key="step.key" class="relative z-10 flex items-start gap-6 pb-10 group last:pb-0">
                        
                        <div class="flex-shrink-0 w-10 h-10 rounded-full border-4 flex items-center justify-center bg-white transition-all duration-500 z-10"
                            :class="{
                                'border-sesi-green text-sesi-green': getStepStatus(step.key) === 'completed',
                                'border-sesi-blue ring-4 ring-blue-50 text-sesi-blue scale-110': getStepStatus(step.key) === 'current',
                                'border-gray-200 text-gray-300': getStepStatus(step.key) === 'pending'
                            }">
                            <svg v-if="getStepStatus(step.key) === 'completed'" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                            </svg>
                            <span v-else class="text-sm font-bold">{{ index + 1 }}</span>
                        </div>

                        <div class="flex-1 bg-white border rounded-xl p-5 transition-all duration-300"
                            :class="{
                                'border-gray-200 opacity-60 bg-gray-50': getStepStatus(step.key) === 'pending',
                                'border-sesi-blue shadow-md bg-white ring-1 ring-blue-50': getStepStatus(step.key) === 'current',
                                'border-sesi-green/30 bg-green-50/10': getStepStatus(step.key) === 'completed'
                            }">
                            
                            <div class="flex flex-col md:flex-row justify-between md:items-start gap-4">
                                <div class="flex-1">
                                    <h4 class="font-bold text-lg flex items-center gap-2"
                                        :class="getStepStatus(step.key) === 'pending' ? 'text-gray-500' : 'text-gray-800'">
                                        {{ step.icon }} {{ step.label }}
                                    </h4>
                                    
                                    <div class="text-sm text-gray-500 mt-2 leading-relaxed">
                                        <div v-if="step.key === 'visita_tecnica' && company.technical_visit_at" 
                                             class="inline-flex items-center gap-3 bg-blue-50 text-sesi-blue px-4 py-2 rounded-lg border border-blue-100 shadow-sm">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            <span class="font-bold">Agendada para: {{ formatVisitDate(company.technical_visit_at) }}</span>
                                        </div>
                                        <p v-else class="text-sm text-gray-600">
                                            {{ step.desc }}
                                        </p>
                                    </div>
                                </div>

                                <div class="flex-shrink-0 text-right">
                                    <span v-if="getStepStatus(step.key) === 'completed'" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800 uppercase tracking-wide border border-green-200">
                                        Concluído
                                    </span>
                                    <span v-else-if="getStepStatus(step.key) === 'current'" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800 uppercase tracking-wide border border-blue-200 animate-pulse">
                                        Em Andamento
                                    </span>
                                    <span v-else class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-gray-200 text-gray-500 uppercase tracking-wide border border-gray-300">
                                        Aguardando
                                    </span>
                                    
                                    <p v-if="getStepStatus(step.key) !== 'completed'" class="text-xs text-gray-400 mt-1">
                                        Resp: {{ step.responsible }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-else class="space-y-8 animate-fade-in">
            
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center bg-white p-6 rounded-xl shadow-sm border border-gray-200 gap-4">
                 <div>
                    <h2 class="text-2xl font-bold text-sesi-blue mb-1">Olá, {{ company.name }}</h2>
                    <p class="text-gray-500 text-sm">Seu painel está ativo e operante.</p>
                </div>
              
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-sesi-blue">
                    <div class="text-sesi-blue font-bold text-xs uppercase tracking-wide opacity-70">Funcionários Ativos</div>
                    <div class="text-4xl font-extrabold text-gray-800 mt-2">150</div>
                    <Link :href="route('employees.index')" class="text-sm text-sesi-green hover:underline font-bold mt-4 inline-block">Gerenciar &rarr;</Link>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-sesi-green">
                    <div class="text-sesi-green font-bold text-xs uppercase tracking-wide opacity-70">Total de Exames</div>
                    <div class="text-4xl font-extrabold text-gray-800 mt-2">{{ appointments.length }}</div>
                    <Link :href="route('appointments.index')" class="text-sm text-sesi-blue hover:underline font-bold mt-4 inline-block">Ver Histórico &rarr;</Link>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-blue-400">
                    <div class="text-blue-500 font-bold text-xs uppercase tracking-wide opacity-70">Próximos Agendamentos</div>
                    <div class="text-4xl font-extrabold text-gray-800 mt-2">{{ upcomingAppointments.length }}</div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                        📅 Detalhes dos Próximos Exames
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left min-w-[700px]">
                        <thead class="bg-gray-50 text-gray-500 text-xs uppercase font-bold">
                            <tr>
                                <th class="p-4">Data / Hora</th>
                                <th class="p-4">Colaborador</th>
                                <th class="p-4">Exame</th>
                                <th class="p-4">Clínica</th>
                                <th class="p-4">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            <tr v-for="apt in upcomingAppointments" :key="apt.id" class="hover:bg-blue-50/30">
                                <td class="p-4 font-mono font-bold text-sesi-blue">{{ formatDateTime(apt.scheduled_at) }}</td>
                                <td class="p-4 font-bold text-gray-700">{{ apt.employee_name || apt.employee?.name }}</td>
                                <td class="p-4"><span class="bg-gray-100 px-2 py-1 rounded text-xs">{{ apt.exam_type }}</span></td>
                                <td class="p-4 text-gray-500">{{ apt.provider?.name || 'Local não definido' }}</td>
                                <td class="p-4"><span class="text-yellow-600 font-bold text-xs uppercase bg-yellow-50 px-2 py-1 rounded border border-yellow-100">Agendado</span></td>
                            </tr>
                            <tr v-if="upcomingAppointments.length === 0">
                                <td colspan="5" class="p-8 text-center text-gray-400">Nenhum exame agendado para os próximos dias.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.animate-fade-in { animation: fadeIn 0.5s ease-out; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>