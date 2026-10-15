export type Locale = 'en' | 'ar';
export interface RequestDetails {
    accommodation_id: string | number;
    arrival: string;
    departure: string;
    guests: number | string;
}
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
    category?: string;
    galleryVisible?: boolean;
}
export interface Accommodation {
    id: number;
    slug: string;
    title: string;
    description: string;
    inclusions: string;
    capacity: number | null;
    capacityBasis?: 'guests' | 'adults';
    images: ResortImage[];
    published: boolean;
    shortDescription?: string;
    badge?: string;
    facts?: string;
    categories?: string[];
    sourcePage?: string | null;
}
export interface ResortPromotion {
    title: string;
    description: string;
    image: ResortImage;
    downloadUrl: string;
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

export interface RedesignContent {
    revision: string;
    pages: Record<
        string,
        { title: string; description: string; copy: Record<string, string> }
    >;
    images: Record<string, ResortImage>;
    gallery: ResortImage[];
}
export interface WebsiteProps {
    locale: Locale;
    copy: Record<string, string>;
    page: WebsitePage | 'experiences' | 'dining' | 'sport' | 'schools';
    profile: ResortProfile;
    hero: ResortImage | null;
    promotion: ResortPromotion | null;
    branding: { light: ResortImage | null; dark: ResortImage | null };
    media: ResortImage[];
    accommodations: Accommodation[];
    faqs: { id: number; question: string; answer: string }[];
    stay: Accommodation | null;
    receipt: { reference: string; kind: string } | null;
    isPreview: boolean;
    acceptsRequests: boolean;
    indexable: boolean;
    alternateUrl: string;
    canonicalUrl: string;
    submissionToken: string | null;
    submissionTokens: { booking: string; enquiry: string };
    selectedAccommodation: string | null;
    today: string;
    redesign: RedesignContent;
}
