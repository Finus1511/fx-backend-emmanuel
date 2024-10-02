<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{

    protected $guarded = ['id'];

    protected $appends = ['subscription_id', 'subscription_unique_id', 'amount_formatted','plan_type_formatted'];

    public function getSubscriptionIdAttribute() {

        return $this->id;
    }

    public function getSubscriptionUniqueIdAttribute() {

        return $this->unique_id;
    }

    public function getAmountFormattedAttribute() {

    	return formatted_amount($this->amount);
    }

    public function getPlanTypeFormattedAttribute() {

    	return formatted_plan($this->plan, $this->plan_type);
    }

    /**
     * Scope a query to basic subscription details
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeApproved($query) {

        return $query->where('subscriptions.status', APPROVED);
    }

    public function subscriptionPayments() {
        return $this->hasMany('App\Models\SubscriptionPayment', 'subscription_id');
    }
}
