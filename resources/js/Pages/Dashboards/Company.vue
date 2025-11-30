<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

// Props vindas do DashboardController
const props = defineProps({
    company: {
        type: Object,
        required: true,
        default: () => ({}) // <- segurança extra
    },
    appointments: {
        type: Array,
        default: () => []
    },
    upcomingAppointments: {
        type: Array,
        default: () => []
    },
    onboardingDocuments: {
        type: Array,
        default: () => []
    }
});

// Formulário para Ação de Aprovação
const formApproval = useForm({});

// Configuração das Etapas da Timeline (Mantida)
const stepsConfig = [
    { key: 'contrato_assinado', label: 'Contrato Assinado', icon: '📝', desc: 'Vínculo jurídico estabelecido.', responsible: 'Vendas/Admin' },
    { key: 'importacao_m1', label: 'Importação de Dados (M1)', icon: '📊', desc: 'Carga de dados de funcionários e cargos.' , responsible: 'Vendas/Admin'},
    { key: 'visita_tecnica', label: 'Visita Técnica', icon: '👷', desc: 'Levantamento de riscos in-loco.', responsible: 'Técnica de Segurança'},
    { key: 'aprovacao_pgr', label: 'Elaboração do PGR', icon: '✅', desc: 'Programa de Gerenciamento de Riscos em análise.', responsible: 'Técnica de Segurança'},
    { key: 'aprovacao_pcmso', label: 'Emissão do PCMSO', icon: '🩺', desc: 'Médico coordenador define os exames ocupacionais.', responsible: 'Médico Coordenador'},
    { key: 'concluido', label: 'Acesso Liberado', icon: '🚀', desc: 'Sua empresa está 100% apta a operar.', responsible: 'Sistema' },
];

// Lógica para verificar o status visual de cada etapa (Mantida)
const getStepStatus = (stepKey) => {
    const keys = stepsConfig.map(s => s.key);
    const currentIndex = keys.indexOf(props.company.onboarding_step);
    const stepIndex = keys.indexOf(stepKey);

    if (stepIndex < currentIndex) return 'completed';
    if (stepIndex === currentIndex) return 'current';
    return 'pending';
};

// ✅ FUNÇÃO CORRIGIDA COM VERIFICAÇÃO DE UNDEFINED/NULL (resolve o erro 'find')
const getDocumentForStep = (stepKey) => {
    // 1. Defesa: Se a prop não for um array válido, retorna null imediatamente.
    if (!Array.isArray(props.onboardingDocuments)) return null;

    // 2. Busca o primeiro documento para o passo que AINDA NÃO FOI APROVADO
    return props.onboardingDocuments.find(doc => 
        doc.step_key === stepKey && doc.is_approved_by_client === false
    );
};

// Funções Auxiliares de Data (Mantidas)
const formatVisitDate = (date) => date ? new Date(date + 'T12:00:00').toLocaleDateString('pt-BR') : '';
const formatDateTime = (date) => new Date(date).toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit', hour: '2-digit', minute: '2-digit' });


// Ação de Aprovação do Cliente
const submitApproval = (companyId, stepKey) => {
    if(confirm(`Tem certeza que deseja APROVAR o documento ${stepKey.toUpperCase()} e avançar para a próxima etapa?`)) {
        formApproval.post(route('client.onboarding.approve', [companyId, stepKey]));
    }
};
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
                    Sua ação é necessária nas etapas com status **Em Andamento**.
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
                                    <h4 class="text-lg font-bold flex items-center gap-2"
                                        :class="getStepStatus(step.key) === 'pending' ? 'text-gray-500' : 'text-gray-800'">
                                        {{ step.icon }} {{ step.label }}
                                    </h4>
                                    
                                    <p class="text-sm text-gray-500 mt-2 leading-relaxed">{{ step.desc }}</p>

                                    <div v-if="getStepStatus(step.key) === 'current' && (step.key === 'aprovacao_pgr' || step.key === 'aprovacao_pcmso')">
                                        <div v-if="getDocumentForStep(step.key)" class="mt-3 p-3 bg-red-50 border border-red-200 rounded-lg flex flex-col sm:flex-row items-center justify-between gap-3">
                                            
                                            <span class="text-red-700 font-bold text-sm flex items-center gap-2">
                                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.3 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                                Ação Requerida: Seu OK é necessário.
                                            </span>

                                            <div class="flex flex-col sm:flex-row gap-2">
                                                <a :href="route('safety.document.download', [company.id, step.key])" target="_blank" class="px-3 py-1.5 border border-red-500 text-red-600 font-bold rounded-lg hover:bg-red-100 transition-colors text-xs flex justify-center">
                                                    Baixar Laudo Final
                                                </a>
                                                <button @click="submitApproval(company.id, step.key)" 
                                                    :disabled="formApproval.processing"
                                                    class="px-3 py-1.5 bg-sesi-green text-white font-bold rounded-lg hover:bg-green-600 transition-colors text-xs flex justify-center">
                                                    APROVAR E AVANÇAR
                                                </button>
                                            </div>
                                        </div>
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
                <Link :href="route('appointments.create')" class="w-full md:w-auto text-center px-6 py-3 bg-sesi-green hover:bg-green-600 text-white font-bold rounded-lg shadow-sm transition-transform active:scale-95 flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    Novo Agendamento
                </Link>
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
                    <div class="text-blue-500 font-bold text-xs uppercase tracking-wide opacity-70">Agendados (Futuro)</div>
                    <div class="text-4xl font-extrabold text-gray-800 mt-2">{{ upcomingAppointments.length }}</div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                    <h3 class="font-bold text-gray-800 flex items-center gap-2">
                        📅 Detalhes dos Próximos Exames
                    </h3>
                    <Link :href="route('appointments.index')" class="text-sm text-sesi-blue hover:underline font-bold">Ver todos</Link>
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