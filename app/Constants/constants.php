<?php

/*
|--------------------------------------------------------------------------
| Application Constants
|--------------------------------------------------------------------------
|
|
|
*/

if(!defined('TAKE_COUNT')) define('TAKE_COUNT', 6);

if(!defined('NO')) define('NO', 0);
if(!defined('YES')) define('YES', 1);

if(!defined('PAID')) define('PAID',1);
if(!defined('UNPAID')) define('UNPAID', 0);

if(!defined('DEVICE_ANDROID')) define('DEVICE_ANDROID', 'android');
if(!defined('DEVICE_IOS')) define('DEVICE_IOS', 'ios');
if(!defined('DEVICE_WEB')) define('DEVICE_WEB', 'web');

if(!defined('MALE')) define('MALE', 'male');
if(!defined('FEMALE')) define('FEMALE', 'female');
if(!defined('OTHERS')) define('OTHERS', 'others');
if(!defined('RATHER_NOT_SELECT')) define('RATHER_NOT_SELECT', 'rather-not-select');
if(!defined('COUPLE')) define('COUPLE', 'couple');

if(!defined('APPROVED')) define('APPROVED', 1);
if(!defined('DECLINED')) define('DECLINED', 0);

if(!defined('DEFAULT_TRUE')) define('DEFAULT_TRUE', true);
if(!defined('DEFAULT_FALSE')) define('DEFAULT_FALSE', false);

if(!defined('ADMIN')) define('ADMIN', 'admin');
if(!defined('SUB_ADMIN')) define('SUB_ADMIN', 'sub_admin');
if(!defined('USER')) define('USER', 'user');
if(!defined('ContentCreator')) define('ContentCreator', 'creator');

if(!defined('COD')) define('COD',   'COD');
if(!defined('PAYPAL')) define('PAYPAL', 'PAYPAL');
if(!defined('CARD')) define('CARD',  'CARD');
if(!defined('BANK_TRANSFER')) define('BANK_TRANSFER',  'BANK_TRANSFER');
if(!defined('PAYMENT_OFFLINE')) define('PAYMENT_OFFLINE',  'OFFLINE');
if(!defined('PAYMENT_MODE_WALLET')) define('PAYMENT_MODE_WALLET',  'WALLET');
if(!defined('CCBILL')) define('CCBILL', 'CCBILL');
if(!defined('COINPAYMENT')) define('COINPAYMENT',  'COINPAYMENT');
if(!defined('INAPP_PURCHASE')) define('INAPP_PURCHASE',  'INAPP_PURCHASE');

if(!defined('STRIPE_MODE_LIVE')) define('STRIPE_MODE_LIVE',  'live');
if(!defined('STRIPE_MODE_SANDBOX')) define('STRIPE_MODE_SANDBOX',  'sandbox');

//////// USERS

if(!defined('USER_PENDING')) define('USER_PENDING', 2);

if(!defined('USER_APPROVED')) define('USER_APPROVED', 1);

if(!defined('USER_DECLINED')) define('USER_DECLINED', 0);

if(!defined('USER_EMAIL_NOT_VERIFIED')) define('USER_EMAIL_NOT_VERIFIED', 0);

if(!defined('USER_EMAIL_VERIFIED')) define('USER_EMAIL_VERIFIED', 1);


if(!defined('CONTENT_CREATOR_EMAIL_NOT_VERIFIED')) define('CONTENT_CREATOR_EMAIL_NOT_VERIFIED', 0);

if(!defined('CONTENT_CREATOR_EMAIL_VERIFIED')) define('CONTENT_CREATOR_EMAIL_VERIFIED', 1);

//////// USERS END

/***** ADMIN CONTROLS KEYS ********/

if(!defined('ADMIN_CONTROL_ENABLED')) define('ADMIN_CONTROL_ENABLED', 1);
if(!defined('ADMIN_CONTROL_DISABLED')) define('ADMIN_CONTROL_DISABLED', 0);

if(!defined('NO_DEVICE_TOKEN')) define("NO_DEVICE_TOKEN", "NO_DEVICE_TOKEN");

if(!defined('PLAN_TYPE_MONTH')) define('PLAN_TYPE_MONTH', 'months');

if(!defined('PLAN_TYPE_YEAR')) define('PLAN_TYPE_YEAR', 'years');

if(!defined('PLAN_TYPE_WEEK')) define('PLAN_TYPE_WEEK', 'weeks');

if(!defined('PLAN_TYPE_DAY')) define('PLAN_TYPE_DAY', 'days');

if(!defined('PLAN_TYPE_VOD')) define('PLAN_TYPE_VOD', 'vod');

if(!defined('TODAY')) define('TODAY', 'today');

if(!defined('COMPLETED')) define('COMPLETED',3);

if(!defined('SORT_BY_APPROVED')) define('SORT_BY_APPROVED',1);

