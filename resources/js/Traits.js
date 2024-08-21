export const generateRandomString = () => {
    return Math.random().toString(36).slice(-8);
}
