<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Antvel\Orders\Models\Concerns;

use Antvel\Users\Models\User;
use Antvel\Product\Models\Product;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Antvel\Orders\Models\OrderDetail;
use Antvel\AddressBook\Models\Address;
use Antvel\Users\Notifications\OrderWasUpdated;
use Antvel\Product\Repositories\ProductsRepository;

trait Management
{
    /**
     * Start the checkout process for any type of order.
     *
     * @param int $type_order Type of order to be processed
     *
     * @return Response
     */
    public static function placeOrders($type_order)
    {
        $cart = self::ofType($type_order)
            ->where('user_id', Auth::user()->id)
            ->orderBy('id', 'desc')
            ->first();

        $show_order_route = 'orders.show_cart';

        $cartDetail = OrderDetail::where('order_id', $cart->id)->get();

        $address_id = 0;

        //When address is invalid, it is because it comes from the creation of a free product. You must have a user direction (Default)
        if (is_null($cart->address_id)) {
            $useraddress = Address::auth()->orderBy('default', 'DESC')->first();
            if ($useraddress) {
                $address_id = $useraddress->address_id;
            } else {
                return trans('address.no_registered');
            }
        } else {
            $address_id = $cart->address_id;
        }

        $address = Address::where('id', $address_id)->first();

        //Checks if the user has points for the cart price and the store has stock
        //and set the order prices to the current ones if different
        //Creates the lists or sellers to send mail to
        $total_points = 0;
        $seller_email = [];
        foreach ($cartDetail as $orderDetail) {
            $product = Product::find($orderDetail->product_id);
            $seller = User::find($product->updated_by);

            // dd($product, $seller);

            if (!in_array($seller->email, $seller_email)) {
                $seller_email[] = $seller->email;
            }
            $total_points += $orderDetail->quantity * $product->price;
            if ($orderDetail->price != $product->price) {
                $orderDetail->price = $product->price;
                $orderDetail->save();
            }

            if ($product->stock < $orderDetail->quantity) {
                return trans('store.insufficientStock');
            }
        }

        //Checks if the user has points for the cart price
        $user = Auth::user();
        if ($user->current_points < $total_points && config('app.payment_method') == 'Points') {
            return trans('store.cart_view.insufficient_funds');
        }

        if (config('app.payment_method') == 'Points') {
            $negativeTotal = -1 * $total_points;
            //7 is the action type id for order checkout
            // $pointsModified = $user->modifyPoints($negativeTotal, 7, $cart->id);
            // while refactoring
        } else {
            $pointsModified = true;
        }

        if ($pointsModified) {
            //Separate the order for each seller
            //Looks for all the different sellers in the cart
            $sellers = [];
            foreach ($cartDetail as $orderDetail) {
                if (!in_array($orderDetail->product->updated_by, $sellers)) {
                    $sellers[] = $orderDetail->product->updated_by;
                }
            }
            foreach ($sellers as $seller) {
                //Creates a new order and address for each seller
                $newOrder = new self();
                $newOrder->user_id = $user->id;
                $newOrder->address_id = $address->id;
                $newOrder->status = ($type_order == 'freeproduct') ? 'paid' : 'open';
                $newOrder->type = ($type_order == 'freeproduct') ? 'freeproduct' : 'order';
                $newOrder->seller_id = $seller;
                $newOrder->save();

                $newOrder->seller->notify(new OrderWasUpdated($newOrder));

                //moves the details to the new orders
                foreach ($cartDetail as $orderDetail) {
                    if ($orderDetail->product->updated_by == $seller) {
                        $orderDetail->order_id = $newOrder->id;
                        $orderDetail->save();
                    }

                    //Increasing product counters.
                    (new ProductsRepository)->increment('sale_counts', $orderDetail->product);

                    //saving tags in users preferences
                    if (trim($orderDetail->product->tags) != '' && auth()->check()) {
                        auth()->user()->updatePreferences('product_purchased', $product->tags);
                    }
                }
            }

            //Changes the stock of each product in the order
            foreach ($cartDetail as $orderDetail) {
                $product = Product::find($orderDetail->product_id);
                $product->stock = $product->stock - $orderDetail->quantity;
                $product->save();
            }
            foreach ($seller_email as $email) {
                $mailed_order = self::where('id', $newOrder->id)->with('details')->get()->first();

                //Send a mail to the user: Order has been placed
                $data = [
                    'orderId' => $newOrder->id,
                    'order'   => $mailed_order,
                ];
                //dd($data['order']->details,$newOrder->id);
                $title = trans('email.new_order_for_user.subject')." (#$newOrder->id)";
                Mail::send('emails.neworder', compact('data', 'title'), function ($message) use ($user) {
                    $message->to($user->email)->subject(trans('email.new_order_for_user.subject'));
                });
                //Send a mail to the seller: Order has been placed
                $title = trans('email.new_order_for_seller.subject')." (#$newOrder->id)";
                Mail::send('emails.sellerorder', compact('data', 'title'), function ($message) use ($email) {
                    $message->to($email)->subject(trans('email.new_order_for_seller.subject'));
                });
            }

            return;
        } else {
            return trans('store.insufficientFunds');
        }
    }
}