if(!defined('SORT_BY_DECLINED')) define('SORT_BY_DECLINED',0);

if(!defined('SORT_BY_EMAIL_VERIFIED')) define('SORT_BY_EMAIL_VERIFIED',3);

if(!defined('SORT_BY_EMAIL_NOT_VERIFIED')) define('SORT_BY_EMAIL_NOT_VERIFIED',4);

if(!defined('SORT_BY_DOCUMENT_VERIFIED')) define('SORT_BY_DOCUMENT_VERIFIED',5);

if(!defined('SORT_BY_DOCUMENT_APPROVED')) define('SORT_BY_DOCUMENT_APPROVED',6);

if(!defined('SORT_BY_DOCUMENT_PENDING')) define('SORT_BY_DOCUMENT_PENDING',7);


if(!defined('STATIC_PAGE_SECTION_1')) define('STATIC_PAGE_SECTION_1', 1);

if(!defined('STATIC_PAGE_SECTION_2')) define('STATIC_PAGE_SECTION_2', 2);

if(!defined('STATIC_PAGE_SECTION_3')) define('STATIC_PAGE_SECTION_3', 3);

if(!defined('STATIC_PAGE_SECTION_4')) define('STATIC_PAGE_SECTION_4', 4);

if(!defined('USER_DOCUMENT_VERIFIED')) define('USER_DOCUMENT_VERIFIED', 1);


if(!defined('STARDOM')) define('STARDOM', 'stardom');

if(!defined('USER'))  define('USER', 'user');

if(!defined('FREE')) define('FREE', 3);

if(!defined('FREE_POST')) define('FREE_POST',0);

if(!defined('PAID_POST')) define('PAID_POST',1);

if(!defined('SORT_BY_FREE_POST')) define('SORT_BY_FREE_POST',5);

if(!defined('SORT_BY_PAID_POST')) define('SORT_BY_PAID_POST',6);

if(!defined('SORT_BY_ORDER_PLACED')) define('SORT_BY_ORDER_PLACED',1);

if(!defined('SORT_BY_ORDER_SHIPPED')) define('SORT_BY_ORDER_SHIPPED',2);

if(!defined('SORT_BY_ORDER_DELIVERD')) define('SORT_BY_ORDER_DELIVERD',3);

if(!defined('SORT_BY_ORDER_CANCELLED')) define('SORT_BY_ORDER_CANCELLED',4);

if(!defined('SORT_BY_ORDER_PACKED')) define('SORT_BY_ORDER_PACKED',5);


if(!defined('ORDER_PLACED')) define('ORDER_PLACED',0);

if(!defined('ORDER_PLACED')) define('ORDER_PLACED',1);

if(!defined('ORDER_SHIPPED')) define('ORDER_SHIPPED',2);

if(!defined('ORDER_DELIVERD')) define('ORDER_DELIVERD',3);

if(!defined('ORDER_CACELLED')) define('ORDER_CACELLED',4);

if(!defined('ORDER_PACKED')) define('ORDER_PACKED',5);

if(!defined('PAYMENT_OFFLINE')) define('PAYMENT_OFFLINE','offline_payment');

if(!defined('WITHDRAW_INITIATED')) define('WITHDRAW_INITIATED', 0);

if(!defined('WITHDRAW_PAID')) define('WITHDRAW_PAID', 1);

if(!defined('WITHDRAW_ONHOLD')) define('WITHDRAW_ONHOLD', 2);

if(!defined('WITHDRAW_DECLINED')) define('WITHDRAW_DECLINED', 3);

if(!defined('WITHDRAW_CANCELLED')) define('WITHDRAW_CANCELLED', 4);

if(!defined('USER_WALLET_PAYMENT_INITIALIZE')) define('USER_WALLET_PAYMENT_INITIALIZE', 0);
if(!defined('USER_WALLET_PAYMENT_PAID')) define('USER_WALLET_PAYMENT_PAID', 1);
if(!defined('USER_WALLET_PAYMENT_UNPAID')) define('USER_WALLET_PAYMENT_UNPAID', 2);
if(!defined('USER_WALLET_PAYMENT_CANCELLED')) define('USER_WALLET_PAYMENT_CANCELLED', 3);
if(!defined('USER_WALLET_PAYMENT_DISPUTED')) define('USER_WALLET_PAYMENT_DISPUTED', 4);
if(!defined('USER_WALLET_PAYMENT_WAITING')) define('USER_WALLET_PAYMENT_WAITING', 5);


// amount_type - add and debitedd
if(!defined('WALLET_AMOUNT_TYPE_ADD')) define('WALLET_AMOUNT_TYPE_ADD', 'add');
if(!defined('WALLET_AMOUNT_TYPE_MINUS')) define('WALLET_AMOUNT_TYPE_MINUS', 'minus');

