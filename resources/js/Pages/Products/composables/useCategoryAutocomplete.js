import { ref } from 'vue';

/**
 * Composable for category autocomplete logic
 * @param {Array} categories - list of categories
 * @returns {Object} - query, selectedCategory, searchCategory, categoryDisplay
 */
export function useCategoryAutocomplete(categories = []) {
    const query = ref('');
    const selectedCategory = ref(null);

    const searchCategory = async (q) => {
        query.value = q;
        if (!q) return categories;
        return categories.filter(c => c.name.toLowerCase().includes(q.toLowerCase()));
    };

    const categoryDisplay = (c) => c?.name || '';

    return {
        query,
        selectedCategory,
        searchCategory,
        categoryDisplay,
    };
}
