<script setup>
import { ref, computed, defineProps } from 'vue';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    audits: Array,
    user: Object,
});

const translateUser = (type) => {
    switch (type) {
        case 'T':
            return 'Administrador da TI'
        case 'S':
            return 'Administrador do Sistema';
        case 'A':
            return 'Atendente';
        default:
            return 'Desconhecido';
    }
};

const translateEvent = (event) => {
    switch (event) {
        case 'created':
            return 'criado'
        case 'updated':
            return 'atualizado';
        case 'deleted':
            return 'excluído';
        default:
            return 'Desconhecido';
    }
}
// Filtros e paginação
const search = ref('');
const page = ref(1);
const itemsPerPage = 10;

const filteredaudits = computed(() => {
    const searchTerm = search.value.toLowerCase().trim();
    return props.audits.filter(audit => {
        const userNameTranslated = audit.user.name.toLowerCase().includes(searchTerm);
        const eventTranslated = translateEvent(audit.event).toLowerCase().includes(searchTerm);
        const auditableTable = audit.auditable_type ? audit.auditable_type.toString().toLowerCase().includes(searchTerm) : false;
        const auditableDate = formatDate(audit.created_at).toLowerCase().includes(searchTerm);

        return userNameTranslated || auditableDate || eventTranslated || auditableTable;
    });
});


const displayedaudits = computed(() => {
    const start = (page.value - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    return filteredaudits.value.slice(start, end);
});

const pageCount = computed(() => {
    return Math.ceil(filteredaudits.value.length / itemsPerPage);
});

const updatePage = (newPage) => {
    page.value = newPage;
};

const formatDate = (dateString) => {
    const options = {
        day: '2-digit', month: '2-digit', year: 'numeric',
        hour: '2-digit', minute: '2-digit', hour12: false
    };
    return new Date(dateString).toLocaleDateString(undefined, options);
};
</script>

<template>
    <AppLayout>
        <div class="py-12 max-w-7xl mx-auto sm:px-6 lg:px-8">
            <v-card>
                <div class="bg-purple-100 flex justify-between items-center p-4">
                    <v-tab value="one">Auditorias</v-tab>
                </div>
                <v-card flat>
                    <template v-slot:text>
                        <v-text-field v-model="search" label="Search" prepend-inner-icon="mdi-magnify"
                            variant="outlined" hide-details single-line></v-text-field>
                    </template>
                </v-card>
                <v-table>
                    <thead>
                        <tr>
                            <th class="text-left">Id</th>
                            <th class="text-left">Nome do úsuario</th>
                            <th class="text-left">Evento</th>
                            <th class="text-left">Data e horário</th>
                            <th class="text-left">Tabela</th>
                            <th class="text-left">Id Auditado</th>
                            <th class="text-left">Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="audit in displayedaudits" :key="audit.id">
                            <td>{{ audit.id }}</td>
                            <td>{{ audit.user.name }}</td>
                            <td>{{ translateEvent(audit.event) }}</td>
                            <td>{{ formatDate(audit.created_at) }}</td>
                            <td>{{ audit.auditable_type }}</td>
                            <td>{{ audit.auditable_id }}</td>
                            <td>
                                <Link :href="route('audit.show', audit.id)">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor"
                                    class="w-6 h-6 hover:scale-125 ease-in-out hover:stroke-green-500">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                </svg>
                                </Link>
                            </td>
                        </tr>
                    </tbody>
                </v-table>
                <div class="text-center pt-2">
                    <v-pagination v-model="page" :length="pageCount" @input="updatePage"></v-pagination>
                </div>
            </v-card>
        </div>
    </AppLayout>
</template>