
export const generateRandomString = () => {
    return Math.random().toString(36).slice(-8);
}

export const getElementByKey = (elements, key, keyName) => {
    for(const elem of elements) {
        if(elem[keyName].toString() === key.toString()) return elem;
    }
}
