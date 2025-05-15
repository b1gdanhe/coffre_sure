// types.ts - TypeScript interfaces for CoffreSure Password Manager

// Enums
export enum ActionType {
    LOGIN = 'login',
    LOGOUT = 'logout',
    FAILED_LOGIN = 'failed_login',
    PASSWORD_CHANGE = 'password_change',
    MFA_ENABLED = 'mfa_enabled',
    MFA_DISABLED = 'mfa_disabled',
    ENTRY_VIEW = 'entry_view',
    ENTRY_CREATE = 'entry_create',
    ENTRY_UPDATE = 'entry_update',
    ENTRY_DELETE = 'entry_delete',
    VAULT_CREATE = 'vault_create',
    VAULT_UPDATE = 'vault_update',
    VAULT_DELETE = 'vault_delete',
    VAULT_SHARE = 'vault_share',
    EXPORT_DATA = 'export_data',
    IMPORT_DATA = 'import_data',
    DEVICE_ADDED = 'device_added',
    DEVICE_REMOVED = 'device_removed',
    OTHER = 'other'
  }
  
  export enum LogStatus {
    SUCCESS = 'success',
    FAILED = 'failed',
    BLOCKED = 'blocked',
    SUSPICIOUS = 'suspicious'
  }
  
  export enum FieldType {
    TEXT = 'text',
    PASSWORD = 'password',
    EMAIL = 'email',
    NUMBER = 'number',
    DATE = 'date',
    BOOLEAN = 'boolean',
    URL = 'url',
    PHONE = 'phone',
    FILE = 'file'
  }
  
  export enum DeviceType {
    DESKTOP = 'desktop',
    MOBILE = 'mobile',
    TABLET = 'tablet',
    BROWSER_EXTENSION = 'browser_extension',
    OTHER = 'other'
  }
  
  export enum CategoryType {
    LOGIN = 'login',
    CARD = 'card',
    IDENTITY = 'identity',
    SECURE_NOTE = 'secure_note',
    CRYPTO = 'crypto',
    MEDICAL = 'medical',
    WIFI = 'wifi',
    DOCUMENT = 'document',
    OTHER = 'other'
  }
  
  export enum PermissionLevel {
    VIEW = 'view',
    EDIT = 'edit',
    MANAGE = 'manage',
    ADMIN = 'admin'
  }
  
  export enum InvitationStatus {
    PENDING = 'pending',
    ACCEPTED = 'accepted',
    REJECTED = 'rejected',
    REVOKED = 'revoked'
  }
  
  export enum UserStatus {
    ACTIVE = 'active',
    INACTIVE = 'inactive',
    SUSPENDED = 'suspended',
    PENDING = 'pending'
  }
  export interface Role {
    id: string;
    name: string;
    description: string;
    created_at: string;
    updated_at: string;
  }
  
  // User-related interfaces
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
  
  // Vault-related interfaces
  export interface Vault {
    id: string;
    user_id: string;
    name: string;
    description: string | null;
    icon: string | null;
    encryption_key: string;
    is_default: boolean;
    created_at: string;
    updated_at: string;
    entries?: Entry[];
    shared_users?: SharedVault[];
  }
  
  export interface SharedVault {
    id: string;
    vault_id: string;
    user_id: string;
    permission_level: PermissionLevel;
    invitation_status: InvitationStatus;
    encrypted_key: string;
    created_at: string;
    updated_at: string;
    user?: User;
    vault?: Vault;
  }
  
  // Entry-related interfaces
  export interface Entry {
    id: string;
    vault_id: string;
    title: string;
    username: string | null;
    password: string | null;
    url: string | null;
    notes: string | null;
    icon: string | null;
    last_used: string | null;
    category: CategoryType;
    favorite: boolean;
    created_at: string;
    updated_at: string;
    custom_fields?: CustomField[];
    tags?: Tag[];
    vault?: Vault;
  }
  
  export interface CustomField {
    id: string;
    entry_id: string;
    field_name: string;
    field_value: string;
    field_type: FieldType;
    is_encrypted: boolean;
    created_at: string;
    updated_at: string;
  }
  
  export interface Tag {
    id: string;
    name: string;
    color: string;
    created_at: string;
    entries?: Entry[];
  }
  
  // Security and device-related interfaces
  export interface Device {
    id: string;
    user_id: string;
    name: string;
    device_type: DeviceType;
    last_active: string | null;
    created_at: string;
    auth_token: string;
    ip_address: string;
    user_agent: string;
    is_trusted: boolean;
    user?: User;
  }
  
  export interface AccessLog {
    id: string;
    user_id: string;
    action: ActionType;
    details: string | null;
    ip_address: string;
    device_info: string;
    status: LogStatus;
    created_at: string;
    user?: User;
  }
  
  // API response interfaces
  export interface ApiResponse<T> {
    data: T;
    message?: string;
    success: boolean;
  }
  
  export interface PaginatedResponse<T> {
    data: T[];
    meta: {
      current_page: number;
      from: number;
      last_page: number;
      path: string;
      per_page: number;
      to: number;
      total: number;
    };
    links: {
      first: string;
      last: string;
      prev: string | null;
      next: string | null;
    };
  }
  
  // Authentication-related interfaces
  export interface LoginCredentials {
    email: string;
    password: string;
    remember?: boolean;
    device_name?: string;
  }
  
  export interface RegisterData {
    name: string;
    email: string;
    password: string;
    password_confirmation: string;
    master_password: string;
    master_password_confirmation: string;
  }
  
  export interface AuthState {
    user: User | null;
    token: string | null;
    isAuthenticated: boolean;
    masterKeyVerified: boolean;
  }
  
  // Request/Response interfaces for specific operations
  export interface CreateVaultRequest {
    name: string;
    description?: string;
    icon?: string;
    master_password: string;
  }
  
  export interface CreateEntryRequest {
    vault_id: string;
    title: string;
    username?: string;
    password?: string;
    url?: string;
    notes?: string;
    icon?: string;
    category: CategoryType;
    favorite?: boolean;
    custom_fields?: Omit<CustomField, 'id' | 'entry_id' | 'created_at' | 'updated_at'>[];
    tags?: string[];
  }
  
  export interface ShareVaultRequest {
    email: string;
    permission_level: PermissionLevel;
    master_password: string;
  }
  
  export interface ExportVaultRequest {
    vault_id: string;
    master_password: string;
    format: 'json' | 'csv' | 'encrypted';
  }
  
  export interface ImportVaultRequest {
    file: File;
    master_password: string;
    format: 'json' | 'csv' | 'encrypted' | 'lastpass' | 'bitwarden' | '1password';
  }
  
  export interface ChangePasswordRequest {
    current_password: string;
    password: string;
    password_confirmation: string;
    master_password: string;
    new_master_password: string;
    new_master_password_confirmation: string;
  }
  
  export interface EnableMfaRequest {
    master_password: string;
    token: string;
  }
  
  // Store interfaces (for use with Vuex/Pinia)
  export interface RootState {
    auth: AuthState;
    vaults: VaultsState;
    entries: EntriesState;
    ui: UiState;
  }
  
  export interface VaultsState {
    vaults: Vault[];
    currentVault: Vault | null;
    isLoading: boolean;
    error: string | null;
  }
  
  export interface EntriesState {
    entries: Entry[];
    currentEntry: Entry | null;
    filteredEntries: Entry[];
    isLoading: boolean;
    error: string | null;
  }
  
  export interface UiState {
    darkMode: boolean;
    sidebarExpanded: boolean;
    notifications: Notification[];
    currentModal: string | null;
    searchQuery: string;
    filterCategory: CategoryType | null;
    filterTags: string[];
    isMobile: boolean;
  }
  
  export interface Notification {
    id: string;
    type: 'success' | 'error' | 'info' | 'warning';
    message: string;
    duration?: number;
    read: boolean;
    created_at: string;
  }