// payment type - specifies the transaction usage
if(!defined('WALLET_PAYMENT_TYPE_ADD')) define('WALLET_PAYMENT_TYPE_ADD', 'add');
if(!defined('WALLET_PAYMENT_TYPE_PAID')) define('WALLET_PAYMENT_TYPE_PAID', 'paid');
if(!defined('WALLET_PAYMENT_TYPE_CREDIT')) define('WALLET_PAYMENT_TYPE_CREDIT', 'credit');
if(!defined('WALLET_PAYMENT_TYPE_WITHDRAWAL')) define('WALLET_PAYMENT_TYPE_WITHDRAWAL', 'withdrawal');

if (!defined('PAID_STATUS')) define('PAID_STATUS', 1);


// Subscribed user status

if(!defined('SUBSCRIBED_USER')) define('SUBSCRIBED_USER', 1);

if(!defined('NON_SUBSCRIBED_USER')) define('NON_SUBSCRIBED_USER', 0);

if(!defined('TAKE_COUNT')) define('TAKE_COUNT', 12);

if(!defined('SHOW')) define('SHOW', 1);

if(!defined('HIDE')) define('HIDE', 0);

if(!defined('READ')) define('READ', 1);

if(!defined('UNREAD')) define('UNREAD', 0);

// AUTORENEWAL STATUS

if(!defined('AUTORENEWAL_ENABLED')) define('AUTORENEWAL_ENABLED',0);

if(!defined('AUTORENEWAL_CANCELLED')) define('AUTORENEWAL_CANCELLED',1);

if(!defined('PRODUCT_AVAILABLE')) define('PRODUCT_AVAILABLE',1);

if(!defined('PRODUCT_NOT_AVAILABLE')) define('PRODUCT_NOT_AVAILABLE',0);

if(!defined('PUBLISHED')) define('PUBLISHED',1);

if(!defined('UNPUBLISHED')) define('UNPUBLISHED', 0);


if(!defined('USER_DOCUMENT_NONE')) define('USER_DOCUMENT_NONE', 0);
if(!defined('USER_DOCUMENT_PENDING')) define('USER_DOCUMENT_PENDING', 1);
if(!defined('USER_DOCUMENT_APPROVED')) define('USER_DOCUMENT_APPROVED', 2);
if(!defined('USER_DOCUMENT_DECLINED')) define('USER_DOCUMENT_DECLINED', 3);

if(!defined('USER_FREE_ACCOUNT')) define('USER_FREE_ACCOUNT', 0);
if(!defined('USER_PREMIUM_ACCOUNT')) define('USER_PREMIUM_ACCOUNT', 1);

if(!defined('USER_SUBSCRIPTION_MONTHLY')) define('USER_SUBSCRIPTION_MONTHLY', 'monthly');
if(!defined('USER_SUBSCRIPTION_YEARLY')) define('USER_SUBSCRIPTION_YEARLY', 'yearly');

if(!defined('BOOKMARK_TYPE_ALL')) define('BOOKMARK_TYPE_ALL', 'all');
if(!defined('BOOKMARK_TYPE_PHOTOS')) define('BOOKMARK_TYPE_PHOTOS', 'photos');
if(!defined('BOOKMARK_TYPE_VIDEOS')) define('BOOKMARK_TYPE_VIDEOS', 'videos');
if(!defined('BOOKMARK_TYPE_AUDIOS')) define('BOOKMARK_TYPE_AUDIOS', 'audios');
if(!defined('BOOKMARK_TYPE_LOCKED')) define('BOOKMARK_TYPE_LOCKED', 'locked');
if(!defined('BOOKMARK_TYPE_OTHERS')) define('BOOKMARK_TYPE_OTHERS', 'others');

// Bell notification status

if(!defined('BELL_NOTIFICATION_STATUS_UNREAD')) define('BELL_NOTIFICATION_STATUS_UNREAD', 1);

if(!defined('BELL_NOTIFICATION_STATUS_READ')) define('BELL_NOTIFICATION_STATUS_READ', 2);

if(!defined('POSTS_ALL')) define('POSTS_ALL', 'all');
if(!defined('POSTS_IMAGE')) define('POSTS_IMAGE', 'image');
if(!defined('POSTS_VIDEO')) define('POSTS_VIDEO', 'video');
if(!defined('POSTS_AUDIO')) define('POSTS_AUDIO', 'audio');
if(!defined('POSTS_TEXT')) define('POSTS_TEXT', 'text');
if(!defined('POSTS_LOCKED')) define('POSTS_LOCKED', 'locked');

if(!defined('POSTS_PAYMENT_SUBSCRIPTION')) define('POSTS_PAYMENT_SUBSCRIPTION', 'subscription');

if(!defined('POSTS_PAYMENT_PPV')) define('POSTS_PAYMENT_PPV', 'ppv');


