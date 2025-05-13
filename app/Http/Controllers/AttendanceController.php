<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\Employee;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use App\Jobs\AutoCheckoutJob;

class AttendanceController extends Controller
{
    public function create()
    {
        $employees = Employee::all();
        return view('attendance.create', compact('employees'));
    }

    public function show($id)
    {
        $attendance = Attendance::findOrFail($id);
        
        return response()->json([
            'foto' => $attendance->foto ? asset('storage/' . $attendance->foto) : asset('images/default-user.jpg'),
            'foto_checkout' => $attendance->foto_checkout ? asset('storage/' . $attendance->foto_checkout) : null,
            'nama' => $attendance->nama,
            'lokasi' => $attendance->lokasi,
            'hadir_untuk' => $attendance->hadir_untuk,
            'tanggal' => $attendance->tanggal->format('Y-m-d'),
            'check_in' => $attendance->check_in ? date('H:i:s', strtotime($attendance->check_in)) : null,
            'check_out' => $attendance->check_out ? date('H:i:s', strtotime($attendance->check_out)) : null,
            'work_hours' => $attendance->work_hours,
            'early_leave_reason' => $attendance->early_leave_reason,
            'attendance_percentage' => $attendance->attendance_percentage
        ]);
    }

    public function destroy(Attendance $attendance)
    {
        try {
            // Hapus file foto check-in dari storage
            if ($attendance->foto) {
                Storage::disk('public')->delete($attendance->foto);
                Log::info('Foto check-in berhasil dihapus: ' . $attendance->foto);
            }
            
            // Hapus file foto check-out dari storage (jika ada)
            if ($attendance->foto_checkout) {
                Storage::disk('public')->delete($attendance->foto_checkout);
                Log::info('Foto check-out berhasil dihapus: ' . $attendance->foto_checkout);
            }
            
            // Hapus record dari database
            $attendance->delete();
            
            // Buat notifikasi untuk penghapusan presensi
            Notification::create([
                'user_id' => Auth::id(),
                'title' => 'Presensi Dihapus',
                'message' => 'Data presensi ' . $attendance->nama . ' telah dihapus.',
                'type' => 'warning'
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Data presensi dan foto berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            Log::error('Error saat menghapus presensi: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function checkAttendance(Employee $employee)
    {
        $today = now()->toDateString();
        
        // Check if employee has any attendance record for today
        $attendance = Attendance::where('nama', $employee->nama)
            ->whereDate('tanggal', $today)
            ->first();

        $response = [
            'checkedIn' => false,
            'canCheckIn' => true,
            'message' => null
        ];

        if ($attendance) {
            if ($attendance->check_out) {
                // Jika sudah checkout, tidak bisa check-in lagi
                $response['canCheckIn'] = false;
                $response['message'] = 'Anda sudah melakukan check-out hari ini dan tidak dapat check-in lagi.';
            } else {
                // Jika belum checkout, masih dalam sesi check-in
                $response['checkedIn'] = true;
                $response['message'] = 'Anda sudah check-in. Silakan melakukan check-out.';
            }
        }

        return response()->json($response);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'foto' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'lokasi' => 'required|string',
            'hadir_untuk' => 'required|string|max:255',
            'is_checkout' => 'required|boolean',
            'early_leave_reason' => 'required_if:is_checkout,true'
        ]);
    
        try {
            $today = now()->toDateString();
            
            if ($request->is_checkout) {
                // Update existing attendance record for check-out
                $attendance = Attendance::where('nama', $request->nama)
                    ->whereDate('tanggal', $today)
                    ->whereNull('check_out')
                    ->firstOrFail();
    
                try {
                    // Get current time for check-out
                    $now = now();
                    $checkOutTime = $now->format('H:i:s');
                    
                    // Parse times more safely
                    $checkIn = \Carbon\Carbon::parse($attendance->check_in);
                    $checkOut = \Carbon\Carbon::parse($checkOutTime);
                    
                    // Calculate work duration in minutes
                    $workMinutes = $checkOut->diffInMinutes($checkIn);
                    
                    // Ensure work_hours doesn't exceed reasonable limits (24 hours = 1440 minutes)
                    if ($workMinutes > 1440) {
                        $workMinutes = 1440;
                    }
                    
                    // Calculate attendance percentage (8 hours = 480 minutes is 100%)
                    $targetMinutes = 480; // 8 hours * 60 minutes
                    $attendancePercentage = min(100, round(($workMinutes / $targetMinutes) * 100, 2));
    
                    // Log for debugging
                    Log::info('Attendance calculation:', [
                        'check_in_time' => $attendance->check_in,
                        'check_out_time' => $checkOutTime,
                        'work_minutes' => $workMinutes,
                        'percentage' => $attendancePercentage
                    ]);
    
                    $attendance->update([
                        'check_out' => $checkOutTime,
                        'foto_checkout' => $request->file('foto')->store('attendances', 'public'),
                        'early_leave_reason' => $request->early_leave_reason,
                        'work_hours' => $workMinutes,
                        'attendance_percentage' => $attendancePercentage
                    ]);
    
                    // Create notification for check-out
                    $notificationMessage = $request->nama . ' telah melakukan check out';
                    if ($request->early_leave_reason) {
                        $notificationMessage .= ' dengan alasan: ' . $request->early_leave_reason;
                    }
                    
                    // Format duration text more precisely
                    $hours = floor($workMinutes / 60);
                    $minutes = $workMinutes % 60;
                    $durationText = '';
                    if ($hours > 0) {
                        $durationText .= $hours . ' jam ';
                    }
                    if ($minutes > 0 || $hours == 0) {
                        $durationText .= $minutes . ' menit';
                    }
                    $totalMinutes = (int) round(abs($workMinutes));
                    $durationText = '';
                    if ($totalMinutes < 60) {
                        $durationText = $totalMinutes . ' menit';
                    } elseif ($totalMinutes == 60) {
                        $durationText = '1 jam';
                    } else {
                        $hours = floor($totalMinutes / 60);
                        $minutes = $totalMinutes % 60;
                        $durationText = $hours . ' jam' . ($minutes > 0 ? ' ' . $minutes . ' menit' : '');
                    }
                    $notificationMessage .= " (Durasi kerja: $durationText)";
    
                    Notification::create([
                        'user_id' => Auth::id(),
                        'title' => 'Check Out',
                        'message' => $notificationMessage,
                        'type' => 'info'
                    ]);
                } catch (\Exception $e) {
                    Log::error('Error processing check-out: ' . $e->getMessage());
                    throw new \Exception('Gagal memproses check-out: ' . $e->getMessage());
                }
            } else {
                // Create new attendance record for check-in
                $attendance = Attendance::create([
                    'nama' => $request->nama,
                    'foto' => $request->file('foto')->store('attendances', 'public'),
                    'lokasi' => $request->lokasi,
                    'hadir_untuk' => $request->hadir_untuk,
                    'tanggal' => $today,
                    'check_in' => now()->format('H:i:s'),
                    'work_hours' => 0,
                    'attendance_percentage' => 0,
                    'auto_checkout' => false
                ]);
    
                // Create notification for check-in
                Notification::create([
                    'user_id' => Auth::id(),
                    'title' => 'Check In',
                    'message' => $request->nama . ' telah melakukan check in',
                    'type' => 'info'
                ]);
    
                // Dispatch auto checkout job
                Log::info("Dispatching AutoCheckoutJob for attendance ID: {$attendance->id}");
                AutoCheckoutJob::dispatch($attendance->id)->delay(now()->addHours(8))->onQueue('default');
                Log::info("AutoCheckoutJob dispatched successfully");
            }
    
            return response()->json([
                'success' => true,
                'message' => 'Data presensi berhasil ' . ($request->is_checkout ? 'di-update!' : 'disimpan!'),
                'data' => $attendance
            ]);
        } catch (\Exception $e) {
            Log::error('Error saving attendance: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage()
            ], 500);
        }
    }
}