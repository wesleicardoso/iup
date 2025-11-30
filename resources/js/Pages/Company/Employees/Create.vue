<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

// Recebe a lista apenas para autocompletar (sugestão)
const props = defineProps({
    roles: Array 
});

const form = useForm({
    name: '',
    cpf: '', // Onde a máscara será aplicada
    role_name: '',
    birth_date: '',
    admission_date: '',
});

/**
 * Aplica a máscara 000.000.000-00 ao campo CPF.
 * @param {Event} event 
 */
const mascaraCPF = (event) => {
    let value = event.target.value.replace(/\D/g, ""); // Remove tudo que não é dígito
    if (value.length > 11) value = value.slice(0, 11); // Limita a 11 números
    
    // Aplica a máscara (000.000.000-00)
    value = value.replace(/(\d{3})(\d)/, "$1.$2");
    value = value.replace(/(\d{3})(\d)/, "$1.$2");
    value = value.replace(/(\d{3})(\d{1,2})$/, "$1-$2");
    
    form.cpf = value; // Atualiza o valor reativo do formulário
};

const submit = () => {
    // Note: O CPF já vai formatado aqui, mas o backend deve remover a pontuação antes de salvar no DB
    form.post(route('employees.store'));
};
</script>

<template>
    <Head title="Novo Colaborador" />

    <AuthenticatedLayout>
        <template #header>Gestão de Vidas</template>

        <div class="max-w-5xl mx-auto">
            <div class="bg-white rounded-xl shadow-xl border border-gray-100 p-8 md:p-12 relative overflow-hidden">
                
                <div class="absolute top-0 right-0 w-64 h-64 bg-blue-50/50 rounded-full blur-3xl -translate-y-1/2 translate-x-1/3 pointer-events-none"></div>

                <div class="relative z-10">
                    <h2 class="text-2xl font-bold text-sesi-blue mb-2">Cadastrar Colaborador</h2>
                    <p class="text-gray-500 mb-8">Preencha os dados abaixo.</p>

                    <form @submit.prevent="submit" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nome Completo</label>
                            <input 
                                v-model="form.name" 
                                type="text" 
                                class="w-full rounded-xl border-gray-300 bg-white shadow-sm p-3 transition-all focus:border-sesi-blue focus:ring-sesi-blue" 
                                required
                            >
                        </div>

                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 mb-2">CPF</label>
                            <input 
                                :value="form.cpf" 
                                @input="mascaraCPF" 
                                type="text" 
                                class="w-full rounded-xl border-gray-300 bg-white shadow-sm p-3 transition-all focus:border-sesi-blue focus:ring-sesi-blue" 
                                required
                                placeholder="000.000.000-00"
                                maxlength="14"
                            >
                            <div v-if="form.errors.cpf" class="text-red-500 text-sm mt-1">{{ form.errors.cpf }}</div>
                        </div>

                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Função / Cargo</label>
                            <input 
                                v-model="form.role_name"
                                list="roles-list" 
                                type="text"
                                class="w-full rounded-xl border-gray-300 bg-white shadow-sm p-3 transition-all focus:border-sesi-blue focus:ring-sesi-blue" 
                                placeholder="Digite para buscar (Ex: Soldador, Dev...)"
                                required
                            >

                            <datalist id="roles-list">
                                <option 
                                    v-for="role in roles" 
                                    :key="role.id" 
                                    :value="role.name"
                                >
                                    CBO: {{ role.cbo || 'Sem CBO' }}
                                </option>
                            </datalist>
                            
                            <p class="text-xs text-gray-500 mt-1">
                                A função será criada automaticamente se não for encontrada.
                            </p>
                            <div v-if="form.errors.role_name" class="text-red-500 text-sm mt-1">{{ form.errors.role_name }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Data de Nascimento</label>
                            <input v-model="form.birth_date" type="date" class="w-full rounded-xl border-gray-300 bg-white shadow-sm p-3 focus:border-sesi-blue focus:ring-sesi-blue" required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Data de Admissão</label>
                            <input v-model="form.admission_date" type="date" class="w-full rounded-xl border-gray-300 bg-white shadow-sm p-3 focus:border-sesi-blue focus:ring-sesi-blue" required>
                        </div>

                        <div class="col-span-2 flex justify-end gap-6 mt-6">
                            <Link :href="route('employees.index')" class="text-gray-500 hover:text-gray-700 py-3">Cancelar</Link>
                            <button type="submit" :disabled="form.processing" 
                                class="px-8 py-3 bg-sesi-green text-white font-bold rounded-xl shadow-lg hover:bg-green-600 transition-all">
                                Salvar Cadastro
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>