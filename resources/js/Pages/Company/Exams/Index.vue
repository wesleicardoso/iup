<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

defineProps({ exams: Array });

const form = useForm({
    name: '',
    tuss_code: '',
    validity_months: 12
});

const submit = () => {
    form.post(route('exams.store'), {
        onSuccess: () => form.reset()
    });
};
</script>

<template>
    <Head title="Catálogo de Exames" />

    <AuthenticatedLayout>
        <template #header>Catálogo de Exames</template>

        <div class="flex flex-col lg:flex-row gap-8">
            
            <div class="flex-1 bg-white rounded-[2.5rem] shadow-xl border border-gray-100 p-8">
                <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center gap-2">
                    <svg class="w-6 h-6 text-cyan-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                    Exames Disponíveis
                </h3>

                <div class="overflow-y-auto max-h-[600px] custom-scrollbar pr-2">
                    <div v-for="exam in exams" :key="exam.id" 
                        class="flex items-center justify-between p-4 mb-3 rounded-2xl bg-gray-50 border border-gray-100 hover:border-cyan-200 hover:bg-cyan-50/30 transition-all group">
                        
                        <div>
                            <div class="font-bold text-gray-800">{{ exam.name }}</div>
                            <div class="text-xs text-gray-400 font-mono">TUSS: {{ exam.tuss_code || 'N/A' }}</div>
                        </div>

                        <div class="flex items-center gap-4">
                            <span class="text-xs font-semibold text-gray-500 bg-white px-3 py-1 rounded-full border border-gray-200">
                                Validade: {{ exam.validity_months }} meses
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="w-full lg:w-96">
                <div class="bg-gradient-to-br from-[#151e32] to-[#0b1121] rounded-[2.5rem] p-8 text-white shadow-2xl relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-40 h-40 bg-cyan-500/20 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2"></div>

                    <h3 class="text-lg font-bold mb-6 relative z-10">Novo Exame</h3>

                    <form @submit.prevent="submit" class="space-y-4 relative z-10">
                        <div>
                            <label class="text-sm text-gray-400">Nome do Exame</label>
                            <input v-model="form.name" type="text" class="w-full mt-1 bg-white/10 border border-white/10 rounded-xl px-4 py-2 text-white placeholder-gray-500 focus:ring-cyan-400 focus:border-cyan-400" placeholder="Ex: Raio-X">
                        </div>

                        <div>
                            <label class="text-sm text-gray-400">Código TUSS (Opcional)</label>
                            <input v-model="form.tuss_code" type="text" class="w-full mt-1 bg-white/10 border border-white/10 rounded-xl px-4 py-2 text-white placeholder-gray-500 focus:ring-cyan-400 focus:border-cyan-400">
                        </div>

                        <div>
                            <label class="text-sm text-gray-400">Periodicidade (Meses)</label>
                            <select v-model="form.validity_months" class="w-full mt-1 bg-white/10 border border-white/10 rounded-xl px-4 py-2 text-white focus:ring-cyan-400 focus:border-cyan-400 [&>option]:text-gray-900">
                                <option :value="6">Semestral (6 meses)</option>
                                <option :value="12">Anual (12 meses)</option>
                                <option :value="24">Bienal (24 meses)</option>
                            </select>
                        </div>

                        <button type="submit" :disabled="form.processing" class="w-full py-3 mt-4 bg-cyan-500 hover:bg-cyan-400 text-[#0b1121] font-bold rounded-xl transition-all shadow-lg shadow-cyan-500/30">
                            Adicionar ao Catálogo
                        </button>
                    </form>
                </div>

                <div class="mt-6 bg-cyan-50/50 border border-cyan-100 p-6 rounded-3xl">
                    <p class="text-sm text-cyan-800">
                        <strong>Dica:</strong> Vincule estes exames aos cargos na tela de "Configurações do PCMSO" para automação.
                    </p>
                </div>
            </div>

        </div>
    </AuthenticatedLayout>
</template>