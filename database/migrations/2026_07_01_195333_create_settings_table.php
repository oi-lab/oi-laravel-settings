<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // A host application may already own a settings table (created by its
        // own migration). Skip creation in that case so the package migration
        // stays a safe no-op instead of clashing.
        if (Schema::hasTable($this->table())) {
            return;
        }

        Schema::create($this->table(), function (Blueprint $table) {
            $table->id();
            $table->string('scope')->nullable()->index();
            $table->string('key');
            $table->string('label');
            $table->string('type')->default('string');
            $table->text('value')->nullable();
            $table->timestamps();

            $table->unique(['scope', 'key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists($this->table());
    }

    protected function table(): string
    {
        return config('oi-laravel-settings.table', 'settings');
    }
};
