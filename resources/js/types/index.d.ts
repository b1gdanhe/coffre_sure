import type { PageProps } from '@inertiajs/core';
import type { LucideIcon } from 'lucide-vue-next';
import type { Config } from 'ziggy-js';

export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavItem {
    title: string;
    href: string;
    icon?: LucideIcon;
    isActive?: boolean;
}

export interface SharedData extends PageProps {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    ziggy: Config & { location: string };
    sidebarOpen: boolean;
}

  export interface User {
    id: string;
    name: string;
    email: string;
    email_verified_at: string | null;
    master_key_hash: string;
    salt: string;
    encryption_key: string;
    last_login: string | null;
    mfa_enabled: boolean;
    mfa_secret: string | null;
    status: UserStatus;
    created_at: string;
    updated_at: string;
  }

export type BreadcrumbItemType = BreadcrumbItem;
