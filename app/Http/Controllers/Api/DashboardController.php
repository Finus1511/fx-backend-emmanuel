<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Helpers\Helper;

use DB, Log, Hash, Validator, Exception, Setting;

use App\Models\{ User, UserWallet, Visitor, FavUser, Subscription, UserSubscriptionPayment, Order, UserProduct, OrderProduct, Collection, PostPayment, Post, PostFile, UserWalletPayment };

use Carbon\Carbon;

use Illuminate\Validation\Rule;

use App\Repositories\ProductRepository;

class DashboardController extends Controller
{
 	protected $loginUser;

    protected $skip, $take;

	public function __construct(Request $request) {

        Log::info(url()->current());

        Log::info("Request Data".print_r($request->all(), true));
        
        $this->loginUser = User::find($request->id);

        $this->skip = $request->skip ?: 0;

        $this->take = $request->take ?: (Setting::get('admin_take_count') ?: TAKE_COUNT);

        $this->timezone = $this->loginUser->timezone ?? "America/New_York";

    }

    public function chart(Request $request) {

        try {

            $data['last_x_revenue'] = last_x_months_content_creator_revenue(12, $request->id);

            $data['total_revenue_breakdown'] = total_revenue_breakdown($request->id);

            return $this->sendResponse($message = "", $code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }
    
    }

    public function dashboard_analytics(Request $request) {

        try {

            $today_start = Carbon::today()->startOfDay();

            $today_end   = Carbon::today()->endOfDay();

            $user_wallet = UserWallet::where('user_id', $request->id)->first();

            $data['total_revenue_breakdown'] = total_and_today_revenue($request->id);

            $data['balance_remaining'] = formatted_amount($user_wallet->remaining ?? 0);

            $data['visitor_count'] = Visitor::where('user_id', $request->id)->selectRaw("
                                        COUNT(*) as total,
                                        SUM(CASE WHEN created_at BETWEEN ? AND ? THEN 1 ELSE 0 END) as today", [$today_start, $today_end])
                                        ->first();

             $fav_user = FavUser::where('fav_user_id', $request->id)->pluck('user_id');

            $data['fav_user_count'] = count($fav_user);

            $data['latest_suppoters'] = User::whereIn('id', $fav_user)->take(5)->select('name', 'picture')->get();

            $subscription_ids = Subscription::where('user_id', $request->id)->pluck('id');

            $data['paid_subscription_count'] = UserSubscriptionPayment::whereIn('user_subscription_id', $subscription_ids)->where('is_current_subscription', YES)->count();

            $paid_user = UserSubscriptionPayment::whereIn('user_subscription_id', $subscription_ids)->where('is_current_subscription', YES)->pluck('from_user_id');

            $data['latest_subscribers'] = User::whereIn('id', $paid_user)->select('name', 'picture')->get();

            $visitors_by_country = Visitor::where('user_id', $request->id)
                                    ->selectRaw(
                                        'country_code, country as country_name,
                                         COUNT(*) as visit_count,
                                         CASE WHEN (SELECT COUNT(*) FROM visitors WHERE user_id = ?) = 0
                                              THEN 0
                                              ELSE ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM visitors WHERE user_id = ?), 2)
                                         END as percentage',
                                        [$request->id, $request->id]
                                    )
                                    ->groupBy('country_code')
                                    ->orderByDesc('visit_count')
                                    ->get();

            $data['visitors_by_country'] = $visitors_by_country;

            $user_payments = UserWalletPayment::where(['user_id' => $request->id, 'amount_type' => WALLET_AMOUNT_TYPE_ADD])
                            ->select(
                                'received_from_user_id',
                                DB::raw('SUM(paid_amount) as total_amount'),
                                DB::raw('COUNT(id) as total_payments')
                            )
                            ->groupBy('received_from_user_id')
                            ->orderByDesc('total_amount')
                            ->limit(5)
                            ->get();

            $data['user_payments'] = $user_payments ?? [];

            return $this->sendResponse($message = "", $code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }
    
    }

    public function best_selling_list(Request $request) {

        try {

            $user_product_ids = UserProduct::where('user_id', $request->id)->pluck('id');

            $order_ids = OrderProduct::whereIn('user_product_id', $user_product_ids)->pluck('order_id');

            $base_query = $total_query = Order::with(['user:id,name', 'deliveryAddressDetails:id,contact_number'])->whereIn('id',$order_ids)->orderBy('orders.created_at', 'desc');

            $orders = $base_query->take(5)->get();

            $orders = $orders->map(function ($order, $key) use ($request) {

                        $order->customer_name = $order->user->name;

                        $order->created = common_date($order->created_at, $this->timezone);

                        unset($order->user);

                        return $order;
                    });

            $best_selling_products = UserProduct::where('user_id', $request->id)
                                    ->withCount('orderProducts as total_orders')
                                    ->withSum('orderProducts as total_pieces_sold', 'quantity')
                                    ->withSum('orderProducts as total_earnings', 'total')
                                    ->orderByDesc('total_pieces_sold')
                                    ->take(5)
                                    ->get();

            $total_grossing_folders = Collection::where('user_id', $request->id)
                                    ->withSum('collectionPayments as total_earnings', 'amount')
                                    ->withCount([
                                        'collectionFiles as audio_count' => function ($query) {
                                            $query->where('file_type', 'audio');
                                        },
                                        'collectionFiles as video_count' => function ($query) {
                                            $query->where('file_type', 'video');
                                        },
                                        'collectionFiles as image_count' => function ($query) {
                                            $query->where('file_type', 'image');
                                        },
                                    ])
                                    ->orderByDesc('total_earnings')
                                    ->take(3)
                                    ->get();

            $user_post_ids = Post::where('user_id', $request->id)->pluck('id');

            $total_grossing_contents = PostPayment::whereIn('post_id', $user_post_ids)
                            ->select(
                                'post_id',
                                DB::raw('SUM(paid_amount) as total_amount'),
                                DB::raw('COUNT(id) as total_payments')
                            )
                            ->groupBy('post_id')
                            ->orderByDesc('total_amount')
                            ->limit(3)
                            ->get();

            $total_grossing_contents = $total_grossing_contents->map(function ($content, $key) use ($request) {

                        $content->post_file = PostFile::where('post_id', $content->post_id)->first();

                        return $content;
                    });

            $data['recent_orders'] = $orders ?? [];

            $data['best_selling_products'] = $best_selling_products ?? [];

            $data['total_grossing_folders'] = $total_grossing_folders ?? [];

            $data['total_grossing_contents'] = $total_grossing_contents ?? [];

            return $this->sendResponse($message = "", $code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }
    
    }

}