if(!defined('FOLLOWER_ACTIVE')) define('FOLLOWER_ACTIVE', 1);

if(!defined('FOLLOWER_EXPIRED')) define('FOLLOWER_EXPIRED', 0);

if(!defined('BELL_NOTIFICATION_TYPE_FOLLOW')) define('BELL_NOTIFICATION_TYPE_FOLLOW', 'follow');
if(!defined('BELL_NOTIFICATION_TYPE_NEW_POST')) define('BELL_NOTIFICATION_TYPE_NEW_POST', 'new-post');
if(!defined('BELL_NOTIFICATION_TYPE_LIKE')) define('BELL_NOTIFICATION_TYPE_LIKE', 'like');
if(!defined('BELL_NOTIFICATION_TYPE_DISLIKE')) define('BELL_NOTIFICATION_TYPE_DISLIKE', 'dislike');
if(!defined('BELL_NOTIFICATION_TYPE_POST_COMMENT')) define('BELL_NOTIFICATION_TYPE_POST_COMMENT', 'comment');
if(!defined('BELL_NOTIFICATION_TYPE_SUBSCRIPTION')) define('BELL_NOTIFICATION_TYPE_SUBSCRIPTION', 'subscription');
if(!defined('BELL_NOTIFICATION_TYPE_SEND_TIP')) define('BELL_NOTIFICATION_TYPE_SEND_TIP', 'tips');
if(!defined('BELL_NOTIFICATION_TYPE_POST_PAYMENT')) define('BELL_NOTIFICATION_TYPE_POST_PAYMENT', 'post-payment');
if(!defined('BELL_NOTIFICATION_TYPE_CHAT_ASSET_PAYMENT')) define('BELL_NOTIFICATION_TYPE_CHAT_ASSET_PAYMENT', 'chat-asset-payment');
if(!defined('BELL_NOTIFICATION_TYPE_VIDEO_CALL')) define('BELL_NOTIFICATION_TYPE_VIDEO_CALL', 'video-call');
if(!defined('BELL_NOTIFICATION_TYPE_VIDEO_CALL_PAYMENT')) define('BELL_NOTIFICATION_TYPE_VIDEO_CALL_PAYMENT', 'video-call-payment');
if(!defined('BELL_NOTIFICATION_TYPE_AUDIO_CALL')) define('BELL_NOTIFICATION_TYPE_AUDIO_CALL', 'audio-call');
if(!defined('BELL_NOTIFICATION_TYPE_AUDIO_CALL_PAYMENT')) define('BELL_NOTIFICATION_TYPE_AUDIO_CALL_PAYMENT', 'audio-call-payment');
if(!defined('BELL_NOTIFICATION_TYPE_LIVE_VIDEO')) define('BELL_NOTIFICATION_TYPE_LIVE_VIDEO', 'live-video');
if(!defined('BELL_NOTIFICATION_TYPE_LIVE_VIDEO_PAYMENT')) define('BELL_NOTIFICATION_TYPE_LIVE_VIDEO_PAYMENT', 'live-video-payment');
if(!defined('BELL_NOTIFICATION_TYPE_CHAT')) define('BELL_NOTIFICATION_TYPE_CHAT', 'chat');
if(!defined('BELL_NOTIFICATION_TYPE_ORDER')) define('BELL_NOTIFICATION_TYPE_ORDER', 'order');

if(!defined('PRODUCTION')) define('PRODUCTION', 'production');
if(!defined('SANDBOX')) define('SANDBOX', 'sandbox');

if(!defined('FILE_TYPE_IMAGE')) define('FILE_TYPE_IMAGE', 'image');
if(!defined('FILE_TYPE_VIDEO')) define('FILE_TYPE_VIDEO', 'video');
if(!defined('FILE_TYPE_AUDIO')) define('FILE_TYPE_AUDIO', 'audio');
if(!defined('FILE_TYPE_TEXT')) define('FILE_TYPE_TEXT', 'text');
if(!defined('FILE_TYPE_DOCUMENT')) define('FILE_TYPE_DOCUMENT', 'document');
if(!defined('FILE_TYPE_URL')) define('FILE_TYPE_URL', 'url');

if(!defined('STORAGE_TYPE_S3')) define('STORAGE_TYPE_S3', 1);
if(!defined('STORAGE_TYPE_LOCAL')) define('STORAGE_TYPE_LOCAL', 0);

if(!defined('USAGE_TYPE_PPV')) define('USAGE_TYPE_PPV', 'ppv');

if(!defined('USAGE_TYPE_SUBSCRIPTION')) define('USAGE_TYPE_SUBSCRIPTION', 'subscription');

if(!defined('USAGE_TYPE_TIP')) define('USAGE_TYPE_TIP', 'tip');

if(!defined('USAGE_TYPE_SEND_MONEY')) define('USAGE_TYPE_SEND_MONEY', 'Sent Money');

