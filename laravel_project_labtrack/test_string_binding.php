<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "Running update_booking_status procedure with string bindings via DB::statement...\n";
    
    // First, let's reset status of 5009 back to PENDING so we can update it
    DB::table('booking_requests')->where('booking_id', 5009)->update([
        'status' => 'PENDING',
        'approved_by' => null,
        'approval_date' => null,
        'remarks' => null
    ]);
    
    // Call the procedure using strings for numbers
    DB::statement('BEGIN update_booking_status(:booking_id, :teacher_id, :status, :remarks); END;', [
        'booking_id' => '5009',
        'teacher_id' => '1007001',
        'status'     => 'APPROVED',
        'remarks'    => 'Approved',
    ]);
    
    echo "String binding procedure executed successfully!\n";
    
    $row = DB::table('booking_requests')->where('booking_id', 5009)->first();
    print_r($row);
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
