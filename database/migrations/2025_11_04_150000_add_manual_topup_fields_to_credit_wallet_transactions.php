<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddManualTopUpFieldsToCreditWalletTransactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('credit_wallet_transactions', function (Blueprint $table) {
            // Add fields for manual top-up functionality
            $table->unsignedBigInteger('admin_id')->nullable()->after('credit_wallet_id');
            $table->string('admin_note')->nullable()->after('admin_id');
            $table->timestamp('approved_at')->nullable()->after('admin_note');
            $table->unsignedBigInteger('approved_by')->nullable()->after('approved_at');
            $table->timestamp('rejected_at')->nullable()->after('approved_by');
            $table->unsignedBigInteger('rejected_by')->nullable()->after('rejected_at');
            $table->text('rejection_reason')->nullable()->after('rejected_by');
            
            // Add foreign key constraints
            $table->foreign('admin_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('rejected_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('credit_wallet_transactions', function (Blueprint $table) {
            $table->dropForeign(['admin_id']);
            $table->dropForeign(['approved_by']);
            $table->dropForeign(['rejected_by']);
            $table->dropColumn(['admin_id', 'admin_note', 'approved_at', 'approved_by', 'rejected_at', 'rejected_by', 'rejection_reason']);
        });
    }
}