if(!defined('USAGE_TYPE_WITHDRAW')) define('USAGE_TYPE_WITHDRAW', 'withdraw');

if(!defined('USAGE_TYPE_REFERRAL')) define('USAGE_TYPE_REFERRAL', 'referral');

if(!defined('USAGE_TYPE_CHAT')) define('USAGE_TYPE_CHAT', 'chat');
if(!defined('USAGE_TYPE_COLLECTION')) define('USAGE_TYPE_COLLECTION', 'collection');
if(!defined('USAGE_TYPE_CHAT_MESSAGE')) define('USAGE_TYPE_CHAT_MESSAGE', 'chat-message');


if(!defined('SORT_BY_HIGH')) define('SORT_BY_HIGH',1);

if(!defined('SORT_BY_LOW')) define('SORT_BY_LOW',2);

if(!defined('SORT_BY_FREE')) define('SORT_BY_FREE',3);

if(!defined('SORT_BY_PAID')) define('SORT_BY_PAID',4);


if(!defined('TYPE_PUBLIC')) define('TYPE_PUBLIC', 'public');
if(!defined('TYPE_PRIVATE')) define('TYPE_PRIVATE', 'private');

if(!defined('BROADCAST_TYPE_BROADCAST')) define('BROADCAST_TYPE_BROADCAST', 'broadcast');
if(!defined('BROADCAST_TYPE_CONFERENCE')) define('BROADCAST_TYPE_CONFERENCE', 'conference');
if(!defined('BROADCAST_TYPE_SCREENSHARE')) define('BROADCAST_TYPE_SCREENSHARE', 'screenshare');

if(!defined('PAID_VIDEO')) define('PAID_VIDEO', 1);

if(!defined('FREE_VIDEO')) define('FREE_VIDEO', 0);


// VIDEO STATUS

if (!defined('VIDEO_STREAMING_STOPPED')) define('VIDEO_STREAMING_STOPPED' , 1);

if (!defined('VIDEO_STREAMING_ONGOING')) define('VIDEO_STREAMING_ONGOING' , 0);

// VIDEO STATUS

if (!defined('IS_STREAMING_YES')) define('IS_STREAMING_YES' , 1);

if (!defined('IS_STREAMING_NO')) define('IS_STREAMING_NO' , 0);


if (!defined('NORMAL_POST')) define('NORMAL_POST' , 'normal_post');

if (!defined('LIVE_VIDEO')) define('LIVE_VIDEO' , 'live_video');



if (!defined('VIDEO_CALL_REQUEST_SENT')) define('VIDEO_CALL_REQUEST_SENT' , 1);
if (!defined('VIDEO_CALL_REQUEST_ACCEPTED')) define('VIDEO_CALL_REQUEST_ACCEPTED' , 2);
if (!defined('VIDEO_CALL_REQUEST_REJECTED')) define('VIDEO_CALL_REQUEST_REJECTED' , 3);
if (!defined('VIDEO_CALL_REQUEST_JOINED')) define('VIDEO_CALL_REQUEST_JOINED' , 4);
if (!defined('VIDEO_CALL_REQUEST_ENDED')) define('VIDEO_CALL_REQUEST_ENDED' , 5);
if (!defined('VIDEO_CALL_REQUEST_STARTED')) define('VIDEO_CALL_REQUEST_STARTED' , 6);


if(!defined('WATERMARK_TOP_LEFT')) define('WATERMARK_TOP_LEFT','top-left');
if(!defined('WATERMARK_TOP_RIGHT')) define('WATERMARK_TOP_RIGHT','top-right');
if(!defined('WATERMARK_BOTTOM_LEFT')) define('WATERMARK_BOTTOM_LEFT','bottom-left');
if(!defined('WATERMARK_BOTTOM_RIGHT')) define('WATERMARK_BOTTOM_RIGHT','bottom-right');
if(!defined('WATERMARK_CENTER')) define('WATERMARK_CENTER','center');

if (!defined('TIP_PAYMENT')) define('TIP_PAYMENT' , 1);
if (!defined('SUBSCRIPTION_PAYMENT')) define('SUBSCRIPTION_PAYMENT' , 2);
if (!defined('POST_PAYMENT')) define('POST_PAYMENT' , 3);
if (!defined('LIVE_VIDEO_PAYMENT')) define('LIVE_VIDEO_PAYMENT' , 4);

if(!defined('BANK_TYPE_SAVINGS')) define('BANK_TYPE_SAVINGS', 'savings');
if(!defined('BANK_TYPE_CHECKING')) define('BANK_TYPE_CHECKING', 'checking');

if(!defined('DISLIKE')) define('DISLIKE', 0);
if(!defined('LIKE')) define('LIKE',1);
if(!defined('REMOVE_LIKE_OR_DISLIKE')) define('REMOVE_LIKE_OR_DISLIKE',2);

