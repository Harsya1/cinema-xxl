<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Studio;
use App\Models\InventoryItem;
use App\Models\MenuItem;
use App\Models\Recipe;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ==================== USERS ====================
        // Admin
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@cinema-xxl.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone_number' => '081234567890',
        ]);

        // Manager
        User::create([
            'name' => 'Manager Cinema',
            'email' => 'manager@cinema-xxl.com',
            'password' => Hash::make('password'),
            'role' => 'manager',
            'phone_number' => '081234567891',
        ]);

        // Cashier
        User::create([
            'name' => 'Kasir 1',
            'email' => 'cashier@cinema-xxl.com',
            'password' => Hash::make('password'),
            'role' => 'cashier',
            'phone_number' => '081234567892',
        ]);

        // FnB Staff
        User::create([
            'name' => 'FnB Staff 1',
            'email' => 'fnb@cinema-xxl.com',
            'password' => Hash::make('password'),
            'role' => 'fnb_staff',
            'phone_number' => '081234567893',
        ]);

        // Cleaner
        User::create([
            'name' => 'Cleaner 1',
            'email' => 'cleaner@cinema-xxl.com',
            'password' => Hash::make('password'),
            'role' => 'cleaner',
            'phone_number' => '081234567894',
        ]);

        // Regular User
        User::create([
            'name' => 'Test User',
            'email' => 'user@cinema-xxl.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'phone_number' => '081234567895',
        ]);

        // ==================== STUDIOS ====================
        Studio::create(['name' => 'Studio 1', 'type' => 'Regular', 'total_seats' => 100]);
        Studio::create(['name' => 'Studio 2', 'type' => 'Regular', 'total_seats' => 100]);
        Studio::create(['name' => 'Studio 3', 'type' => 'Premier', 'total_seats' => 50]);
        Studio::create(['name' => 'Studio 4', 'type' => '3D', 'total_seats' => 80]);

        // ==================== INVENTORY ITEMS ====================
        $corn = InventoryItem::create(['name' => 'Raw Corn', 'type' => 'ingredient', 'stock_quantity' => 50, 'unit' => 'kg', 'min_stock_level' => 10]);
        $sugar = InventoryItem::create(['name' => 'Sugar', 'type' => 'ingredient', 'stock_quantity' => 30, 'unit' => 'kg', 'min_stock_level' => 5]);
        $caramel = InventoryItem::create(['name' => 'Caramel Sauce', 'type' => 'ingredient', 'stock_quantity' => 20, 'unit' => 'kg', 'min_stock_level' => 3]);
        $cheese = InventoryItem::create(['name' => 'Cheese Powder', 'type' => 'ingredient', 'stock_quantity' => 15, 'unit' => 'kg', 'min_stock_level' => 2]);
        $cola = InventoryItem::create(['name' => 'Cola Syrup', 'type' => 'ingredient', 'stock_quantity' => 40, 'unit' => 'liter', 'min_stock_level' => 10]);
        $water = InventoryItem::create(['name' => 'Soda Water', 'type' => 'ingredient', 'stock_quantity' => 100, 'unit' => 'liter', 'min_stock_level' => 20]);
        $ice = InventoryItem::create(['name' => 'Ice Cubes', 'type' => 'ingredient', 'stock_quantity' => 50, 'unit' => 'kg', 'min_stock_level' => 10]);
        $nachos = InventoryItem::create(['name' => 'Nachos Chips', 'type' => 'ingredient', 'stock_quantity' => 25, 'unit' => 'kg', 'min_stock_level' => 5]);
        $hotdog = InventoryItem::create(['name' => 'Hotdog Sausage', 'type' => 'ingredient', 'stock_quantity' => 100, 'unit' => 'pcs', 'min_stock_level' => 20]);
        $bread = InventoryItem::create(['name' => 'Hotdog Bread', 'type' => 'ingredient', 'stock_quantity' => 100, 'unit' => 'pcs', 'min_stock_level' => 20]);

        // Packaging
        $cupSmall = InventoryItem::create(['name' => 'Small Cup', 'type' => 'packaging', 'stock_quantity' => 500, 'unit' => 'pcs', 'min_stock_level' => 100]);
        $cupMedium = InventoryItem::create(['name' => 'Medium Cup', 'type' => 'packaging', 'stock_quantity' => 500, 'unit' => 'pcs', 'min_stock_level' => 100]);
        $cupLarge = InventoryItem::create(['name' => 'Large Cup', 'type' => 'packaging', 'stock_quantity' => 500, 'unit' => 'pcs', 'min_stock_level' => 100]);
        $popcornBucket = InventoryItem::create(['name' => 'Popcorn Bucket', 'type' => 'packaging', 'stock_quantity' => 300, 'unit' => 'pcs', 'min_stock_level' => 50]);
        $nachosBox = InventoryItem::create(['name' => 'Nachos Box', 'type' => 'packaging', 'stock_quantity' => 200, 'unit' => 'pcs', 'min_stock_level' => 50]);

        // ==================== MENU ITEMS ====================
        // Popcorn
        $popcornCaramel = MenuItem::create(['name' => 'Popcorn Caramel', 'description' => 'Sweet caramel flavored popcorn', 'price' => 35000, 'category' => 'Food', 'is_available' => true]);
        $popcornCheese = MenuItem::create(['name' => 'Popcorn Cheese', 'description' => 'Savory cheese flavored popcorn', 'price' => 35000, 'category' => 'Food', 'is_available' => true]);
        $popcornOriginal = MenuItem::create(['name' => 'Popcorn Original', 'description' => 'Classic salted popcorn', 'price' => 30000, 'category' => 'Food', 'is_available' => true]);

        // Snacks
        $nachosMenu = MenuItem::create(['name' => 'Nachos with Cheese', 'description' => 'Crispy nachos with melted cheese', 'price' => 40000, 'category' => 'Food', 'is_available' => true]);
        $hotdogMenu = MenuItem::create(['name' => 'Hotdog', 'description' => 'Juicy hotdog in a bun', 'price' => 25000, 'category' => 'Food', 'is_available' => true]);

        // Beverages
        $colaSmall = MenuItem::create(['name' => 'Cola Small', 'description' => 'Refreshing cola drink (Small)', 'price' => 15000, 'category' => 'Beverage', 'is_available' => true]);
        $colaMedium = MenuItem::create(['name' => 'Cola Medium', 'description' => 'Refreshing cola drink (Medium)', 'price' => 20000, 'category' => 'Beverage', 'is_available' => true]);
        $colaLarge = MenuItem::create(['name' => 'Cola Large', 'description' => 'Refreshing cola drink (Large)', 'price' => 25000, 'category' => 'Beverage', 'is_available' => true]);

        // Combos
        $comboA = MenuItem::create(['name' => 'Combo A', 'description' => 'Popcorn Caramel + Cola Medium', 'price' => 50000, 'category' => 'Combo', 'is_available' => true]);
        $comboB = MenuItem::create(['name' => 'Combo B', 'description' => 'Popcorn Cheese + Cola Large', 'price' => 55000, 'category' => 'Combo', 'is_available' => true]);

        // ==================== RECIPES ====================
        // Popcorn Caramel
        Recipe::create(['menu_item_id' => $popcornCaramel->id, 'inventory_item_id' => $corn->id, 'quantity_needed' => 0.1]);
        Recipe::create(['menu_item_id' => $popcornCaramel->id, 'inventory_item_id' => $caramel->id, 'quantity_needed' => 0.05]);
        Recipe::create(['menu_item_id' => $popcornCaramel->id, 'inventory_item_id' => $popcornBucket->id, 'quantity_needed' => 1]);

        // Popcorn Cheese
        Recipe::create(['menu_item_id' => $popcornCheese->id, 'inventory_item_id' => $corn->id, 'quantity_needed' => 0.1]);
        Recipe::create(['menu_item_id' => $popcornCheese->id, 'inventory_item_id' => $cheese->id, 'quantity_needed' => 0.03]);
        Recipe::create(['menu_item_id' => $popcornCheese->id, 'inventory_item_id' => $popcornBucket->id, 'quantity_needed' => 1]);

        // Popcorn Original
        Recipe::create(['menu_item_id' => $popcornOriginal->id, 'inventory_item_id' => $corn->id, 'quantity_needed' => 0.1]);
        Recipe::create(['menu_item_id' => $popcornOriginal->id, 'inventory_item_id' => $popcornBucket->id, 'quantity_needed' => 1]);

        // Nachos
        Recipe::create(['menu_item_id' => $nachosMenu->id, 'inventory_item_id' => $nachos->id, 'quantity_needed' => 0.15]);
        Recipe::create(['menu_item_id' => $nachosMenu->id, 'inventory_item_id' => $cheese->id, 'quantity_needed' => 0.05]);
        Recipe::create(['menu_item_id' => $nachosMenu->id, 'inventory_item_id' => $nachosBox->id, 'quantity_needed' => 1]);

        // Hotdog
        Recipe::create(['menu_item_id' => $hotdogMenu->id, 'inventory_item_id' => $hotdog->id, 'quantity_needed' => 1]);
        Recipe::create(['menu_item_id' => $hotdogMenu->id, 'inventory_item_id' => $bread->id, 'quantity_needed' => 1]);

        // Cola Small
        Recipe::create(['menu_item_id' => $colaSmall->id, 'inventory_item_id' => $cola->id, 'quantity_needed' => 0.05]);
        Recipe::create(['menu_item_id' => $colaSmall->id, 'inventory_item_id' => $water->id, 'quantity_needed' => 0.2]);
        Recipe::create(['menu_item_id' => $colaSmall->id, 'inventory_item_id' => $ice->id, 'quantity_needed' => 0.05]);
        Recipe::create(['menu_item_id' => $colaSmall->id, 'inventory_item_id' => $cupSmall->id, 'quantity_needed' => 1]);

        // Cola Medium
        Recipe::create(['menu_item_id' => $colaMedium->id, 'inventory_item_id' => $cola->id, 'quantity_needed' => 0.08]);
        Recipe::create(['menu_item_id' => $colaMedium->id, 'inventory_item_id' => $water->id, 'quantity_needed' => 0.35]);
        Recipe::create(['menu_item_id' => $colaMedium->id, 'inventory_item_id' => $ice->id, 'quantity_needed' => 0.08]);
        Recipe::create(['menu_item_id' => $colaMedium->id, 'inventory_item_id' => $cupMedium->id, 'quantity_needed' => 1]);

        // Cola Large
        Recipe::create(['menu_item_id' => $colaLarge->id, 'inventory_item_id' => $cola->id, 'quantity_needed' => 0.1]);
        Recipe::create(['menu_item_id' => $colaLarge->id, 'inventory_item_id' => $water->id, 'quantity_needed' => 0.5]);
        Recipe::create(['menu_item_id' => $colaLarge->id, 'inventory_item_id' => $ice->id, 'quantity_needed' => 0.1]);
        Recipe::create(['menu_item_id' => $colaLarge->id, 'inventory_item_id' => $cupLarge->id, 'quantity_needed' => 1]);

        // Combo A (Popcorn Caramel + Cola Medium)
        Recipe::create(['menu_item_id' => $comboA->id, 'inventory_item_id' => $corn->id, 'quantity_needed' => 0.1]);
        Recipe::create(['menu_item_id' => $comboA->id, 'inventory_item_id' => $caramel->id, 'quantity_needed' => 0.05]);
        Recipe::create(['menu_item_id' => $comboA->id, 'inventory_item_id' => $popcornBucket->id, 'quantity_needed' => 1]);
        Recipe::create(['menu_item_id' => $comboA->id, 'inventory_item_id' => $cola->id, 'quantity_needed' => 0.08]);
        Recipe::create(['menu_item_id' => $comboA->id, 'inventory_item_id' => $water->id, 'quantity_needed' => 0.35]);
        Recipe::create(['menu_item_id' => $comboA->id, 'inventory_item_id' => $cupMedium->id, 'quantity_needed' => 1]);

        // Combo B (Popcorn Cheese + Cola Large)
        Recipe::create(['menu_item_id' => $comboB->id, 'inventory_item_id' => $corn->id, 'quantity_needed' => 0.1]);
        Recipe::create(['menu_item_id' => $comboB->id, 'inventory_item_id' => $cheese->id, 'quantity_needed' => 0.03]);
        Recipe::create(['menu_item_id' => $comboB->id, 'inventory_item_id' => $popcornBucket->id, 'quantity_needed' => 1]);
        Recipe::create(['menu_item_id' => $comboB->id, 'inventory_item_id' => $cola->id, 'quantity_needed' => 0.1]);
        Recipe::create(['menu_item_id' => $comboB->id, 'inventory_item_id' => $water->id, 'quantity_needed' => 0.5]);
        Recipe::create(['menu_item_id' => $comboB->id, 'inventory_item_id' => $cupLarge->id, 'quantity_needed' => 1]);

        $this->command->info('Database seeded successfully!');
        $this->command->info('Default credentials:');
        $this->command->table(
            ['Role', 'Email', 'Password'],
            [
                ['Admin', 'admin@cinema-xxl.com', 'password'],
                ['Manager', 'manager@cinema-xxl.com', 'password'],
                ['Cashier', 'cashier@cinema-xxl.com', 'password'],
                ['FnB Staff', 'fnb@cinema-xxl.com', 'password'],
                ['Cleaner', 'cleaner@cinema-xxl.com', 'password'],
                ['User', 'user@cinema-xxl.com', 'password'],
            ]
        );
    }
}
