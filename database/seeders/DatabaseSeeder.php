<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\Tag;
use App\Models\Role;
use App\Models\User;
use App\Models\Entry;
use App\Models\Vault;
use App\Models\Device;
use App\Models\AccessLog;
use App\Models\CustomField;
use App\Models\SharedVault;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $roles = $this->createRoles();

        // Créer des tags
        $tags = $this->createTags();

        // Créer quelques utilisateurs
        $users = $this->createUsers();

        // Pour chaque utilisateur, créer des coffres-forts
        foreach ($users as $user) {
            $vaults = $this->createVaultsForUser($user);

            // Pour chaque coffre-fort, créer des entrées
            foreach ($vaults as $vault) {
                $entries = $this->createEntriesForVault($vault);

                // Pour chaque entrée, créer des champs personnalisés
                foreach ($entries as $entry) {
                    $this->createCustomFieldsForEntry($entry);

                    // Associer des tags aléatoires à l'entrée
                    $this->attachTagsToEntry($entry, $tags);
                }
            }

            // Créer des appareils pour l'utilisateur
            $this->createDevicesForUser($user);

            // Créer des journaux d'accès pour l'utilisateur
            $this->createAccessLogsForUser($user);
        }

        // Créer des partages de coffres-forts entre utilisateurs
        $this->createSharedVaults($users);
    }

    private function createRoles(): array
    {
        $roles = [
            [
                'name' => 'admin',
                'description' => 'Administrateur système avec tous les droits',
            ],
            [
                'name' => 'user',
                'description' => 'Utilisateur standard avec droits limités',
            ],
            [
                'name' => 'guest',
                'description' => 'Utilisateur invité avec droits très limités',
                'permissions' => [
                    'view vaults',
                    'view entries',
                ]
            ]
        ];

        $createdRoles = [];

        foreach ($roles as $role) {
            $newRole = Role::create([
                'id' => Str::uuid()->toString(),
                'name' => $role['name'],
                'description' => $role['description'],
                'guard_name' => 'web'
            ]);
            $createdRoles[$role['name']] = $newRole->id;
        }
        return $createdRoles;
    }

    /**
     * Créer les tags de base
     */
    private function createTags(): array
    {
        $tagData = [
            ['name' => 'Personnel', 'color' => '#3498db'],
            ['name' => 'Travail', 'color' => '#e74c3c'],
            ['name' => 'Finance', 'color' => '#2ecc71'],
            ['name' => 'Social', 'color' => '#9b59b6'],
            ['name' => 'Important', 'color' => '#f1c40f'],
            ['name' => 'Sécurisé', 'color' => '#1abc9c'],
        ];

        $createdTags = [];

        foreach ($tagData as $tag) {
            $id = Str::uuid()->toString();
            DB::table('tags')->insert([
                'id' => $id,
                'name' => $tag['name'],
                'color' => $tag['color'],
                'created_at' => now()
            ]);
            $createdTags[] = $id;
        }

        return $createdTags;
    }

    /**
     * Créer des utilisateurs de test
     */
    private function createUsers(): array
    {
        $userData = [
            [
                'name' => 'Admin User',
                'email' => 'admin@coffresure.com',
                'password' => Hash::make('password123'),
                'status' => 'active',
                'mfa_enabled' => true,
                'role' => 'admin'

            ],
            [
                'name' => 'Test User',
                'email' => 'user@coffresure.com',
                'password' => Hash::make('password123'),
                'status' => 'active',
                'mfa_enabled' => false,
                'role' => 'user'
            ],
            [
                'name' => 'New User',
                'email' => 'new@coffresure.com',
                'password' => Hash::make('password123'),
                'status' => 'pending',
                'mfa_enabled' => false,
                'role' => 'guest'

            ]
        ];

        $createdUsers = [];

        foreach ($userData as $user) {
            $id = Str::uuid()->toString();
            $salt = bin2hex(random_bytes(16));

            // Simuler une clé d'encryption (dans un cas réel elle serait dérivée du mot de passe maître)
            $encryptionKey = bin2hex(random_bytes(32));

            DB::table('users')->insert([
                'id' => $id,
                'name' => $user['name'],
                'email' => $user['email'],
                'password' => $user['password'],
                'master_key_hash' => Hash::make('master_password' . $salt),
                'salt' => $salt,
                'encryption_key' => $encryptionKey,
                'last_login' => now(),
                'mfa_enabled' => $user['mfa_enabled'],
                'mfa_secret' => $user['mfa_enabled'] ? bin2hex(random_bytes(16)) : null,
                'status' => $user['status'],
                'email_verified_at' => $user['status'] === 'active' ? now() : null,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            $userModel = User::find($id);
            $userModel->assignRole($user['role']);

            $createdUsers[] = $id;
        }

        return $createdUsers;
    }

    /**
     * Créer des coffres-forts pour un utilisateur
     */
    private function createVaultsForUser(string $userId): array
    {
        $vaultNames = [
            'Coffre principal',
            'Sites web',
            'Documents personnels',
            'Informations financières'
        ];

        $createdVaults = [];

        // Créer un coffre-fort par défaut
        $defaultVaultId = Str::uuid()->toString();
        $encryptionKey = bin2hex(random_bytes(32)); // Simuler une clé d'encryption

        DB::table('vaults')->insert([
            'id' => $defaultVaultId,
            'user_id' => $userId,
            'name' => 'Mon cofrre',
            'description' => 'Coffre-fort par défaut pour toutes vos informations',
            'icon' => 'vault-default',
            'encryption_key' => $encryptionKey,
            'is_default' => true,
            'created_at' => now()->subDays(30),
            'updated_at' => now()
        ]);

        $createdVaults[] = $defaultVaultId;

        // Créer d'autres coffres-forts
        foreach ($vaultNames as $index => $name) {
            $vaultId = Str::uuid()->toString();
            $encryptionKey = bin2hex(random_bytes(32)); // Simuler une clé d'encryption

            DB::table('vaults')->insert([
                'id' => $vaultId,
                'user_id' => $userId,
                'name' => $name,
                'description' => 'Description pour ' . $name,
                'icon' => 'vault-' . ($index + 1),
                'encryption_key' => $encryptionKey,
                'is_default' => false,
                'created_at' => now()->subDays(rand(1, 29)),
                'updated_at' => now()->subDays(rand(0, 5))
            ]);

            $createdVaults[] = $vaultId;
        }

        return $createdVaults;
    }

    /**
     * Créer des entrées pour un coffre-fort
     */
    private function createEntriesForVault(string $vaultId): array
    {
        $entryTypes = [
            'login' => [
                ['title' => 'Gmail', 'url' => 'https://gmail.com'],
                ['title' => 'Facebook', 'url' => 'https://facebook.com'],
                ['title' => 'Twitter', 'url' => 'https://twitter.com'],
                ['title' => 'Amazon', 'url' => 'https://amazon.com'],
                ['title' => 'Netflix', 'url' => 'https://netflix.com']
            ],
            'card' => [
                ['title' => 'Carte Visa', 'url' => null],
                ['title' => 'Carte Mastercard', 'url' => null]
            ],
            'identity' => [
                ['title' => 'Identité personnelle', 'url' => null],
                ['title' => 'Passeport', 'url' => null]
            ],
            'secure_note' => [
                ['title' => 'Note secrète', 'url' => null],
                ['title' => 'Informations privées', 'url' => null]
            ],
            'wifi' => [
                ['title' => 'WiFi Maison', 'url' => null],
                ['title' => 'WiFi Bureau', 'url' => null]
            ]
        ];

        $createdEntries = [];

        foreach ($entryTypes as $category => $entries) {
            foreach ($entries as $entry) {
                $entryId = Str::uuid()->toString();

                DB::table('entries')->insert([
                    'id' => $entryId,
                    'vault_id' => $vaultId,
                    'title' => $entry['title'],
                    'username' => $category === 'login' ? 'user_' . Str::random(5) : null,
                    'password' => in_array($category, ['login', 'wifi']) ? bin2hex(random_bytes(16)) : null,
                    'url' => $entry['url'],
                    'notes' => 'Notes pour ' . $entry['title'],
                    'icon' => strtolower(str_replace(' ', '_', $entry['title'])),
                    'last_used' => rand(0, 1) ? now()->subDays(rand(0, 30)) : null,
                    'category' => $category,
                    'favorite' => rand(0, 10) > 8, // 20% chance d'être favori
                    'created_at' => now()->subDays(rand(10, 60)),
                    'updated_at' => now()->subDays(rand(0, 9))
                ]);

                $createdEntries[] = $entryId;
            }
        }

        return $createdEntries;
    }

    /**
     * Créer des champs personnalisés pour une entrée
     */
    private function createCustomFieldsForEntry(string $entryId): void
    {
        $fieldTypes = ['text', 'password', 'email', 'url', 'date', 'boolean', 'phone'];

        // 50% de chance d'avoir des champs personnalisés
        if (rand(0, 1) === 0) {
            return;
        }

        // Créer entre 1 et 3 champs personnalisés
        $numFields = rand(1, 3);

        for ($i = 0; $i < $numFields; $i++) {
            $fieldType = $fieldTypes[array_rand($fieldTypes)];
            $fieldName = 'Champ personnalisé ' . ($i + 1);

            // Générer une valeur appropriée en fonction du type
            switch ($fieldType) {
                case 'email':
                    $fieldValue = 'email_' . Str::random(5) . '@example.com';
                    break;
                case 'url':
                    $fieldValue = 'https://example.com/' . Str::random(8);
                    break;
                case 'date':
                    $fieldValue = now()->subDays(rand(1, 365))->format('Y-m-d');
                    break;
                case 'boolean':
                    $fieldValue = rand(0, 1) ? 'true' : 'false';
                    break;
                case 'phone':
                    $fieldValue = '+33' . rand(600000000, 799999999);
                    break;
                default:
                    $fieldValue = 'Valeur de ' . $fieldName;
            }

            DB::table('custom_fields')->insert([
                'id' => Str::uuid()->toString(),
                'entry_id' => $entryId,
                'field_name' => $fieldName,
                'field_value' => $fieldValue,
                'field_type' => $fieldType,
                'is_encrypted' => $fieldType === 'password' || rand(0, 1) === 1, // Les mots de passe sont toujours chiffrés, 50% pour les autres
                'created_at' => now()->subDays(rand(1, 30)),
                'updated_at' => now()->subDays(rand(0, 5))
            ]);
        }
    }

    /**
     * Attacher des tags à une entrée
     */
    private function attachTagsToEntry(string $entryId, array $tags): void
    {
        // 70% de chance d'avoir des tags
        if (rand(0, 10) <= 3) {
            return;
        }

        // Attacher entre 1 et 3 tags
        $numTags = rand(1, 3);
        $selectedTags = array_rand(array_flip($tags), min($numTags, count($tags)));

        if (!is_array($selectedTags)) {
            $selectedTags = [$selectedTags];
        }

        foreach ($selectedTags as $tagId) {
            DB::table('entry_tag')->insert([
                'entry_id' => $entryId,
                'tag_id' => $tagId
            ]);
        }
    }

    /**
     * Créer des appareils pour un utilisateur
     */
    private function createDevicesForUser(string $userId): void
    {
        $deviceTypes = [
            ['type' => 'desktop', 'agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'],
            ['type' => 'mobile', 'agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.0 Mobile/15E148 Safari/604.1'],
            ['type' => 'tablet', 'agent' => 'Mozilla/5.0 (iPad; CPU OS 16_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.0 Mobile/15E148 Safari/604.1'],
            ['type' => 'browser_extension', 'agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36']
        ];

        $ipAddresses = [
            '192.168.1.1',
            '10.0.0.1',
            '172.16.0.1',
            '82.45.128.33',
            '91.198.174.192'
        ];

        // Créer entre 1 et 3 appareils
        $numDevices = rand(1, 3);

        for ($i = 0; $i < $numDevices; $i++) {
            $device = $deviceTypes[array_rand($deviceTypes)];
            $deviceName = match ($device['type']) {
                'desktop' => 'PC Windows ' . Str::random(3),
                'mobile' => 'iPhone ' . Str::random(3),
                'tablet' => 'iPad ' . Str::random(3),
                'browser_extension' => 'Chrome Extension ' . Str::random(3),
                default => 'Appareil ' . Str::random(3),
            };

            DB::table('devices')->insert([
                'id' => Str::uuid()->toString(),
                'user_id' => $userId,
                'name' => $deviceName,
                'device_type' => $device['type'],
                'last_active' => now()->subHours(rand(0, 72)),
                'created_at' => now()->subDays(rand(1, 60)),
                'auth_token' => bin2hex(random_bytes(32)),
                'ip_address' => $ipAddresses[array_rand($ipAddresses)],
                'user_agent' => $device['agent'],
                'is_trusted' => rand(0, 1) === 1
            ]);
        }
    }

    /**
     * Créer des journaux d'accès pour un utilisateur
     */
    private function createAccessLogsForUser(string $userId): void
    {
        $actions = [
            'login',
            'logout',
            'failed_login',
            'password_change',
            'mfa_enabled',
            'entry_view',
            'entry_create',
            'entry_update',
            'vault_create',
            'export_data',
            'import_data'
        ];

        $statuses = ['success', 'failed', 'blocked', 'suspicious'];
        $statusWeights = [85, 10, 3, 2]; // Probabilités en pourcentage

        $ipAddresses = [
            '192.168.1.1',
            '10.0.0.1',
            '172.16.0.1',
            '82.45.128.33',
            '91.198.174.192'
        ];

        $userAgents = [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            'Mozilla/5.0 (iPhone; CPU iPhone OS 16_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.0 Mobile/15E148 Safari/604.1',
            'Mozilla/5.0 (iPad; CPU OS 16_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.0 Mobile/15E148 Safari/604.1'
        ];

        // Créer entre 10 et 30 entrées de journal
        $numLogs = rand(10, 30);

        for ($i = 0; $i < $numLogs; $i++) {
            $action = $actions[array_rand($actions)];

            // Attribution pondérée des statuts
            $randStatus = rand(1, 100);
            $cumulativeWeight = 0;
            $status = 'success';

            foreach ($statusWeights as $index => $weight) {
                $cumulativeWeight += $weight;
                if ($randStatus <= $cumulativeWeight) {
                    $status = $statuses[$index];
                    break;
                }
            }

            // Détails en fonction de l'action
            $details = match ($action) {
                'login' => json_encode(['method' => rand(0, 1) ? 'password' : 'mfa']),
                'failed_login' => json_encode(['reason' => rand(0, 1) ? 'wrong_password' : 'wrong_mfa']),
                'entry_view', 'entry_create', 'entry_update', 'entry_delete' => json_encode(['entry_title' => 'Entrée ' . rand(1, 100)]),
                'vault_create', 'vault_update', 'vault_delete' => json_encode(['vault_name' => 'Coffre ' . rand(1, 10)]),
                'export_data', 'import_data' => json_encode(['format' => rand(0, 1) ? 'csv' : 'json']),
                default => null,
            };

            // Date dans les 60 derniers jours
            $date = Carbon::now()->subDays(rand(0, 60))->subHours(rand(0, 23))->subMinutes(rand(0, 59));

            DB::table('access_logs')->insert([
                'id' => Str::uuid()->toString(),
                'user_id' => $userId,
                'action' => $action,
                'details' => $details,
                'ip_address' => $ipAddresses[array_rand($ipAddresses)],
                'device_info' => $userAgents[array_rand($userAgents)],
                'status' => $status,
                'created_at' => $date
            ]);
        }
    }

    /**
     * Créer des partages de coffres-forts entre utilisateurs
     */
    private function createSharedVaults(array $users): void
    {
        if (count($users) < 2) {
            return;
        }

        // Récupérer tous les coffres
        $vaults = DB::table('vaults')->whereIn('user_id', $users)->get();

        // Pour chaque coffre, 30% de chance d'être partagé
        foreach ($vaults as $vault) {
            if (rand(0, 10) <= 7) {
                continue;
            }

            // Trouver un utilisateur différent du propriétaire
            $otherUsers = array_diff($users, [$vault->user_id]);
            if (empty($otherUsers)) {
                continue;
            }

            $sharedWithUserId = $otherUsers[array_rand($otherUsers)];

            $permissions = ['view', 'edit', 'manage', 'admin'];
            $statuses = ['pending', 'accepted', 'rejected', 'revoked'];
            $statusWeights = [20, 70, 5, 5]; // Probabilités en pourcentage

            // Attribution pondérée des statuts
            $randStatus = rand(1, 100);
            $cumulativeWeight = 0;
            $status = 'accepted';

            foreach ($statusWeights as $index => $weight) {
                $cumulativeWeight += $weight;
                if ($randStatus <= $cumulativeWeight) {
                    $status = $statuses[$index];
                    break;
                }
            }

            DB::table('shared_vaults')->insert([
                'id' => Str::uuid()->toString(),
                'vault_id' => $vault->id,
                'user_id' => $sharedWithUserId,
                'permission_level' => $permissions[array_rand($permissions)],
                'invitation_status' => $status,
                'encrypted_key' => bin2hex(random_bytes(32)), // Simuler une clé chiffrée
                'created_at' => now()->subDays(rand(1, 30)),
                'updated_at' => now()->subDays(rand(0, 5))
            ]);
        }
    }
}
