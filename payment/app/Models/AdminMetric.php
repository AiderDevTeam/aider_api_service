<?php

namespace App\Models;

use App\Enum\Status;
use AppierSign\RealtimeModel\Traits\RealtimeModel;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AdminMetric extends Model
{
    use HasFactory, RealtimeModel;

    protected $fillable = ['external_id', 'service'];

    private function currentMonth(): string
    {
        return now()->format('Y-m');
    }

    private function previousMonth(): string
    {
        return Carbon::now()->subMonth()->format('Y-m');
    }

    public function getSyncKey(): string
    {
        return 'service';
    }

    public function toRealtimeData(): array
    {
        return [
            'overview' => $this->overview(),
//            'profitability' => $this->profitability(),
//            'affordability' => $this->affordability(),
//            'customerService' => $this->customerService(),
        ];
    }

    public function overview(): array
    {
        return
            [
            'totalTransactionValue' => $this->getTotalTransactionValue(),
            'totalTransactionCount' => $this->getTotalTransactionCount(),
            'avgTransactionValue' => $this->getAvgTransactionValue(),
            'totalUserCount' => DB::table('users')->count(),
            'totalRevenue' => 3232,
            'profitability' => 8011,
            'activeUsers' => $this->getActiveUsers(),
            'newUsers' => $this->getNewUsers(),
            'totalSignups' => $this->getTotalSignups(),
            'returningUsers' => $this->getReturningUsers(),
//            'retentionRate' => $this->getRetentionRate(),
            'productSummary' => $this->getProductSummary(),
        ];
    }

    public function getAvgTransactionValue(): array
    {
        return [
            'previous' => $this->getTotalTransactionCount()['previous'] === 0 ? 0 : $this->getTotalTransactionValue()['previous'] / $this->getTotalTransactionCount()['previous'],
            'current' => $this->getTotalTransactionCount()['current'] === 0 ? 0 : $this->getTotalTransactionValue()['current'] / $this->getTotalTransactionCount()['current']
            ];
    }



    public function getTotalTransactionValue(): array
    {
        return [
            'previous' => $this->getPreviousTransactionValue(),
            'current' => $this->getCurrentTransactionValue()
        ];
    }

    public function getTotalTransactionCount(): array
    {
        return [
            'previous' => $this->getPreviousTransactionCount(),
            'current' => $this->getCurrentTransactionCount()
        ];
    }


    public function getPreviousTransactionValue()
    {
        return DB::table('transactions')
            ->whereIn('status', [Status::SUCCESS->value, Status::COMPLETED->value])
            ->where(DB::raw('DATE_FORMAT(transactions.created_at, "%Y-%m")'), $this->previousMonth())
            ->sum('amount');
    }


    public function getCurrentTransactionValue()
    {
        return DB::table('transactions')
            ->whereIn('status', [Status::SUCCESS->value, Status::COMPLETED->value])
            ->where(DB::raw('DATE_FORMAT(transactions.created_at, "%Y-%m")'), $this->currentMonth())
            ->sum('amount');
    }


    public function getPreviousTransactionCount(): int
    {
        return DB::table('transactions')
            ->whereIn('status', [Status::SUCCESS->value, Status::COMPLETED->value])
            ->where(DB::raw('DATE_FORMAT(transactions.created_at, "%Y-%m")'), $this->previousMonth())
            ->count();
    }


    public function getCurrentTransactionCount(): int
    {
        return DB::table('transactions')
            ->whereIn('status', [Status::SUCCESS->value, Status::COMPLETED->value])
            ->where(DB::raw('DATE_FORMAT(transactions.created_at, "%Y-%m")'), $this->currentMonth())
            ->count();
    }

    private function getProductSummary(): array
    {
        return [
            'daily' => [
                'transactionAmount' => [
                    'eCommerce' => $this->getDailyTransactionAmount(DeliveryPayment::class),
                    'vas' => $this->getDailyTransactionAmount(VASPayment::class),
                    ],
                'numberOfTransactions' => [
                    'eCommerce' => $this->getDailyTransactionCount(DeliveryPayment::class),
                    'vas' => $this->getDailyTransactionCount(VASPayment::class),
                ],
                'revenue' => [],
                'users' => [
                    'eCommerce' => $this->getDailyUsers(DeliveryPayment::class),
                    'vas' => $this->getDailyUsers(VASPayment::class),
                ],
            ],
            'weekly' => [
                'transactionAmount' => [
                    'eCommerce' => $this->getWeeklyTransactionAmount(DeliveryPayment::class),
                    'vas' => $this->getWeeklyTransactionAmount(VASPayment::class),
                ],
                'numberOfTransactions' => [
                    'eCommerce' => $this->getWeeklyTransactionCount(DeliveryPayment::class),
                    'vas' => $this->getWeeklyTransactionCount(VASPayment::class),
                ],
                'revenue' => [],
                'users' => [
                    'eCommerce' => $this->getWeeklyUsers(DeliveryPayment::class),
                    'vas' => $this->getWeeklyUsers(VASPayment::class),
                ],
            ],
            'monthly' => [
                'transactionAmount' => [
                    'eCommerce' => $this->getMonthlyTransactionAmount(DeliveryPayment::class),
                    'vas' => $this->getMonthlyTransactionAmount(VASPayment::class),
                ],
                'numberOfTransactions' => [
                    'eCommerce' => $this->getMonthlyTransactionCount(DeliveryPayment::class),
                    'vas' => $this->getMonthlyTransactionCount(VASPayment::class),
                ],
                'revenue' => [],
                'users' => [
                    'eCommerce' => $this->getMonthlyUsers(DeliveryPayment::class),
                    'vas' => $this->getMonthlyUsers(VASPayment::class),
                ],
            ],
            'quarterly' => [
                'transactionAmount' => [
                    'eCommerce' => $this->getQuarterlyTransactionAmount(DeliveryPayment::class),
                    'vas' => $this->getQuarterlyTransactionAmount(VASPayment::class),
                ],
                'numberOfTransactions' => [
                    'eCommerce' => $this->getQuarterlyTransactionCount(DeliveryPayment::class),
                    'vas' => $this->getQuarterlyTransactionCount(VASPayment::class),
                ],
                'revenue' => [],
                'users' => [
                    'eCommerce' => $this->getQuarterlyUsers(DeliveryPayment::class),
                    'vas' => $this->getQuarterlyUsers(VASPayment::class),
                ],
            ],
        ];
    }

    public function getActiveUsers(): array
    {

        return [
            'previous' => DB::table('users')
                ->whereExists(fn($query) => $query->select(DB::raw(1))
                    ->from('transactions')
                    ->whereRaw('users.id = transactions.user_id')
                    ->where(DB::raw('DATE_FORMAT(transactions.created_at, "%Y-%m")'), $this->previousMonth())
                )->whereExists(fn($query) => $query->select(DB::raw(1))
                    ->from('transactions')
                    ->whereRaw('users.id = transactions.user_id')
                    ->where('transactions.created_at', '<', DB::raw('CURDATE()')))
                ->pluck('external_id')
                ->toArray(),

            'current' => DB::table('users')
                ->whereExists(fn($query) => $query->select(DB::raw(1))
                    ->from('transactions')
                    ->whereRaw('users.id = transactions.user_id')
                    ->where(DB::raw('DATE_FORMAT(transactions.created_at, "%Y-%m")'), $this->currentMonth())
                )
                ->whereExists(fn($query) => $query->select(DB::raw(1))
                    ->from('transactions')
                    ->whereRaw('users.id = transactions.user_id')
                    ->where('transactions.created_at', '<', DB::raw('CURDATE()'))
                )
                ->pluck('external_id')
                ->toArray(),
        ];
    }

    public function getNewUsers(): array
    {

        return [
            'previous' => DB::table('users')
                ->whereExists(fn($query) => $query->select(DB::raw(1))
                    ->from('transactions')
                    ->whereRaw('users.id = transactions.user_id')
                    ->where(DB::raw('DATE_FORMAT(transactions.created_at, "%Y-%m")'), $this->previousMonth())
                )
                ->pluck('external_id')
                ->toArray(),

            'current' => DB::table('users')
                ->whereExists(fn($query) => $query->select(DB::raw(1))
                    ->from('transactions')
                    ->whereRaw('users.id = transactions.user_id')
                    ->where(DB::raw('DATE_FORMAT(transactions.created_at, "%Y-%m")'), $this->currentMonth())
                )
                ->pluck('external_id')
                ->toArray(),
        ];
    }

    public function getReturningUsers(): array
    {
        $previousMonths = collect(range(1, 11))->map(function ($months) {
            return Carbon::now()->subMonths($months)->format('Y-m');
        })->toArray();


        return [
            'previous' => DB::table('users')
                ->where(function ($query) use ($previousMonths) {
                    $query->whereExists(function ($subQuery) use ($previousMonths) {
                        $subQuery->select(DB::raw(1))
                            ->from('transactions')
                            ->whereRaw('users.id = transactions.user_id')
                            ->whereIn(DB::raw('DATE_FORMAT(transactions.created_at, "%Y-%m")'), $previousMonths);
                    })
                        ->whereExists(function ($subQuery) {
                            $subQuery->select(DB::raw(1))
                                ->from('transactions')
                                ->whereRaw('users.id = transactions.user_id')
                                ->where(DB::raw('DATE_FORMAT(transactions.created_at, "%Y-%m")'), $this->previousMonth());
                        });
                })
                ->pluck('external_id')
                ->toArray(),

            'current' => DB::table('users')
                ->where(function ($query) use ($previousMonths) {
                    $query->whereExists(function ($subQuery) use ($previousMonths) {
                        $subQuery->select(DB::raw(1))
                            ->from('transactions')
                            ->whereRaw('users.id = transactions.user_id')
                            ->whereIn(DB::raw('DATE_FORMAT(transactions.created_at, "%Y-%m")'), $previousMonths);
                    })
                        ->whereExists(function ($subQuery) {
                            $subQuery->select(DB::raw(1))
                                ->from('transactions')
                                ->whereRaw('users.id = transactions.user_id')
                                ->where(DB::raw('DATE_FORMAT(transactions.created_at, "%Y-%m")'), $this->currentMonth());
                        });
                })
                ->pluck('external_id')
                ->toArray(),
        ];
    }

    public function getTotalSignups(): array
    {

        return [
            'previous' => DB::table('users')
                ->where(DB::raw('DATE_FORMAT(created_at, "%Y-%m-%d %H:%i:%s")'), 'like', $this->previousMonth() . '%')
                ->get()
                ->pluck('external_id')
                ->toArray(),

            'current' => DB::table('users')
                ->where(DB::raw('DATE_FORMAT(created_at, "%Y-%m-%d %H:%i:%s")'), 'like', $this->currentMonth() . '%')
                ->get()
                ->pluck('external_id')
                ->toArray(),
        ];
    }


//    PRODUCT SUMMARY QUERIES
    private function getDailyTransactionAmount(string $paymentableType): array
    {
        $startDate = now()->subDays(11);

        $dateRange = CarbonPeriod::create($startDate, now())->toArray();

        $result = DB::table('payments')
            ->select(DB::raw('SUM(amount) as total_amount, DATE(created_at) as created_date'))
            ->where('collection_status', '=', Status::SUCCESS->value)
            ->where('paymentable_type', '=', $paymentableType)
            ->where('created_at', '>=', $startDate)
            ->groupBy('created_date')
            ->get();

        $totals = [];
        foreach ($dateRange as $date) {
            $formattedDate = $date->format('M d, Y');
            $totals[$formattedDate] = 0;

            foreach ($result as $row) {
                if ($date->isSameDay(Carbon::parse($row->created_date))) {
                    $totals[$formattedDate] = $row->total_amount;
                }
            }
        }

        return $totals;
    }


    private function getWeeklyTransactionAmount(string $paymentableType): array
    {
        $startDate = Carbon::now()->subWeeks(11);

        $result = DB::table('payments')
            ->select(DB::raw('WEEK(created_at) as week_number'), DB::raw('SUM(amount) as total_amount'))
            ->where('collection_status', '=', Status::SUCCESS->value)
            ->where('paymentable_type', '=', $paymentableType)
            ->where('created_at', '>=', $startDate)
            ->groupBy(DB::raw('WEEK(created_at)'))
            ->orderBy('week_number')
            ->get();

        $totals = [];

        $dateRange = CarbonPeriod::create($startDate, now())->weeks();

        foreach ($dateRange as $date) {
            $weekNumber = $date->weekOfYear;
            $formattedWeek = 'Week ' . $weekNumber;

            $found = false;

            foreach ($result as $row) {
                if ($row->week_number == $weekNumber) {
                    $totals[$formattedWeek] = $row->total_amount;
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                $totals[$formattedWeek] = 0;
            }
        }

        return $totals;
    }

    private function getMonthlyTransactionAmount(string $paymentableType): array
    {
        $startDate = Carbon::now()->subMonths(11)->startOfMonth();

        $result = DB::table('payments')
            ->select(DB::raw('MONTH(created_at) as month_number'), DB::raw('SUM(amount) as total_amount'))
            ->where('collection_status', '=', Status::SUCCESS->value)
            ->where('paymentable_type', '=', $paymentableType)
            ->where('created_at', '>=', $startDate)
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->orderBy('month_number')
            ->get();

        $totals = [];

        $dateRange = CarbonPeriod::create($startDate, '1 month', now());

        foreach ($dateRange as $date) {
            $monthNumber = $date->month;
            $monthName = $date->format('M');
            $formattedMonth = $monthName;

            $found = false;

            foreach ($result as $row) {
                if ($row->month_number == $monthNumber) {
                    $totals[$formattedMonth] = $row->total_amount;
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                $totals[$formattedMonth] = 0;
            }
        }

        return $totals;
    }

    private function getQuarterlyTransactionAmount(string $paymentableType): array
    {
        $startDate = Carbon::now()->startOfYear();

        $result = DB::table('payments')
            ->select(DB::raw('QUARTER(created_at) as quarter'), DB::raw('SUM(amount) as total_amount'))
            ->where('collection_status', '=', Status::SUCCESS->value)
            ->where('paymentable_type', '=', $paymentableType)
            ->where('created_at', '>=', $startDate)
            ->groupBy(DB::raw('QUARTER(created_at)'))
            ->orderBy('quarter')
            ->get();

        $totals = [];

        for ($i = 1; $i <= 4; $i++) {
            $quarterName = 'Quarter ' . $i;
            $totals[$quarterName] = 0;
        }

        foreach ($result as $row) {
            $quarterName = 'Quarter ' . $row->quarter;
            $totals[$quarterName] = $row->total_amount;
        }

        return $totals;
    }

    private function getDailyTransactionCount(string $paymentableType): array
    {
        $startDate = Carbon::now()->subDays(11);

        $dateRange = CarbonPeriod::create($startDate, now())->toArray();

        $result = DB::table('payments')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as total_count'))
            ->where('collection_status', '=', Status::SUCCESS->value)
            ->where('paymentable_type', '=', $paymentableType)
            ->where('created_at', '>=', $startDate)
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();

        $counts = [];
        foreach ($dateRange as $date) {
            $formattedDate = $date->format('M d, Y');
            $counts[$formattedDate] = 0;

            foreach ($result as $row) {
                if ($date->isSameDay(Carbon::parse($row->date))) {
                    $counts[$formattedDate] = $row->total_count;
                }
            }
        }
        return $counts;
    }

    private function getWeeklyTransactionCount(string $paymentableType): array
    {
        $startDate = Carbon::now()->subWeeks(11);

        $result = DB::table('payments')
            ->select(DB::raw('WEEK(created_at) as week_number'), DB::raw('COUNT(*) as total_count'))
            ->where('collection_status', '=', Status::SUCCESS->value)
            ->where('paymentable_type', '=', $paymentableType)
            ->where('created_at', '>=', $startDate)
            ->groupBy(DB::raw('WEEK(created_at)'))
            ->get();

        $counts = [];

        foreach ($result as $row) {
            $weekNumber = $row->week_number;
            $counts['Week ' . $weekNumber] = $row->total_count;
        }

        $currentWeek = Carbon::now()->weekOfYear;
        for ($i = $currentWeek - 11; $i <= $currentWeek; $i++) {
            if (!isset($counts['Week ' . $i])) {
                $counts['Week ' . $i] = 0;
            }
        }
        return $counts;
    }

    private function getMonthlyTransactionCount(string $paymentableType): array
    {
        $startDate = Carbon::now()->subMonths(11);

        $result = DB::table('payments')
            ->select(DB::raw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as total_count'))
            ->where('collection_status', '=', Status::SUCCESS->value)
            ->where('paymentable_type', '=', $paymentableType)
            ->where('created_at', '>=', $startDate)
            ->groupBy(DB::raw('YEAR(created_at)'), DB::raw('MONTH(created_at)'))
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        $counts = [];

        $dateRange = CarbonPeriod::create($startDate, now())->months();

        foreach ($dateRange as $date) {
            $monthName = $date->format('M');
            $formattedMonth = $monthName . ' ' . $date->format('Y');
            $found = false;

            foreach ($result as $row) {
                if ($row->year == $date->year && $row->month == $date->month) {
                    $counts[$formattedMonth] = $row->total_count;
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                $counts[$formattedMonth] = 0;
            }
        }
        return $counts;
    }

    private function getQuarterlyTransactionCount(string $paymentableType): array
    {
        $startDate = Carbon::now()->startOfYear();

        $result = DB::table('payments')
            ->select(DB::raw('QUARTER(created_at) as quarter, COUNT(*) as total_count'))
            ->where('collection_status', '=', Status::SUCCESS->value)
            ->where('paymentable_type', '=', $paymentableType)
            ->where('created_at', '>=', $startDate)
            ->groupBy(DB::raw('QUARTER(created_at)'))
            ->orderBy('quarter')
            ->get();

        $counts = [];

        for ($quarter = 1; $quarter <= 4; $quarter++) {
            $quarterName = 'Quarter ' . $quarter;
            $counts[$quarterName] = 0;
        }

        foreach ($result as $row) {
            $quarterName = 'Quarter ' . $row->quarter;
            $counts[$quarterName] = $row->total_count;
        }

        return $counts;
    }

    private function getDailyUsers(string $paymentableType): array
    {
        $startDate = Carbon::now()->subDays(11);
        $dateRange = CarbonPeriod::create($startDate, now())->toArray();

        $result = DB::table('payments')
            ->join('users', 'users.id', '=', 'payments.user_id')
            ->select(DB::raw('DATE(payments.created_at) as date'), 'users.external_id')
            ->where('payments.collection_status', '=', Status::SUCCESS->value)
            ->where('payments.created_at', '>=', $startDate)
            ->where('paymentable_type', '=', $paymentableType)
            ->distinct()
            ->orderBy('date', 'asc')
            ->get();

        $users = [];
        foreach ($dateRange as $date) {
            $formattedDate = $date->format('M d, Y');
            $users[$formattedDate] = [];

            foreach ($result as $row) {
                if ($date->isSameDay(Carbon::parse($row->date))) {
                    $users[$formattedDate][] = $row->external_id;
                }
            }
        }

        return $users;
    }


    // CONTINUE FROM HERE


    private function getWeeklyUsers(string $paymentableType): array
    {
        $startDate = Carbon::now()->subWeeks(11)->startOfWeek();

        $result = DB::table('payments')
            ->join('users', 'users.id', '=', 'payments.user_id')
            ->select(DB::raw('WEEK(payments.created_at) as week_number'), 'users.external_id')
            ->where('payments.collection_status', '=', Status::SUCCESS->value)
            ->where('payments.created_at', '>=', $startDate)
            ->where('paymentable_type', '=', $paymentableType)
            ->distinct()
            ->get();

        $users = [];

        $dateRange = CarbonPeriod::create($startDate, now())->weeks();

        foreach ($dateRange as $date) {
            $weekNumber = $date->weekOfYear;
            $formattedWeek = 'Week ' . $weekNumber;

            $users[$formattedWeek] = [];

            foreach ($result as $row) {
                if ($row->week_number == $weekNumber) {
                    $users[$formattedWeek][] = $row->external_id;
                }
            }
        }

        return $users;
    }

    private function getMonthlyUsers(string $paymentableType): array
    {
        $startDate = Carbon::now()->subMonths(11)->startOfMonth();

        $result = DB::table('payments')
            ->join('users', 'payments.user_id', '=', 'users.id')
            ->select(DB::raw('MONTH(payments.created_at) as month'), 'users.external_id')
            ->where('payments.collection_status', '=', Status::SUCCESS->value)
            ->where('payments.created_at', '>=', $startDate)
            ->groupBy(DB::raw('MONTH(payments.created_at)'), 'users.external_id')
            ->orderBy('month', 'asc')
            ->get();

        $counts = [];

        $dateRange = CarbonPeriod::create($startDate, '1 month', now());

        foreach ($dateRange as $date) {
            $monthNumber = $date->month;
            $formattedMonth = DateTime::createFromFormat('!m', $monthNumber)->format('M');

            $counts[$formattedMonth] = [];

            foreach ($result as $row) {
                if ($row->month == $monthNumber) {
                    $counts[$formattedMonth][] = $row->external_id;
                }
            }
        }

        return $counts;
    }

    private function getQuarterlyUsers(string $paymentableType): array
    {
        $startDate = Carbon::now()->startOfYear();

        $result = DB::table('payments')
            ->select(DB::raw('QUARTER(payments.created_at) as quarter'), DB::raw('GROUP_CONCAT(DISTINCT users.external_id) as distinct_external_ids'))
            ->join('users', 'payments.user_id', '=', 'users.id')
            ->where('payments.collection_status', '=', Status::SUCCESS->value)
            ->where('payments.created_at', '>=', $startDate)
            ->groupBy(DB::raw('QUARTER(payments.created_at)'))
            ->orderBy('quarter', 'asc')
            ->get();

        $counts = [];
        $quarters = [1, 2, 3, 4];

        foreach ($quarters as $quarter) {
            $quarterName = 'Quarter ' . $quarter;

            $quarterResult = $result->where('quarter', $quarter)->first();

            if ($quarterResult) {
                $counts[$quarterName] = explode(',', $quarterResult->distinct_external_ids);
            } else {
                $counts[$quarterName] = [];
            }
        }

        return $counts;
    }


}



