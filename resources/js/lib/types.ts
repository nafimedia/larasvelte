export interface User {
    id: number;
    name: string;
    email: string;
    is_active: boolean;
    avatar_url: string;
    roles: string[];
    permissions: string[];
    created_at?: string;
}

export interface SiteSettings {
    name: string;
    description: string;
    maintenance_mode: boolean;
    enable_registration: boolean;
}

export interface BrandingSettings {
    public_logo_light?: string | null;
    public_logo_dark?: string | null;
    public_logo_mobile?: string | null;
    public_logo_footer?: string | null;
    public_favicon?: string | null;
    public_apple_touch_icon?: string | null;
    admin_logo_light?: string | null;
    admin_logo_dark?: string | null;
    admin_logo_collapsed?: string | null;
    admin_favicon?: string | null;
    admin_login_logo?: string | null;
}

export interface BrandingAsset {
    key: string;
    value: string | null;
    url: string | null;
    label: string;
    description?: string;
}

export interface ModuleItem {
    id: number;
    key: string;
    name: string;
    group: 'content' | 'builder' | 'seo' | 'media' | 'system';
    description?: string;
    icon?: string;
    is_active: boolean;
    is_system: boolean;
    order: number;
}

export interface FlashMessages {
    success?: string;
    error?: string;
    warning?: string;
    info?: string;
}

export interface PageProps {
    auth: {
        user: User | null;
    };
    site: SiteSettings;
    branding: BrandingSettings;
    modules: Record<string, boolean>;
    flash: FlashMessages;
    errors: Record<string, string>;
}

export interface RoleItem {
    id: number;
    name: string;
    users_count: number;
    permissions: string[];
    created_at: string;
}

export interface PermissionItem {
    id: number;
    name: string;
}

export interface ActivityItem {
    id: number;
    log_name: string;
    description: string;
    subject_type?: string;
    subject_id?: number;
    causer_name: string;
    causer_email?: string;
    causer_avatar?: string;
    properties?: Record<string, any>;
    created_at: string;
    created_at_human: string;
}

export interface SettingItem {
    id: number;
    key: string;
    value: string;
    group: string;
    type: 'string' | 'boolean' | 'text' | 'json';
    label: string;
    description?: string;
}

export interface PaginatedData<T> {
    data: T[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number;
    to: number;
    links: { url: string | null; label: string; active: boolean }[];
}
