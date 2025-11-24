export interface Translations {
  meta: {
    title: string;
    description: string;
    keywords: string;
    author: string;
    og_title: string;
    og_description: string;
  };
  nav: {
    home: string;
    concept: string;
    problems: string;
    examples: string;
    how: string;
    ammk: string;
    tech: string;
    pricing: string;
    faq: string;
    login: string;
    cta: string;
  };
  hero: {
    headline_html: string;
    subheadline: string;
    cta_primary: string;
    cta_secondary: string;
    scroll_text: string;
  };
  intro: {
    title: string;
    subtitle: string;
    description: string;
    [key: string]: string;
  };
  problems: {
    title: string;
    subtitle: string;
    [key: string]: string;
  };
}

declare global {
  interface Window {
    florenceEgiTranslations?: Translations;
  }
}

export {};
