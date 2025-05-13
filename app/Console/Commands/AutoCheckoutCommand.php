<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AutoCheckoutCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:auto-checkout';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Proses auto checkout untuk karyawan yang belum checkout pada hari ini';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai proses auto checkout...');

        try {
            // Ambil semua kehadiran yang belum checkout hari ini
            $today = now()->toDateString();
            $attendances = DB::table('attendances')
                ->whereDate('tanggal', $today)
                ->whereNotNull('check_in')
                ->whereNull('check_out')
                ->get();

            $this->info("Menemukan " . count($attendances) . " kehadiran yang belum checkout");

            foreach ($attendances as $attendance) {
                try {
                    // Hanya checkout jika waktu check-in lebih dari 8 jam yang lalu
                    // atau jika sekarang sudah lewat dari jam 5 sore
                    $checkInTime = Carbon::parse($attendance->check_in);
                    $current = Carbon::now();
                    $workMinutes = $current->diffInMinutes($checkInTime);

                    $isAfter5PM = $current->format('H') >= 17; // Setelah jam 5 sore
                    $isWorkingMoreThan8Hours = $workMinutes >= 480; // Sudah bekerja >= 8 jam

                    if ($isAfter5PM || $isWorkingMoreThan8Hours) {
                        // Hitung persentase kehadiran (8 jam = 480 menit adalah 100%)
                        $targetMinutes = 480; // 8 jam * 60 menit
                        $attendancePercentage = min(100, round(($workMinutes / $targetMinutes) * 100, 2));

                        // Lakukan update
                        DB::table('attendances')
                            ->where('id', $attendance->id)
                            ->update([
                                'check_out' => now()->format('H:i:s'),
                                'early_leave_reason' => 'Auto checkout by system',
                                'work_hours' => $workMinutes,
                                'attendance_percentage' => $attendancePercentage,
                                'auto_checkout' => true
                            ]);

                        $this->info("Auto checkout berhasil untuk ID: " . $attendance->id . " - " . $attendance->nama);
                    } else {
                        $this->info("Melewati checkout untuk ID: " . $attendance->id . " - Belum waktunya (belum jam 5 dan belum 8 jam)");
                    }
                } catch (\Exception $e) {
                    $this->error("Error saat auto checkout ID: " . $attendance->id . " - " . $e->getMessage());
                }
            }

            $this->info("Proses auto checkout selesai");
            return 0; // Success
        } catch (\Exception $e) {
            $this->error("Error utama: " . $e->getMessage());
            return 1; // Error
        }
    }
}