//stories

if(!defined('STORY_ALL')) define('STORY_ALL', 'all');
if(!defined('STORY_IMAGE')) define('STORY_IMAGE', 'image');
if(!defined('STORY_VIDEO')) define('STORY_VIDEO', 'video');
if(!defined('STORY_AUDIO')) define('STORY_AUDIO', 'audio');
if(!defined('STORY_TEXT')) define('STORY_TEXT', 'text');
if(!defined('STORY_LOCKED')) define('STORY_LOCKED', 'locked');

if(!defined('STORIES_APPROVED')) define('STORIES_APPROVED', 1);
if(!defined('STORIES_DECLINED')) define('STORIES_DECLINED', 0);

if(!defined('DEFAULT_USER')) define('DEFAULT_USER', 1);
if(!defined('CONTENT_CREATOR')) define('CONTENT_CREATOR', 2);

if(!defined('WEELKY_REPORT')) define('WEELKY_REPORT', 1);

if(!defined('MONTHLY_REPORT')) define('MONTHLY_REPORT', 2);

if(!defined('CUSTOM_REPORT')) define('CUSTOM_REPORT', 3);

if(!defined('VOD_APPROVED')) define('VOD_APPROVED', 1);
if(!defined('VOD_DECLINED')) define('VOD_DECLINED', 2);

if(!defined('POST_CATEGORY_APPROVED')) define('POST_CATEGORY_APPROVED', 1);
if(!defined('POST_CATEGORY_DECLINED')) define('POST_CATEGORY_DECLINED', 2);

if(!defined('PERCENTAGE')) define('PERCENTAGE',0);

if(!defined('ABSOULTE')) define('ABSOULTE',1);

if(!defined('USAGE_TYPE_VOD')) define('USAGE_TYPE_VOD', 'vod');

if(!defined('PROMO_CODE_APPLIED')) define('PROMO_CODE_APPLIED',1);
if(!defined('PROMO_CODE_NOT_APPLIED')) define('PROMO_CODE_NOT_APPLIED', 0);


if (!defined('AUDIO_CALL_REQUEST_SENT')) define('AUDIO_CALL_REQUEST_SENT' , 1);
if (!defined('AUDIO_CALL_REQUEST_ACCEPTED')) define('AUDIO_CALL_REQUEST_ACCEPTED' , 2);
if (!defined('AUDIO_CALL_REQUEST_REJECTED')) define('AUDIO_CALL_REQUEST_REJECTED' , 3);
if (!defined('AUDIO_CALL_REQUEST_JOINED')) define('AUDIO_CALL_REQUEST_JOINED' , 4);
if (!defined('AUDIO_CALL_REQUEST_ENDED')) define('AUDIO_CALL_REQUEST_ENDED' , 5);
if (!defined('AUDIO_CALL_REQUEST_STARTED')) define('AUDIO_CALL_REQUEST_STARTED' , 6);

if(!defined('SORT_BY_FREE_SUBSCRIPTION')) define('SORT_BY_FREE_SUBSCRIPTION',0);

if(!defined('SORT_BY_PAID_SUBSCRIPTION')) define('SORT_BY_PAID_SUBSCRIPTION',1);

if (!defined('CONTENT_CREATOR_INITIAL')) define('CONTENT_CREATOR_INITIAL' , 0);
if (!defined('CONTENT_CREATOR_DOC_UPLOADED')) define('CONTENT_CREATOR_DOC_UPLOADED' , 1);
if (!defined('CONTENT_CREATOR_DOC_VERIFIED')) define('CONTENT_CREATOR_DOC_VERIFIED' , 2);
if (!defined('CONTENT_CREATOR_BILLING_UPDATED')) define('CONTENT_CREATOR_BILLING_UPDATED' , 3);
if (!defined('CONTENT_CREATOR_SUBSCRIPTION_UPDATED')) define('CONTENT_CREATOR_SUBSCRIPTION_UPDATED' , 4);
if (!defined('CONTENT_CREATOR_APPROVED')) define('CONTENT_CREATOR_APPROVED' , 5);

if(!defined('USAGE_TYPE_VIDEO_CALL')) define('USAGE_TYPE_VIDEO_CALL', 'video call');
if(!defined('USAGE_TYPE_AUDIO_CALL')) define('USAGE_TYPE_AUDIO_CALL', 'audio call');
if(!defined('USAGE_TYPE_ORDER')) define('USAGE_TYPE_ORDER', 'order product');

if (!defined('IS_CURRENT_SESSION')) define('IS_CURRENT_SESSION' , 1);

if (!defined('IS_CURRENT_SESSION_NO')) define('IS_CURRENT_SESSION_NO' , 0);

if(!defined('TWO_STEP_AUTH_DISABLE')) define('TWO_STEP_AUTH_DISABLE', 0);

