<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import axios from 'axios';

// Props recebidas do DashboardController
defineProps({ 
    user: Object, 
    upcomingAppointments: Array,
    stats: Object,
    companies: Array // ✅ Lista de empresas para o QR Code
});

// --- LÓGICA DO QR CODE (LOGIN MÁGICO) ---
const showQrModal = ref(false);
const qrLoading = ref(false);
const qrData = ref({ svg: '', name: '', url: '' });

const openCompanyQr = async (companyId) => {
    qrLoading.value = true;
    qrData.value = { svg: '', name: 'Carregando...', url: '' };
    showQrModal.value = true;

    try {
        const response = await axios.get(route('magic.qr.company', companyId));
        
        qrData.value = {
            svg: response.data.qr_code,
            name: response.data.company_name,
            url: response.data.url
        };
    } catch (error) {
        alert('Erro: ' + (error.response?.data?.error || 'Esta empresa não possui um usuário gestor vinculado.'));
        showQrModal.value = false;
    } finally {
        qrLoading.value = false;
    }
};

const copyLink = () => {
    navigator.clipboard.writeText(qrData.value.url);
    alert('Link de acesso copiado!');
};

const printQr = () => {
    const printWindow = window.open('', '', 'height=600,width=600');
    printWindow.document.write('<html><head><title>QR Code de Acesso</title>');
    printWindow.document.write('</head><body style="text-align:center; font-family: sans-serif;">');
    printWindow.document.write('<h1>Acesso Rápido: ' + qrData.value.name + '</h1>');
    printWindow.document.write(document.getElementById('printable-qr-content').innerHTML);
    printWindow.document.write('<p>Escaneie para acessar o painel.</p>');
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    printWindow.print();
};

// Formatador de Data
const formatDateTime = (date) => {
    return new Date(date).toLocaleDateString('pt-BR', { 
        day: '2-digit', month: '2-digit', hour: '2-digit', minute: '2-digit' 
    });
};
</script>

