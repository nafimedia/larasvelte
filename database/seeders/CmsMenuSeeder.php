<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\MenuItem;
use Illuminate\Database\Seeder;

class CmsMenuSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Navbar Main Menu
        $navbar = Menu::updateOrCreate(
            ['location' => 'navbar'],
            ['name' => 'Navbar Navigation Main', 'is_active' => true]
        );

        MenuItem::where('menu_id', $navbar->id)->delete();

        $navItems = [
            ['title' => 'Home', 'url' => '/', 'type' => 'custom', 'order' => 1],
            ['title' => 'Fitur Unggulan', 'url' => '#features', 'type' => 'custom', 'order' => 2],
            ['title' => 'Tech Stack', 'url' => '#tech-stack', 'type' => 'custom', 'order' => 3],
            ['title' => 'Quick Start', 'url' => '#quickstart', 'type' => 'custom', 'order' => 4],
            ['title' => 'Statistik', 'url' => '#stats', 'type' => 'custom', 'order' => 5],
            ['title' => 'Blog & Artikel', 'url' => '/blog', 'type' => 'custom', 'order' => 6],
        ];

        foreach ($navItems as $item) {
            MenuItem::create(array_merge($item, ['menu_id' => $navbar->id, 'target' => '_self']));
        }

        // 2. Create Footer Menu
        $footer = Menu::updateOrCreate(
            ['location' => 'footer'],
            ['name' => 'Footer Links', 'is_active' => true]
        );

        MenuItem::where('menu_id', $footer->id)->delete();

        $footerItems = [
            ['title' => 'Tentang Platform', 'url' => '#about', 'type' => 'custom', 'order' => 1],
            ['title' => 'Katalog Artikel Blog', 'url' => '/blog', 'type' => 'custom', 'order' => 2],
            ['title' => 'Kebijakan Privasi', 'url' => '/privacy', 'type' => 'custom', 'order' => 3],
            ['title' => 'Syarat & Ketentuan', 'url' => '/terms', 'type' => 'custom', 'order' => 4],
        ];

        foreach ($footerItems as $item) {
            MenuItem::create(array_merge($item, ['menu_id' => $footer->id, 'target' => '_self']));
        }
    }
}
