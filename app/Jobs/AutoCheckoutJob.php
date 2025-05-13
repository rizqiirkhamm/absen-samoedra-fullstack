<?php
namespace App\Jobs;

use App\Models\Attendance;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AutoCheckoutJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $attendanceId;

    public function __construct($attendanceId)
    {
        $this->attendanceId = $attendanceId;
    }

    public function handle()
    {
        try {
            Log::info("=== Starting AutoCheckoutJob ===");
            Log::info("Attempting to find attendance with ID: {$this->attendanceId}");
            
            $attendance = Attendance::find($this->attendanceId);
            
            if (!$attendance) {
                Log::error("Attendance record not found for ID: {$this->attendanceId}");
                return;
            }

            Log::info("Found attendance record:", [
                'id' => $attendance->id,
                'nama' => $attendance->nama,
                'tanggal' => $attendance->tanggal,
                'check_in' => $attendance->check_in,
                'check_out' => $attendance->check_out
            ]);

            if (is_null($attendance->check_out)) {
                try {
                    $checkInTime = \Carbon\Carbon::parse($attendance->check_in);
                    $checkOutTime = now();
                    
                    Log::info("Time calculations:", [
                        'check_in_time' => $checkInTime->format('Y-m-d H:i:s'),
                        'check_out_time' => $checkOutTime->format('Y-m-d H:i:s'),
                        'current_time' => now()->format('Y-m-d H:i:s')
                    ]);
                    
                    $workMinutes = $checkOutTime->diffInMinutes($checkInTime);
                    $attendancePercentage = min(100, ($workMinutes / 480) * 100);
                    
                    Log::info("Calculated values:", [
                        'work_minutes' => $workMinutes,
                        'attendance_percentage' => $attendancePercentage
                    ]);

                    $updateData = [
                        'check_out' => $checkOutTime->format('H:i:s'),
                        'work_hours' => $workMinutes,
                        'attendance_percentage' => $attendancePercentage,
                        'early_leave_reason' => 'Auto checkout by system',
                        'auto_checkout' => true
                    ];

                    Log::info("Attempting to update attendance with data:", $updateData);

                    $updated = $attendance->update($updateData);

                    if ($updated) {
                        Log::info("Successfully updated attendance record");
                        // Verify the update
                        $refreshedAttendance = Attendance::find($this->attendanceId);
                        Log::info("Verified updated record:", [
                            'check_out' => $refreshedAttendance->check_out,
                            'work_hours' => $refreshedAttendance->work_hours,
                            'auto_checkout' => $refreshedAttendance->auto_checkout
                        ]);
                    } else {
                        Log::error("Failed to update attendance record");
                    }

                } catch (\Exception $e) {
                    Log::error("Error during time calculations: " . $e->getMessage());
                    Log::error("Stack trace: " . $e->getTraceAsString());
                    throw $e;
                }
            } else {
                Log::info("Attendance already has check_out time: " . $attendance->check_out);
            }
            
            Log::info("=== Completed AutoCheckoutJob ===");
            
        } catch (\Exception $e) {
            Log::error("Critical error in AutoCheckoutJob: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());
            throw $e;
        }
    }
}