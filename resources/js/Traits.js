export const generateRandomString = () => {
    return Math.random().toString(36).slice(-8);
}

export const getElementByKey = (elements, key, keyName) => {
    for (const elem of elements) {
        if (elem[keyName].toString() === key.toString()) return elem;
    }
}

export const prettifyNumber = (number) => {
    return (Math.round(number * 100) / 100).toFixed(0)
}

export const priceValue = (number) => {
    if (!number) return number
    if (isNaN(Number(number))) return number
    return number.toFixed(0) + " ₽"
}
