<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Exports\AttendanceExport;
use Maatwebsite\Excel\Facades\Excel;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); 
    }

    public function dashboard()
    {
        $totalAbsensi = Attendance::count();
        $absensiHariIni = Attendance::whereDate('tanggal', today())->count();
        $absensiBulanIni = Attendance::whereMonth('tanggal', now()->month)->count();
        $totalKaryawan = Employee::count();
        
        // Calculate weekly attendance data with actual percentages
        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();
        $weeklyAttendances = Attendance::whereBetween('tanggal', [$startOfWeek, $endOfWeek])
            ->selectRaw('DATE(tanggal) as date, AVG(attendance_percentage) as avg_percentage, COUNT(*) as total_attendance')
            ->groupBy('date')
            ->get()
            ->mapWithKeys(function ($item) use ($totalKaryawan) {
                return [date('D', strtotime($item->date)) => [
                    'count' => $item->total_attendance,
                    'percentage' => abs((float) round($item->avg_percentage, 2)) // Ensure float and apply abs
                ]];
            });
    
        // Fill in missing days with zero
        $weekDays = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
        foreach ($weekDays as $day) {
            if (!isset($weeklyAttendances[$day])) {
                $weeklyAttendances[$day] = ['count' => 0, 'percentage' => 0];
            }
        }
        $weeklyAttendances = collect($weekDays)->mapWithKeys(function($day) use ($weeklyAttendances) {
            return [$day => $weeklyAttendances[$day]];
        });
    
        $weeklyPercentage = $weeklyAttendances->avg('percentage');
    
        // Calculate monthly attendance data with actual percentages
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();
        $monthlyAttendances = Attendance::whereBetween('tanggal', [$startOfMonth, $endOfMonth])
            ->selectRaw('DATE(tanggal) as date, AVG(attendance_percentage) as avg_percentage, COUNT(*) as total_attendance')
            ->groupBy('date')
            ->get()
            ->mapWithKeys(function ($item) use ($totalKaryawan) {
                return [date('d', strtotime($item->date)) => [
                    'count' => $item->total_attendance,
                    'percentage' => abs((float) round($item->avg_percentage, 2)) // Ensure float and apply abs
                ]];
            });
    
        // Fill in missing days with zero
        $daysInMonth = now()->daysInMonth;
        for ($i = 1; $i <= $daysInMonth; $i++) {
            $day = str_pad($i, 2, '0', STR_PAD_LEFT);
            if (!isset($monthlyAttendances[$day])) {
                $monthlyAttendances[$day] = ['count' => 0, 'percentage' => 0];
            }
        }
        $monthlyAttendances = collect(range(1, $daysInMonth))->mapWithKeys(function($day) use ($monthlyAttendances) {
            $day = str_pad($day, 2, '0', STR_PAD_LEFT);
            return [$day => $monthlyAttendances[$day]];
        });
    
        $monthlyPercentage = $monthlyAttendances->avg('percentage');
    
        // Calculate daily attendance percentage based on actual attendance percentages
        $dailyAttendances = Attendance::whereDate('tanggal', today())->get();
        $dailyPercentage = $dailyAttendances->isEmpty() ? 0 : round($dailyAttendances->avg('attendance_percentage'), 2);
    
        // Calculate per-employee attendance statistics
        $employeeStats = Employee::all()->map(function($employee) {
            $totalWorkDays = now()->diffInDays(now()->startOfYear()) + 1;
            
            // Weekly stats
            $weeklyAttendances = Attendance::where('nama', $employee->nama)
                ->whereBetween('tanggal', [now()->startOfWeek(), now()->endOfWeek()])
                ->get();
            $weeklyPercentage = $weeklyAttendances->isEmpty() ? 0 : round($weeklyAttendances->avg('attendance_percentage'), 2);
    
            // Monthly stats
            $monthlyAttendances = Attendance::where('nama', $employee->nama)
                ->whereMonth('tanggal', now()->month)
                ->whereYear('tanggal', now()->year)
                ->get();
            $monthlyPercentage = $monthlyAttendances->isEmpty() ? 0 : round($monthlyAttendances->avg('attendance_percentage'), 2);
    
            // Yearly stats
            $yearlyAttendances = Attendance::where('nama', $employee->nama)
                ->whereYear('tanggal', now()->year)
                ->get();
            $yearlyPercentage = $yearlyAttendances->isEmpty() ? 0 : round($yearlyAttendances->avg('attendance_percentage'), 2);
    
            return [
                'id' => $employee->id,
                'nama' => $employee->nama,
                'hadir_untuk' => $employee->hadir_untuk,
                'weekly' => [
                    'attendance' => $weeklyAttendances->count(),
                    'percentage' => abs($weeklyPercentage) // Ensure positive
                ],
                'monthly' => [
                    'attendance' => $monthlyAttendances->count(),
                    'percentage' => abs($monthlyPercentage) // Ensure positive
                ],
                'yearly' => [
                    'attendance' => $yearlyAttendances->count(),
                    'percentage' => abs($yearlyPercentage) // Ensure positive
                ]
            ];
        });
        
        // Get perPage from request or default to 5
        $perPage = request('per_page', 5);
        
        // Get the attendances and convert them to objects
        $attendances = Attendance::latest()
            ->paginate($perPage);
        
        $employees = Employee::all();
    
        return view('admin.dashboard', compact(
            'totalAbsensi',
            'absensiHariIni',
            'absensiBulanIni',
            'totalKaryawan',
            'attendances',
            'employees',
            'dailyPercentage',
            'weeklyPercentage',
            'monthlyPercentage',
            'weeklyAttendances',
            'monthlyAttendances',
            'employeeStats'
        ));
    }

    public function index()
    {
        $query = Attendance::query();
        
        if(request('date')) {
            $query->whereDate('tanggal', request('date'));
        }
        
        $attendances = $query->latest()->paginate(10);
        $recentAttendances = Attendance::latest()->take(5)->get();
        
        return view('admin.attendances.index', compact('attendances', 'recentAttendances'));
    }

    public function storeEmployee(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'hadir_untuk' => 'required|string|max:255',
        ]);

        try {
            $employee = Employee::create([
                'nama' => $request->nama,
                'hadir_untuk' => $request->hadir_untuk,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Karyawan berhasil ditambahkan!',
                'data' => $employee
            ]);
        } catch (\Exception $e) {
            Log::error('Error saving employee: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan karyawan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroyEmployee(Employee $employee)
    {
        try {
            $employee->delete();
            return response()->json([
                'success' => true,
                'message' => 'Karyawan berhasil dihapus!'
            ]);
        } catch (\Exception $e) {
            Log::error('Delete employee error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus karyawan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateEmployee(Request $request, Employee $employee)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'hadir_untuk' => 'required|string|max:255',
        ]);

        try {
            $employee->update([
                'nama' => $request->nama,
                'hadir_untuk' => $request->hadir_untuk,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Karyawan berhasil diperbarui!',
                'data' => $employee
            ]);
        } catch (\Exception $e) {
            Log::error('Update employee error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui karyawan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function searchEmployees(Request $request)
    {
        try {
            $search = trim($request->input('search', ''));

            Log::info('Search employees query: ' . $search);

            if (empty($search)) {
                Log::info('Search query is empty, returning empty array');
                return response()->json([]);
            }

            $query = Employee::query();

            foreach (explode(' ', $search) as $keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('nama', 'like', "%{$keyword}%")
                      ->orWhere('hadir_untuk', 'like', "%{$keyword}%");
                });
            }

            $employees = $query->limit(10)->get()->map(function($employee) {
                return [
                    'id' => $employee->id,
                    'nama' => $employee->nama,
                    'hadir_untuk' => $employee->hadir_untuk,
                    'foto' => $employee->foto ? Storage::url($employee->foto) : null,
                ];
            });

            return response()->json($employees);
        } catch (\Exception $e) {
            Log::error('Search error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal melakukan pencarian karyawan'
            ], 500);
        }
    }
    
    public function searchAttendances(Request $request)
    {
        try {
            $search = trim($request->input('search', ''));
            $sort = $request->input('sort', 'latest');
            $date = $request->input('date');
            $perPage = (int) $request->input('per_page', 5);
    
            // Build the query
            $query = Attendance::query();
    
            // Apply search filter
            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('nama', 'like', '%' . $search . '%')
                      ->orWhere('hadir_untuk', 'like', '%' . $search . '%')
                      ->orWhere('lokasi', 'like', '%' . $search . '%');
                });
            }
    
            // Apply date filter
            if ($date) {
                $query->whereDate('tanggal', $date);
            }
    
            // Apply sort
            if ($sort === 'latest') {
                $query->orderBy('tanggal', 'desc')
                      ->orderBy('check_in', 'desc');
            } elseif ($sort === 'oldest') {
                $query->orderBy('tanggal', 'asc')
                      ->orderBy('check_in', 'asc');
            }
    
            // Paginate results
            $attendances = $query->paginate($perPage);
    
            // Format response
            $formattedAttendances = $attendances->map(function($attendance) {
                return [
                    'id' => $attendance->id,
                    'nama' => $attendance->nama,
                    'hadir_untuk' => $attendance->hadir_untuk,
                    'lokasi' => $attendance->lokasi,
                    'tanggal' => $attendance->tanggal->format('Y-m-d'),
                    'check_in' => $attendance->check_in ? date('H:i:s', strtotime($attendance->check_in)) : '-',
                    'check_out' => $attendance->check_out ? date('H:i:s', strtotime($attendance->check_out)) : '-',
                    'foto' => $attendance->foto ? asset('storage/' . $attendance->foto) : null,
                    'duration' => $attendance->work_hours,
                ];
            });
    
            return response()->json([
                'success' => true,
                'data' => $formattedAttendances,
                'current_page' => $attendances->currentPage(),
                'last_page' => $attendances->lastPage(),
                'total' => $attendances->total(),
                'per_page' => $attendances->perPage(),
            ]);
    
        } catch (\Exception $e) {
            \Log::error('Search attendances error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal melakukan pencarian presensi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function export(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        
        $filename = 'attendance_export_' . date('Y-m-d_H-i-s') . '.xlsx';
        
        return Excel::download(
            new AttendanceExport($startDate, $endDate),
            $filename
        );
    }
}
