<?php

namespace App\Http\Services;

use App\Custom\Status;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class SearchService
{
    private string $searchInput;

    public function __construct(string $searchInput)
    {
        $this->searchInput = $searchInput;
    }

    public function productSearch(): Builder
    {
        $input = $this->searchInput;

        return Product::query()->whereIn('status', [Status::ACTIVE, Status::PENDING])->whereHas('photos')
            ->whereHas('vendor', fn($vendor) => $vendor->whereJsonContains('details->status', Status::ACTIVE))
            ->selectRaw(
                '*, MATCH(name) AGAINST(?)
                   + MATCH(description) AGAINST(?) AS relevance',
                [$input, $input]
            )->where(function ($query) use ($input) {
                $query->whereRaw('MATCH(name) AGAINST(?)', $input)
                    ->orWhereRaw('MATCH(description) AGAINST(?)', $input)
                    ->orWhere('name', 'LIKE', "%$input%")
                    ->orWhere('description', 'LIKE', "%$input%");
            })->orderBy('relevance', 'DESC');
    }

    public function profileSearch(): Builder
    {
        $input = $this->searchInput;

        return User::query()->whereJsonContains('details->status', Status::ACTIVE)
            ->selectRaw('*, MATCH(display_name) AGAINST(?) AS relevance', [$input])
            ->where(function ($query) use ($input) {
                $query->whereRaw('MATCH(display_name) AGAINST(?)', $input)
                    ->orWhere('display_name', 'LIKE', "%$input%");
            })->orderBy('relevance', 'DESC');
    }

    public function recordSearch(User $user, array $data): void
    {
        $user->searchRecords()->create([
            'search_term' => $data['searchTerm'],
            'profiles_found' => $data['profileCount'],
            'products_found' => $data['productCount']
        ]);
    }
}