if(!defined('TWO_STEP_AUTH_ENABLE')) define('TWO_STEP_AUTH_ENABLE', 1);

if(!defined('USAGE_TYPE_LIVE_VIDEO')) define('USAGE_TYPE_LIVE_VIDEO', 'live video');

if(!defined('USAGE_TYPE_PERSONALIZE_REQUEST')) define('USAGE_TYPE_PERSONALIZE_REQUEST', 'personalize request');

if(!defined('OUT_OF_STOCK')) define('OUT_OF_STOCK', 0);

if(!defined('IN_STOCK')) define('IN_STOCK', 1);

if(!defined('SORT_BY_ASC')) define('SORT_BY_ASC',1);

if(!defined('SORT_BY_DESC')) define('SORT_BY_DESC',2);

if(!defined('SORT_BY_CONTENT_ASC')) define('SORT_BY_CONTENT_ASC',3);

if(!defined('SORT_BY_CONTENT_DESC')) define('SORT_BY_CONTENT_DESC',4);

if(!defined('CATEGORY_TYPE_PROFILE')) define('CATEGORY_TYPE_PROFILE', 'profile');
if(!defined('CATEGORY_TYPE_POST')) define('CATEGORY_TYPE_POST', 'post');

if(!defined('SUBSCRIPTION_PAYMENTS')) define('SUBSCRIPTION_PAYMENTS', 'subscription-payments');
if(!defined('USER_TIPS')) define('USER_TIPS', 'user-tips');
if(!defined('POST_PAYMENTS')) define('POST_PAYMENTS', 'post-payments');
if(!defined('VIDEO_CALL_PAYMENTS')) define('VIDEO_CALL_PAYMENTS', 'video-call-payments');
if(!defined('AUDIO_CALL_PAYMENTS')) define('AUDIO_CALL_PAYMENTS', 'audio-call-payments');
if(!defined('CHAT_ASSET_PAYMENTS')) define('CHAT_ASSET_PAYMENTS', 'chat-asset-payments');
if(!defined('CHAT_MESSAGE_PAYMENTS')) define('CHAT_MESSAGE_PAYMENTS', 'chat-message-payments');
if(!defined('ORDER_PAYMENTS')) define('ORDER_PAYMENTS', 'order-payments');
if(!defined('LIVE_VIDEO_PAYMENTS')) define('LIVE_VIDEO_PAYMENTS', 'live-video-payments');
if(!defined('TOTAL_PAYMENTS')) define('TOTAL_PAYMENTS', 'total-payments');
if(!defined('COLLECTION_PAYMENTS')) define('COLLECTION_PAYMENTS', 'collection-payments');
if(!defined('ALL_PAYMENTS')) define('ALL_PAYMENTS', 'all-payments');

if(!defined('PLATFORMS')) define('PLATFORMS', implode(',', [SUBSCRIPTION_PAYMENTS,USER_TIPS,POST_PAYMENTS,VIDEO_CALL_PAYMENTS,AUDIO_CALL_PAYMENTS,CHAT_ASSET_PAYMENTS,ORDER_PAYMENTS,LIVE_VIDEO_PAYMENTS,CHAT_MESSAGE_PAYMENTS, ALL_PAYMENTS, COLLECTION_PAYMENTS]));

if(!defined('ACTIVE')) define('ACTIVE', 1);
if(!defined('INACTIVE')) define('INACTIVE', 0);

if(!defined('DECREMENT')) define('DECREMENT', 0);
if(!defined('INCREMENT')) define('INCREMENT', 1);
if(!defined('NO_CHANGE')) define('NO_CHANGE', 2);

if(!defined('TIPS_TYPE_PROFILE')) define('TIPS_TYPE_PROFILE', 'profile');
if(!defined('TIPS_TYPE_POST')) define('TIPS_TYPE_POST', 'post');
if(!defined('TIPS_TYPE_LIVE_VIDEO')) define('TIPS_TYPE_LIVE_VIDEO', 'live-video');
if(!defined('TIPS_TYPE_VIDEO_CALL')) define('TIPS_TYPE_VIDEO_CALL', 'video-call');
if(!defined('TIPS_TYPE_AUDIO_CALL')) define('TIPS_TYPE_AUDIO_CALL', 'audio-call');
if(!defined('TIPS_TYPE_CHAT_ASSET')) define('TIPS_TYPE_CHAT_ASSET', 'chat-asset');

if(!defined('USAGE_TYPE_RECHARGE')) define('USAGE_TYPE_RECHARGE', 'recharge');

if(!defined('PREFIX')) define('PREFIX', 'prefix');
if(!defined('SUFFIX')) define('SUFFIX', 'suffix');

