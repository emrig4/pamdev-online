<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddWalletCreditTrigger extends Migration
{
    public function up()
    {
        // Check if we're using MySQL
        $driver = DB::getDriverName();
        
        if ($driver === 'mysql') {
            // Create MySQL function and trigger
            DB::statement("
                DELIMITER //
                CREATE FUNCTION ranc_equivalent(fiat_amount DECIMAL(10,2), fiat_currency VARCHAR(3))
                RETURNS DECIMAL(10,2)
                READS SQL DATA
                DETERMINISTIC
                BEGIN
                    -- Convert fiat to RANC (adjust conversion rate as needed)
                    IF fiat_currency = 'NGN' THEN
                        RETURN fiat_amount * 10; -- 10 RANC per 1 NGN
                    ELSEIF fiat_currency = 'USD' THEN
                        RETURN fiat_amount * 1500; -- 1500 RANC per 1 USD
                    ELSE
                        RETURN fiat_amount;
                    END IF;
                END//
                DELIMITER ;
            ");
            
            DB::statement("
                DELIMITER //
                CREATE TRIGGER credit_wallet_on_payment
                AFTER INSERT ON payments
                FOR EACH ROW
                BEGIN
                    IF NEW.txntype = 'subscription' AND NEW.status = 'success' THEN
                        -- Insert credit wallet transaction if not exists
                        INSERT IGNORE INTO credit_wallet_transactions (
                            reference, amount, ranc, currency, type, status, 
                            remark, credit_wallet_id, created_at, updated_at
                        )
                        SELECT 
                            NEW.reference,
                            NEW.amount / 100,
                            ranc_equivalent(NEW.amount / 100, NEW.currency),
                            'RNC',
                            'subscription',
                            'processed',
                            'Subscription Payment - Auto Credit',
                            cw.id,
                            NOW(),
                            NOW()
                        FROM users u
                        INNER JOIN credit_wallet cw ON cw.user_id = u.id
                        WHERE u.id = NEW.user_id;
                        
                        -- Update wallet balance
                        UPDATE credit_wallet cw
                        INNER JOIN users u ON cw.user_id = u.id
                        SET cw.ranc = cw.ranc + ranc_equivalent(NEW.amount / 100, NEW.currency),
                            cw.updated_at = NOW()
                        WHERE u.id = NEW.user_id;
                    END IF;
                END//
                DELIMITER ;
            ");
        }
    }

    public function down()
    {
        $driver = DB::getDriverName();
        
        if ($driver === 'mysql') {
            DB::statement("DROP TRIGGER IF EXISTS credit_wallet_on_payment");
            DB::statement("DROP FUNCTION IF EXISTS ranc_equivalent");
        }
    }
}