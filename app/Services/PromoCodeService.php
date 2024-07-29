<?php 

namespace App\Services;
use Illuminate\Http\Request;
use App\Models\{Subscription, Post, LiveVideo, ChatAsset, AudioCallRequest, VideoCallRequest, User};
use Akaunting\Setting\Facade as Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Exception;

class PromoCodeService {
    /**
     * To call the API from EfiTrader.
     * 
     * @created
     *  
     * @param string $type
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handle($type, $request) {
        try {
            $data = [];
            switch ($type) {
                case SUBSCRIPTION_PAYMENTS:
                    $subscription = Subscription::firstWhere('id', $request->subscription_id);
                    $data= $subscription->amount ?: 0.00;
                    break;
                case POST_PAYMENTS:
                    $post = Post::firstWhere('id', $request->post_id);
                    $data = $post->token ?: 0.00;
                    break;
                case VIDEO_CALL_PAYMENTS:
                    $video_call_request = VideoCallRequest::firstWhere('id', $request->video_call_request_id);
                    $data = $video_call_request->model->video_call_token ?? 0.00;
                    break;
                case AUDIO_CALL_PAYMENTS:
                    $audio_call_request = AudioCallRequest::firstWhere('id', $request->audio_call_request_id);
                    $data = $audio_call_request->model->audio_call_token ?? 0.00;
                    break;
                case CHAT_ASSET_PAYMENTS:
                    $chat_asset = ChatAsset::firstWhere('id', $request->chat_asset_id);
                    $data = $chat_asset->token ?: 0.00;
                    break;
                case LIVE_VIDEO_PAYMENTS:
                    $live_video = LiveVideo::firstWhere('id', $request->live_video_id);
                    $data = $live_video->token ?: 0.00;
                    break;
                case CHAT_MESSAGE_PAYMENTS:
                    $to_user = User::firstWhere(['id' => $request->to_user_id, 'is_content_creator' => CONTENT_CREATOR]);
                    $data = $to_user->chat_message_token ?: 0.00;
                    break;
                    
                default:
                    throw new Exception(api_error(156), 156);
            }
            $response = [
                'success' => true,
                'message' => tr('success'),
                'code' => 200,
                'data' => $data
            ];
        } catch(Exception $e) {
            $response = [
                'success' => false,
                'message' => $e->getMessage(),
                'code' => $e->getCode() ?: 500,
                'data' => []
            ];
        }
        return response()->json($response, 200);
    }
}