if(!defined('CHAT_MESSAGE_STATUS_SENT')) define('CHAT_MESSAGE_STATUS_SENT', 1);
if(!defined('CHAT_MESSAGE_STATUS_RECEIVED')) define('CHAT_MESSAGE_STATUS_RECEIVED', 2);

if(!defined('FILE_ORIGIN_PROFILE')) define('FILE_ORIGIN_PROFILE', 'common');
if(!defined('FILE_ORIGIN_POST')) define('FILE_ORIGIN_POST', 'post');
if(!defined('FILE_ORIGIN_LIVE')) define('FILE_ORIGIN_LIVE', 'live');
if(!defined('FILE_ORIGIN_AUDIO_CALL')) define('FILE_ORIGIN_AUDIO_CALL', 'audio');
if(!defined('FILE_ORIGIN_VIDEO_CALL')) define('FILE_ORIGIN_VIDEO_CALL', 'video');
if(!defined('FILE_ORIGIN_CHAT_ASSET')) define('FILE_ORIGIN_CHAT_ASSET', 'chat-asset');
if(!defined('FILE_ORIGIN_STORY')) define('FILE_ORIGIN_STORY', 'story');
if(!defined('FILE_ORIGIN_ECOM')) define('FILE_ORIGIN_ECOM', 'ecom');


if(!defined('VIRTUAL_EXPERIENCE_CREATED')) define('VIRTUAL_EXPERIENCE_CREATED', 1);

if(!defined('VIRTUAL_EXPERIENCE_STARTED')) define('VIRTUAL_EXPERIENCE_STARTED', 2);

if(!defined('VIRTUAL_EXPERIENCE_COMPLETED')) define('VIRTUAL_EXPERIENCE_COMPLETED', 3);

if(!defined('VIRTUAL_EXPERIENCE_CANCELLED')) define('VIRTUAL_EXPERIENCE_CANCELLED', 4);

if(!defined('VIRTUAL_EXPERIENCE_FULL')) define('VIRTUAL_EXPERIENCE_FULL', 5);

define('VIRTUAL_EXPERIENCE_PAID', 6);
define('VIRTUAL_EXPERIENCE_UNPAID', 7);

define('SCHEDULED_START_TIME', '00:00:00'); // Start time set to midnight

define('SCHEDULED_END_TIME', '23:59:59');

define('DEFAULT_TIMEZONE', "Asia/Kolkata");

define('ALL_USERS', "all-users");
define('CONTENT_CREATORS', "content-creators");
define('DEFAULT_USERS', "default-users");

define('ALL_USERS_NUMBER', 0);
define('NORMAL_USER_NUMBER', 1);
define('CONTENT_CREATOR_NUMBER', 2);

define('PERSONALIZE_USER_REQUESTED', 1);
define('PERSONALIZE_CREATOR_ACCEPTED', 2);
define('PERSONALIZE_CREATOR_REJECTED', 3);
define('PERSONALIZE_USER_REJECTED', 4);
define('PERSONALIZE_USER_PAID', 5);
define('PERSONALIZE_CREATOR_UPLOADED', 6);
define('PERSONALIZE_USER_CANCELLED', 7);

define('PRODUCT_TYPE_NONE', 0);
define('PRODUCT_TYPE_DIGITAL', 1);
define('PRODUCT_TYPE_PHYSICAL', 2);

define('PERSONALIZE_TYPE_IMAGE', 'image');
define('PERSONALIZE_TYPE_VIDEO', 'video');
define('PERSONALIZE_TYPE_PRODUCT', 'product');
define('PERSONALIZE_TYPE_AUDIO', 'audio');

define('STREAM_TYPE_PUBLIC', 'public');
define('STREAM_TYPE_PRIVATE', 'private');

define('PAYMENT_TYPE_FREE', 'free');
define('PAYMENT_TYPE_PAID', 'paid');

define('SCHEDULE_TYPE_NOW', 1);
define('SCHEDULE_TYPE_LATER', 2);

define('LIVE_STREAM_SHOPPING_CREATED', 1);
define('LIVE_STREAM_SHOPPING_ONGOING', 2);
define('LIVE_STREAM_SHOPPING_COMPLETED', 3);

define('USAGE_TYPE_LIVE_STREAM_SHOPPING', 'live-stream-shopping');

define('LAST_7_DAYS', 7);
define('LAST_30_DAYS', 30);
define('LAST_90_DAYS', 90);

define('EXPIRY', 'expiry');

define('PUBLISH_TYPE_ALL', 'all');
define('PUBLISH_TYPE_FOLLOWERS', 'followers');
define('PUBLISH_TYPE_FAVOURITES', 'favourites');

define('VIP_VE_RAISED', 1);
define('VIP_VE_ACCEPTED', 2);
define('VIP_VE_CANCELED', 3);
define('VIP_VE_PAID', 4);

define('VIP_VE_SCHEDULED', 1);
define('VIP_VE_BOOKED', 2);