<template>
    <Head title="Painel Administrativo" />
    
    <AuthenticatedLayout>
        <template #header>Central de Comando</template>

        <div class="space-y-8 animate-fade-in">
            
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center bg-white p-6 rounded-xl shadow-sm border border-gray-200 gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-iup-blue">Visão Geral IUp</h2>
                    <p class="text-sm text-gray-500">Monitoramento em tempo real do ecossistema.</p>
                </div>
                
                <div class="flex flex-wrap gap-3 w-full md:w-auto">
                    <Link :href="route('sales.index')" class="flex-1 md:flex-none text-center px-5 py-2.5 bg-blue-50 text-iup-blue font-bold rounded-lg border border-blue-100 hover:bg-blue-100 transition-colors text-sm">
                        Gerenciar Vendas
                    </Link>
                    <Link :href="route('support.index')" class="flex-1 md:flex-none justify-center px-5 py-2.5 bg-iup-green text-white font-bold rounded-lg shadow-sm hover:bg-green-600 transition-colors text-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                        Chamados
                    </Link>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-iup-blue flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wide">Empresas</p>
                        <p class="text-3xl font-extrabold text-iup-blue">{{ stats.total_companies }}</p>
                    </div>
                    <div class="p-3 bg-blue-50 rounded-lg text-iup-blue">
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
                <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-iup-green flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wide">Exames</p>
                        <p class="text-3xl font-extrabold text-iup-green">{{ stats.total_exams }}</p>
                    </div>
                    <div class="p-3 bg-green-50 rounded-lg text-iup-green">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-red-500 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wide">Suporte</p>
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
                            <span class="w-2 h-2 rounded-full bg-iup-blue animate-pulse"></span>
                            Agenda Global (Próximos)
                        </h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left min-w-[700px]">
                            <thead class="bg-white text-gray-500 text-xs uppercase font-bold border-b border-gray-100">
                                <tr>
                                    <th class="p-4">Horário</th>
                                    <th class="p-4">Colaborador</th>
                                    <th class="p-4">Empresa</th>
                                    <th class="p-4">Local</th>
                                    <th class="p-4 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 text-sm text-gray-700">
                                <tr v-for="apt in upcomingAppointments" :key="apt.id" class="hover:bg-blue-50/30 transition-colors">
                                    <td class="p-4 font-mono font-bold text-iup-blue">{{ formatDateTime(apt.scheduled_at) }}</td>
                                    <td class="p-4 font-bold">{{ apt.employee_name || apt.employee?.name }}</td>
                                    <td class="p-4 text-xs uppercase font-semibold text-gray-500">{{ apt.company?.name || 'Particular' }}</td>
                                    <td class="p-4 text-xs">{{ apt.provider?.name || 'Matriz Principal' }}</td>
                                    <td class="p-4 text-center">
                                        <span class="px-2 py-1 rounded text-xs font-bold uppercase border"
                                            :class="{
                                                'bg-green-100 text-green-700 border-green-200': apt.status === 'concluido',
                                                'bg-yellow-100 text-yellow-700 border-yellow-200': apt.status === 'pendente' || apt.status === 'aguardando',
                                                'bg-purple-100 text-purple-700 border-purple-200': apt.status === 'solicitado',
                                                'bg-red-100 text-red-700 border-red-200': apt.status === 'cancelado'
                                            }">
                                            {{ apt.status }}
                                        </span>
                                    </td>
                                </tr>
                                <tr v-if="upcomingAppointments.length === 0">
                                    <td colspan="5" class="p-8 text-center text-gray-400 italic">Nenhum agendamento futuro.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="space-y-6">
                    
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="p-5 border-b border-gray-100 bg-iup-blue text-white flex justify-between items-center">
                            <h3 class="font-bold text-sm uppercase tracking-wide">Acesso Rápido</h3>
                            <Link :href="route('sales.index')" class="text-xs text-blue-100 hover:text-white underline">Ver todas</Link>
                        </div>
                        <ul class="divide-y divide-gray-100">
                            <li v-for="comp in companies" :key="comp.id" class="p-4 flex items-center justify-between hover:bg-gray-50 transition-colors">
                                <div class="flex items-center gap-3 overflow-hidden">
                                    <div class="w-8 h-8 rounded bg-gray-100 flex items-center justify-center text-gray-600 font-bold text-xs flex-shrink-0 border border-gray-200">
                                        {{ comp.name.charAt(0) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-bold text-gray-800 truncate">{{ comp.name }}</p>
                                        <p class="text-xs text-gray-400 truncate">{{ comp.cnpj }}</p>
                                    </div>
                                </div>
                                
                                <button @click="openCompanyQr(comp.id)" 
                                    class="text-iup-blue bg-blue-50 hover:bg-iup-blue hover:text-white p-2 rounded-lg transition-colors" 
                                    title="Gerar Crachá de Acesso">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                                    </svg>
                                </button>
                            </li>
                            <li v-if="!companies || companies.length === 0" class="p-6 text-center text-xs text-gray-400">
                                Nenhuma empresa recente.
                            </li>
                        </ul>
                    </div>

                    <div class="bg-gray-50 rounded-xl border border-gray-200 p-6">
                        <h3 class="font-bold text-gray-700 mb-4 text-sm">Ferramentas</h3>
                        <div class="grid grid-cols-2 gap-3">
                            <button class="p-3 bg-white border border-gray-200 shadow-sm hover:shadow-md rounded-lg text-sm font-bold text-gray-600 transition-all text-center">
                                Usuários
                            </button>
                            <button class="p-3 bg-white border border-gray-200 shadow-sm hover:shadow-md rounded-lg text-sm font-bold text-gray-600 transition-all text-center">
                                Configurações
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div v-if="showQrModal" class="fixed inset-0 bg-black/80 flex items-center justify-center z-50 px-4 backdrop-blur-sm" @click.self="showQrModal = false">
            <div class="bg-white p-8 rounded-2xl shadow-2xl max-w-sm w-full text-center relative animate-fade-in">
                
                <button @click="showQrModal = false" class="absolute top-4 right-4 text-gray-400 hover:text-red-500 transition-colors">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>

                <div id="printable-qr-content">
                    <h3 class="text-xl font-bold text-iup-blue mb-1">Acesso Rápido</h3>
                    <p class="text-gray-500 text-sm mb-6">Login automático para: <br><strong class="text-gray-800 text-lg">{{ qrData.name }}</strong></p>

                    <div class="bg-white p-2 inline-block mb-4">
                         <div v-if="qrLoading" class="w-48 h-48 flex items-center justify-center text-gray-400 animate-pulse bg-gray-100 rounded-xl">Gerando...</div>
                         <div v-else v-html="qrData.svg" class="border-4 border-iup-green rounded-xl overflow-hidden shadow-sm"></div>
                    </div>
                    <p class="text-xs text-gray-400 mb-4">Aponte a câmera para entrar sem senha.</p>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <button @click="copyLink" class="px-4 py-2 border border-gray-300 text-gray-600 font-bold rounded-lg hover:bg-gray-50 text-sm transition-colors">
                        Copiar Link
                    </button>
                    <button @click="printQr" class="px-4 py-2 bg-iup-green text-white font-bold rounded-lg hover:bg-green-600 text-sm transition-colors shadow-md">
                        Imprimir
                    </button>
                </div>
            </div>
        </div>

    </AuthenticatedLayout>
</template>

<style scoped>
.animate-fade-in { animation: fadeIn 0.5s ease-out; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>