export type Locale = 'en' | 'ar';
export interface ResortImage {
    id: number;
    src: string;
    large: string;
    srcset: string;
    width: number;
    height: number;
    alt: string;
    caption: string;
    position: string;
}
export interface Accommodation {
    id: number;
    slug: string;
    title: string;
    description: string;
    inclusions: string;
    capacity: number | null;
    images: ResortImage[];
    published: boolean;
}
export interface ResortProfile {
    introduction: string;
    address: string;
    arrival: string;
    policies: string;
    privacy: string;
    response_hours: string;
    seo_description: string;
    phone: string | null;
    email: string | null;
    whatsapp: string | null;
    map_url: string | null;
    instagram_url: string | null;
    facebook_url: string | null;
}
export type WebsitePage =
    | 'home'
    | 'accommodation'
    | 'stay'
    | 'gallery'
    | 'plan'
    | 'contact'
    | 'booking'
    | 'privacy'
    | 'received';
