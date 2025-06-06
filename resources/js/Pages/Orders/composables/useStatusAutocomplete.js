import { ref } from 'vue';

/**
 * Composable for status autocomplete logic
 * @param {Array} statuses - list of statuses
 * @returns {Object} - query, selectedStatus, searchStatus, statusDisplay
 */
const useStatusAutocomplete = (statuses = []) => {
    const query = ref('');
    const selectedStatus = ref(null);

    const searchStatus = async (q) => {
        query.value = q;
        if (!q) return statuses;
        return statuses.filter(c => c.name.toLowerCase().includes(q.toLowerCase()));
    };

    const statusDisplay = (c) => c?.label || '';

    return {
        query,
        selectedStatus,
        searchStatus,
        statusDisplay,
    };
}

export { useStatusAutocomplete };