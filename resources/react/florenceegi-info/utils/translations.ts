/**
 * Utility per accedere alle traduzioni dalla pagina informativa FlorenceEGI
 * Le traduzioni sono caricate dal backend Laravel e rese disponibili in window.florenceEgiTranslations
 */

declare global {
    interface Window {
        florenceEgiTranslations?: Record<string, string>;
    }
}

/**
 * Recupera una traduzione dalla chiave specificata
 * @param key - Chiave della traduzione (es. 'hero.title')
 * @param fallback - Valore di fallback se la chiave non esiste
 * @returns La traduzione o il fallback
 */
export const getTranslation = (key: string, fallback: string): string => {
    if (typeof window !== 'undefined' && window.florenceEgiTranslations) {
        return window.florenceEgiTranslations[key] || fallback;
    }
    return fallback;
};

/**
 * Verifica se le traduzioni sono state caricate
 */
export const hasTranslations = (): boolean => {
    return typeof window !== 'undefined' && !!window.florenceEgiTranslations;
};

export default getTranslation;
