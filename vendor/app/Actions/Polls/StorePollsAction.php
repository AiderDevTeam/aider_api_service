<?php

namespace App\Actions\Polls;

use App\Http\Requests\PollsRequest;
use App\Http\Services\Api\UserService;
use App\Models\Category;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StorePollsAction
{
    public function handle(Request $request, PollsRequest $pollsRequest){
        try {
            if (isset($request->user['externalId'])) {

                if($request->user['voted'] == 1){
                    return errorJsonResponse(message: 'User has voted already');
                }

                $user = User::authUser($request->user);

                $categoriesIds = $pollsRequest['categoriesIds'];
                logger($categoriesIds);

                $user->categories()->syncWithoutDetaching($categoriesIds);

                foreach ($categoriesIds as $id){
                    $votedCategory = Category::where('id', $id)->first();

                    $divisor = DB::table('category_user')->distinct()->pluck('user_id')->count();

                    $categoryPercentage = $divisor == 0? 0 : (($votedCategory->users()->count()/$divisor)*100);
                    $votedCategory->update(['percentage' => number_format($categoryPercentage, 2) ]);
                }

                UserService::vote($request);

                return successfulJsonResponse(
                    data: [],
                    message: 'Vote registered successfully',
                    statusCode: 201
                );
            }

            return errorJsonResponse(
                message: 'User Authentication failed',
                statusCode: 401
            );

        } catch (Exception $exception){
            report($exception);
        }

        return errorJsonResponse();
    }
}
