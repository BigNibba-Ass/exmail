
export const generateRandomString = () => {
    return Math.random().toString(36).slice(-8);
}

export const getElementByKey = (elements, key, keyName) => {
    for (const elem of elements) {
        if (elem[keyName].toString() === key.toString()) return elem;
    }
}

export const prettifyNumber = (number) => {
    return Math.round(number * 100) / 100
}

export const priceValue = (number) => {
    const options1 = { style: 'currency', currency: 'RUB' };
    return new Intl.NumberFormat('ru-RU', options1).format(number);
}
