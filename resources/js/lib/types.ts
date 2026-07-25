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
