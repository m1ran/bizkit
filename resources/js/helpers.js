const firstToUpper = (str) => {
    return str.charAt(0).toUpperCase() + str.slice(1)
}

const getItemById = (id, items, key = null) => {
    const item = items.find(i => i.id === id);

    if (!item) {
        return null;
    }

    if (key) {
        return item[key] ?? null;
    }

    return item;
}

export { firstToUpper, getItemById };
