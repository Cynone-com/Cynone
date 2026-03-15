<?php

use App\Models\Currency;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('currencies', 'name')) {
            Schema::table('currencies', function (Blueprint $table) {
                $table->string('name')->after('code')->nullable();
            });
        }

        // Set name for existing currencies
        Currency::query()
            ->whereNull('name')
            ->each(function ($currency) {
                $currency->name = $currency->code;
                $currency->save();
            });

        if (!Schema::hasColumn('currencies', 'name')) {
            return;
        }

        if (Currency::query()->whereNull('name')->doesntExist()) {
            Schema::table('currencies', function (Blueprint $table) {
                $table->string('name')->nullable(false)->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasColumn('currencies', 'name')) {
            return;
        }

        Schema::table('currencies', function (Blueprint $table) {
            $table->dropColumn('name');
        });
    }
};
