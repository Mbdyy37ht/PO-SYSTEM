<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;
use App\Models\Warehouse;
use App\Models\Item;
use App\Models\Supplier;
use App\Models\Customer;
use App\Models\ItemStock;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Roles sudah dibuat di migration
        $adminRole = Role::where('name', 'admin')->first();
        $managerRole = Role::where('name', 'manager')->first();
        $staffRole = Role::where('name', 'staff')->first();

        // Create Users
        $admin = User::create([
            'role_id' => $adminRole->id,
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
            'is_active' => true,
        ]);

        $manager = User::create([
            'role_id' => $managerRole->id,
            'name' => 'Manager User',
            'email' => 'manager@example.com',
            'password' => Hash::make('password123'),
            'is_active' => true,
        ]);

        $staff = User::create([
            'role_id' => $staffRole->id,
            'name' => 'Staff User',
            'email' => 'staff@example.com',
            'password' => Hash::make('password123'),
            'is_active' => true,
        ]);

        // Warehouses (sudah ada 1 dari migration, tambah beberapa lagi)
        $warehouse1 = Warehouse::first();
        $warehouse2 = Warehouse::create([
            'code' => 'WH002',
            'name' => 'Warehouse Jakarta',
            'address' => 'Jl. Jakarta No. 456',
            'phone' => '021-87654321',
            'is_active' => true,
        ]);

        // Items
        $items = [
            [
                'code' => 'ITM001',
                'name' => 'Laptop Dell XPS 13',
                'description' => 'Laptop Dell XPS 13 inch',
                'unit' => 'pcs',
                'purchase_price' => 15000000,
                'selling_price' => 18000000,
                'minimum_stock' => 5,
            ],
            [
                'code' => 'ITM002',
                'name' => 'Mouse Logitech M185',
                'description' => 'Wireless Mouse Logitech',
                'unit' => 'pcs',
                'purchase_price' => 150000,
                'selling_price' => 200000,
                'minimum_stock' => 20,
            ],
            [
                'code' => 'ITM003',
                'name' => 'Keyboard Mechanical RGB',
                'description' => 'Mechanical Keyboard with RGB',
                'unit' => 'pcs',
                'purchase_price' => 500000,
                'selling_price' => 700000,
                'minimum_stock' => 10,
            ],
            [
                'code' => 'ITM004',
                'name' => 'Monitor LG 24 inch',
                'description' => 'Monitor LG 24 inch Full HD',
                'unit' => 'pcs',
                'purchase_price' => 2000000,
                'selling_price' => 2500000,
                'minimum_stock' => 8,
            ],
            [
                'code' => 'ITM005',
                'name' => 'Printer HP LaserJet',
                'description' => 'Printer HP LaserJet Pro',
                'unit' => 'pcs',
                'purchase_price' => 3000000,
                'selling_price' => 3500000,
                'minimum_stock' => 3,
            ],
        ];

        foreach ($items as $itemData) {
            $item = Item::create($itemData);

            // Create initial stock for each warehouse
            ItemStock::create([
                'item_id' => $item->id,
                'warehouse_id' => $warehouse1->id,
                'quantity' => 0,
            ]);

            ItemStock::create([
                'item_id' => $item->id,
                'warehouse_id' => $warehouse2->id,
                'quantity' => 0,
            ]);
        }

        // Suppliers
        $suppliers = [
            [
                'code' => 'SUP001',
                'name' => 'PT Komputer Sejahtera',
                'address' => 'Jl. Mangga Dua, Jakarta',
                'phone' => '021-11111111',
                'email' => 'info@komputersejahtera.com',
                'contact_person' => 'Budi Santoso',
            ],
            [
                'code' => 'SUP002',
                'name' => 'CV Elektronik Jaya',
                'address' => 'Jl. Glodok, Jakarta',
                'phone' => '021-22222222',
                'email' => 'sales@elektronikjaya.com',
                'contact_person' => 'Siti Aminah',
            ],
            [
                'code' => 'SUP003',
                'name' => 'PT Hardware Indonesia',
                'address' => 'Jl. Sudirman, Jakarta',
                'phone' => '021-33333333',
                'email' => 'contact@hardwareid.com',
                'contact_person' => 'Ahmad Rizki',
            ],
        ];

        foreach ($suppliers as $supplierData) {
            Supplier::create($supplierData);
        }

        // Customers
        $customers = [
            [
                'code' => 'CUST001',
                'name' => 'PT Teknologi Maju',
                'address' => 'Jl. Thamrin, Jakarta',
                'phone' => '021-44444444',
                'email' => 'purchasing@teknologimaju.com',
                'contact_person' => 'Dewi Lestari',
            ],
            [
                'code' => 'CUST002',
                'name' => 'CV Kreatif Digital',
                'address' => 'Jl. Kuningan, Jakarta',
                'phone' => '021-55555555',
                'email' => 'order@kreatifdigital.com',
                'contact_person' => 'Rudi Hermawan',
            ],
            [
                'code' => 'CUST003',
                'name' => 'PT Solusi Bisnis',
                'address' => 'Jl. Gatot Subroto, Jakarta',
                'phone' => '021-66666666',
                'email' => 'procurement@solusibisnis.com',
                'contact_person' => 'Linda Wijaya',
            ],
        ];

        foreach ($customers as $customerData) {
            Customer::create($customerData);
        }

        $this->command->info('Database seeded successfully!');
        $this->command->info('');
        $this->command->info('Login Credentials:');
        $this->command->info('Admin   - Email: admin@example.com, Password: password123');
        $this->command->info('Manager - Email: manager@example.com, Password: password123');
        $this->command->info('Staff   - Email: staff@example.com, Password: password123');
    }
}
